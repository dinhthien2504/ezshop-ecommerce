<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Tax;
use App\Models\Fee;
use App\Repositories\CategoryEloquentRepository;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryEloquentRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoryTree($search = null)
    {
        $allCategories = Category::all();

        $rootQuery = Category::whereNull('parent_id');

        if ($search) {
            $matched = $allCategories->filter(function ($cate) use ($search) {
                return strpos(strtolower($cate->name), strtolower($search)) !== false;
            });

            $rootIds = $matched->map(function ($cate) use ($allCategories) {
                while ($cate->parent_id !== null) {
                    $cate = $allCategories->firstWhere('id', $cate->parent_id);
                }
                return $cate->id;
            })->unique()->toArray();

            $rootQuery = Category::whereNull('parent_id')->whereIn('id', $rootIds);
        }

        $rootCategories = $rootQuery->paginate(8);

        $tree = [];
        foreach ($rootCategories as $root) {
            $category = $root;
            $category->children = $this->buildTree($allCategories, $root->id);
            $tree[] = $category;
        }

        $fullTree = $this->buildTree($allCategories);
        $taxes = Tax::all();
        $fees = Fee::all();

        return [
            'current_page' => $rootCategories->currentPage(),
            'per_page' => $rootCategories->perPage(),
            'last_page' => $rootCategories->lastPage(),
            'total' => $allCategories->count(),
            'categories_tree' => $tree,
            'categories_full_tree' => $fullTree,
            'taxes' => $taxes,
            'fees' => $fees
        ];
    }

    private function buildTree($categories, $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);
                $category->children = $children ?: [];
                $branch[] = $category;
            }
        }

        return $branch;
    }

    public function createCategory(array $data = [], $image = null)
    {
        if ($image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $frontendPath = base_path(path: '../frontend/public/imgs/categories/' . $imageName);
            $image->move(dirname($frontendPath), basename($frontendPath));
            $data['image'] = $imageName;
        }

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $cate_id, array $data, $image = null)
    {
        $category = $this->categoryRepository->find($cate_id);
        if ($data['parent_id'] == $cate_id) {
            return response()->json([
                'errors' => [
                    'parent_id' => ['Danh mục cha không thể là chính nó.']
                ]
            ], 422);
        }

        if ($image) {
            if ($category && $category->image) {
                $this->unlinkImage($category->image);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $frontendPath = base_path('../frontend/public/imgs/categories/' . $imageName);
            $image->move(dirname($frontendPath), basename($frontendPath));
            $data['image'] = $imageName;
        }
        return $this->categoryRepository->update($cate_id, $data);
    }

    public function deleteCategories(array $ids)
    {
        $categories = $this->categoryRepository->getByIds($ids);

        $cannotDelete = [];
        $canDelete = [];

        foreach ($categories as $category) {
            $hasChildren = $this->categoryRepository->hasChildren($category->id);
            $hasProducts = $category->products()->exists();
            if ($hasProducts || $hasChildren) {
                $cannotDelete[] = $category->name;
                continue;
            }
            $img = $category->image;
            if (!empty($img)) {
                $this->unlinkImage($img);
            }
            $canDelete[] = $category->id;
        }

        if (!empty($canDelete)) {

            DB::transaction(function () use ($canDelete) {
                $this->categoryRepository->deleteByIds($canDelete);
            });
        }

        return [
            'deleted_ids' => $canDelete,
            'not_deleted_names' => implode(', ', $cannotDelete)
        ];
    }

    private function unlinkImage($img)
    {
        $path = base_path('../frontend/public/imgs/categories/' . $img);
        if ($img && file_exists($path)) {
            unlink($path);
        }
    }

    public function getLeafCategoriesWithProducts($category)
    {
        $children = $category->children;

        if ($children->isEmpty()) {
            if ($category->products()->exists()) {
                return [$category];
            }
            return [];
        }

        $result = [];

        foreach ($children as $child) {
            $result = array_merge(
                $result,
                $this->getLeafCategoriesWithProducts($child)
            );
        }

        return $result;
    }

    public function getCategoriesHavingProducts()
    {
        return $this->categoryRepository->getCategoriesWithProductDescendants();
    }

    public function getCategoriesParents()
    {
        return $this->categoryRepository->getCategoriesParents();
    }
}