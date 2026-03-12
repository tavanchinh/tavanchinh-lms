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
            $this->view('auth/dang-nhap', ['error' => 'Email hoặc mật khẩu không đúng!']);
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
}