<?php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/LessonModel.php';

class LessonController extends BaseController {

    public function __construct() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);
    }

    // Lưu bài học mới (Từ Modal trong trang Edit Course)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_id'   => $_POST['course_id'],
                'chapter_id'  => $_POST['chapter_id'], // Quan trọng: lấy từ select box
                'name'        => $_POST['name'],
                'link_video'  => $_POST['link_video'],
                'duration'    => (int)$_POST['duration'],
                'position'    => (int)$_POST['position'],
                'is_preview'  => isset($_POST['is_preview']) ? 1 : 0,
                'status'      => 1
            ];

            $lessonModel = new LessonModel();
            if ($lessonModel->store($data)) {
                header("Location: /admin/courses/edit/" . $data['course_id'] . "?success=lesson_added");
            } else {
                echo "Lỗi lưu dữ liệu!";
            }
            exit();
        }
    }

    // Xóa bài học
    public function delete($id) {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($id);
        
        if ($lesson) {
            $courseId = $lesson['course_id'];
            $lessonModel->delete($id);
            header("Location: /admin/courses/edit/" . $courseId . "?deleted=1");
        } else {
            header("Location: /admin/courses?error=not_found");
        }
        exit();
    }

    // Cập nhật bài học qua AJAX
    public function updateAjax() {
        // Chỉ xử lý nếu là POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            
            $data = [
                'chapter_id' => $_POST['chapter_id'],
                'name'       => $_POST['name'],
                'link_video' => $_POST['link_video'],
                'duration'   => (int)$_POST['duration'],
                'position'   => (int)$_POST['position'],
                'is_preview' => (int)$_POST['is_preview']
            ];

            $lessonModel = new LessonModel();
            // Gọi hàm update trong Model
            if ($lessonModel->update($id, $data)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật Database']);
            }
            exit; // Kết thúc để không render thêm giao diện thừa
        }
    }

    public function completeLesson() {
        // 1. Mở và đóng Session nhanh để tránh treo web (như đã thảo luận)
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;
        session_write_close(); 

        // 2. Kiểm tra bảo mật cơ bản
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 403);
        }

        // 3. Đọc dữ liệu từ JavaScript gửi lên
        $input = json_decode(file_get_contents('php://input'), true);
        $lessonId = $input['lesson_id'] ?? null;

        if (!$lessonId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Thiếu ID bài học']);
        }

        // 4. Gọi Model xử lý (Đây là lý do Controller ngắn gọn)
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->findById($lessonId);

        if (!$lesson) {
            return $this->jsonResponse(['success' => false, 'message' => 'Bài học không tồn tại']);
        }

        // Lưu trạng thái hoàn thành vào bảng user_lessons
        $result = $lessonModel->markAsCompleted($userId, $lessonId, $lesson['course_id']);

        if ($result) {
            // Tìm bài tiếp theo để trả về cho Client mở khóa giao diện
            $nextLesson = $lessonModel->getNextLesson($lesson['course_id'], $lesson['position']);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Chúc mừng bạn đã hoàn thành bài học!',
                'next_lesson_id' => $nextLesson ? $nextLesson['id'] : null
            ]);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Lỗi lưu tiến độ']);
    }


    /**
     * Hàm hỗ trợ trả về dữ liệu dạng JSON và dừng chương trình
     */
    private function jsonResponse($data, $code = 200) {
        // Ngăn chặn việc gửi thêm bất kỳ dữ liệu thừa nào (như lỗi <br />)
        if (ob_get_length()) ob_clean(); 
        
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }


    // app/controllers/LessonController.php

    public function adminStudy() {
        $search = $_GET['search'] ?? ''; // Lấy từ khóa tìm kiếm
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $lessonModel = new LessonModel();
        
        // Truyền $search vào Model
        $progressData = $lessonModel->getStudentProgressPaginated($perPage, $offset, $search);
        $totalRecords = $lessonModel->countStudentProgress($search);
        $totalPages = ceil($totalRecords / $perPage);

        foreach ($progressData as &$item) {
            $item['percent'] = ($item['total_lessons'] > 0) 
                ? round(($item['completed_lessons'] / $item['total_lessons']) * 100) 
                : 0;
        }

        return $this->view('admin/lessons/study_progress', [
            'progress' => $progressData,
            'title' => 'Quản lý Tiến độ Học viên',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'search' => $search // Truyền ngược lại View để hiển thị trong ô input
        ]);
    }

    public function adminFastComplete() {
        // Chỉ xử lý nếu là POST để tránh việc click nhầm link
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $courseId = $_POST['course_id'] ?? null;

            if ($userId && $courseId) {
                $lessonModel = new LessonModel();
                $result = $lessonModel->completeAllForStudent($userId, $courseId);
                
                if ($result) {
                    // Bạn có thể dùng Session để báo thành công
                    // $_SESSION['flash_message'] = "Đã mở khóa thành công!";
                }
            }
        }
        // Quay lại trang danh sách sau khi xử lý xong
        header('Location: /admin/study');
        exit;
    }
}