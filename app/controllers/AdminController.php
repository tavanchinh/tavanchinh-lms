<?php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AdminController extends BaseController {

    public function __construct() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);
    }

    /**
     * Trang Dashboard tổng quan
     */
    public function index() {
        $courseModel = new CourseModel();
        $userModel = new UserModel();

        $data = [
            'courses' => $courseModel->getAllCourses(),
            'users'   => $userModel->getAllUsers(), 
            'title'   => 'Bảng điều khiển Admin'
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * Danh sách học viên
     */
    public function students() {
        $userModel = new UserModel();
        $students = $userModel->getAllStudents(); 
        $courseModel = new CourseModel();

        $this->view('admin/students/index', [
            'users' => $students,
            'courses' => $courseModel->getAllCourses(), 
            'title' => 'Quản lý học viên'
        ]);
    }

    /**
     * Lưu học viên mới
     */
    public function storeStudent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();
            $courseModel = new CourseModel();

            // 1. Kiểm tra email
            if ($userModel->checkEmailExists($_POST['email'])) {
                header("Location: /admin/students?error=email_exists");
                exit();
            }

            // 2. Tạo dữ liệu học viên
            $userData = [
                'name'     => $_POST['name'],
                'email'    => $_POST['email'],
                'phone'    => $_POST['phone_number'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];

            // 3. Lưu học viên và lấy ID vừa tạo
            $studentId = $userModel->createStudentAndGetId($userData);

            if ($studentId) {
                // 4. Xử lý gán nhiều khóa học (nếu có chọn)
                if (!empty($_POST['course_ids'])) {
                    foreach ($_POST['course_ids'] as $courseId) {
                        // Gọi hàm gán khóa học sẵn có của bạn
                        $courseModel->assignUserToCourse($studentId, $courseId);
                    }
                }
                header("Location: /admin/students?success=1");
            } else {
                header("Location: /admin/students?error=system");
            }
            exit();
        }
    }


    // AdminController.php

    public function getCourses($id) {
        $courseModel = new CourseModel();
        $enrolled = $courseModel->getUserEnrolledIds($id); // Bạn viết hàm này trong Model (trả về array ID)
        echo json_encode($enrolled);
        exit;
    }

    public function updateStudentAjax() {
        $id = $_POST['id'];
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $userData = [
            'name'  => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone_number']
        ];

        // Nếu người dùng nhập mật khẩu mới thì mới mã hóa và gửi đi
        if (!empty($_POST['password'])) {
            $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        // Thực hiện cập nhật thông tin cá nhân
        if ($userModel->updateUser($id, $userData)) {
            // Thực hiện đồng bộ khóa học được chọn từ Modal
            $courseIds = $_POST['course_ids'] ?? []; // Lấy mảng từ checkbox card
            $courseModel->syncUserCourses($id, $courseIds);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
        }
        exit;
    }
}