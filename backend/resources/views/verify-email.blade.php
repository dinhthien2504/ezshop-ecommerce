<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }} - Ezshop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 0;
            margin: 0;
        }
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            width: 100%;
            background-color: #fff;
            padding: 30px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #e53935;
            margin-bottom: 16px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #e53935;
            letter-spacing: 4px;
            margin: 24px 0;
        }
        .footer {
            font-size: 13px;
            color: #777;
            margin-top: 20px;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="brand">Ezshop</div>
            <p>Xin chào,</p>

            @if ($subject === 'Xác thực địa chỉ email')
                <p>Bạn vừa yêu cầu xác thực địa chỉ email của mình trên hệ thống <strong>Ezshop</strong>.</p>
                <p>Vui lòng sử dụng mã xác thực dưới đây:</p>
            @elseif ($subject === 'Xác nhận thay đổi mật khẩu')
                <p>Bạn vừa yêu cầu đổi mật khẩu tài khoản của mình trên hệ thống <strong>Ezshop</strong>.</p>
                <p>Vui lòng sử dụng mã xác nhận dưới đây:</p>
            @else
                <p>Đây là mã xác nhận từ hệ thống <strong>Ezshop</strong>:</p>
            @endif

            <div class="code">{{ $otp }}</div>

            <p>Mã {{ $subject === 'Xác nhận thay đổi mật khẩu' ? 'xác nhận' : 'xác thực' }} này sẽ hết hạn sau <strong>5 phút</strong>.</p>
            <p>Vui lòng không chia sẻ mã này với bất kỳ ai.</p>

            <p>Trân trọng,<br>Đội ngũ Ezshop</p>

            <div class="footer">
                © {{ date('Y') }} Ezshop. Mọi quyền được bảo lưu.
            </div>
        </div>
    </div>
</body>
</html>
