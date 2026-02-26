<?php
// Nhớ lùi 2 cấp để vào core và 1 cấp để vào models
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class AdminController extends BaseController {

    public function __construct() {
        // Kiểm tra quyền: Chỉ cho phép admin đã đăng nhập
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit();
        }
    }

    public function index() {
        $courseModel = new CourseModel();
        $userModel = new UserModel();

        // Lấy danh sách để hiển thị
        $data = [
            'courses' => $courseModel->getAllCourses(),
            'users'   => $userModel->getAllUsers(), // Để dùng cho chức năng gán khóa học
            'title'   => 'Bảng điều khiển Admin'
        ];

        $this->view('admin/dashboard', $data);
    }

    public function createCourse() {
        // Lấy danh sách categories để đổ vào ô Select (vì bảng courses có khóa ngoại category_id)
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAllCategories();
        
        $this->view('admin/courses/create', ['categories' => $categories]);
    }


    public function storeCourse() {
        // 1. Kiểm tra nếu không phải POST thì không xử lý
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /dashboard");
            exit();
        }

        // 2. Xử lý Upload Ảnh
        $imageName = 'default.jpg'; // Ảnh mặc định nếu không upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = __DIR__ . '/../../public/uploads/'; // Đường dẫn thư mục upload
            
            // Tạo thư mục nếu chưa có
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = time() . '_' . uniqid() . '.' . $extension; // Tên file duy nhất
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        }

        // 3. Tạo Slug từ Name (Ví dụ: "Khóa Học PHP" -> "khoa-hoc-php")
        $slug = $this->createSlug($_POST['name']);

        // 4. Lưu vào Database
        // Sử dụng CourseModel để xử lý logic lưu trữ
        require_once __DIR__ . '/../models/CourseModel.php';
        $courseModel = new CourseModel();

        $data = [
            'category_id' => $_POST['category_id'],
            'name'        => $_POST['name'],
            'slug'        => $slug,
            'image'       => $imageName,
            'summary'     => $_POST['summary'],
            'description' => $_POST['description'],
            'price'       => $_POST['price'],
            'sale_price'  => $_POST['sale_price'] ?? 0,
            'level'       => $_POST['level'],
            'status'      => 1, // Mặc định hiển thị
            'position'    => 0  
        ];

        $courseModel->insertCourse($data);

        // 5. Quay lại Dashboard kèm thông báo thành công
        header("Location: /dashboard?success=1");
        exit();
    }

    public function storeAssign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $courseId = $_POST['course_id'] ?? null;

            if (!$userId || !$courseId) {
                header("Location: /dashboard?error=missing_data");
                exit();
            }

            require_once __DIR__ . '/../models/CourseModel.php';
            $courseModel = new CourseModel();

            $result = $courseModel->assignUserToCourse($userId, $courseId);

            if ($result) {
                header("Location: /dashboard?assign_success=1");
            } else {
                header("Location: /dashboard?error=already_assigned");
            }
            exit();
        }
    }

    public function students() {
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        
        // Lấy danh sách học viên (role = 'student')
        $students = $userModel->getAllStudents(); 

        $this->view('admin/students/index', [
            'users' => $students,
            'title' => 'Quản lý học viên'
        ]);
    }


    public function storeStudent() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userModel = new UserModel();

        // 1. Kiểm tra email đã tồn tại chưa
        if ($userModel->checkEmailExists($_POST['email'])) {
            header("Location: /students?error=email_exists");
            exit();
        }

        // 2. Chuẩn bị dữ liệu
        $data = [
            'name'     => $_POST['name'],
            'email'    => $_POST['email'],
            'phone'    => $_POST['phone'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT) // Mã hóa bảo mật
        ];

        // 3. Gọi hàm tạo trong Model
        if ($userModel->createStudent($data)) {
            header("Location: /admin/students?success=1");
        } else {
            header("Location: /admin/students?error=system");
        }
        exit();
    }
}
}