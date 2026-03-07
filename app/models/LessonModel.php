<?php
require_once __DIR__ . '/../../core/Database.php';
class LessonModel extends Database {

    /**
     * Lấy danh sách bài học theo ID khóa học
     * Sắp xếp theo tên chương và vị trí hiển thị
     */
    public function getLessonsByCourse($courseId) {
        $sql = "SELECT * FROM lessons WHERE course_id = ? ORDER BY chapter_name ASC, position ASC";
        
        // Sử dụng prepare và execute để truyền mảng tham số đúng chuẩn PDO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function getLessonsByChapter($chapterId) {
        $sql = "SELECT * FROM lessons WHERE chapter_id = ? ORDER BY position ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết một bài học theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM lessons WHERE id = ? LIMIT 1";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }

    /**
     * Thêm bài học mới
     */
    public function store($data) {
        // 1. Chuẩn bị câu lệnh SQL (Chỉ dùng các cột thực tế có trong DB và Form)
        $sql = "INSERT INTO lessons (course_id, chapter_id, name, link_video, duration, position, is_preview, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $driveId = $this->extractDriveId($data['link_video']);
        
        // 2. Dùng prepare thay vì query để tránh lỗi Fatal Error
        $stmt = $this->db->prepare($sql);
        
        // 3. Thực thi và truyền mảng dữ liệu
        return $stmt->execute([
            $data['course_id'],
            $data['chapter_id'], // Dùng ID chương
            $data['name'],
            $driveId, // Lưu ID đã trích xuất vào DB
            $data['duration'] ?? 0,
            $data['position'] ?? 0,
            $data['is_preview'] ?? 0,
            $data['status'] ?? 1
        ]);
    }

    public function update($id, $data) {

        // Tự động lọc ID từ link video trước khi gán vào SQL
        $driveId = $this->extractDriveId($data['link_video']);

        // 1. Câu lệnh SQL chuẩn (Chỉ dùng các cột thực tế trong DB)
        $sql = "UPDATE lessons SET 
                chapter_id = ?, 
                name = ?, 
                link_video = ?, 
                duration = ?, 
                position = ?, 
                is_preview = ?, 
                status = ? 
                WHERE id = ?";
                
        // 2. Sử dụng prepare/execute để bảo mật và tránh lỗi Type Error
        $stmt = $this->db->prepare($sql);
        
        // 3. Thực thi với mảng các giá trị (chú ý thứ tự phải khớp với dấu ? ở trên)
        return $stmt->execute([
            $data['chapter_id'],
            $data['name'],
            $driveId, // Gán ID đã trích xuất vào DB
            $data['duration'] ?? 0,
            $data['position'] ?? 0,
            $data['is_preview'] ?? 0,
            $data['status'] ?? 1, // Fix lỗi Undefined "status"
            $id
        ]);
    }

    /**
     * Xóa bài học
     */
    public function delete($id) {
        $sql = "DELETE FROM lessons WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }


    /**
     * Trích xuất ID từ link Google Drive
     * Hỗ trợ các định dạng: /file/d/ID/view, id=ID, /open?id=ID...
     */
    public function extractDriveId($url) {
        // Nếu bản chất nó đã là ID (không chứa dấu / hay .) thì trả về luôn
        if (!preg_match('/[\/\.]/', $url)) {
            return $url;
        }

        // Pattern để tìm ID nằm giữa /d/ và / (hoặc cuối chuỗi)
        $pattern = '/[-\w]{25,}/'; 
        if (preg_match($pattern, $url, $matches)) {
            return $matches[0];
        }

        return $url; // Trả về gốc nếu không tìm thấy mẫu phù hợp
    }
}