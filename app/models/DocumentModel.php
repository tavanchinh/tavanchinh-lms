<?php
require_once __DIR__ . '/../../core/Database.php';

class DocumentModel extends Database {

    /**
     * Lấy danh sách tài liệu theo ID khóa học
     */
    public function getDocsByCourse($courseId) {
        $sql = "SELECT * FROM course_documents WHERE course_id = ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm một tài liệu mới
     */
    public function addDocument($data) {
        $sql = "INSERT INTO course_documents (course_id, file_name, file_path, file_size, file_type) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['course_id'],
            $data['file_name'],
            $data['file_path'],
            $data['file_size'],
            $data['file_type']
        ]);
    }

    /**
     * Lấy thông tin chi tiết 1 tài liệu để xóa file vật lý
     */
    public function findById($id) {
        $sql = "SELECT * FROM course_documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Xóa tài liệu khỏi Database
     */
    public function deleteDocument($id) {
        $sql = "DELETE FROM course_documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}