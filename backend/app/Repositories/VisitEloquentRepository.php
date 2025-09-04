<?php

namespace App\Repositories;

use App\Models\Visit;

class VisitEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Visit::class;
    }

    public function recordVisit($shop_id, $type = 'product', $product_id = null)
    {
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        // Kiểm tra trùng IP + user + product_id (nếu có) trong vòng 5 phút
        $recentVisit = $this->_model::where('shop_id', $shop_id)
            ->where('type', $type)
            ->when($product_id, function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            })
            ->where(function ($query) use ($ipAddress, $userAgent) {
                $query->where('ip_address', $ipAddress)
                    ->where('user_agent', $userAgent);
            })
            ->where('visited_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentVisit) {
            return false;
        }

        return $this->_model::create([
            'shop_id' => $shop_id,
            'product_id' => $product_id,
            'type' => $type,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'visited_at' => now(),
        ]);
    }


    public function countVisits($shopId, $start, $end)
    {
        return $this->_model::where('shop_id', $shopId)
            ->whereBetween('visited_at', [$start, $end])
            ->count();
    }

}