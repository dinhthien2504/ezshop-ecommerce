<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Category::class;
    }

    public function hasChildren($categoryId)
    {
        return Category::where('parent_id', operator: $categoryId)->exists();
    }

    public function getCategoriesWithProductDescendants()
    {
        $categories = Category::whereNull('parent_id')->get();

        $result = $categories->filter(function ($category) {
            return $this->hasProductsInTree($category);
        });

        return $result->values();
    }

    private function hasProductsInTree($category)
    {
        if ($category->products()->exists()) {
            return true;
        }

        foreach ($category->children as $child) {
            if ($this->hasProductsInTree($child)) {
                return true;
            }
        }

        return false;
    }

    public function getCategoriesParents()
    {
        return $this->_model::whereNull('parent_id')->get();
    }
}