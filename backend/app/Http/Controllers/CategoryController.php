<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getCategoryTree(Request $request)
    {
        try {
            $search = $request->input('search') ?? null;
            $data = $this->categoryService->getCategoryTree($search);
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'messaeg' => 'Lấy danh mục thất bại.'
            ]);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $image = $request->file('image');
            $dataCategory = $request->only(['name', 'description', 'parent_id', 'tax_id', 'fee_id']);

            $this->categoryService->createCategory($dataCategory, $image);
            return response()->json([
                'message' => 'Sản phẩm đã được thêm!'
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi thêm danh mục.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $image = $request->file('image');
            $dataCategory = $request->only(['name', 'description', 'parent_id', 'tax_id', 'fee_id']);
            $this->categoryService->updateCategory($id, $dataCategory, $image);
            return response()->json([
                'message' => 'Danh mục đã được cập nhật!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật danh mục thất bại.'
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('ids');

            $result = $this->categoryService->deleteCategories($ids);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi hệ thống.'], 500);
        }
    }

    public function getAvailableLeafCategories(Category $category)
    {
        try {
            $category->load('children', 'products');

            $leafCategories = $this->categoryService->getLeafCategoriesWithProducts($category);

            array_unshift($leafCategories, $category);
            return response()->json($leafCategories);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getRootCategoriesWithProducts()
    {
        try {
            $categories = $this->categoryService->getCategoriesHavingProducts();

            return response()->json($categories);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getRootCategories()
    {
        try {
            $categories = $this->categoryService->getCategoriesParents();
            return response()->json($categories);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ]);
        }
    }


    public function getCategoriesByProduct($productId)
    {
        try {
            $product = \App\Models\Product::with('category')->findOrFail($productId);
            $categories = collect();
            $category = $product->category;
            // Lấy category hiện tại và các category cha (nếu có)
            while ($category) {
                $categories->push($category);
                $category = $category->parent;
            }
            // Đảo ngược thứ tự để có danh sách từ gốc đến lá
            $categories = $categories->reverse()->values();
            return response()->json($categories);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Không tìm thấy sản phẩm hoặc lỗi hệ thống',
                'error' => $th->getMessage()
            ], 404);
        }
    }
}
