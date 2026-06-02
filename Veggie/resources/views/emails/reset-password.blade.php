<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
    <h2>Xin chào {{ $user->name }},</h2>
    <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản Veggie của bạn.</p>
    <p>
        Nhấn vào liên kết bên dưới để đặt lại mật khẩu:
    </p>
    <p>
        <a href="{{ $resetUrl }}">Đặt lại mật khẩu</a>
    </p>
    <p>Nếu bạn không yêu cầu thao tác này, hãy bỏ qua email này.</p>
    <p>Trân trọng,<br>Đội ngũ Veggie</p>
</body>

</html>
