<?php
require_once '../core/Database.php';

class UserModel extends Database {
    
    public function __construct() {
        parent::__construct(); // Gọi hàm kết nối của lớp Database
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        return $this->query($sql, [$email])->fetch();
    }

    /**
     * Tìm người dùng theo ID (Dùng cho checkLogin từ Cookie)
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        
        // 1. Chuẩn bị câu lệnh (Prepare)
        $stmt = $this->db->prepare($sql);
        
        // 2. Thực thi với mảng tham số (Execute)
        $stmt->execute([$id]);
        
        // 3. Lấy dữ liệu (Fetch)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // app/models/UserModel.php

    public function getAccounts($role = 'student', $keyword = '') {
        $sql = "SELECT id, name, email, phone_number, role, created_at FROM users WHERE role = :role";
        $params = ['role' => $role];

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE :kw OR email LIKE :kw2 OR phone_number LIKE :kw3)";
            $kw = "%$keyword%";
            $params['kw'] = $kw;
            $params['kw2'] = $kw;
            $params['kw3'] = $kw;
        }

        $sql .= " ORDER BY created_at DESC";
        
        // BƯỚC 1: Chuẩn bị câu lệnh
        $stmt = $this->db->prepare($sql);
        
        // BƯỚC 2: Thực thi (Hàm này trả về true/false)
        $stmt->execute($params);
        
        // BƯỚC 3: Lấy dữ liệu từ đối tượng $stmt (Không gọi trực tiếp sau execute)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllStudents() {
        $sql = "SELECT id, name, email, phone_number, created_at FROM users WHERE role = 'student'";
        return $this->query($sql)->fetchAll();
    }

    public function getAllStaff() {
        $sql = "SELECT id, name, email, phone_number, created_at FROM users WHERE role <> 'student'";
        return $this->query($sql)->fetchAll();
    }


    // Hàm lấy tất cả người dùng (Dùng cho quản trị)
    public function getAllUsers($limit = null) {
        $sql = "SELECT id, name, email, role, registered_at  FROM users ORDER BY registered_at DESC";
        
        if ($limit !== null) {
            // Ép kiểu (int) để đảm bảo an toàn tuyệt đối trước khi nối chuỗi
            $limit = (int)$limit;
            $sql .= " LIMIT $limit"; 
        }

        // Truyền mảng rỗng vì không còn dấu ? nào trong câu lệnh SQL
        return $this->query($sql, [])->fetchAll(PDO::FETCH_ASSOC);
    }


    // Hàm lấy dữ liệu có LIMIT và OFFSET
    public function getAccountsPaginated($tab, $search, $limit, $offset) {
        $role = $tab;
        $sql = "SELECT id, name, email, phone_number, role, registered_at, created_at FROM users WHERE role = ?";
        $params = [$role];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone_number  LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY registered_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm đếm tổng số bản ghi
    public function countAccounts($tab, $search) {
        $role = ($tab === 'admin') ? 'admin' : 'student';
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $params = [$role];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $result = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }


    public function create($data) {
        $sql = "INSERT INTO users (name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['name'], 
            $data['email'], 
            $data['phone_number'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
    }

    /**
     * Tạo tài khoản học viên mới
     * @param array $data Mảng chứa name, email, phone_number, password
     * @return bool Trả về true nếu thành công
     */
    public function createStudent($data) {
        $sql = "INSERT INTO users (name, email, phone_number, password, role, created_at) 
                VALUES (?, ?, ?, ?, 'student', NOW())";

        // Thực thi query với tham số truyền vào
        // Password nên được mã hóa trước khi truyền vào mảng $data
        return $this->query($sql, [
            $data['name'],
            $data['email'],
            $data['phone_number'] ?? null,
            $data['password']
        ]);
    }

    public function registerStudent($data) {
        $data['registered_at'] = date('Y-m-d H:i:s'); // Thêm trường registered_at vào mảng dữ liệu
        $data['role'] = 'student'; // Đảm bảo role luôn là student khi đăng ký  
        try {
            $sql = "INSERT INTO users (name, email, phone_number, password, role, registered_at) 
                    VALUES (:name, :email, :phone_number, :password, :role, :registered_at)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($data);

            // ĐÚNG: Trả về ID của bản ghi vừa chèn
            return $this->db->lastInsertId(); 
        } catch (PDOException $e) {
            return false;
        }
    }


    public function createStudentAndGetId($data) {
        // 1. Chuẩn bị câu lệnh SQL (Lưu ý các tên cột phải khớp với DB của bạn)
        $sql = "INSERT INTO users (name, email, phone_number, password, role, created_at, registered_at) 
                VALUES (:name, :email, :phone, :pass, :role, NOW(), NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone_number'] ?? '',
                'pass'  => $data['password'],
                'role'  => $data['role'] ?? 'student'
            ]);

            // 2. Trả về ID vừa được tạo tự động (AI)
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // Ghi log lỗi nếu cần
            error_log("Lỗi Create Student: " . $e->getMessage());
            return false;
        }
    }
    
    // Đừng quên hàm kiểm tra email trùng lặp (Rất quan trọng)
    public function checkEmailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        return $this->query($sql, [$email])->fetch();
    }
    
    /**
     * Cập nhật thông tin cơ bản của người dùng
     */
    public function updateUser($id, $data) {
        $role = $data['role'] ?? 'student';
        
        // Kiểm tra xem có cập nhật mật khẩu mới không
        if (!empty($data['password'])) {
            // Đổi phone -> phone_number
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ?, password = ?, role = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone_number'], $data['password'], $role, $id];
        } else {
            // Đổi phone -> phone_number
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone_number'], $role, $id];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    // app/models/UserModel.php

    public function delete($id) {
        try {
            // 1. Bắt đầu một Transaction (Giao dịch) để đảm bảo an toàn dữ liệu
            $this->db->beginTransaction();

            // 2. Xóa các liên kết khóa học của user này trong bảng trung gian (nếu có)
            // Việc này giúp tránh lỗi liên kết khóa ngoại (Foreign Key Constraint)
            $sql1 = "DELETE FROM user_courses WHERE user_id = :id";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute(['id' => $id]);

            // 3. Xóa các bản ghi tiến độ bài học (nếu bạn có bảng user_lessons)
            $sql2 = "DELETE FROM user_lessons WHERE user_id = :id2";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['id2' => $id]);

            // 4. Cuối cùng mới xóa User trong bảng chính
            $sql3 = "DELETE FROM users WHERE id = :id3";
            $stmt3 = $this->db->prepare($sql3);
            $result = $stmt3->execute(['id3' => $id]);

            // Nếu mọi thứ ổn, xác nhận thay đổi vào Database
            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            // Nếu có bất kỳ lỗi nào, hoàn tác (hủy bỏ) toàn bộ các lệnh xóa trên
            $this->db->rollBack();
            error_log("Lỗi khi xóa User: " . $e->getMessage());
            return false;
        }
    }

    
    public function updateInfo($id, $data) {
        // 1. Xây dựng câu lệnh SQL động dựa trên việc có đổi mật khẩu hay không
        $fields = "name = :name, phone_number = :phone_number";
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'phone_number' => $data['phone_number']
        ];

        // 2. Nếu có mật khẩu mới thì mới cập nhật cột password
        if (!empty($data['password'])) {
            $fields .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        //print_r($params);die();
        $sql = "UPDATE users SET $fields WHERE id = :id";
        //echo $sql;die();
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Lỗi Update Profile: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Kích hoạt tài khoản người dùng (Chuyển status từ 0 sang 1 hoặc active)
     */
    public function activeUser($userId) {
        // Giả sử bảng users của anh có cột 'status'
        // Nếu anh dùng kiểu số (0: khóa, 1: hoạt động) thì dùng SET status = 1
        // Nếu anh dùng kiểu chữ thì dùng SET status = 'active'
        $sql = "UPDATE users SET status = 1, updated_at = NOW() WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Lỗi Active User: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Tạo dấu vân tay thiết bị dựa trên trình duyệt và ngôn ngữ
     */
    private function getDeviceFingerprint() {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'unknown';
        // Tạo chuỗi băm duy nhất cho thiết bị này
        return hash('sha256', $agent . $lang);
    }


    public function writeAccessLog($userId, $eventType = 'login') {
        // 1. Cập nhật thời gian đăng nhập gần nhất vào bảng users
        if ($eventType == 'login') {
            $sqlUrl = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $stmtUrl = $this->db->prepare($sqlUrl);
            $stmtUrl->execute([$userId]);
        }

        // 2. Ghi Log chi tiết
        $sql = "INSERT INTO access_logs (user_id, event_type, ip_address, device_fingerprint, user_agent) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $userId,
            $eventType,
            $_SERVER['REMOTE_ADDR'],
            $this->getDeviceFingerprint(), // Hàm tạo mã hash ở bước trước
            $_SERVER['HTTP_USER_AGENT']
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }


    /**
     * Lấy danh sách người dùng đăng nhập trên nhiều thiết bị nhất
     */
    public function getTopSuspectedUsers($limit = 5) {
        $sql = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.last_login,
                    COUNT(DISTINCT al.device_fingerprint) as total_devices, 
                    COUNT(DISTINCT al.ip_address) as total_ips
                FROM access_logs al
                JOIN users u ON al.user_id = u.id
                GROUP BY al.user_id
                HAVING total_devices > 0
                ORDER BY total_devices DESC, total_ips DESC
                LIMIT :limit"; // Dùng placeholder có tên :limit
                
        $stmt = $this->db->prepare($sql);
        
        // Ép kiểu bắt buộc là số nguyên để SQL không bị lỗi nháy đơn
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết lịch sử truy cập của một User cụ thể
     */
    public function getUserAccessLogs($userId) {
        $sql = "SELECT * FROM access_logs 
                WHERE user_id = ? 
                ORDER BY created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }


    /**
     * Thống kê số lượng học viên và doanh thu theo ngày
     */
    public function getStudentRegistrationStats($days = 30) {
        $sql = "SELECT 
                    DATE_FORMAT(enrolled_at, '%d/%m') as date_label, -- Chỉ lấy Ngày/Tháng
                    COUNT(*) as student_count,
                    SUM(price_at_purchase) as total_revenue
                FROM user_courses 
                WHERE enrolled_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(enrolled_at)
                ORDER BY DATE(enrolled_at) ASC"; // Sắp xếp theo ngày gốc để không bị lệch thứ tự
                //echo $sql;die;
        return $this->query($sql, [$days])->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách học viên đang online (có hoạt động trong 10 phút qua)
     */
    public function getOnlineUsers($minutes = 50) {
        // Ai có last_seen trong vòng  phút đổ lại thì coi là đang Online
        $sql = "SELECT id, name, email, last_seen 
                FROM users 
                WHERE last_seen >= DATE_SUB(NOW(), INTERVAL ? MINUTE) 
                AND role = 'student'
                ORDER BY last_seen DESC LIMIT 15";
                
        return $this->query($sql, [$minutes])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Cập nhật nhịp đập (thời gian hoạt động cuối cùng) của User
     */
    public function updateHeartbeat($userId) {
        $sql = "UPDATE users SET last_seen = NOW() WHERE id = ?";
        return $this->query($sql, [$userId]);
    }


    
}