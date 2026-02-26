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
            header("Location: /dashboard");
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

            $userModel = new UserModel();
            $user = $userModel->findByEmail($email);
            //echo "<pre>"; print_r($user); echo "</pre>"; // Debug thông tin user lấy được từ DB
            //echo "Password nhập vào: " . $password . "<br>"; // Debug password nhập vào
            //echo "Password hash " . password_hash($password, PASSWORD_DEFAULT) . "<br>";
            //echo "Password hash trong DB: " . $user['password'] . "<br>"; die(); // Dừng ở đây để xem thông tin debug
            // Kiểm tra user và mật khẩu (Sử dụng password_verify)
            if ($user && password_verify($password, $user['password'])) {
                // Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: /dashboard");
            } else {
                $this->view('auth/login', ['error' => 'Email hoặc mật khẩu không đúng!']);
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /login");
    }
}