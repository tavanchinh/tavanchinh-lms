<?php

require_once __DIR__ . '/../../core/Database.php';
class ChapterModel extends Database {

    /**
     * Lấy danh sách tất cả các chương của một khóa học cụ thể
     * Sắp xếp theo vị trí (position) từ nhỏ đến lớn
     */
    public function getByCourse($courseId) {
        $sql = "SELECT * FROM chapters WHERE course_id = ? ORDER BY position ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin chi tiết một chương theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM chapters WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm mới một chương
     */
    public function store($data) {
        $sql = "INSERT INTO chapters (course_id, name, position, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['course_id'],
            $data['name'],
            $data['position'] ?? 0,
            $data['status'] ?? 1
        ]);
    }

    /**
     * Cập nhật thông tin chương
     */
    public function update($id, $data) {
        $sql = "UPDATE chapters SET 
                name = ?, 
                position = ?, 
                status = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['name'],
            $data['position'] ?? 1, // Nếu không có position, mặc định là 1
            $data['status'] ?? 1,   // Nếu không có status, mặc định là 1
            $id
        ]);
    }

    /**
     * Xóa chương
     * Lưu ý: Vì có ràng buộc FOREIGN KEY ON DELETE CASCADE, 
     * các bài học thuộc chương này cũng sẽ bị xóa tự động trong DB.
     */
    public function delete($id) {
        $sql = "DELETE FROM chapters WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách chương kèm theo các bài học bên trong (Dùng cho trang Edit Course)
     * Đây là hàm "thông minh" giúp bạn lấy dữ liệu lồng nhau chỉ bằng một vài truy vấn
     */
    public function getChaptersWithLessons($courseId) {
        // 1. Lấy tất cả các chương
        $chapters = $this->getByCourse($courseId);
        
        // 2. Với mỗi chương, lấy các bài học tương ứng
        foreach ($chapters as $key => $chapter) {
            $sql = "SELECT * FROM lessons WHERE chapter_id = ? ORDER BY position ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$chapter['id']]);
            $chapters[$key]['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $chapters;
    }
}