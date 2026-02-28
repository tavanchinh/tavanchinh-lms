<?php
require_once __DIR__ . "/../../core/BaseController.php";
require_once '../app/models/UserModel.php';
require_once '../app/models/CourseModel.php';

class CourseController extends BaseController {

    public function showAssignForm() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']); // Nhân viên và Admin đều làm được

        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $data = [
            'students' => $userModel->getAllStudents(),
            'courses'  => $courseModel->getAllActive()
        ];

        $this->view('admin/assign_course', $data);
    }

    public function processAssign() {
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

    public function storeCourse() {
        // 1. Xử lý upload ảnh
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName);

        // 2. Lưu vào DB
        $sql = "INSERT INTO courses (name, category_id, image, price, level, status) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $_POST['name'],
            $_POST['category_id'],
            $imageName,
            $_POST['price'],
            $_POST['level'],
            1 // Trạng thái mặc định là hiển thị
        ]);

        header("Location: /dashboard");
    }

    public function watch($slug, $id) {
        // Bây giờ $slug sẽ là "khoa-hoc" và $id sẽ là "123"
        // Bạn có thể dùng $id để tìm bài học trong Database
        $this->view('frontend/course/watch', ['slug' => $slug, 'id' => $id]);
    }

    /**
     * Hàm Stream Video từ Google Drive
     * Route ví dụ: /course/stream/{fileId}
     */
    public function stream($id) {
        
        set_time_limit(0);
        $fileId       = '13d6Df5Q1MfPUxG-j0Rp8Gp_bhFnLmPOd';
        // Thông tin cấu hình (Nên đưa vào file config hoặc biến môi trường)
        $clientId     = '456429941433-dr052glfigqvuardgarc7r9kce0ojuv1.apps.googleusercontent.com';
        $clientSecret = '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';
        $refreshToken = '1//04o9tE7X0Nv0LCgYIARAAGAQSNwF-L9IrzDu2OC0F7ws93njMQQ7g1JIIGOPdwQIrO1GzX-4GFczPCQ_7ZqVckZL33djJcMphMuk';

        $accessToken = $this->getGoogleAccessToken($clientId, $clientSecret, $refreshToken);
        $driveUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media";

        $headers = ["Authorization: Bearer $accessToken"];

        // XỬ LÝ RANGE - QUAN TRỌNG ĐỂ TUA VIDEO TRÊN VIDEO.JS
        if (isset($_SERVER['HTTP_RANGE'])) {
            $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $driveUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // CHUYỂN TIẾP HEADER TỪ GOOGLE VỀ TRÌNH DUYỆT
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

        // Tắt output buffering để dữ liệu stream mượt hơn
        while (ob_get_level()) ob_end_clean();

        curl_exec($ch);
        curl_close($ch);
        exit; // Kết thúc để tránh BaseController render thêm dữ liệu thừa
    }

    /**
     * Hàm hỗ trợ lấy Access Token từ Refresh Token
     */
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