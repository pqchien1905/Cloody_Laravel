<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service - Xử lý tích hợp thanh toán VNPay
 */
class VNPayService
{
    protected $tmnCode;
    protected $hashSecret;
    protected $url;
    protected $returnUrl;
    protected $ipnUrl;

    /**
     * Khởi tạo service với cấu hình từ config
     */
    public function __construct()
    {
        $this->tmnCode = config('cloody.payment.vnpay.tmn_code');
        $this->hashSecret = config('cloody.payment.vnpay.hash_secret');
        $this->url = config('cloody.payment.vnpay.url');
        
        // Đảm bảo URL không có trailing slash và là absolute URL
        $appUrl = rtrim(config('app.url', 'http://127.0.0.1:8000'), '/');
        $this->returnUrl = config('cloody.payment.vnpay.return_url') ?: $appUrl . '/cloody/payment/callback';
        $this->ipnUrl = config('cloody.payment.vnpay.ipn_url') ?: $appUrl . '/cloody/payment/ipn';
        
        // Đảm bảo URL là absolute và không có trailing slash
        $this->returnUrl = rtrim($this->returnUrl, '/');
        $this->ipnUrl = rtrim($this->ipnUrl, '/');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(array $params): string
    {
        $vnp_TxnRef = $params['txn_ref']; // Mã tham chiếu giao dịch
        $vnp_OrderInfo = $params['order_info']; // Thông tin đơn hàng
        $vnp_OrderType = $params['order_type'] ?? 'other';
        $vnp_Amount = $params['amount'] * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = $params['locale'] ?? 'vn';
        $vnp_IpAddr = $params['ip_address'] ?? request()->ip();
        $vnp_CreateDate = date('YmdHis');

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        if (isset($params['bank_code']) && $params['bank_code'] != "") {
            $inputData['vnp_BankCode'] = $params['bank_code'];
        }

        // Loại bỏ các tham số rỗng
        $inputData = array_filter($inputData, function($value) {
            return $value !== null && $value !== '';
        });
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            // VNPay yêu cầu dùng urlencode cho hashdata và query string
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        // Tính toán chữ ký - VNPay yêu cầu hash_hmac SHA512
        if (isset($this->hashSecret) && !empty($this->hashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
            // Thêm vnp_SecureHash vào query string (query đã có dấu & cuối cùng)
            $query .= 'vnp_SecureHash=' . $vnpSecureHash; // Không encode secure hash
        } else {
            // Nếu không có hash secret, loại bỏ dấu & cuối cùng
            $query = rtrim($query, '&');
        }

        $vnp_Url = $this->url . "?" . $query;

        // Log để debug (chỉ trong development)
        if (config('app.debug')) {
            Log::info('VNPay Payment URL Created', [
                'input_data' => $inputData,
                'hashdata' => $hashdata,
                'hash_secret' => substr($this->hashSecret ?? '', 0, 10) . '...',
                'hash_secret_length' => strlen($this->hashSecret ?? ''),
                'secure_hash' => $vnpSecureHash ?? 'NOT CALCULATED',
                'return_url' => $this->returnUrl,
                'url_length' => strlen($vnp_Url),
            ]);
        }

        return $vnp_Url;
    }

    /**
     * Xác thực chữ ký từ VNPay callback
     */
    public function validateSignature(array $data): bool
    {
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash']);

        ksort($data);
        $i = 0;
        $hashData = "";
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        return $secureHash === $vnp_SecureHash;
    }

    /**
     * Xử lý response từ VNPay
     */
    public function processResponse(array $data): array
    {
        $responseCode = $data['vnp_ResponseCode'] ?? '';
        $transactionStatus = $data['vnp_TransactionStatus'] ?? '';
        $txnRef = $data['vnp_TxnRef'] ?? '';
        $amount = ($data['vnp_Amount'] ?? 0) / 100; // Chia 100 để lấy số tiền thực

        $status = 'failed';
        $message = 'Giao dịch thất bại';

        if ($responseCode == '00' && $transactionStatus == '00') {
            $status = 'completed';
            $message = 'Giao dịch thành công';
        } elseif ($responseCode == '07') {
            $status = 'failed';
            $message = 'Trừ tiền thành công nhưng giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)';
        } elseif ($responseCode == '09') {
            $status = 'failed';
            $message = 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking';
        } elseif ($responseCode == '10') {
            $status = 'failed';
            $message = 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần';
        } elseif ($responseCode == '11') {
            $status = 'failed';
            $message = 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch';
        } elseif ($responseCode == '12') {
            $status = 'failed';
            $message = 'Thẻ/Tài khoản bị khóa';
        } elseif ($responseCode == '51') {
            $status = 'failed';
            $message = 'Tài khoản không đủ số dư để thực hiện giao dịch';
        } elseif ($responseCode == '65') {
            $status = 'failed';
            $message = 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày';
        } elseif ($responseCode == '75') {
            $status = 'failed';
            $message = 'Ngân hàng thanh toán đang bảo trì';
        } elseif ($responseCode == '79') {
            $status = 'failed';
            $message = 'Nhập sai mật khẩu thanh toán quá số lần quy định';
        }

        return [
            'status' => $status,
            'message' => $message,
            'txn_ref' => $txnRef,
            'amount' => $amount,
            'response_code' => $responseCode,
            'transaction_status' => $transactionStatus,
            'data' => $data,
        ];
    }
}

