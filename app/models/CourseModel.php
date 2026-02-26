<?php
require_once __DIR__ . '/../../core/Database.php';

class CourseModel extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Lấy tất cả khóa học đang hoạt động để nhân viên chọn
    public function getAllActive() {
        $sql = "SELECT id, name, price FROM courses WHERE status = 1 ORDER BY position ASC";
        return $this->query($sql)->fetchAll();
    }

    // Gán khóa học cho học viên (Lưu vào bảng trung gian)
    public function assignToStudent($userId, $courseId, $price) {
        $sql = "INSERT INTO user_courses (user_id, course_id, price_at_purchase, payment_status) 
                VALUES (?, ?, ?, 'completed')";
        return $this->query($sql, [$userId, $courseId, $price]);
    }

    public function getAllCourses() {
        // Lấy tất cả khóa học, có thể sắp xếp theo vị trí (position)
        return $this->query("SELECT * FROM courses ORDER BY position ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertCourse($data) {
        $sql = "INSERT INTO courses (
                    category_id, 
                    name, 
                    slug, 
                    image, 
                    summary, 
                    description, 
                    price, 
                    sale_price, 
                    level, 
                    status, 
                    position
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Chuyển đổi dữ liệu từ mảng thành các tham số cho câu lệnh prepared statement
        $params = [
            $data['category_id'] ?: null, // Nếu trống thì để NULL để không lỗi khóa ngoại
            $data['name'],
            $data['slug'],
            $data['image'],
            $data['summary'],
            $data['description'],
            $data['price'] ?: 0,
            $data['sale_price'] ?: 0,
            $data['level'],
            $data['status'] ?? 1,
            $data['position'] ?: 0
        ];

        return $this->query($sql, $params);
    }


    public function assignUserToCourse($userId, $courseId) {
        // Kiểm tra xem đã gán chưa để tránh trùng lặp (tùy chọn)
        $checkSql = "SELECT * FROM user_courses WHERE user_id = ? AND course_id = ?";
        $exists = $this->query($checkSql, [$userId, $courseId])->fetch();

        if ($exists) return false; // Đã gán rồi

        $sql = "INSERT INTO user_courses (user_id, course_id, created_at) VALUES (?, ?, NOW())";
        return $this->query($sql, [$userId, $courseId]);
    }
}