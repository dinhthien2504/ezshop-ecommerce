<x-mail::message>
# Xác minh Email

Chào bạn,

Vui lòng nhấn nút dưới đây để xác minh email và kích hoạt tài khoản của bạn:

<x-mail::button :url="$verificationUrl">
Xác minh Email
</x-mail::button>

Nếu bạn không đăng ký tài khoản, vui lòng bỏ qua email này.

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
<x-slot:footer>
    <p style="color: #999; font-size: 12px; text-align: center;">
        Nếu bạn gặp khó khăn trong việc nhấp vào nút, hãy sao chép và dán URL sau vào trình duyệt của bạn:
        <a href="{{ $verificationUrl }}" style="color: #999;">{{ $verificationUrl }}</a>
    </p>
</x-slot:footer>
# Xác minh Email
