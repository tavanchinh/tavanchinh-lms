<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ChapterModel.php';
require_once __DIR__ . '/../models/LessonModel.php';
require_once __DIR__ . '/../models/DocumentModel.php'; 

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
        $docModel = new DocumentModel(); // Khởi tạo Model tài liệu

        $course = $courseModel->findById($id);
        if (!$course) {
            header("Location: /admin/courses?error=not_found");
            exit;
        }
        
        // Lấy dữ liệu phân cấp: Chương -> Bài học
        $chaptersWithLessons = $chapterModel->getChaptersWithLessons($id);
        
        // Lấy danh sách tài liệu của khóa học này
        $documents = $docModel->getDocsByCourse($id);

        $this->view('admin/courses/edit', [
            'course' => $course,
            'chapters' => $chaptersWithLessons,
            'documents' => $documents, // Truyền sang View
            'isBackend' => true,
            'activePage' => 'courses',
            'title' => 'Sửa khóa học: ' . $course['name']
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseModel = new CourseModel();
            $docModel = new DocumentModel();
            
            $oldCourse = $courseModel->findById($id);
            $imageName = $oldCourse['image'];
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';

            // --- 1. Xử lý Ảnh đại diện (Giữ nguyên logic của bạn) ---
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $newFileName = time() . '_thumb_' . md5($_FILES['image']['name']) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                    if ($imageName && $imageName != 'default.jpg' && file_exists($uploadDir . $imageName)) {
                        unlink($uploadDir . $imageName);
                    }
                    $imageName = $newFileName;
                }
            }

            // --- 2. Xử lý TÀI LIỆU ĐÍNH KÈM (Upload nhiều file) ---
            if (!empty($_FILES['documents']['name'][0])) {
                $docDir = $uploadDir . 'documents/'; // Lưu vào public/uploads/documents/
                if (!is_dir($docDir)) mkdir($docDir, 0777, true);

                foreach ($_FILES['documents']['name'] as $key => $val) {
                    if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                        $originalName = $_FILES['documents']['name'][$key];
                        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $tmpName = $_FILES['documents']['tmp_name'][$key];
                        $size = $_FILES['documents']['size'][$key];
                        
                        // Tạo tên file an toàn để lưu trên server
                        $savedFileName = time() . '_' . md5($originalName) . '.' . $ext;
                        
                        if (move_uploaded_file($tmpName, $docDir . $savedFileName)) {
                            // Lưu thông tin vào Database
                            $docModel->addDocument([
                                'course_id' => $id,
                                'file_name' => $originalName,
                                'file_path' => 'uploads/documents/' . $savedFileName,
                                'file_size' => $this->formatSizeUnits($size),
                                'file_type' => $ext
                            ]);
                        }
                    }
                }
            }

            // --- 3. Lưu thông tin khóa học (Giữ nguyên logic của bạn) ---
            $data = [
                'name'  => $_POST['name'],
                'slug'  => $this->createSlug($_POST['name']),
                'price' => str_replace('.', '', $_POST['price']), // Bỏ dấu chấm nếu có
                'summary'     => $_POST['summary'] ?? '',
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 1,
                'position' => $_POST['position'] ?? 0,
                'image' => $imageName
            ];

            if ($courseModel->updateCourse($id, $data)) {
                header("Location: /admin/courses/edit/$id?success=1");
            } else {
                echo "Lỗi cập nhật Database!";
            }
            exit;
        }
    }

    // Hàm hỗ trợ định dạng dung lượng file
    private function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) { $bytes = number_format($bytes / 1073741824, 2) . ' GB'; }
        elseif ($bytes >= 1048576) { $bytes = number_format($bytes / 1048576, 2) . ' MB'; }
        elseif ($bytes >= 1024) { $bytes = number_format($bytes / 1024, 2) . ' KB'; }
        else { $bytes = $bytes . ' bytes'; }
        return $bytes;
    }

    public function deleteDoc($id) {
        header('Content-Type: application/json');
        $docModel = new DocumentModel();
        
        // 1. Tìm thông tin file để xóa file vật lý trên ổ cứng
        $doc = $docModel->findById($id);
        
        if ($doc) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $doc['file_path'];
            
            // Xóa file vật lý
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // 2. Xóa trong Database
            if ($docModel->deleteDocument($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa trong Database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tài liệu không tồn tại.']);
        }
        exit;
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
        // 1. Khởi tạo Model
        $courseModel = new CourseModel();
        $chapterModel = new ChapterModel();
        $lessonModel = new LessonModel();
        $docModel = new DocumentModel(); 
        $userModel = new UserModel();

        // 2. Lấy thông tin khóa học
        $course = $courseModel->findBySlug($slug);
        if (!$course) die("Khóa học không tồn tại!");

        // 3. Kiểm tra quyền sở hữu & Tài liệu
        $userId = $_SESSION['user_id'] ?? null;
        $isOwned = $userId ? $courseModel->checkOwnership($userId, $course['id']) : false;
        // 2. Logic mới: Chỉ lấy tài liệu nếu đã sở hữu (đã trả phí)
        if ($isOwned) {
            $documents = $docModel->getDocsByCourse($course['id']);
            $userModel->writeAccessLog($userId, 'view_course'); // Ghi log xem tài liệu
        } else {
            $documents = []; // Trả về mảng rỗng để giao diện không hiện gì cả
        }

        // 4. Lấy dữ liệu bài học
        $chapters = $chapterModel->getChaptersWithLessons($course['id']);

        // 5. Xử lý logic bài học (Lọc bài học thử + Tính tổng số bài)
        $totalLessons = 0;
        foreach ($chapters as &$chapter) {
            if (!empty($chapter['lessons'])) {
                // Nếu chưa mua, lọc bỏ các bài không phải preview
                if (!$isOwned) {
                    $chapter['lessons'] = array_filter($chapter['lessons'], function($lesson) {
                        return $lesson['is_preview'] == 1;
                    });
                }
                $totalLessons += count($chapter['lessons']);
            }
        }
        unset($chapter); // Giải phóng tham chiếu

        // 6. Tính toán tiến độ học tập
        $completedLessonIds = ($userId && $isOwned) ? $lessonModel->getCompletedLessonIds($userId, $course['id']) : [];
        $progressPercent = ($totalLessons > 0) ? round((count($completedLessonIds) / $totalLessons) * 100) : 0;

        // 7. Xác định bài học đầu tiên để tự động phát
        $firstLesson = null;
        foreach ($chapters as $chapter) {
            if (!empty($chapter['lessons'])) {
                $firstLesson = reset($chapter['lessons']);
                break; // Tìm thấy bài đầu tiên rồi thì thoát vòng lặp ngay
            }
        }

        // 8. Trả dữ liệu về View
        $this->view('frontend/course/learning', [
            'course'             => $course,
            'chapters'           => $chapters,
            'documents'          => $documents, 
            'isOwned'            => $isOwned,
            'isTrial'            => !$isOwned,
            'firstLesson'        => $firstLesson,
            'completedLessonIds' => $completedLessonIds,
            'progressPercent'    => $progressPercent,
            'totalLessons'       => $totalLessons
        ]);
    }
    

    public function stream($id) {
        // 1. Kiểm tra quyền của học viên (Giữ nguyên logic cũ của bạn)
        $tokenFromUrl = $_GET['token'] ?? '';
        $savedTokenData = $_SESSION['video_tokens'][$id] ?? null;
        if (!$savedTokenData || $savedTokenData['token'] !== $tokenFromUrl || time() > $savedTokenData['expires']) {
            http_response_code(403);
            exit;
        }

        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($id);
        if (!$lesson) { http_response_code(404); exit; }

        $tokenFromUrl = $_GET['token'] ?? '';
        $savedTokenData = $_SESSION['video_tokens'][$id] ?? null;
        session_write_close(); 

        if (!$savedTokenData || $savedTokenData['token'] !== $tokenFromUrl || time() > $savedTokenData['expires']) {
            http_response_code(403);
            die("Truy cập bị chặn.");
        }
        // 1. Cấu hình Bunny của bạn
        $libraryId = "614514"; 
        $apiKey    = "d651e95f-6cf9-48f2-a5aabac225c4-18a1-4962"; 
        $videoId   = $lesson['link_video'];
        $pullZoneHost = "vz-2f4a27cf-8a9.b-cdn.net"; 

        // 2. Tạo Link gốc và Token
        $expires = time() + 3600; 
        $token = md5($apiKey . $videoId . $expires);
        $masterUrl = "https://{$pullZoneHost}/{$videoId}/playlist.m3u8?token={$token}&expires={$expires}";
        
        // Đường dẫn gốc để nối link tuyệt đối
        $baseUrl = "https://{$pullZoneHost}/{$videoId}/";

        // 3. Đọc nội dung file Master từ Bunny
        $content = file_get_contents($masterUrl);
        if (!$content) { http_response_code(404); die(); }

        $lines = explode("\n", $content);
        $filteredLines = [];
        $skipNext = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Lọc chất lượng thấp (< 720p)
            if (strpos($line, 'RESOLUTION=') !== false) {
                preg_match('/RESOLUTION=\d+x(\d+)/', $line, $matches);
                $height = isset($matches[1]) ? (int)$matches[1] : 0;
                if ($height > 0 && $height < 720) {
                    $skipNext = true; 
                    continue;
                }
            }

            if ($skipNext) {
                $skipNext = false;
                continue;
            }

            // QUAN TRỌNG: Biến link tương đối thành tuyệt đối
            // Nếu dòng không bắt đầu bằng '#' và không có 'http', thì đó là link file con
            if (strpos($line, '#') !== 0 && strpos($line, 'http') !== 0) {
                // Nối thêm baseUrl và truyền lại token/expires để các file con cũng có quyền truy cập
                $line = $baseUrl . $line . (strpos($line, '?') !== false ? '&' : '?') . "token={$token}&expires={$expires}";
            }

            $filteredLines[] = $line;
        }

        // 4. Trả về cho VideoJS
        header('Content-Type: application/x-mpegURL');
        header('Access-Control-Allow-Origin: *'); // Tránh lỗi CORS
        echo implode("\n", $filteredLines);
        exit;

        
    }
    

    public function getStreamToken($id) {
        // 1. Lấy thông tin bài học
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($id);
        
        if (!$lesson) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Bài học không tồn tại']);
            return;
        }

        // 2. Kiểm tra quyền sở hữu hoặc bài học thử (Preview)
        $userId = $_SESSION['user_id'] ?? null;
        $courseModel = new CourseModel();
        $isOwned = $userId ? $courseModel->checkOwnership($userId, $lesson['course_id']) : false;
        $isPreview = (isset($lesson['is_preview']) && $lesson['is_preview'] == 1);

        // Nếu không có quyền => Chặn ngay lập tức
        if (!$isOwned && !$isPreview) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Bạn không có quyền xem nội dung này']);
            return;
        }

        // 3. Quản lý Token trong Session
        if (!isset($_SESSION['video_tokens'])) {
            $_SESSION['video_tokens'] = [];
        }

        $now = time();
        // Vì Google Access Token có hạn 3600s, ta nên để token nội bộ 
        // sống khoảng 1 tiếng (3600s) cho đồng bộ.
        $expireSeconds = 3600; 

        // Kiểm tra nếu đã có token cũ và còn hạn (trên 60 giây) thì dùng lại để tiết kiệm tài nguyên
        if (isset($_SESSION['video_tokens'][$id])) {
            $existing = $_SESSION['video_tokens'][$id];
            if ($existing['expires'] > ($now + 60)) {
                header('Content-Type: application/json');
                echo json_encode(['token' => $existing['token']]);
                return;
            }
        }

        // 4. Tạo Token mới nếu chưa có hoặc đã hết hạn
        $token = bin2hex(random_bytes(16));
        $_SESSION['video_tokens'][$id] = [
            'token' => $token,
            'expires' => $now + $expireSeconds
        ];

        // Ghi lại session và đóng để tránh lock session khi load video (quan trọng cho performance)
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
        $description = $course['summary'] ?? '';
        
        $this->view('frontend/course/detail', [
            'course' => $course,
            'chapters' => $chapters,
            'title' => $course['name'],
            'meta_description' => $description,
            'isOwned' => $isOwned,
            'isLoggedIn' => !empty($userId)
        ]);
    }   
}