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
            'users'   => $userModel->getAllUsers($limit = 10), 
            'title'   => 'Bảng điều khiển Admin'
        ];

        $this->view('admin/dashboard', $data);
    }

    // app/controllers/UserController.php

    public function accounts() {
        // 1. Lấy tham số từ URL
        $tab = $_GET['tab'] ?? 'student';
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20; // Số học viên trên mỗi trang
        $offset = ($page - 1) * $perPage;

        // 2. Gọi Model lấy dữ liệu
        $userModel = new UserModel();
        
        // Lấy danh sách có phân trang
        $users = $userModel->getAccountsPaginated($tab, $search, $perPage, $offset);
        
        // Lấy tổng số học viên để tính tổng số trang
        $totalUsers = $userModel->countAccounts($tab, $search);
        $totalPages = ceil($totalUsers / $perPage);

        $courseModel = new CourseModel();

        // 3. Trả về View
        return $this->view('admin/users/accounts', [
            'users' => $users,
            'currentTab' => $tab,
            'search' => $search,
            'courses' => $courseModel->getAllCourses(), 
            'title' => 'Quản lý tài khoản',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers
        ]);
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
     * Danh sách nhân viên
     */
    public function staff() {
        $userModel = new UserModel();
        $staff = $userModel->getAllStaff(); 
        $courseModel = new CourseModel();

        $this->view('admin/staff/index', [
            'users' => $staff,
            'courses' => $courseModel->getAllCourses(), 
            'title' => 'Quản lý nhân viên'
        ]);
    }

    /**
     * Lưu học viên mới
     */
    public function storeStudent() {
        // Chỉ xử lý POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            exit;
        }

        $userModel = new UserModel();
        $courseModel = new CourseModel();
        header('Content-Type: application/json'); // Đảm bảo trình duyệt hiểu đây là JSON

        // 1. Kiểm tra email
        if ($userModel->checkEmailExists($_POST['email'])) {
            echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng!']);
            exit();
        }

        // 2. Tạo dữ liệu học viên
        $userData = [
            'name'         => $_POST['name'],
            'email'        => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'role'         => $_POST['role'] ?? 'student', // Mặc định là student nếu không có
            'password'     => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        // 3. Lưu học viên
        $studentId = $userModel->createStudentAndGetId($userData);

        if ($studentId) {
            // 4. Gán khóa học
            if (!empty($_POST['course_ids'])) {
                foreach ($_POST['course_ids'] as $courseId) {
                    $courseModel->assignUserToCourse($studentId, $courseId);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Đã thêm học viên và gán khóa học thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, không thể lưu dữ liệu.']);
        }
        exit();
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

        // Chỉ cập nhật role nếu người đang thực hiện là admin và có gửi role lên
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' && isset($_POST['role'])) {
            $userData['role'] = $_POST['role'];
        }

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

    

    // app/controllers/AdminController.php

    public function deleteUser($id) {
        // 1. Tận dụng logic checkRole: Chỉ cho phép 'admin' được thực hiện lệnh xóa
        // Mặc dù construct cho cả staff vào, nhưng hành động XÓA phải lọc lại lần nữa
        //var_dump($_SESSION); // Debug session để kiểm tra thông tin người dùng
        //die();
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_msg'] = "Chỉ Quản trị viên cao cấp mới có quyền xóa tài khoản!";
            header('Location: /admin/accounts');
            exit;
        }

        // 2. Kiểm tra tránh tự xóa chính mình (Bảo vệ tài khoản đang đăng nhập)
        if ($_SESSION['user_id'] == $id) {
            $_SESSION['error_msg'] = "Bạn không thể tự xóa chính mình!";
            header('Location: /admin/accounts');
            exit;
        }

        // 3. Thực hiện xóa qua Model
        $userModel = new UserModel();
        if ($userModel->delete($id)) {
            $_SESSION['success_msg'] = "Đã xóa tài khoản thành công.";
        } else {
            $_SESSION['error_msg'] = "Lỗi: Không thể xóa dữ liệu từ hệ thống.";
        }
        session_write_close(); // Đảm bảo session đã được ghi trước khi chuyển hướng

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/accounts'));
        exit;
    }
}