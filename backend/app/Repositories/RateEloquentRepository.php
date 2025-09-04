<?php

namespace App\Repositories;

use App\Models\OrderDetail;
use App\Models\Rate;

class RateEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Rate::class;
    }

    public function bulkInsertRates(array $feedbacks, int $userId)
    {
        $rows = [];
        $now = now();
        $orderDetailIds = [];

        foreach ($feedbacks as $item) {
            $imageUrls = [];

            if (!empty($item['images'])) {
                foreach ($item['images'] as $base64) {
                    $path = $this->saveBase64Image($base64);
                    if ($path) {
                        $imageUrls[] = $path;
                    }
                }
            }

            $rows[] = [
                'order_detail_id' => $item['order_detail_id'],
                'user_id' => $userId,
                'rate' => $item['rating'],
                'content' => $item['review'] ?? '',
                'images' => empty($imageUrls) ? null : json_encode($imageUrls),
                'rate_status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $orderDetailIds[] = $item['order_detail_id'];
        }

        $this->_model::insert($rows);

        OrderDetail::whereIn('id', $orderDetailIds)
            ->update(['is_reviewed' => true]);
        return true;
    }


    private function saveBase64Image(string $base64): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1]);
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }

        $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
        if ($imageData === false) {
            return null;
        }

        $filename = time() . '_' . uniqid() . '.' . $extension;

        $targetDir = base_path("../frontend/public/imgs/rates");

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fullPath = "$targetDir/$filename";
        file_put_contents($fullPath, $imageData);
        return $filename;
    }

    public function countShopRates($shopId)
    {
        return $this->_model::whereHas('orderDetail.productVariant.product', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->count();
    }
}
