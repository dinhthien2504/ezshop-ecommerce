<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return ProductVariant::class;
    }

    public function getUserCart($user)
    {
        return $this->_model
            ->where('user_id', $user->id)
            ->with([
                'productVariant.product.shop',
                'productVariant.variantAttribute.attributeValue.attribute'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getVariantsByProductIds(array $productIds = [])
    {
        return $this->_model::with([
            'variantAttribute.attributeValue.attribute',
            'product.category.tax'
        ])
            ->whereIn('product_id', $productIds)
            ->get();
    }
}