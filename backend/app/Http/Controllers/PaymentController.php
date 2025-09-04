<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class PaymentController extends Controller
{
    protected $orderService;
    protected $paymentService;
    public function __construct(
        OrderService $orderService,
        PaymentService $paymentService
    ) {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function initPayment(Request $request)
    {
        $paymentMethod = $request->input('payment_method');

        switch ($paymentMethod) {
            case '1':
                return $this->handleCodPayment($request);

            case '2':
                return $this->createVnpayUrl($request);

            case '3':
                return $this->createMomoUrl($request);

            default:
                return response()->json(['message' => 'Phương thức thanh toán không hỗ trợ'], 400);
        }
    }
    private function handleCodPayment(Request $request)
    {
        try {
            $ordersData = $request->input('orders');
            $cartIds = $request->input('cart_ids') ?? [];
            $address_id = $request->input('address_id') ?? null;
            $user = auth()->user();
            $vouchers = $request->input('vouchers', []);
            $success = $this->orderService->createOrders(
                $ordersData,
                $vouchers,
                $cartIds,
                $address_id,
                $user->id,
                1
            );
            return response()->json([
                'message' => 'Thanh toán thành công.',
                'success' => $success
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng.',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    private function createVnpayUrl(Request $request)
    {
        try {
            $vnp_Url = $this->paymentService->buildVnpayUrl($request);
            Log::info('VNpay URL: ' . $vnp_Url);
            return response()->json(['payment_url' => $vnp_Url]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo URL VNPAY.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            $vnp_ResponseCode = $request->input('vnp_ResponseCode');

            if ($vnp_ResponseCode !== '00') {
                return redirect(env('FRONTEND_URL') . '/hoan-tat-dat-hang?success=false');
            }
            $encodedData = Cache::get("vnpay_payload_{$request->input('token')}");
            $payload = json_decode(base64_decode($encodedData), true);

            if (!$payload || !isset($payload['orders'])) {
                return response()->json(['message' => 'Không lấy được dữ liệu đơn hàng.'], 400);
            }

            $success = $this->paymentService->handleVnpayReturn($payload);
            return redirect(env('FRONTEND_URL') . '/hoan-tat-dat-hang?success=' . ($success ? 'true' : 'false'));

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function createMomoUrl(Request $request)
    {
        try {
            $jsonResult = $this->paymentService->buildMomoUrl($request);
            return response()->json(['payment_url' => $jsonResult['payUrl']]);
        } catch (\Throwable $th) {
            return response()->json([
                'payment_url' => $jsonResult,
                'error' => 'Không tạo được liên kết thanh toán MoMo.' . $th->getMessage()
            ], 500);
        }
    }

    public function momoReturn(Request $request)
    {
        try {
            $resultCode = $request->input('resultCode');
            $encodedData = $request->query('data');

            $success = $this->paymentService->processMomoResponse($resultCode, $encodedData);

            return redirect(env('FRONTEND_URL') . '/hoan-tat-dat-hang?success=' . ($success ? 'true' : 'false'));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // public function handleMomoIpn(Request $request)
    // {
    //     $data = $request->all();
    //     $resultCode = $data['resultCode'];
    //     $orderInfo = $data['orderInfo'];

    //     $success = $this->processMomoResponse($resultCode, $orderInfo);

    //     return response()->json([
    //         'message' => $success ? 'IPN received successfully' : 'Payment failed or cancelled'
    //     ], $success ? 200 : 400);
    // }
}