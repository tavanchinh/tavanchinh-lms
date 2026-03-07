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
}