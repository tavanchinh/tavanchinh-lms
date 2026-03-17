<?php
require_once __DIR__ . "/../../core/BaseController.php";
// Giả sử bạn đã có UserModel để truy vấn DB
require_once '../app/models/UserModel.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        // Nếu đã login rồi thì vào thẳng Dashboard
        if (isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: /");
            exit();
        }
        $this->view('auth/login');
    }

    public function showDashboard() {
        $this->checkLogin(); // Kiểm tra xem đã đăng nhập chưa
        $this->view('admin/dashboard');
    }

    public function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']); 

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // 1. Lưu Session như cũ
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // 2. Xử lý Ghi nhớ đăng nhập (Cookie)
            if ($remember) {
                $expiry = time() + (7 * 24 * 60 * 60); // 7 ngày
                setcookie('remember_user', $user['id'], $expiry, "/");
                // Tạo một token bảo mật dựa trên hash password (nếu đổi pass, token này sẽ hỏng -> an toàn)
                setcookie('remember_token', md5($user['password']), $expiry, "/");
            }

            header("Location: /");
            exit;
        } else {
            $this->view('auth/login', ['error' => 'Email hoặc mật khẩu không đúng!']);
        }
    }
}

    

    public function logout() {
        session_destroy();
        
        // Xóa Cookie bằng cách cho hết hạn ở quá khứ
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, "/");
            setcookie('remember_token', '', time() - 3600, "/");
        }

        header("Location: /dang-nhap");
        exit;
    }

    // Hiển thị trang Đăng ký
    public function register() {
        $this->view('auth/register');
    }

    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CỰC KỲ QUAN TRỌNG: Xóa mọi nội dung đã echo trước đó (nếu có)
            ob_clean(); 
            header('Content-Type: application/json');

            $userModel = new UserModel();
            // Định nghĩa các biến rõ ràng từ đầu
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // 2. Định nghĩa biến $userData (Đảm bảo biến này tồn tại trước khi dùng)
            $userData = [
                'name' => $name,
                'email' => $email,
                'phone_number' => trim($_POST['phone_number']) ?? '',
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => 'student',
                'registered_at' => date('Y-m-d H:i:s')
            ];

            // 1. Kiểm tra email tồn tại
            if ($userModel->checkEmailExists($email)) {
                // SAI: $this->view('auth/register', [...]); 
                // ĐÚNG:
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email này đã được đăng ký! Vui lòng dùng email khác.'
                ]);
                exit; // Dừng ngay lập tức
            }

            // ... (phần code lưu database giữ nguyên) ...
            
            $userId = $userModel->registerStudent($userData);

            if ($userId && is_numeric($userId)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'student';
                // ... set session ...
                echo json_encode([
                    'success' => true, 
                    'message' => 'Chào mừng bạn đến với hệ thống!',
                    'redirect' => $_POST['back_url'] ?? '/'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Lỗi hệ thống khi tạo tài khoản.'
                ]);
            }
            exit;
        }
    }
}