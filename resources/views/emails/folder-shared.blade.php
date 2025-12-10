<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư mục được chia sẻ với bạn</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">📂 Thư mục được chia sẻ</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p>Xin chào <strong>{{ $sharedWith->name }}</strong>,</p>
        
        <p><strong>{{ $sharedBy->name }}</strong> đã chia sẻ một thư mục với bạn:</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <h2 style="margin-top: 0; color: #667eea;">{{ $folder->name }}</h2>
            @if($folder->description)
            <p style="margin: 5px 0;"><strong>Mô tả:</strong> {{ $folder->description }}</p>
            @endif
            @if($expiresAt)
            <p style="margin: 5px 0;"><strong>Hết hạn:</strong> {{ $expiresAt->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>
            @endif
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $shareUrl }}" 
               style="display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Xem Thư mục
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            Nếu bạn không mong đợi email này, bạn có thể bỏ qua nó.
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>© {{ date('Y') }} Cloody. Tất cả quyền được bảo lưu.</p>
    </div>
</body>
</html>

