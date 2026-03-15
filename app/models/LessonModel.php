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
        $sql = "SELECT * FROM lessons WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm bài học mới
     */
    public function store($data) {
        // 1. Chuẩn bị câu lệnh SQL (Chỉ dùng các cột thực tế có trong DB và Form)
        $sql = "INSERT INTO lessons (course_id, chapter_id, name, link_video, duration, position, is_preview, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $driveId = $this->extractBunnyId($data['link_video']);
        
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
        $driveId = $this->extractBunnyId($data['link_video']);

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
        // 1. Chuẩn bị câu lệnh SQL với placeholder :id
        $sql = "DELETE FROM lessons WHERE id = :id";
        
        // 2. Sử dụng prepare thay vì query
        $stmt = $this->db->prepare($sql);
        
        // 3. Thực thi với mảng tham số
        return $stmt->execute(['id' => $id]);
    }

    // Đánh dấu bài học đã hoàn thành cho người dùng
    public function markAsCompleted($userId, $lessonId, $courseId) {
        // Câu lệnh này sẽ: 
        // - Chèn mới nếu chưa có
        // - Cập nhật lại thời gian nếu đã có (ON DUPLICATE KEY)
        $sql = "INSERT INTO user_lessons (user_id, lesson_id, course_id, is_completed, completed_at) 
                VALUES (:user_id, :lesson_id, :course_id, 1, NOW())
                ON DUPLICATE KEY UPDATE is_completed = 1, completed_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id'   => $userId,
            'lesson_id' => $lessonId,
            'course_id' => $courseId
        ]);
    }

    public function getCompletedLessonIds($userId, $courseId) {
        if (!$userId) return [];

        $sql = "SELECT lesson_id FROM user_lessons 
                WHERE user_id = :user_id 
                AND course_id = :course_id 
                AND is_completed = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'   => $userId,
            'course_id' => $courseId
        ]);

        // Trả về mảng một chiều các ID bài học
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getNextLesson($courseId, $currentPosition) {
        $sql = "SELECT id FROM lessons 
                WHERE course_id = :course_id 
                AND position > :current_pos 
                ORDER BY position ASC 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'current_pos' => $currentPosition
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về mảng chứa 'id' hoặc false
    }

    

    public function getStudentProgress() {
        // Chúng ta sử dụng LEFT JOIN để đếm bài học và tiến độ trong một lần truy vấn duy nhất
        $sql = "SELECT 
                    u.id as user_id, 
                    u.name as fullname, 
                    u.email,
                    c.id as course_id, 
                    c.name as course_name,
                    -- Đếm số lượng ID bài học duy nhất thuộc khóa học này
                    COUNT(DISTINCT l.id) as total_lessons,
                    -- Chỉ đếm những bài học đã hoàn thành của user này trong khóa học này
                    COUNT(DISTINCT CASE WHEN ul.is_completed = 1 THEN ul.lesson_id END) as completed_lessons
                FROM users u
                JOIN user_courses uc ON u.id = uc.user_id
                JOIN courses c ON uc.course_id = c.id
                -- Lấy danh sách tất cả bài học của khóa học
                LEFT JOIN lessons l ON l.course_id = c.id
                -- Lấy tiến độ tương ứng của học viên cho các bài học đó
                LEFT JOIN user_lessons ul ON (ul.lesson_id = l.id AND ul.user_id = u.id)
                GROUP BY u.id, c.id
                ORDER BY u.name ASC";
                //echo $sql;die();    
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function completeAllForStudent($userId, $courseId) {
        // Câu lệnh INSERT ... SELECT để mở khóa toàn bộ bài học của khóa học đó
        $sql = "INSERT INTO user_lessons (user_id, course_id, lesson_id, is_completed, updated_at)
                SELECT :user_id, :course_id, id, 1, NOW()
                FROM lessons
                WHERE course_id = :course_id2
                ON DUPLICATE KEY UPDATE is_completed = 1, updated_at = NOW()";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId,
            'course_id2' => $courseId
        ]);
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

    public function extractBunnyId($url) {
        // 1. Nếu bản chất nó đã là UUID (dạng 8-4-4-4-12 ký tự) thì trả về luôn
        $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        if (preg_match($uuidPattern, trim($url))) {
            return trim($url);
        }

        // 2. Pattern để tìm UUID nằm trong URL của Bunny (Embed hoặc Play link)
        // Nó sẽ tìm chuỗi có định dạng xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        $pattern = '/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return $url; // Trả về gốc nếu không tìm thấy mẫu phù hợp
    }


    
    public function getStudentProgressPaginated($limit, $offset, $search = '') {
        // Chúng ta sử dụng COUNT(DISTINCT) để đảm bảo mỗi bài học chỉ được đếm 1 lần
        $sql = "SELECT 
                    u.id as user_id, 
                    u.name, 
                    u.email, 
                    c.id as course_id,
                    c.name as course_name, 
                    u.registered_at as registration_date,
                    -- 1. Đếm số lượng bài học duy nhất thuộc khóa học này
                    COUNT(DISTINCT l.id) as total_lessons,
                    -- 2. Đếm số lượng bản ghi tiến độ đã hoàn thành duy nhất của học viên
                    COUNT(DISTINCT CASE WHEN up.is_completed = 1 THEN up.lesson_id END) as completed_lessons
                FROM users u
                JOIN user_courses uc ON u.id = uc.user_id
                JOIN courses c ON uc.course_id = c.id
                -- LEFT JOIN với lessons để lấy danh sách bài học gốc
                LEFT JOIN lessons l ON l.course_id = c.id
                -- LEFT JOIN với tiến độ, nhưng phải khớp cả user_id và lesson_id
                LEFT JOIN user_lessons up ON (up.lesson_id = l.id AND up.user_id = u.id)
                WHERE 1=1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone_number LIKE ?)";
            $searchKey = "%$search%";
            $params = [$searchKey, $searchKey, $searchKey];
        }

        // Nhóm theo cả User và Course để tách biệt từng dòng
        $sql .= " GROUP BY u.id, c.id 
                ORDER BY u.registered_at DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countStudentProgress($search = '') {
        $sql = "SELECT COUNT(*) as total FROM (
                    SELECT u.id FROM users u 
                    JOIN user_courses uc ON u.id = uc.user_id 
                    WHERE (u.name LIKE ? OR u.email LIKE ? OR u.phone_number LIKE ?)
                    GROUP BY u.id, uc.course_id
                ) as subquery";
                
        $searchKey = "%$search%";
        $result = $this->query($sql, [$searchKey, $searchKey, $searchKey])->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    

    /**
     * Lấy tổng số bài học của 1 khóa học
     */
    public function getTotalLessonsByCourseId($courseId) {
        $sql = "SELECT COUNT(id) as total FROM lessons WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Lấy số lượng bài đã hoàn thành của 1 user trong 1 khóa học
     */
    public function getCompletedCount($userId, $courseId) {
        // Dựa trên bảng user_lessons bạn đang dùng ở các hàm khác
        $sql = "SELECT COUNT(lesson_id) as total FROM user_lessons 
                WHERE user_id = ? AND course_id = ? AND is_completed = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}