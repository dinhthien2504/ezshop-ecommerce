<?php

namespace App\Repositories;

use App\Models\CartItem;

class CartItemEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return CartItem::class;
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

    public function findByUserAndVariant($userId, $variantId)
    {
        return $this->_model
            ->where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function getCartHeaderByUserId($userId)
    {
        return $this->_model
            ->where('user_id', $userId)
            ->with([
                'productVariant.product.category.tax'
            ])
            ->get();
    }

    public function deleteByIds(array $ids)
    {
        return $this->_model::whereIn('id', $ids)->delete();
    }

}