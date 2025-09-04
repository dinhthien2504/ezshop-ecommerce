<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViolationType;
use App\Models\Violation;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreViolationTypeRequest;
use App\Http\Requests\UpdateViolationTypeRequest;

class ViolationController extends Controller
{
    public function getViolationTypes()
    {
        $types = ViolationType::all();

        return response()->json(
            [
                'types' => $types
            ]
        );
    }

    public function sendReport(Request $request)
    {
        $user = auth()->user();

        Violation::create([
            'reporter_id' => $user->id,
            'shop_id' => $request->shop_id,
            'violation_type_id' => $request->type_id,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Đã gửi báo cáo thành công'
        ], 201);
    }

    public function getTypesAdmin (Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = ViolationType::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        };
        $types = $query->paginate($perPage);

        return response()->json([
            'types' => $types->items(),
            'current_page' => $types->currentPage(),
            'per_page' => $types->perPage(),
            'last_page' => $types->lastPage(),
            'total' => $types->total(),
        ]);
    }

    public function store(StoreViolationTypeRequest $request)
    {
        // Lấy dữ liệu đã validate
        $data = $request->validated();

        // Tạo mới loại vi phạm
        $type = ViolationType::create($data);

        return response()->json([
            'message' => 'Thêm loại vi phạm thành công',
            'type' => $type
        ]);
    }

    public function destroy($id)
    {
        try {
            $type = ViolationType::findOrFail($id);
            $type->delete();
            Log::info('Đã xóa loại vi phạm với ID: ' . $id);
            return response()->json(['message' => 'Loại vi phạm đã được xóa thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa loại vi phạm: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa loại vi phạm'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $types = ViolationType::whereIn('id', $ids)->get();
            foreach ($types as $type) {
                $type->delete();
            }
            Log::info('Đã xóa nhiều loại vi phạm với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã xóa các loại vi phạm thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa nhiều loại vi phạm: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa các loại vi phạm'], 500);
        }
    }

    public function update(UpdateViolationTypeRequest $request, $id)
    {
        try {
            $type = ViolationType::findOrFail($id);
            $type->name = $request->input('name');
            $type->code = $request->input('code');
            $type->save();
            return response()->json(['message' => 'Cập nhật loại vi phạm thành công', 'type' => $type], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật loại vi phạm: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật loại vi phạm'], 500);
        }
    }

    public function getViolations (Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status'); // Thêm filter status
        $perPage = 8;
        $query = Violation::query();
        
        if ($search) {
            $query->where('description', 'like', '%' . $search . '%');
        }
        
        // Filter theo status nếu có
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        $query->with(['reporter', 'shop', 'type']);
        $violations = $query->paginate($perPage);

        // Đếm tổng số vi phạm cho từng trạng thái (luôn lấy tất cả, không filter)
        $allQuery = Violation::query();
        // Không filter theo search cho việc đếm để giữ số hiển thị ổn định
        $total = (clone $allQuery)->count();
        $pending = (clone $allQuery)->where('status', 'pending')->count();
        $resolved = (clone $allQuery)->where('status', 'resolved')->count();
        $ignored = (clone $allQuery)->where('status', 'ignored')->count();

        // Tính tổng số vi phạm cho pagination (đã filter)
        $filteredTotal = $violations->total();

        return response()->json([
            'violations' => $violations->items(),
            'current_page' => $violations->currentPage(),
            'per_page' => $violations->perPage(),
            'last_page' => $violations->lastPage(),
            'total' => $filteredTotal, // Số vi phạm cho pagination hiện tại
            'all_total' => $total, // Tổng số tất cả vi phạm
            'pending' => $pending,
            'resolved' => $resolved,
            'ignored' => $ignored,
        ]);
    }

    public function changeStatusViolation(Request $request, $id)
    {
        $violation = Violation::find($id);
        $violation->status = $request->input('status');
        $violation->save();
        return response()->json([
            'message' => 'Đổi trạng thái thành công'
        ]);
    }

    public function destroyViolation($id)
    {
        try {
            $violation = Violation::findOrFail($id);
            $violation->delete();
            Log::info('Đã xóa vi phạm với ID: ' . $id);
            return response()->json(['message' => 'Vi phạm đã được xóa thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa vi phạm: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa vi phạm'], 500);
        }
    }

    public function deleteMultipleViolations(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $violations = Violation::whereIn('id', $ids)->get();
            foreach ($violations as $violation) {
                $violation->delete();
            }
            Log::info('Đã xóa nhiều vi phạm với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã xóa các vi phạm thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa nhiều vi phạm: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa các vi phạm'], 500);
        }
    }

    public function getViolationShops(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = Shop::query();
        
        // Chỉ lấy các shop có vi phạm
        $query->whereHas('violations', function ($q) {
            $q->where('status', 'resolved');
        });
        
        // lấy tên các loại vi phạm của shop (chỉ lấy 1 vi phạm cho mỗi loại)
        $query->with(['violations' => function ($q) {
            $q->select('id', 'shop_id', 'violation_type_id')
              ->where('status', 'resolved')
              ->with(['type:id,name']);
        }]);

        if ($search) {
            $query->where('shop_name', 'like', '%' . $search . '%');
        }
        
        $shops = $query->paginate($perPage);
        
        // Lọc vi phạm để chỉ lấy 1 vi phạm cho mỗi loại
        $shops->getCollection()->transform(function ($shop) {
            $uniqueViolations = $shop->violations->unique('violation_type_id')->values();
            $shop->setRelation('violations', $uniqueViolations);
            return $shop;
        });

        return response()->json([
            'shops' => $shops->items(),
            'current_page' => $shops->currentPage(),
            'per_page' => $shops->perPage(),
            'last_page' => $shops->lastPage(),
            'total' => $shops->total(),
        ]);
    }
}
