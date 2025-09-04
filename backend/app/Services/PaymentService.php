<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PaymentService
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handleCodPayment(Request $request)
    {
        $ordersData = $request->input('orders');
        $cartIds = $request->input('cart_ids') ?? [];
        $address_id = $request->input('address_id') ?? null;
        $user = auth()->user();
        $vouchers = $request->input('vouchers', []);

        return $this->orderService->createOrders(
            $ordersData,
            $vouchers,
            $cartIds,
            $address_id,
            $user->id,
            1
        );
    }

    public function buildVnpayUrl(Request $request)
    {
        $ordersData = $request->input('orders');
        $cartIds = $request->input('cart_ids');
        $address_id = $request->input('address_id');
        $user = auth()->user();
        $vouchers = $request->input('vouchers', []);
        $payload = base64_encode(json_encode([
            'orders' => $ordersData,
            'vouchers' => $vouchers,
            'cart_ids' => $cartIds,
            'address_id' => $address_id,
            'user_id' => $user->id,
        ]));
        $token = Str::uuid()->toString();
        Cache::put("vnpay_payload_{$token}", $payload, now()->addMinutes(15));

        $vnp_Url = env('VNP_URL');
        $totalAmount = collect($ordersData)->sum('total_amount');
        $vnp_Returnurl = route('vnpay.return') . '?token=' . $token;
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = uniqid();
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $vnp_TxnRef;
        $vnp_Amount = $totalAmount * 100;
        $vnp_Locale = 'vn';
        // $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => 'OrderPayment',
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,

        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        return $vnp_Url;
    }

    public function handleVnpayReturn(array $payload)
    {
        return $this->orderService->createOrders(
            $payload['orders'],
            $payload['vouchers'],
            $payload['cart_ids'] ?? [],
            $payload['address_id'] ?? null,
            $payload['user_id'] ?? null,
            2,
            'paid',
            2
        );
    }

    public function buildMomoUrl(Request $request)
    {
        $ordersData = $request->input('orders');
        $cartIds = $request->input('cart_ids');
        $address_id = $request->input('address_id');
        $user = auth()->user();
        $vouchers = $request->input('vouchers', []);
        $payload = base64_encode(json_encode([
            'orders' => $ordersData,
            'vouchers' => $vouchers,
            'cart_ids' => $cartIds,
            'address_id' => $address_id,
            'user_id' => $user->id,
        ]));

        $totalAmount = collect($ordersData)->sum('total_amount');

        // Thông tin cấu hình MoMo (nên lấy từ env)
        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');


        $orderId = uniqid();
        $orderInfo = 'Thành Toán MoMo ' . $orderId;
        $redirectUrl = route('momo.return') . '?data=' . urlencode($payload);
        $ipnUrl = route('momo.ipn');
        $requestId = uniqid();
        $extraData = "";
        $requestType = "payWithATM";

        // Chuỗi cần ký (thứ tự chính xác)
        $rawHash = "accessKey=$accessKey&amount=$totalAmount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $totalAmount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        return json_decode($result, true);
    }

    public function handleMomoResponse($resultCode, $encodedData)
    {
        if ($resultCode != 0)
            return false;

        $payload = json_decode(base64_decode($encodedData), true);
        if (!$payload || !isset($payload['orders']))
            return false;

        return $this->orderService->createOrders(
            $payload['orders'],
            $payload['vouchers'],
            $payload['cart_ids'] ?? [],
            $payload['address_id'] ?? null,
            $payload['user_id'] ?? null,
            3,
            'paid',
            2
        );
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tăng timeout lên 10 giây
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if ($result === false) {
            die('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }

    public function processMomoResponse($resultCode, $encodedData)
    {
        if ($resultCode != 0) {
            return false;
        }
        $payload = json_decode(base64_decode($encodedData), true);
        if (!$payload || !isset($payload['orders'])) {
            return false;
        }

        return $this->orderService->createOrders(
            $payload['orders'],
            $payload['vouchers'],
            $payload['cart_ids'] ?? [],
            $payload['address_id'] ?? null,
            $payload['user_id'] ?? null,
            3,
            'paid',
            2 //2 => Đã xác nhận
        );
    }
}