@component('mail::message')
# Cảm ơn bạn đã đặt hàng tại {{ config('app.name') }} 🎉

Xin chào **{{ $order->user->user_name }}**,<br>
Đơn hàng của bạn đã được tạo thành công.<br>

**Mã đơn hàng:** #{{ $order->id }}<br>
**Ngày đặt:** {{ $order->created_date->format('d/m/Y H:i') }}<br>
**Phương thức thanh toán:** {{ $order->paymentMethod->title }}<br>
**Trạng thái thanh toán:** {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}

---

### 🛍️ Sản phẩm đặt mua:
@foreach($order->orderDetails as $detail)
    - {{ $detail->productVariant->product->name }}
    Biến thể: {{ $detail->full_name_variant }}
    SL: {{ $detail->quantity }}
    Giá: {{ number_format($detail->productVariant->price) }}₫
@endforeach

---

**Tổng tiền:** {{ number_format($order->total_amount) }}₫

---

Cảm ơn bạn đã mua sắm cùng chúng tôi! ❤️

@endcomponent