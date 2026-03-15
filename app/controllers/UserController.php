<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/LessonModel.php';

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
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /dang-nhap');
            exit;
        }

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