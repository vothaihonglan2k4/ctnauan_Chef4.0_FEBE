<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <tr>
            <td style="background: #4f46e5; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Công Thức Nấu Ăn</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px;">
                <h2 style="color: #333333; margin-top: 0;">Xác thực email</h2>
                <p style="color: #666666; font-size: 16px;">Chào <strong>{{ $userName }}</strong>,</p>
                <p style="color: #666666; font-size: 16px;">Mã xác thực email của bạn là:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <span style="display: inline-block; background: #f0f0f0; padding: 15px 30px; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #4f46e5; border-radius: 8px;">{{ $code }}</span>
                </div>
                <p style="color: #666666; font-size: 14px;">Mã này có hiệu lực trong 5 phút. Vui lòng không chia sẻ mã này cho bất kỳ ai.</p>
                <p style="color: #999999; font-size: 13px;">Nếu bạn không đăng ký tài khoản, vui lòng bỏ qua email này.</p>
            </td>
        </tr>
        <tr>
            <td style="background: #f9f9f9; padding: 20px; text-align: center; color: #999999; font-size: 12px;">
                <p>&copy; {{ date('Y') }} Công Thức Nấu Ăn. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
