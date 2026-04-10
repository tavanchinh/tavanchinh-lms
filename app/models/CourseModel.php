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


    // Gán khóa học cho học viên (Dùng cho AdminController)
    public function assignUserToCourse($userId, $courseId, $paidAmount = 0) {
        // 1. Kiểm tra xem đã gán chưa
        $checkSql = "SELECT * FROM user_courses WHERE user_id = ? AND course_id = ?";
        $exists = $this->query($checkSql, [$userId, $courseId])->fetch();

        if ($exists) return false; 

        $accessData = $this->calculateAccessLevel($courseId, $cleanAmount);
        $sql = "INSERT INTO user_courses (user_id, course_id, price_at_purchase, remaining_amount, access_level, lock_at_lesson_id, enrolled_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
        return $this->query($sql, [$userId, $courseId, $cleanAmount, $accessData['remaining_amount'], $accessData['access_level'], $accessData['lock_at_lesson_id']]);

    }

    
    public function syncUserCourses_bk($userId, $courseIds = [], $paidAmounts = []) {
        // 1. Xóa những khóa học KHÔNG nằm trong danh sách mới gửi lên
        if (!empty($courseIds)) {
            // Tạo chuỗi ?, ?, ? để dùng cho câu lệnh IN
            $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
            $sqlDelete = "DELETE FROM user_courses WHERE user_id = ? AND course_id NOT IN ($placeholders)";
            $paramsDelete = array_merge([$userId], $courseIds);
            $this->query($sqlDelete, $paramsDelete);
        } else {
            // Nếu danh sách mới rỗng, xóa sạch khóa học của user này
            $this->query("DELETE FROM user_courses WHERE user_id = ?", [$userId]);
            return true;
        }

        // 2. Duyệt danh sách được chọn để Cập nhật hoặc Thêm mới
        foreach ($courseIds as $courseId) {
            $rawAmount = $paidAmounts[$courseId] ?? 0;
            $cleanAmount = (int)preg_replace('/[^0-9]/', '', $rawAmount);

            // --- GỌI LOGIC TÍNH TOÁN TẠI ĐÂY ---
            $accessData = $this->calculateAccessLevel($courseId, $cleanAmount);

            $check = $this->query("SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?", [$userId, $courseId])->fetch();

            if ($check) {
                // CẬP NHẬT: Thêm cả access_level và remaining_amount
                $this->query(
                    "UPDATE user_courses SET 
                        price_at_purchase = ?, 
                        remaining_amount = ?, 
                        access_level = ?, 
                        lock_at_lesson_id = ? 
                    WHERE user_id = ? AND course_id = ?", 
                    [$cleanAmount, $accessData['remaining_amount'], $accessData['access_level'], $accessData['lock_at_lesson_id'], $userId, $courseId]
                );
            } else {
                // THÊM MỚI: Đầy đủ các trường
                $this->query(
                    "INSERT INTO user_courses (user_id, course_id, price_at_purchase, remaining_amount, access_level, lock_at_lesson_id, enrolled_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())", 
                    [$userId, $courseId, $cleanAmount, $accessData['remaining_amount'], $accessData['access_level'], $accessData['lock_at_lesson_id']]
                );
            }
        }
        return true;
    }


    public function syncUserCourses($userId, $courseIds = [], $paidAmounts = []) {
        // 1. Lấy danh sách khóa học hiện tại của user từ DB để so sánh
        $stmt = $this->query("SELECT course_id FROM user_courses WHERE user_id = ?", [$userId]);
        $existingCourseIds = $stmt->fetchAll(PDO::FETCH_COLUMN); // Trả về mảng ví dụ: [10, 15]

        // Bắt đầu Transaction để an toàn dữ liệu
        $this->db->beginTransaction(); 

        try {
            // 2. XÓA: Những khóa học có trong DB nhưng KHÔNG có trong Form gửi lên
            $toDelete = array_diff($existingCourseIds, $courseIds);
            if (!empty($toDelete)) {
                $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
                $this->query("DELETE FROM user_courses WHERE user_id = ? AND course_id IN ($placeholders)", array_merge([$userId], $toDelete));
            }

            // 3. DUYỆT DANH SÁCH TỪ FORM
            foreach ($courseIds as $courseId) {
                $rawAmount = $paidAmounts[$courseId] ?? 0;
                $cleanAmount = (int)preg_replace('/[^0-9]/', '', $rawAmount);
                $accessData = $this->calculateAccessLevel($courseId, $cleanAmount);

                // KIỂM TRA XEM KHÓA HỌC NÀY ĐÃ TỒN TẠI CHƯA
                if (in_array($courseId, $existingCourseIds)) {
                    // TRƯỜNG HỢP CẬP NHẬT: Không lưu giao dịch mới, chỉ update thông tin
                    $this->query(
                        "UPDATE user_courses SET 
                            price_at_purchase = ?, 
                            remaining_amount = ?, 
                            access_level = ?, 
                            lock_at_lesson_id = ? 
                        WHERE user_id = ? AND course_id = ?", 
                        [$cleanAmount, $accessData['remaining_amount'], $accessData['access_level'], $accessData['lock_at_lesson_id'], $userId, $courseId]
                    );
                } else {
                    // TRƯỜNG HỢP THÊM MỚI: ĐÂY LÀ NƠI LƯU GIAO DỊCH
                    $this->query(
                        "INSERT INTO user_courses (user_id, course_id, price_at_purchase, remaining_amount, access_level, lock_at_lesson_id, enrolled_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())", 
                        [$userId, $courseId, $cleanAmount, $accessData['remaining_amount'], $accessData['access_level'], $accessData['lock_at_lesson_id']]
                    );

                    // --- LƯU GIAO DỊCH 1 LẦN DUY NHẤT TẠI ĐÂY ---
                    $this->query(
                        "INSERT INTO transactions (user_id, course_id, amount, type, description, created_at) 
                        VALUES (?, ?, ?, 'enroll', ?, NOW())", 
                        [$userId, $courseId, $cleanAmount, "Đăng ký khóa học mới qua quản trị"]
                    );
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Log lỗi hoặc quăng exception ra ngoài
            return false;
        }
    }
    

    /**
     * Lấy danh sách ID các khóa học học viên ĐÃ tham gia
     */
    public function getUserEnrolledIds($userId) {
        $sql = "SELECT course_id, price_at_purchase FROM user_courses WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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


    private function calculateAccessLevel($courseId, $paidAmount) {
        // 1. Lấy giá gốc khóa học
        $course = $this->query("SELECT price FROM courses WHERE id = ?", [$courseId])->fetch();
        $totalPrice = $course ? (int)$course['price'] : 0;

        $remainingAmount = $totalPrice - $paidAmount;
        
        if ($remainingAmount <= 0) {
            return [
                'access_level' => 2,
                'lock_at_lesson_id' => null,
                'remaining_amount' => 0
            ];
        }

        $courseLockMap = [
            4 => 11,  // Khóa ID 4: Chặn từ bài 11
            5 => 18, // Khóa ID 5: Chặn từ bài 18
            7 => 25,  // Khóa ID 7: Chặn từ bài 25
        ];
        
        return [
            'access_level' => 1,
            'lock_at_lesson_id' => $courseLockMap[$courseId] ??  null,
            'remaining_amount' => $remainingAmount
        ];
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
                        VALUES (?, ?, ?, 'completed', NOW())";
            $stmtInsert = $this->db->prepare($sqlInsert);
            return $stmtInsert->execute([$studentId, $courseId, $price]);
        }
        
        return true; // Đã có rồi thì coi như thành công
    }


    public function getEnrollmentByLessonId($userId, $lessonId) {
        $sql = "SELECT uc.access_level, uc.lock_at_lesson_id 
                FROM user_courses uc
                JOIN lessons l ON l.course_id = uc.course_id
                WHERE uc.user_id = ? AND l.id = ? LIMIT 1";
        return $this->query($sql, [$userId, $lessonId])->fetch();
    }

    /**
     * Lấy thông tin chi tiết đăng ký khóa học của user
     * Bao gồm: Mức độ truy cập, bài học bị chặn, và số tiền nợ
     */
    public function getEnrollmentDetails($userId, $courseId) {
        $sql = "SELECT user_id, access_level, lock_at_lesson_id, remaining_amount 
                FROM user_courses 
                WHERE user_id = ? AND course_id = ? LIMIT 1";
        
        // Sử dụng phương thức query của Base giữ nguyên cấu trúc hệ thống của anh
        $result = $this->query($sql, [$userId, $courseId])->fetch();
        
        return $result ? $result : null;
    }
}