<?php
require_once __DIR__ . '/../../core/Database.php';

class CourseModel extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Lấy tất cả khóa học đang hoạt động để nhân viên chọn
    public function getAllActive() {
        $sql = "SELECT id, name, price, image, slug FROM courses WHERE status = 1 ORDER BY position ASC";
        return $this->query($sql)->fetchAll();
    }

    // Gán khóa học cho học viên (Lưu vào bảng trung gian)
    public function assignToStudent($userId, $courseId, $price) {
        $sql = "INSERT INTO user_courses (user_id, course_id, price_at_purchase, payment_status) 
                VALUES (?, ?, ?, 'completed')";
        return $this->query($sql, [$userId, $courseId, $price]);
    }
    public function findById($id) {
        $sql = "SELECT * FROM courses WHERE id = ? LIMIT 1";
        return $this->query($sql, [$id])->fetch();
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


    /**
 * Cập nhật thông tin khóa học
 */
public function updateCourse($id, $data) {
    // Nếu có ảnh mới thì cập nhật cả ảnh, nếu không thì giữ nguyên ảnh cũ
    $sql = "UPDATE courses SET 
            category_id = ?, 
            name = ?, 
            slug = ?,
            price = ?, 
            sale_price = ?, 
            summary = ?, 
            description = ?, 
            level = ?, 
            status = ?, 
            position = ?,
            image = ?
            WHERE id = ?";
            
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        $data['category_id'],
        $data['name'],
        $data['slug'],
        $data['price'],
        $data['sale_price'] ?? 0,
        $data['summary'],
        $data['description'],
        $data['level'],
        $data['status'] ?? 1,
        $data['position'] ?? 0,
        $data['image'], // Tên file ảnh (cũ hoặc mới)
        $id
    ]);
}


    public function assignUserToCourse($userId, $courseId) {
        // Kiểm tra xem đã gán chưa để tránh trùng lặp (tùy chọn)
        $checkSql = "SELECT * FROM user_courses WHERE user_id = ? AND course_id = ?";
        $exists = $this->query($checkSql, [$userId, $courseId])->fetch();

        if ($exists) return false; // Đã gán rồi

        $sql = "INSERT INTO user_courses (user_id, course_id, enrolled_at) VALUES (?, ?, NOW())";
        return $this->query($sql, [$userId, $courseId]);
    }

    /**
     * Đồng bộ danh sách khóa học của học viên
     */
    public function syncUserCourses($studentId, $courseIds) {
        // 1. Xóa tất cả các gán cũ
        $sqlDelete = "DELETE FROM user_courses WHERE user_id = ?";
        $stmtDelete = $this->db->prepare($sqlDelete);
        $stmtDelete->execute([$studentId]);

        // 2. Gán lại danh sách mới (Bỏ cột created_at nếu bảng chưa có)
        if (!empty($courseIds)) {
            // Chỉ chèn vào 2 cột chắc chắn có là user_id và course_id
            $sqlInsert = "INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)";
            $stmtInsert = $this->db->prepare($sqlInsert);
            
            foreach ($courseIds as $courseId) {
                $stmtInsert->execute([$studentId, $courseId]);
            }
        }
        return true;
    }

    /**
     * Lấy danh sách ID các khóa học học viên ĐÃ tham gia
     */
    public function getUserEnrolledIds($userId) {
        $sql = "SELECT course_id FROM user_courses WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        // Trả về mảng phẳng chỉ chứa ID: [1, 4, 7]
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Lấy danh sách đầy đủ thông tin các khóa học học viên ĐÃ tham gia
     */
    public function getUserEnrolledCourses($userId) {
        $sql = "SELECT DISTINCT c.* FROM courses c JOIN user_courses uc ON c.id = uc.course_id WHERE uc.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        // Trả về mảng phẳng chỉ chứa ID: [1, 4, 7]
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug($slug) {
        // Truy vấn lấy thông tin khóa học theo slug
        $sql = "SELECT * FROM courses WHERE slug = :slug AND status = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkOwnership($userId, $courseId) {
        // Nếu không có userId (chưa đăng nhập) thì chắc chắn chưa mua
        if (!$userId) return false;

        $sql = "SELECT id FROM user_courses 
                WHERE user_id = :user_id AND course_id = :course_id 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId
        ]);

        // Nếu fetch() có dữ liệu, trả về true, ngược lại false
        return $stmt->fetch() ? true : false;
    }


    /**
     * Gán thêm 1 khóa học mới cho học viên (Dùng cho PayOS Webhook)
     */
    public function enrollCourse($studentId, $courseId, $price = 0) {
        // 1. Kiểm tra xem học viên đã sở hữu khóa học này chưa (tránh insert trùng)
        $checkSql = "SELECT id FROM user_courses WHERE user_id = ? AND course_id = ? LIMIT 1";
        $stmtCheck = $this->db->prepare($checkSql);
        $stmtCheck->execute([$studentId, $courseId]);
        $exists = $stmtCheck->fetch();

        if (!$exists) {
            // 2. Chỉ chèn thêm (INSERT), không xóa cái cũ của người ta
            // Giả sử bảng của anh có thêm cột status và enrolled_at (nếu chưa có anh cứ bỏ ra)
            $sqlInsert = "INSERT INTO user_courses (user_id, course_id, price_at_purchase, payment_status, enrolled_at) 
                        VALUES (?, ?, ?, 'paid', NOW())";
            $stmtInsert = $this->db->prepare($sqlInsert);
            return $stmtInsert->execute([$studentId, $courseId, $price]);
        }
        
        return true; // Đã có rồi thì coi như thành công
    }
}