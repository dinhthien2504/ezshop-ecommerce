<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        return response()->json([
            'message_success' => 'Tạo tài khoản thành công.',
        ], 201);
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_name' => 'required|string|max:255',
                'email' => 'required|email|unique:tbl_users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'user_name' => $validated['user_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json(['message' => 'Đăng ký thành công.'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $login = $request->input('login');
            $password = $request->input('password');

            $user = User::where('email', $login)
                ->orWhere('phone', $login)
                ->orWhere('user_name', $login)
                ->first();

            if (!$user) {
                return response()->json(['message' => 'Tài khoản không tồn tại'], 400);
            }

            if (!Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Mật khẩu không chính xác'], 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->load('shop');
            return response()->json([
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'user_name' => $user->user_name,
                'avatar' => $user->avatar,
                'google_id' => $user->google_id,
                'role' => $user->role,
                'shop_id' => $user->shop->id ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function getUsers(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = User::with('rank');
        $query->whereNull('role_id');
        if ($search) {
            $query->where('user_name', 'like', '%' . $search . '%');
        }
        ;
        $users = $query->paginate($perPage);

        $usersData = collect($users->items())->map(function ($user) {
            return [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'rank' => $user->rank ? $user->rank->name : null,
                'status' => $user->status,
                'avatar' => $user->avatar
            ];
        });

        return response()->json([
            'users' => $usersData,
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
        ]);
    }

    public function changeStatus($id)
    {
        $user = User::find($id);
        if ($user->status === 1) {
            $user->status = 0;
        } else {
            $user->status = 1;
        }

        $user->save();

        return response()->json([
            'message' => 'Đổi trạng thái thành công'
        ]);
    }

    public function lockMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $user) {
                $user->status = 0; // 0 = khóa
                $user->save();
            }
            Log::info('Đã khóa nhiều user với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã khóa các user thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi khóa nhiều user: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi khóa các user'], 500);
        }
    }

    public function unlockMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $user) {
                $user->status = 1; // 1 = mở khóa
                $user->save();
            }
            Log::info('Đã mở khóa nhiều user với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã mở khóa các user thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi mở khóa nhiều user: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi mở khóa các user'], 500);
        }
    }

    public function getStaffs(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = User::with('role');
        $query->whereNotNull('role_id')->where('role_id', '!=', 2); // Lấy tất cả nhân viên trừ quản trị viên
        if ($search) {
            $query->where('user_name', 'like', '%' . $search . '%');
        }
        ;
        $users = $query->paginate($perPage);

        $usersData = collect($users->items())->map(function ($user) {
            return [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role ? $user->role->title : null,
                'role_id' => $user->role_id,
                'status' => $user->status,
                'avatar' => $user->avatar
            ];
        });

        return response()->json([
            'users' => $usersData,
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
        ]);
    }

    public function changeRole(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $user->role_id = $request->input('role_id');
            $user->save();
            return response()->json(['message' => 'role updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật role: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật role'], 500);
        }
    }

    public function storeStaff(Request $request)
    {
        User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'role_id' => 1
        ]);

        return response()->json([
            'message_success' => 'Tạo tài khoản thành công.',
        ], 201);
    }


    public function getUserProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tính tổng số tiền đã chi tiêu
        $totalSpent = $user->orders()
            ->where(function ($query) {
                $query->where('order_status', 5)
                    ->orWhere('payment_status', 'paid');
            })
            ->sum('total_amount');

        // Lấy rank hiện tại
        $currentRank = $user->rank;

        // Lấy rank tiếp theo
        $nextRank = null;
        if ($currentRank) {
            $nextRank = \App\Models\Rank::where('min_spent', '>', $currentRank->max_spent)
                ->orderBy('min_spent', 'asc')
                ->first();
        } else {
            // Nếu chưa có rank, lấy rank đầu tiên
            $nextRank = \App\Models\Rank::orderBy('min_spent', 'asc')->first();
        }

        // Tính tiến độ
        $progress = 0;
        $progressToNext = 0;
        $remainingAmount = 0;

        if ($currentRank) {
            // Tiến độ trong rank hiện tại
            $rankRange = $currentRank->max_spent - $currentRank->min_spent;
            $userProgressInRank = $totalSpent - $currentRank->min_spent;
            $progress = $rankRange > 0 ? ($userProgressInRank / $rankRange) * 100 : 100;
            $progress = min(100, max(0, $progress));

            // Tiến độ đến rank tiếp theo
            if ($nextRank) {
                $remainingAmount = $nextRank->min_spent - $totalSpent;
                $progressToNext = max(0, ($totalSpent / $nextRank->min_spent) * 100);
            }
        } else if ($nextRank) {
            // Chưa có rank, tính tiến độ đến rank đầu tiên
            $remainingAmount = $nextRank->min_spent - $totalSpent;
            $progressToNext = max(0, ($totalSpent / $nextRank->min_spent) * 100);
        }

        $userData = [
            'id' => $user->id,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'role' => $user->role ? $user->role->title : null,
            'rank' => $user->rank ? $user->rank->name : null,
            'status' => $user->status,
            'email_verified_at' => $user->email_verified_at,
            'google_id' => $user->google_id,
            'created_at' => $user->created_at,
            'total_spent' => $totalSpent,
            'current_rank' => $currentRank ? [
                'id' => $currentRank->id,
                'name' => $currentRank->name,
                'min_spent' => $currentRank->min_spent,
                'max_spent' => $currentRank->max_spent,
                'benefits' => $currentRank->benefits,
                'progress' => round($progress, 2)
            ] : null,
            'next_rank' => $nextRank ? [
                'id' => $nextRank->id,
                'name' => $nextRank->name,
                'min_spent' => $nextRank->min_spent,
                'max_spent' => $nextRank->max_spent,
                'benefits' => $nextRank->benefits,
                'remaining_amount' => max(0, $remainingAmount),
                'progress_to_next' => round(min(100, $progressToNext), 2)
            ] : null,
        ];

        return response()->json([
            'user' => $userData
        ]);
    }

    public function updateUserProfile(UpdateUserRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $data = [];
            // $data = $request->only(['email', 'phone']);
            if ($request->has('email')) {
                // Kiểm tra xem email có thay đổi không
                if ($user->email !== $request->input('email')) {
                    $data['email'] = $request->input('email');
                    // Nếu email thay đổi, đặt email_verified_at về null
                    $data['email_verified_at'] = null;
                }
            }
            if ($request->has('phone')) {
                $data['phone'] = $request->input('phone');
            }

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $uploadPath = base_path('../frontend/public/imgs/users');
                Log::info('Upload path: ' . $uploadPath);

                if (!file_exists($uploadPath)) {
                    Log::info('Thư mục chưa tồn tại. Đang tạo...');
                    mkdir($uploadPath, 0777, true);
                } else {
                    Log::info('Thư mục đã tồn tại.');
                }

                // Xóa ảnh cũ nếu tồn tại
                if (!empty($user->avatar)) {
                    $oldPath = $uploadPath . '/' . $user->avatar;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                        Log::info('Đã xóa avatar cũ: ' . $oldPath);
                    }
                }

                $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                Log::info('Đã lưu avatar thành: ' . $filename);
                $data['avatar'] = $filename;
            }

            $user->update($data);

            return response()->json([
                'user' => $user,
                'success' => true,
                'message' => 'Cập nhật thông tin thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật thông tin người dùng: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật thông tin người dùng'], 500);
        }
    }

    public function validatePassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu cũ không đúng.',
                'success' => false,
                'error' => 'Mật khẩu cũ không đúng.'
            ]);
        }

        if ($request->old_password === $request->new_password) {
            return response()->json([
                'message' => 'Mật khẩu mới không được trùng với mật khẩu cũ.',
                'success' => false,
                'error' => 'Mật khẩu mới không được trùng với mật khẩu cũ'
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }


    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'Đổi mật khẩu thành công.',
            'success' => true
        ]);
    }
}
