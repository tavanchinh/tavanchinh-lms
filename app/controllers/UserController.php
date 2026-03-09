<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';

class UserController extends BaseController {
    
    public function __construct() {
        // Kiểm tra đăng nhập ngay khi vào bất kỳ hàm nào của Profile
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /dang-nhap");
            exit;
        }
    }

    public function index() {
        $userModel = new UserModel();
        $user = $userModel->findById($_SESSION['user_id']);

        
        $course = new CourseModel();
        $courses = $course->getUserEnrolledCourses($_SESSION['user_id']);

        return $this->view('frontend/client/profile', [
            'user' => $user,
            'myCourses' => $courses,
            'title' => 'Trang cá nhân - ' . $user['name']
        ]);
    }

    public function update() {
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
            if (!empty($newPass)) {
                $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                $userModel->updatePassword($userId, $hashedPass);
            }

            header("Location: /profile?success=1");
            exit;
        }
    }
}