<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rank;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRankRequest;
use App\Http\Requests\UpdateRankRequest;

class RankController extends Controller
{
    public function getRanks(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = Rank::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        };
        $ranks = $query->paginate($perPage);

        return response()->json([
            'ranks' => $ranks->items(),
            'current_page' => $ranks->currentPage(),
            'per_page' => $ranks->perPage(),
            'last_page' => $ranks->lastPage(),
            'total' => $ranks->total(),
        ]);
    }

    public function store(StoreRankRequest $request)
    {
        // Lấy dữ liệu đã validate
        $data = $request->validated();

        // Tạo mới cấp bậc
        $rank = Rank::create($data);

        return response()->json([
            'message' => 'Thêm cấp bậc thành công',
            'rank' => $rank
        ]);
    }

    public function destroy($id)
    {
        try {
            $rank = Rank::findOrFail($id);
            $rank->delete();
            Log::info('Đã xóa rank với ID: ' . $id);
            return response()->json(['message' => 'rank deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa rank: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa rank'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $ranks = Rank::whereIn('id', $ids)->get();
            foreach ($ranks as $rank) {
                $rank->delete();
            }
            Log::info('Đã xóa nhiều rank với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã xóa các rank thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa nhiều rank: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa các rank'], 500);
        }
    }

    public function update(UpdateRankRequest $request, $id)
    {
        try {
            $rank = Rank::findOrFail($id);
            $rank->name = $request->input('name');
            $rank->min_spent = $request->input('min_spent');
            $rank->max_spent = $request->input('max_spent');
            $rank->benefits = $request->input('benefits');
            $rank->save();
            return response()->json(['message' => 'rank updated successfully', 'rank' => $rank], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật rank: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật rank'], 500);
        }
    }

    public function updateUserRank()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $totalSpent = $user->orders()
            ->where(function($query) {
                $query->where('order_status', 5)
                      ->orWhere('payment_status', 'paid');
            })
            ->sum('total_amount');
            
        $rank = Rank::where('min_spent', '<=', $totalSpent)
            ->where('max_spent', '>=', $totalSpent)
            ->first();

        if ($rank) {
            $user->rank_id = $rank->id;
            $user->save();
            
            return response()->json([
                'message' => 'Cập nhật rank thành công',
                'rank' => $rank->name,
                'total_spent' => $totalSpent
            ]);
        }
        
        return response()->json([
            'message' => 'Không tìm thấy rank phù hợp',
            'total_spent' => $totalSpent
        ]);
    }
}
