<?php
require 'vendor/autoload.php';
use PayOS\PayOS;

// Thay các giá trị bên dưới bằng Key bạn lấy từ Dashboard PayOS
$payOS = new PayOS(
    "4093ce07-64e1-4631-91bb-705cc0a8ebac", 
    "da8fa286-0465-4f44-b1e3-1c456c5abe28", 
    "bb33376b17ff532281915d694f0f315a767a0ecfe8703297be4035cca1019ede"
);
$orderCode = intval(filter_var(microtime(true) * 10000, FILTER_SANITIZE_NUMBER_INT));
$data = [
    "orderCode" => $orderCode,
    "amount" => 100000,
    "description" => "TT FITC PLUGIN",
    "returnUrl" => "http://fitc.vn",
    "cancelUrl" => "http://fitc.vn"
];

try {
    $response = $payOS->createPaymentLink($data);
    
    // Lưu email vào session hoặc Database để tí nữa check-status dùng gửi mail
    session_start();
    $_SESSION['email_'.$orderCode] = $_POST['email'];

    echo json_encode([
        'qrCode' => $response['qrCode'],
        'orderCode' => $orderCode
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
