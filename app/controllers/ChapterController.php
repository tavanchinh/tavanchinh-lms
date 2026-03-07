<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ChapterModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ChapterController extends BaseController {

    public function __construct() {
        $this->checkLogin();
        $this->checkRole(['admin', 'staff']);
    }

    /**
     * Lưu chương mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'];
            $name = trim($_POST['name']);
            $position = (int)$_POST['position'];

            if (empty($name)) {
                header("Location: /admin/courses/edit/" . $course_id . "?error=name_required");
                exit;
            }

            $chapterModel = new ChapterModel();
            $data = [
                'course_id' => $course_id,
                'name'      => $name,
                'position'  => $position,
                'status'    => 1
            ];

            if ($chapterModel->store($data)) {
                header("Location: /admin/courses/edit/" . $course_id . "?success=chapter_added");
            } else {
                header("Location: /admin/courses/edit/" . $course_id . "?error=system");
            }
            exit;
        }
    }

    /**
     * Xóa chương
     */
    public function delete($id) {
        $chapterModel = new ChapterModel();
        $chapter = $chapterModel->findById($id);
        
        if ($chapter) {
            $course_id = $chapter['course_id'];
            $chapterModel->delete($id);
            header("Location: /admin/courses/edit/" . $course_id . "?success=chapter_deleted");
        } else {
            header("Location: /admin/courses?error=not_found");
        }
        exit;
    }

    

    /**
     * Xử lý cập nhật tên chương qua AJAX (không load lại trang)
     */
    public function updateAjax() {
        //die("Da vao duoc ham updateAjax"); // Dòng này để test
        // Chỉ xử lý nếu là yêu cầu POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ Fetch API gửi sang
            $id = $_POST['id'] ?? null;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';

            // Kiểm tra dữ liệu đầu vào
            if (!$id || empty($name)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'ID hoặc tên chương không hợp lệ!'
                ]);
                exit;
            }

            $chapterModel = new ChapterModel();
            
            // Chuẩn bị mảng dữ liệu để update
            // (Giả sử hàm update của bạn nhận mảng các cột cần sửa)
            $data = [
                'name' => $name
            ];

            if ($chapterModel->update($id, $data)) {
                // Trả về JSON cho JavaScript xử lý tiếp
                echo json_encode(['success' => true]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Không thể cập nhật cơ sở dữ liệu.'
                ]);
            }
            exit;
        }
    }
} // Kết thúc class (Đảm bảo không có dấu ; thừa sau dấu ngoặc này)