<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Http\Requests\StoreFeeRequest;
use App\Http\Requests\UpdateFeeRequest;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Fee::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $fees= $query->paginate(8);

        return response()->json([
            'meta' => [
                'current_page' => $fees->currentPage(),
                'per_page' => $fees->perPage(),
                'last_page' => $fees->lastPage(),
                'total_item' => $fees->total(),
            ],
            'fees' => $fees->items(),
            'total' => Fee::count()
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeRequest $request)
    {
        $fee = Fee::create([
            'name' => $request->name,
            'percentage' => $request->percentage
        ]);
        return response()->json([
            'message' => 'Thêm phí sàn thành công.',
            $fee
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeRequest $request, $id)
    {
        $fee = Fee::findOrFail($id);
        $fee->name = $request->name;
        $fee->percentage = $request->percentage;
        $fee->save();

        return response()->json([
            'message' => 'Cập nhật phí sàn thành công.',
            'fee' => $fee
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);

        if ($fee->categories()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa phí sàn vì có danh mục đang sử dụng.'
            ], 400);
        }

        $fee->delete();

        return response()->json([
            'message' => 'Xóa phí sàn thành công.'
        ], 200);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        $fees = Fee::whereIn('id', $ids)->get();

        $cannotDelete = '';
        $canDelete = [];

        foreach ($fees as $fee) {
            if ($fee->categories()->count() > 0) {
                $cannotDelete .= $fee->name . '  ';
            } else {
                $canDelete[] = $fee->id;
            }
        }
        if (!empty($canDelete)) {
            Fee::whereIn('id', $canDelete)->delete();
        }

        return response()->json([
            'message' => 'Kết quả xóa phí sàn.',
            'deleted_ids' => $canDelete,
            'not_deleted_names' => $cannotDelete,
            'notice' => $cannotDelete != ''
                ? 'Một số phí sàn không thể xóa vì có danh mục'
                : 'Đã xóa thành công phí sàn.'
        ], 200);
    }
}
