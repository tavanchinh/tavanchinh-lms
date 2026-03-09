<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ChapterModel.php';
require_once __DIR__ . '/../models/LessonModel.php';

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
            $slug = $this->createSlug($_POST['name']);
            //echo "<pre>"; print_r($_POST); echo "</pre>"; // Debug dữ liệu trước khi lưu
            //die("Debugging..."); // Dừng thực thi để xem dữ liệu
            $data = [
                'name'  => $_POST['name'],
                'slug'  => $slug,
                'price' => $_POST['price'],
                'summary'     => $_POST['summary'] ?? '',
                'description' => $_POST['description'] ?? '',
                'position' => $_POST['position'] ?? 0,
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
            'position'    => $_POST['position'] ?? 0,
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

    public function learning($slug) {
        // 1. Khởi tạo các Model cần thiết
        $courseModel = new CourseModel();
        $chapterModel = new ChapterModel();
        $lessonModel = new LessonModel();

        // 2. Lấy thông tin khóa học theo slug
        $course = $courseModel->findBySlug($slug);
        if (!$course) {
            die("Khóa học không tồn tại!");
        }

        // 3. Kiểm tra quyền sở hữu
        $userId = $_SESSION['user_id'] ?? null;
        $isOwned = false;
        
        if ($userId) {
            // Sử dụng hàm checkOwnership chúng ta đã viết trước đó
            $isOwned = $courseModel->checkOwnership($userId, $course['id']);
        }

        // 4. Lấy danh sách chương và bài học
        $chapters = $chapterModel->getChaptersWithLessons($course['id']);

        $firstLesson = null;
        if (!empty($chapters)) {
            // Lấy chương đầu tiên
            $firstChapter = reset($chapters); 
            if (!empty($firstChapter['lessons'])) {
                // Lấy bài học đầu tiên của chương đó
                $firstLesson = reset($firstChapter['lessons']); 
            }
        }
        
        // 5. Nếu KHÔNG sở hữu, lọc ra chỉ những bài cho phép học thử
        if (!$isOwned) {
            foreach ($chapters as &$chapter) {
                $chapter['lessons'] = array_filter($chapter['lessons'], function($lesson) {
                    return $lesson['is_preview'] == 1; // Chỉ giữ lại bài có is_preview = 1
                });
            }
        }

        // Lấy danh sách ID bài học đã hoàn thành
        $completedLessonIds = [];
        if ($userId) {
            $completedLessonIds = $lessonModel->getCompletedLessonIds($userId, $course['id']);
        }
        
        
        // 6. Truyền dữ liệu sang View
        $this->view('frontend/course/learning', [
            'course'   => $course,
            'chapters' => $chapters,
            'isOwned'  => $isOwned,
            'isTrial'  => !$isOwned, // Nếu chưa mua thì mặc định là đang ở chế độ học thử
            'firstLesson' => $firstLesson,
            'completedLessonIds' => $completedLessonIds
        ]);
    }

    public function stream($id) {

        $tokenFromUrl = $_GET['token'] ?? '';
        $savedTokenData = $_SESSION['video_tokens'][$id] ?? null;
        session_write_close();
        

        if (!$savedTokenData || $savedTokenData['token'] !== $tokenFromUrl || time() > $savedTokenData['expires']) {
            http_response_code(403);
            exit;
        }
        // Lấy thông tin bài học từ Database
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($id);

        if (!$lesson) {
            http_response_code(404);
            die("Lỗi: Bài học không tồn tại!");
        }

        // 2. KIỂM TRA QUYỀN TRUY CẬP
        $userId = $_SESSION['user_id'] ?? null;
        $courseModel = new CourseModel();
        
        // Kiểm tra xem đã sở hữu khóa học chưa
        $isOwned = $userId ? $courseModel->checkOwnership($userId, $lesson['course_id']) : false;
        
        // Kiểm tra xem bài học có cho phép xem thử không
        $isPreview = (isset($lesson['is_preview']) && $lesson['is_preview'] == 1);

        // Nếu KHÔNG sở hữu khóa học VÀ KHÔNG phải bài học thử => Chặn
        if (!$isOwned && !$isPreview) {
            http_response_code(403);
            die("Lỗi: Bạn không có quyền xem bài học này. Vui lòng đăng ký khóa học!");
        }

        // --- LOGIC STREAM GOOGLE DRIVE (Giữ nguyên của bạn) ---
        $configFile = __DIR__ . "/../../app/config/google_drive.json";
        if (!file_exists($configFile)) { die("Lỗi: Không tìm thấy file cấu hình!"); }
        
        set_time_limit(0);
        $fileId = $lesson['link_video'];
        
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

    public function getStreamToken($id) {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($id);
        
        if (!$lesson) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Bài học không tồn tại']);
            return;
        }

        // --- KIỂM TRA QUYỀN (Giữ nguyên logic của bạn) ---
        $userId = $_SESSION['user_id'] ?? null;
        $courseModel = new CourseModel();
        $isOwned = $userId ? $courseModel->checkOwnership($userId, $lesson['course_id']) : false;
        $isPreview = ($lesson['is_preview'] == 1);

        if (!$isOwned && !$isPreview) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Truy cập bị chặn']);
            return;
        }

        // --- LOGIC QUẢN LÝ TOKEN THÔNG MINH ---
        if (!isset($_SESSION['video_tokens'])) {
            $_SESSION['video_tokens'] = [];
        }

        $now = time();
        $expireSeconds = $isOwned ? 14400 : 900; // 4 tiếng hoặc 15 phút

        // KIỂM TRA: Nếu đã có token cho ID này và còn hạn trên 30 giây
        if (isset($_SESSION['video_tokens'][$id])) {
            $existing = $_SESSION['video_tokens'][$id];
            if ($existing['expires'] > ($now + 30)) {
                // Tái sử dụng token cũ
                header('Content-Type: application/json');
                echo json_encode(['token' => $existing['token']]);
                return;
            }
        }

        // TẠO MỚI: Nếu chưa có hoặc đã hết hạn
        $token = bin2hex(random_bytes(16));
        $_SESSION['video_tokens'][$id] = [
            'token' => $token,
            'expires' => $now + $expireSeconds
        ];
        session_write_close();

        header('Content-Type: application/json');
        echo json_encode(['token' => $token]);
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

    public function detail($slug) {
        $courseModel = new CourseModel();
        $course = $courseModel->findBySlug($slug);
        if (!$course) {
            header("HTTP/1.0 404 Not Found");
            die("404 - Khóa học không tồn tại!");
        }

        // Giả sử bạn lưu ID người dùng trong Session khi đăng nhập
        $userId = $_SESSION['user_id'] ?? null;
        $isOwned = false;
        //die("Debugging... User ID: " . $userId); // Debug xem có lấy được userId không
        if ($userId) {
            // Hàm này kiểm tra trong bảng user_courses xem userId có courseId này chưa
            $isOwned = $courseModel->checkOwnership($userId, $course['id']);
        }

        // Lấy thêm danh sách chương và bài học thuộc khóa học này (nếu cần)
        $chapterModel = new ChapterModel();
        $chapters = $chapterModel->getChaptersWithLessons($course['id']);
        
        $this->view('frontend/course/detail', [
            'course' => $course,
            'chapters' => $chapters,
            'title' => $course['name'],
            'isOwned' => $isOwned,
            'isLoggedIn' => !empty($userId)
        ]);
    }   
}