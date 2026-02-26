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
}