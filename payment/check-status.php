<?php
require 'vendor/autoload.php';
use PayOS\PayOS;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// 1. Cấu hình PayOS
$payOS = new PayOS(
    "4093ce07-64e1-4631-91bb-705cc0a8ebac", 
    "da8fa286-0465-4f44-b1e3-1c456c5abe28", 
    "bb33376b17ff532281915d694f0f315a767a0ecfe8703297be4035cca1019ede"
);

$orderCode = $_GET['orderCode'] ?? null;
if (!$orderCode) {
    die(json_encode(['status' => 'ERROR', 'message' => 'Missing Order Code']));
}

try {
    $response = $payOS->getPaymentLinkInformation($orderCode);

    if ($response['status'] == 'PAID') {
        // THIẾT LẬP NGÀY HẾT HẠN
        $expiryDate = "2036-12-31"; // Mặc định hiện tại
        
        // Chặn xử lý trùng lặp nếu polling gọi liên tục
        if (isset($_SESSION['processed_' . $orderCode])) {
            // Nếu đã xử lý xong rồi, trả về kết quả đã lưu trong session để giao diện hiển thị lại nếu cần
            echo json_encode([
                'status' => 'PAID', 
                'key' => $_SESSION['processed_' . $orderCode],
                'email' => $_SESSION['email_' . $orderCode] ?? '',
                'expiryDate' => date("d-m-Y", strtotime($expiryDate))   
            ]);
            exit;
        }

        $userEmail = $_SESSION['email_' . $orderCode] ?? null;

        if ($userEmail) {
            // 2. SINH KEY theo cấu trúc GUID: XXXX-XXXX-XXXX-XXXX
            $p1 = strtoupper(bin2hex(random_bytes(2))); 
            $p2 = strtoupper(bin2hex(random_bytes(2))); 
            $p3 = strtoupper(bin2hex(random_bytes(2))); 
            $p4 = strtoupper(bin2hex(random_bytes(2)));
            $generatedKey = "$p1-$p2-$p3-$p4";

            // Lấy mã hiển thị FITC (nếu bạn đã lưu ở checkout.php) hoặc dùng mặc định
            $displayId = $_SESSION['current_display_id'] ?? "FITC" . $orderCode;

            

            // 3. LƯU VÀO MYSQL (Gồm license_key, user_id là mã đơn hàng, và email)
            $dbSaved = saveKeyToDatabase($generatedKey, $displayId, $userEmail, $expiryDate);

            if ($dbSaved) {
                // 4. GỬI EMAIL CHO KHÁCH
                $sendResult = sendKeyEmail($userEmail, $generatedKey, $displayId, $expiryDate);


                // Đánh dấu đã xử lý và lưu key vào session để trả về cho giao diện
                $_SESSION['processed_' . $orderCode] = $generatedKey;

                echo json_encode([
                    'status' => 'PAID', 
                    'key' => $generatedKey, 
                    'email' => $userEmail,
                    'expiryDate' => date("d-m-Y", strtotime($expiryDate))
                ]);
                exit;
            } else {
                echo json_encode(['status' => 'ERROR', 'message' => 'Database save failed']);
                exit;
            }
        }
    }
    
    // Trả về trạng thái hiện tại (PENDING, CANCELLED, v.v.)
    echo json_encode(['status' => $response['status']]);

} catch (\Exception $e) {
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
}

// --- HÀM LƯU DATABASE ---
function saveKeyToDatabase($key, $order_id, $email, $expiry_date) {
    $host = "localhost";
    $user = "fitc";
    $pass = "fitc@2025";
    $db   = "fitc"; 

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) return false;

    // 1. Sửa status thành 0 theo yêu cầu
    $status = 0; 
    
    // 2. Để trống user_id theo yêu cầu (Gán giá trị rỗng)
    $empty_user_id = ""; 

    $sql = "INSERT INTO user_keys (license_key, user_id, email, used_date, expiry_date, status) 
            VALUES (?, ?, ?, NULL, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            user_id = VALUES(user_id), 
            email = VALUES(email), 
            expiry_date = VALUES(expiry_date),
            status = VALUES(status)";

    $stmt = $conn->prepare($sql);
    $result = false;
    
    if ($stmt) {
        // Tham số truyền vào: key(s), empty_user_id(s), email(s), expiry_date(s), status(i)
        $stmt->bind_param("ssssi", $key, $empty_user_id, $email, $expiry_date, $status);
        $result = $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
    return $result;
}

// --- HÀM GỬI MAIL ---
function sendKeyEmail($toEmail, $key, $order_id,$expiryDate) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fitcplugin@gmail.com';
        $mail->Password   = 'chtbwbernkirzjvh'; // App Password đã kiểm tra
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('fitcplugin@gmail.com', 'FitC Plugin');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Key kích hoạt FitC Plugin của bạn - ' . $order_id;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                <h3 style='color: #78b328;'>Cảm ơn bạn đã ủng hộ FitC!</h3>
                <p>Đơn hàng <b>$order_id</b> của bạn đã thanh toán thành công.</p>
                <div style='padding: 15px; background: #f4f4f4; border-radius: 5px; border: 1px solid #ddd;'>
                    <p style='margin: 0;'>Mã kích hoạt của bạn là:</p>
                    <p style='font-size: 24px; font-weight: bold; color: #78b328; margin: 10px 0;'>$key</p>
                    <p style='margin: 0;'>Thời gian sử dụng đến ngày:$expiryDate </p>
                </div>
                <p style='font-size: 12px; color: #666; margin-top: 20px;'>
                    * Lưu ý: Mỗi mã kích hoạt dùng cho 01 máy tính.
                </p>
                
            </div>";
            
        return $mail->send();
    } catch (Exception $e) { 
        return false; 
    }
}

// --- HÀM CẬP NHẬT SETTINGS.JSON (Đã fix logic đọc/ghi) ---
function updateSettingsCount() {
    $filePath = 'settings.json';
    $settings = [];
    if (file_exists($filePath)) {
        $settings = json_decode(file_get_contents($filePath), true);
    }
    $settings['max_count'] = ($settings['max_count'] ?? 0) + 1;
    file_put_contents($filePath, json_encode($settings, JSON_PRETTY_PRINT));
}