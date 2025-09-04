<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Tax::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $taxes = $query->paginate(8);

        return response()->json([
            'meta' => [
                'current_page' => $taxes->currentPage(),
                'per_page' => $taxes->perPage(),
                'last_page' => $taxes->lastPage(),
                'total_item' => $taxes->total(),
            ],
            'taxes' => $taxes->items(),
            'total' => Tax::count()
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
    public function store(StoreTaxRequest $request)
    {
        $taxe = Tax::create([
            'name' => $request->name,
            'vat_percent' => $request->vat_percent,
            'tax_percent' => $request->tax_percent,
            'description' => $request->description
        ]);
        return response()->json([
            'message' => 'Thêm thuế thành công.',
            $taxe
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $taxe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $taxe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxRequest $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->name = $request->name;
        $tax->vat_percent = $request->vat_percent;
        $tax->tax_percent = $request->tax_percent;
        $tax->description = $request->description;

        $tax->save();

        return response()->json([
            'message' => 'Cập nhật thuế thành công.',
            'tax' => $tax
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);

        if ($tax->categories()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa thuế vì có danh mục đang sử dụng.'
            ], 400);
        }

        $tax->delete();

        return response()->json([
            'message' => 'Xóa thuế thành công.'
        ], 200);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        $taxes = Tax::whereIn('id', $ids)->get();

        $cannotDelete = '';
        $canDelete = [];

        foreach ($taxes as $tax) {
            if ($tax->categories()->count() > 0) {
                $cannotDelete .= $tax->name . '  ';
            } else {
                $canDelete[] = $tax->id;
            }
        }
        if (!empty($canDelete)) {
            Tax::whereIn('id', $canDelete)->delete();
        }

        return response()->json([
            'message' => 'Kết quả xóa thuế.',
            'deleted_ids' => $canDelete,
            'not_deleted_names' => $cannotDelete,
            'notice' => $cannotDelete != ''
                ? 'Một số thuế không thể xóa vì có danh mục'
                : 'Đã xóa thành công danh sách.'
        ], 200);
    }
}
