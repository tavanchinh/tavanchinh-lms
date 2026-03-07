<?php
require_once __DIR__ . '/../app/models/UserModel.php';
class BaseController {
    public function __construct() {
        // Khởi động session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hàm gọi View kèm theo dữ liệu
    protected function view($viewName, $data = []) {
        extract($data); // Chuyển array ['user' => 'admin'] thành biến $user
        $viewPath = "../app/views/" . $viewName . ".php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View $viewName không tồn tại.");
        }
    }

    
    protected function checkLogin() {
        // Nếu đã có Session thì cho qua luôn
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        // Nếu không có Session, kiểm tra Cookie "Ghi nhớ"
        if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
            $userId = $_COOKIE['remember_user'];
            $token  = $_COOKIE['remember_token'];

            $userModel = new UserModel();
            $user = $userModel->findById($userId); // Bạn cần viết hàm findById trong UserModel

            if ($user && md5($user['password']) === $token) {
                // Tái tạo lại Session từ Cookie
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                return true;
            }
        }

        // Nếu cả 2 đều không có, đá về trang login
        header("Location: /dang-nhap");
        exit;
    }

    // Kiểm tra quyền (Admin hoặc Staff)
    protected function checkRole($roles = []) {
        if (!in_array($_SESSION['user_role'], $roles)) {
            die("Bạn không có quyền truy cập khu vực này!");
        }
    }

    /**
     * Tạo đường dẫn thân thiện (Slug) từ chuỗi tiếng Việt
     */
    protected function createSlug($str) {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        
        // Loại bỏ ký tự đặc biệt
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        // Chuyển khoảng trắng thành dấu gạch ngang
        $str = preg_replace('/([\s]+)/', '-', $str);
        
        return trim($str, '-');
    }
}