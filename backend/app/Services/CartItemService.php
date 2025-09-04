<?php

namespace App\Services;

use App\Repositories\CartItemEloquentRepository;
use App\Repositories\ProductVariantEloquentRepository;

class CartItemService
{
    protected $cartItemRepository;
    protected $productVariantRepository;

    public function __construct(
        CartItemEloquentRepository $cartItemRepository,
        ProductVariantEloquentRepository $productVariantRepository
    ) {
        $this->cartItemRepository = $cartItemRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function getCartUser()
    {
        $user = auth()->user();
        $cartItems = $this->cartItemRepository->getUserCart($user);
        $cartsGrouped = $this->groupCartItemsByShop($cartItems);
        $productIds = $cartItems
            ->pluck('productVariant.product_id')
            ->unique()
            ->values()
            ->toArray();

        $allVariants = $this->productVariantRepository->getVariantsByProductIds($productIds);
        return [
            'carts' => $cartsGrouped,
            'all_variant' => $this->formatVariantsGroupedByProduct($allVariants),
            'variantAttributeGrouped' => $this->groupVariantAttributes($allVariants),
        ];
    }

    public function getUserCartForHeader()
    {
        $user = auth()->user();

        $cartItems = $this->cartItemRepository->getCartHeaderByUserId($user->id);

        $formattedItems = $cartItems->map(function ($item) {
            $variant = $item->productVariant;
            return [
                'name' => $variant->product->name ?? '',
                'image' => $variant->image ?? null,
                'price' => $variant->price ?? 0,
            ];
        });

        return [
            'success' => true,
            'data' => $formattedItems,
            'message' => 'Lấy sản phẩm thành công.',
            'code' => 200
        ];
    }

    private function groupCartItemsByShop($cartItems)
    {
        return $cartItems->groupBy(fn($item) => $item->productVariant->product->shop_id)
            ->map(function ($items, $shopId) {
                $shop = optional($items->first()->productVariant->product->shop);

                $variants = $items->map(fn($item) => $this->formatCartItem($item));

                return [
                    'shop_id' => $shopId,
                    'shop_name' => $shop->shop_name,
                    'district_id' => $shop->district_id,
                    'ward_code' => $shop->ward_code,
                    'variants' => $variants,
                ];
            })->values();
    }

    private function formatCartItem($item)
    {
        $variant = $item->productVariant;
        $product = $variant->product;
        $variantAttribute = $variant->variantAttribute;
        return [
            'cart_id' => $item->id,
            'variant_id' => $variant->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'image' => $variant->image,
            'price' => $variant->price,
            'total_quantity' => $item->quantity,
            'total_price' => $item->quantity * $variant->price,
            'stock' => $variant->stock,
            'category_id' => $product->category_id,
            'weight' => $product->weight,
            'length' => $product->length,
            'width' => $product->width,
            'height' => $product->height,
            'full_name_variant' => VariantAttributeService::getFullNameVariant($variantAttribute),
            'selected_attribute' => $variantAttribute->mapWithKeys(function ($va) {
                return [$va->attributeValue->attribute->name => $va->attribute_value_id];
            }),
        ];
    }

    private function formatVariantsGroupedByProduct($variants)
    {
        return $variants->map(function ($variant) {
            $variantAttribute = $variant->variantAttribute;
            return [
                'test' => $variant,
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'image' => $variant->image,
                'price' => $variant->price,
                'total_quantity' => 1,
                'total_price' => $variant->price,
                'stock' => $variant->stock,
                'full_name_variant' => VariantAttributeService::getFullNameVariant($variantAttribute),
                'selected_attribute' => $variantAttribute->mapWithKeys(function ($va) {
                    return [$va->attributeValue->attribute->name => $va->attribute_value_id];
                }),
            ];
        })->groupBy('product_id');
    }

    private function groupVariantAttributes($variants)
    {
        return $variants->groupBy('product_id')->map(function ($variantGroup) {
            $attributes = [];

            foreach ($variantGroup as $variant) {
                foreach ($variant->variantAttribute as $va) {
                    $attribute = $va->attributeValue->attribute;
                    $value = $va->attributeValue;

                    $attributes[$attribute->id]['id'] = $attribute->id;
                    $attributes[$attribute->id]['name'] = $attribute->name;
                    $attributes[$attribute->id]['values'][$value->id] = [
                        'id' => $value->id,
                        'value' => $value->value,
                    ];
                }
            }

            foreach ($attributes as &$attr) {
                $attr['values'] = array_values($attr['values']);
            }

            return array_values($attributes);
        });
    }

    public function createCartItem(array $data)
    {
        $user = auth()->user();
        $variantId = $data['variant_id'];
        $quantity = $data['quantity'];

        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return [
                'success' => false,
                'message' => 'Biến thể sản phẩm không tồn tại.',
                'code' => 404
            ];
        }

        $existingItem = $this->cartItemRepository->findByUserAndVariant($user->id, $variantId);
        $newQuantity = ($existingItem ? $existingItem->quantity : 0) + $quantity;

        if ($newQuantity > $variant->stock) {
            return [
                'success' => false,
                'message' => "Lượng hàng trong kho không đủ. Chỉ còn {$variant->stock} sản phẩm.",
                'code' => 400
            ];
        }

        if ($existingItem) {
            $cartItem = $this->cartItemRepository->update($existingItem->id, ['quantity' => $newQuantity]);
        } else {
            $cartItem = $this->cartItemRepository->create([
                'user_id' => $user->id,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return [
            'success' => true,
            'cart_id' => $cartItem->id,
            'message' => 'Thêm vào giỏ hàng thành công.',
            'code' => 201
        ];
    }

    public function updateCartItem($id, array $data)
    {
        $cart = $this->cartItemRepository->find($id);

        if (!$cart) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy giỏ hàng.',
                'code' => 404
            ];
        }

        $quantity = $data['quantity'] ?? 1;

        if (isset($data['variant_id'])) {
            $variant = $this->productVariantRepository->find($data['variant_id']);
            if (!$variant) {
                return [
                    'success' => false,
                    'message' => 'Biến thể sản phẩm không tồn tại.',
                    'code' => 404
                ];
            }

            $cart->variant_id = $data['variant_id'];
        }

        $cart->quantity = $quantity;
        $cart->save();

        return [
            'success' => true,
            'message' => 'Cập nhật giỏ hàng thành công.',
            'code' => 200
        ];
    }

    public function deleteCartItems(array $cartIds): array
    {
        if (empty($cartIds)) {
            return [
                'success' => false,
                'message' => 'Danh sách sản phẩm cần xóa không được để trống.',
                'code' => 422
            ];
        }

        $deleted = $this->cartItemRepository->deleteByIds($cartIds);

        if ($deleted === 0) {
            return [
                'success' => false,
                'message' => 'Không có sản phẩm nào được xóa.',
                'code' => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công.',
            'code' => 200
        ];
    }
}