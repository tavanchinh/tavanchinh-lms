<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/LessonModel.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class UserController extends BaseController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Tạo hàm phụ để bảo vệ các trang riêng tư
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /dang-nhap");
            exit;
        }
    }

    public function index() {
        $this->requireLogin();
        $userId = $_SESSION['user_id'] ?? null;
        

        $userModel = new UserModel();
        $user = $userModel->findById($userId);

        $courseModel = new CourseModel();
        $lessonModel = new LessonModel(); // Khởi tạo thêm Model bài học

        // Lấy danh sách khóa học mà user đã tham gia
        $courses = $courseModel->getUserEnrolledCourses($userId);

        // --- TÍNH TOÁN TIẾN ĐỘ CHO TỪNG KHÓA HỌC ---
        if (!empty($courses)) {
            foreach ($courses as &$course) {
                // 1. Lấy tổng số bài học của khóa này
                $totalLessons = $lessonModel->getTotalLessonsByCourseId($course['id']);
                
                // 2. Lấy số bài học mà user này đã hoàn thành trong khóa này
                $completedCount = $lessonModel->getCompletedCount($userId, $course['id']);

                // 3. Tính %
                $percent = 0;
                if ($totalLessons > 0) {
                    $percent = round(($completedCount / $totalLessons) * 100);
                }

                // Gán thêm dữ liệu vào mảng khóa học
                $course['progress_percent'] = $percent;
                $course['completed_count'] = $completedCount;
                $course['total_lessons'] = $totalLessons;
            }
        }
        // --- KẾT THÚC TÍNH TOÁN ---

        $currentPath = $_SERVER['REQUEST_URI'];
        $activeTab = (strpos($currentPath, 'trang-ca-nhan') !== false) ? 'settings' : 'courses';
        
        return $this->view('frontend/client/profile', [
            'user' => $user,
            'myCourses' => $courses,
            'title' => 'Trang cá nhân - ' . $user['name'],
            'activeTab' => $activeTab
        ]);
    }

    public function update() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone_number'] ?? '';
            
            $userModel = new UserModel();
            
            // Cập nhật thông tin cơ bản
            $userModel->updateInfo($userId, ['name' => $name, 'phone_number' => $phone]);
            
            // Cập nhật lại tên hiển thị trên Session
            $_SESSION['user_name'] = $name;

            // Xử lý đổi mật khẩu nếu có nhập
            $newPass = $_POST['new_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    // Trả về lỗi nếu mật khẩu không khớp (phòng trường hợp vượt qua JS)
                    header("Location: /trang-ca-nhan?activeTab=settings&error=password_mismatch");
                    exit;
                }
                if (strlen($newPassword) < 6) {
                    header("Location: /trang-ca-nhan?activeTab=settings&error=password_too_short");
                    exit;
                }
                $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                $userModel->updatePassword($userId, $hashedPass);
            }

            header("Location: /trang-ca-nhan?success=1");
            exit;
        }
    }


    /**
     * Hiển thị form nhập email quên mật khẩu
     */
    public function forgotPassword() {
        $this->view('auth/forgot_password', ['title' => 'Quên mật khẩu']);
    }


    /**
     * Xử lý gửi mail chứa link reset
     */
    public function sendResetLink() {
        $email = $_POST['email'] ?? '';
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->saveResetToken($email, $token);

            // Gửi mail (Anh thay thông tin Gmail của anh vào đây)
            $this->sendEmail($email, $token,$user['name']);
        }

        // Redirect về trang cũ kèm thông báo thành công (dù email có tồn tại hay không)
        header("Location: /quen-mat-khau?sent=1");
        exit;
    }

    /**
     * Hiển thị form đặt lại mật khẩu mới khi click từ mail
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $userModel = new UserModel();
        $resetData = $userModel->checkResetToken($token);

        if (!$resetData) {
            // Nếu token sai hoặc hết hạn (quá 30p)
            header("Location: /quen-mat-khau?error=token_expired");
            exit;
        }

        $this->view('auth/reset-password', [
            'token' => $token, 
            'title' => 'Đặt lại mật khẩu'
        ]);
    }

    /**
     * Xử lý cập nhật mật khẩu mới vào DB
     */
    public function updatePasswordAfterReset() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm_password || strlen($password) < 6) {
            header("Location: /reset-password?token=$token&error=invalid_password");
            exit;
        }

        $userModel = new UserModel();
        $resetData = $userModel->checkResetToken($token);

        if ($resetData) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Cập nhật pass mới cho user
            $userModel->updatePasswordByEmail($resetData['email'], $hashedPassword);
            
            // Xóa token đã dùng
            $userModel->deleteResetToken($token);

            header("Location: /dang-nhap?reset_success=1");
        } else {
            header("Location: /quen-mat-khau?error=system_error");
        }
        exit;
    }

    private function sendEmail($email, $token,$userName = 'Học viên') {
        // 1. Nạp thủ công PHPMailer (Vì anh đã có trong vendor)
        require_once __DIR__ . '/../../payment/vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../../payment/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../../payment/vendor/phpmailer/phpmailer/src/SMTP.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'chinh.edu.vn@gmail.com'; // Email gửi
            $mail->Password   = 'isuw xrxi zgic ytjy';    // Mã 16 ký tự app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('chinh.edu.vn@gmail.com', 'Hỗ trợ Tạ Văn Chinh');
            $mail->addAddress($email);

            $resetLink = "https://tavanchinh.com/reset-password?token=" . $token;

            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu tài khoản';
            // Nội dung Email tối ưu
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; padding: 30px;'>
                    <div style='text-align: center; margin-bottom: 25px;'>
                        <h2 style='color: #0d6efd; margin: 0;'>Khôi phục mật khẩu</h2>
                        <p style='color: #666; font-size: 14px;'>Hệ thống đào tạo Tạ Văn Chinh</p>
                    </div>
                    
                    <p>Chào <strong>$userName</strong>,</p>
                    <p>Hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn gắn với email này.</p>
                    <p>Vui lòng nhấn vào nút bên dưới để tiến hành thay đổi mật khẩu (Link có hiệu lực trong <strong>30 phút</strong>):</p>
                    
                    <div style='text-align: center; margin: 35px 0;'>
                        <a href='$resetLink' style='background: #0d6efd; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);'>ĐẶT LẠI MẬT KHẨU</a>
                    </div>
                    
                    <p style='color: #666; font-size: 13px; line-height: 1.5; background: #f9f9f9; padding: 15px; border-radius: 5px;'>
                        <strong>Lưu ý:</strong> Nếu bạn không yêu cầu thay đổi này, vui lòng bỏ qua email này. Tài khoản của bạn vẫn sẽ được bảo mật tuyệt đối.
                    </p>
                    
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 25px 0;'>
                    
                    <div style='text-align: center; color: #999; font-size: 12px;'>
                        <p style='margin: 5px 0;'>Hỗ trợ kỹ thuật: <strong>0972.808.368</strong></p>
                        <p style='margin: 5px 0;'>Website: <a href='https://tavanchinh.com' style='color: #0d6efd; text-decoration: none;'>tavanchinh.com</a></p>
                    </div>
                </div>";

            $mail->send();
        } catch (Exception $e) {
            // Ghi log lỗi nếu không gửi được mail
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }
    }


    public function keepAlive() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        if (isset($_SESSION['user_id'])) {
            $userModel = new UserModel();
            $userId = $_SESSION['user_id'];
            $currentSessionId = session_id();

            $user = $userModel->findById($userId);

            if ($user && isset($user['last_session_id']) && $user['last_session_id'] !== $currentSessionId) {
                
                // Ghi log lại để Admin theo dõi
                $userModel->writeSecurityLog([
                    'user_id' => $userId,
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'old_session_id' => $currentSessionId,
                    'new_session_id' => $user['last_session_id']
                ]);

                echo json_encode(['status' => 'warning_multi_device']);
                exit;
            }
        }
        echo json_encode(['status' => 'alive']);
        exit;
    }
}