<?php

namespace App\Http\Controllers;

use App\Services\GhnService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function calculateShippingAll(Request $request, GhnService $ghn)
    {
        $toDistrictId = $request->input('to_district_id');
        $toWardCode = (string) $request->input('to_ward_code');
        $cartItems = $request->input('carts', []);

        $fees = [];
        $total = 0;

        foreach ($cartItems as $cart) {
            $fromDistrictId = $cart['district_id'];
            $fromWardCode = (string) $cart['ward_code'];

            $heights = array_map(fn($v) => (float) $v['height'], $cart['variants']);
            $lengths = array_map(fn($v) => (float) $v['length'], $cart['variants']);
            $widths = array_map(fn($v) => (float) $v['width'], $cart['variants']);
            $weight = array_sum(array_map(function ($v) {
                $qty = isset($v['quantity']) ? (int) $v['quantity'] : 1;
                return (float) $v['weight'] * $qty;
            }, $cart['variants']));

            $height = !empty($heights) ? max($heights) : 10;
            $length = !empty($lengths) ? max($lengths) : 10;
            $width = !empty($widths) ? max($widths) : 10;
            $feeResult = $ghn->calculateShippingFee(
                $fromDistrictId,
                $toDistrictId,
                $fromWardCode,
                $toWardCode,
                $weight,
                $length,
                $width,
                $height
            );
            $fee = $feeResult['data']['total'] ?? 0;
            $fees[] = [
                'shop_id' => $cart['shop_id'],
                'fee' => $fee
            ];

            $total += $fee;
        }

        return response()->json([
            'shipping_fees' => $fees,
            'total_fee' => $total
        ]);
    }
}
