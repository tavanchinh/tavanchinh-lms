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
        $sql = "SELECT id, name, email, role FROM users ORDER BY registered_at DESC";
        
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
        // Kiểm tra xem có cập nhật mật khẩu mới không
        if (!empty($data['password'])) {
            // Đổi phone -> phone_number
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ?, password = ?, role = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone'], $data['password'], $data['role'], $id];
        } else {
            // Đổi phone -> phone_number
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone'], $data['role'], $id];
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


    
}