<?php
require_once __DIR__ . '/../../payment/vendor/autoload.php';
require_once __DIR__ . '/../../core/BaseController.php';
use PayOS\PayOS;

class PaymentController extends BaseController {
    private $payOS;
    private $config;

    // File: C:\xampp\htdocs\tavanchinh.com\app\controllers\PaymentController.php

    public function __construct() {
        // Sử dụng $_SERVER['DOCUMENT_ROOT'] để luôn trỏ về gốc 'tavanchinh.com'
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        
        // Nạp file cấu hình từ thư mục payment ở gốc
        $configFile = $rootPath . '/../payment/config.php';
        
        if (!file_exists($configFile)) {
            die("Lỗi: Không tìm thấy file cấu hình tại: " . $configFile);
        }
        
        $this->config = require $configFile;

        // Nạp Autoload của PayOS
        $autoloadFile = $rootPath . '/../payment/vendor/autoload.php';
        if (file_exists($autoloadFile)) {
            require_once $autoloadFile;
        } else {
            die("Lỗi: Không tìm thấy thư mục vendor của PayOS!");
        }

        $this->payOS = new \PayOS\PayOS(
            $this->config['client_id'], 
            $this->config['api_key'], 
            $this->config['checksum_key']
        );
    }

    /**
     * Kịch bản: Tạo link thanh toán khi khách bấm "Đăng ký"
     */
    public function createPayment() {
        // 1. Lấy dữ liệu từ Ajax gửi lên
        $json = file_get_contents('php://input');
        $dataRequest = json_decode($json, true);

        $name  = $dataRequest['name'] ?? $_POST['name'] ?? null;
        $phone = $dataRequest['phone'] ?? $_POST['phone'] ?? null;

        if (!$phone) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Vui lòng nhập số điện thoại để nhận tài khoản!']);
            return;
        }

        // 2. Tạo mã đơn hàng duy nhất (orderCode)
        $orderCode = intval(filter_var(microtime(true) * 10000, FILTER_SANITIZE_NUMBER_INT));

        // 3. Chuẩn bị dữ liệu gửi sang PayOS
        $data = [
            "orderCode" => $orderCode,
            "amount" => 5000000, // Giá khóa học của anh
            "description" => "HOC CNC " . $phone,
            "returnUrl" => $this->config['return_url'],
            "cancelUrl" => $this->config['cancel_url']
        ];

        try {
            $response = $this->payOS->createPaymentLink($data);
            
            // Bắt đầu session để lưu thông tin đối soát nếu cần (giống file mẫu của anh)
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['pending_order_'.$orderCode] = $phone;

            // Trả về JSON chứa qrCode để JavaScript hiển thị ảnh
            header('Content-Type: application/json');
            echo json_encode([
                'qrCode' => $response['qrCode'], // Đây là chuỗi VietQR hoặc link ảnh QR
                'orderCode' => $orderCode
            ]);

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Kịch bản: Webhook nhận thông báo tiền về từ PayOS
     */
    public function handleWebhook() {
        $body = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Xác thực dữ liệu tránh hacker gửi đơn ảo
            $verifiedData = $this->payOS->verifyPaymentWebhookData($body);
            
            if ($verifiedData['status'] == 'PAID') {
                $description = $verifiedData['description'];
                
                // Tách SĐT từ mô tả bằng Regex
                preg_match('/[0-9]{10}/', $description, $matches);
                $phone = $matches[0] ?? null;

                if ($phone) {
                    // --- ĐOẠN QUAN TRỌNG NHẤT ---
                    // Anh thực hiện gọi hàm mở khóa bài học trong DB của anh tại đây
                    // $db->query("UPDATE users_courses SET status='active' WHERE phone='$phone'");
                    
                    // (Tùy chọn) Gửi thông báo về Telegram/Zalo cho anh Chinh
                    // sendTelegramNotify("Khách $phone vừa thanh toán 5.000.000đ thành công!");
                }
            }
            
            return json_encode(["success" => true]);
        } catch (\Exception $e) {
            return json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
}