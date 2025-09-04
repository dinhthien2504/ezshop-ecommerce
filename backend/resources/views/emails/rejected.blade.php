<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Yêu cầu rút tiền bị từ chối - Ezshop</title>
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
            max-width: 600px;
            width: 100%;
            background-color: #fff;
            padding: 30px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: left;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #e53935;
            text-align: center;
            margin-bottom: 16px;
        }
        .reason-box {
            background-color: #ffecec;
            border-left: 4px solid #e53935;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            font-size: 13px;
            color: #777;
            margin-top: 30px;
            text-align: center;
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

        <p>Xin chào <strong>{{ $shopName }}</strong>,</p>

        <p>Yêu cầu rút tiền <strong>#{{ $payoutCode }}</strong> của bạn đã bị <strong>từ chối</strong> vào lúc <strong>{{ $rejectedAt }}</strong>.</p>

        <p>Lý do từ chối được ghi nhận như sau:</p>

        <div class="reason-box">
            {{ $rejectedReason }}
        </div>

        <p>Vui lòng kiểm tra lại thông tin và tạo lại yêu cầu rút tiền nếu cần thiết.</p>

        <p>Trân trọng,<br>Đội ngũ Ezshop</p>

        <div class="footer">
            © {{ date('Y') }} Ezshop. Mọi quyền được bảo lưu.
        </div>
    </div>
</div>
</body>
</html>
