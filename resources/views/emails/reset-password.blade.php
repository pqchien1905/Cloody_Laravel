<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Cloody</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f5f5; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">
                                🔐 Đặt lại mật khẩu
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333;">
                                Xin chào <strong style="color: #667eea;">{{ $user->name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #555;">
                                Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản Cloody của bạn.
                            </p>
                            
                            <!-- Info Box -->
                            <div style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; border-radius: 5px; margin: 30px 0;">
                                <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                                    <strong style="color: #333;">⏰ Liên kết đặt lại mật khẩu sẽ hết hạn sau 60 phút.</strong>
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #666;">
                                    Vui lòng không chia sẻ liên kết này với bất kỳ ai để đảm bảo an toàn tài khoản của bạn.
                                </p>
                            </div>
                            
                            <!-- Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="padding: 15px 0;">
                                        <a href="{{ $resetUrl }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 16px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                            Đặt lại mật khẩu
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Alternative Link -->
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 30px 0;">
                                <p style="margin: 0 0 10px 0; font-size: 13px; color: #666; font-weight: 600;">
                                    🔗 Nếu nút trên không hoạt động, vui lòng sao chép và dán liên kết sau vào trình duyệt:
                                </p>
                                <p style="margin: 0; font-size: 12px; color: #667eea; word-break: break-all; line-height: 1.8;">
                                    <a href="{{ $resetUrl }}" style="color: #667eea; text-decoration: none;">{{ $resetUrl }}</a>
                                </p>
                            </div>
                            
                            <!-- Security Notice -->
                            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                                <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                                    <strong style="color: #333;">🛡️ Lưu ý bảo mật:</strong>
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #666; line-height: 1.8;">
                                    Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn và không có thay đổi nào được thực hiện.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #667eea; font-weight: 600;">
                                Cloody - Hệ thống lưu trữ đám mây
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                © {{ date('Y') }} Cloody. Tất cả quyền được bảo lưu.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">
                                Email này được gửi tự động, vui lòng không trả lời.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

