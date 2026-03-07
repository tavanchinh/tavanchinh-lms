<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ChapterModel.php';

class CourseController extends BaseController {

    public function __construct() {
        // Một số hàm như watch/stream có thể cho học viên xem, 
        // nhưng các hàm admin thì cần check quyền. 
        // Tôi sẽ check quyền cụ thể trong từng hàm bên dưới.
    }

    // ==========================================
    // CÁC HÀM QUẢN TRỊ (ADMIN/STAFF)
    // ==========================================

    public function index() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);

        $courseModel = new CourseModel();
        $courses = $courseModel->getAllCourses();

        $this->view('admin/courses/list', [
            'courses' => $courses,
            'title'   => 'Danh sách khóa học',
            'isBackend' => true
        ]);
    }

    public function create() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);

        $categoryModel = new CategoryModel();
        $this->view('admin/courses/create', [
            'categories' => $categoryModel->getAllCategories(),
            'title' => 'Thêm khóa học mới'
        ]);
    }

    public function edit($id) {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);

        $courseModel = new CourseModel();
        $chapterModel = new ChapterModel();

        $course = $courseModel->findById($id);
        if (!$course) {
            header("Location: /admin/courses?error=not_found");
            exit;
        }
        
        // Lấy dữ liệu phân cấp: Chương -> Bài học
        $chaptersWithLessons = $chapterModel->getChaptersWithLessons($id);

        $this->view('admin/courses/edit', [
            'course' => $course,
            'chapters' => $chaptersWithLessons,
            'isBackend' => true,
            'activePage' => 'courses',
            'title' => 'Sửa khóa học: ' . $course['name']
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseModel = new CourseModel();
            $oldCourse = $courseModel->findById($id);
            
            // Mặc định giữ ảnh cũ
            $imageName = $oldCourse['image'];

            // Kiểm tra xem người dùng có chọn file không
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                
                // Di chuyển ngược lên 2 cấp từ app/controllers để ra thư mục gốc, sau đó vào public/uploads
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';

                // Kiểm tra để debug (Bạn có thể xóa dòng này sau khi chạy tốt)
                // die($uploadDir); 

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // 2. Tạo thư mục nếu chưa tồn tại
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // 3. Xử lý tên file mới để tránh trùng (ví dụ: 171523456_abc.jpg)
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newFileName = time() . '_' . md5($fileName) . '.' . $fileExtension;

                // 4. Di chuyển file từ bộ nhớ tạm vào thư mục uploads
                $dest_path = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Upload thành công -> Xóa ảnh cũ trên server (tránh rác)
                    if ($imageName && $imageName != 'default.jpg' && file_exists($uploadDir . $imageName)) {
                        unlink($uploadDir . $imageName);
                    }
                    $imageName = $newFileName; // Cập nhật tên file mới để lưu vào DB
                }
            }

            // 5. Chuẩn bị dữ liệu để lưu vào Database
            $data = [
                'name'  => $_POST['name'],
                'price' => $_POST['price'],
                'image' => $imageName // Tên file (mới hoặc cũ)
            ];

            if ($courseModel->updateCourse($id, $data)) {
                header("Location: /admin/courses/edit/$id?success=1");
            } else {
                echo "Lỗi cập nhật Database!";
            }
            exit;
        }
    }

    // Hàm lưu khóa học (Kết hợp xử lý Slug và Upload ảnh của bạn)
    public function storeCourse() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // 1. Xử lý upload ảnh
        $imageName = 'default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $imageName = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        }

        // 2. Tạo Slug (Sử dụng hàm từ BaseController)
        $slug = $this->createSlug($_POST['name']);

        // 3. Lưu vào DB thông qua Model
        $courseModel = new CourseModel();
        $data = [
            'category_id' => $_POST['category_id'],
            'name'        => $_POST['name'],
            'slug'        => $slug,
            'image'       => $imageName,
            'summary'     => $_POST['summary'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price'       => $_POST['price'],
            'sale_price'  => $_POST['sale_price'] ?? 0,
            'level'       => $_POST['level'],
            'status'      => 1,
            'position'    => 0  
        ];

        $courseModel->insertCourse($data);
        header("Location: /admin/courses?success=1");
        exit();
    }

    // ==========================================
    // CÁC HÀM GÁN KHÓA HỌC (GIỮ NGUYÊN)
    // ==========================================

    public function showAssignForm() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);

        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $data = [
            'students' => $userModel->getAllStudents(),
            'courses'  => $courseModel->getAllActive()
        ];

        $this->view('admin/assign_course', $data);
    }

    public function processAssign() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_POST['user_id'];
            $courseId = $_POST['course_id'];
            $price = $_POST['price'];

            $courseModel = new CourseModel();
            if ($courseModel->assignToStudent($userId, $courseId, $price)) {
                header("Location: /dashboard?message=success");
            }
        }
    }

    // ==========================================
    // CÁC HÀM XEM VIDEO & STREAM (GIỮ NGUYÊN)
    // ==========================================

    public function watch($slug, $id) {
        $this->view('frontend/course/watch', ['slug' => $slug, 'id' => $id]);
    }

    public function stream($id) {
        $configFile = __DIR__ . "/../../app/config/google_drive.json";
        if (!file_exists($configFile)) {
            die("Lỗi: Không tìm thấy file cấu hình!");
        }
        set_time_limit(0);
        
        // Lưu ý: Trong thực tế bạn nên lấy fileId từ Database dựa trên $id bài học
        // Ở đây tôi tạm giữ nguyên giá trị hardcode của bạn để code hoạt động ngay
        $fileId = $id; 

        $config = json_decode(file_get_contents($configFile), true);
        $clientId     = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $refreshToken = $config['refresh_token'];

        $accessToken = $this->getGoogleAccessToken($clientId, $clientSecret, $refreshToken);
        $driveUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media";

        $headers = ["Authorization: Bearer $accessToken"];

        if (isset($_SERVER['HTTP_RANGE'])) {
            $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $driveUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $headerLine) {
            $h = strtolower($headerLine);
            if (strpos($h, 'content-type:') !== false || 
                strpos($h, 'content-length:') !== false || 
                strpos($h, 'content-range:') !== false || 
                strpos($h, 'accept-ranges:') !== false) {
                header($headerLine);
            }
            if (strpos($h, 'http/1.1 206') !== false || strpos($h, 'http/2 206') !== false) {
                http_response_code(206);
            }
            return strlen($headerLine);
        });

        while (ob_get_level()) ob_end_clean();
        curl_exec($ch);
        curl_close($ch);
        exit;
    }

    private function getGoogleAccessToken($clientId, $clientSecret, $refreshToken) {
        $url = 'https://oauth2.googleapis.com/token';
        $postData = [
            'client_id' => $clientId, 
            'client_secret' => $clientSecret, 
            'refresh_token' => $refreshToken, 
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        return $response['access_token'] ?? null;
    }
}