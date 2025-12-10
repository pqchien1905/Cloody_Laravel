<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloody Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho hệ thống Cloody
    |
    */

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'upload' => [
        // Giới hạn kích thước file tối đa (KB). Mặc định: 102400 KB = 100MB
        'max_file_size' => env('CLOODY_MAX_FILE_SIZE', 102400), // KB
        
        // Giới hạn tổng dung lượng upload trong một request (KB)
        'max_total_size' => env('CLOODY_MAX_TOTAL_SIZE', 512000), // KB = 500MB
        
        // Số lượng file tối đa trong một request
        'max_files_per_request' => env('CLOODY_MAX_FILES_PER_REQUEST', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    */
    'storage' => [
        // Dung lượng lưu trữ tối đa cho mỗi user (MB). 0 = không giới hạn
        'max_storage_per_user' => env('CLOODY_MAX_STORAGE_PER_USER', 1024), // MB = 1GB
        
        // Dung lượng lưu trữ tối đa cho toàn hệ thống (GB). 0 = không giới hạn
        'max_storage_total' => env('CLOODY_MAX_STORAGE_TOTAL', 0), // GB
    ],

    /*
    |--------------------------------------------------------------------------
    | File Validation Settings
    |--------------------------------------------------------------------------
    */
    'validation' => [
        // Whitelist: Chỉ cho phép các extension này (để trống = cho phép tất cả)
        'allowed_extensions' => trim((string) env('CLOODY_ALLOWED_EXTENSIONS', '')) !== ''
            ? array_map('trim', explode(',', (string) env('CLOODY_ALLOWED_EXTENSIONS', '')))
            : [],
        
        // Blacklist: Không cho phép các extension này
        'blocked_extensions' => trim((string) env('CLOODY_BLOCKED_EXTENSIONS', 'exe,bat,cmd,com,scr,vbs,js,jar,app')) !== ''
            ? array_map('trim', explode(',', (string) env('CLOODY_BLOCKED_EXTENSIONS', 'exe,bat,cmd,com,scr,vbs,js,jar,app')))
            : [],
        
        // Whitelist: Chỉ cho phép các MIME types này (để trống = cho phép tất cả)
        'allowed_mime_types' => trim((string) env('CLOODY_ALLOWED_MIME_TYPES', '')) !== ''
            ? array_map('trim', explode(',', (string) env('CLOODY_ALLOWED_MIME_TYPES', '')))
            : [],
        
        // Blacklist: Không cho phép các MIME types này
        'blocked_mime_types' => trim((string) env('CLOODY_BLOCKED_MIME_TYPES', '')) !== ''
            ? array_map('trim', explode(',', (string) env('CLOODY_BLOCKED_MIME_TYPES', '')))
            : [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Settings
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        // Số request upload tối đa trong một phút
        'upload_per_minute' => env('CLOODY_UPLOAD_RATE_LIMIT', 10),
        
        // Số request upload tối đa trong một giờ
        'upload_per_hour' => env('CLOODY_UPLOAD_RATE_LIMIT_HOUR', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings (VNPay)
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'vnpay' => [
            'tmn_code' => env('VNPAY_TMN_CODE', ''),
            'hash_secret' => env('VNPAY_HASH_SECRET', ''),
            'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
            'return_url' => env('VNPAY_RETURN_URL', rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/') . '/cloody/payment/return'),
            'ipn_url' => env('VNPAY_IPN_URL', rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/') . '/cloody/payment/ipn'),
        ],
    ],
];

