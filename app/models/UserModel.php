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
    public function getAllUsers() {
        return $this->query("SELECT id, name, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [
            $data['name'], 
            $data['email'], 
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
    }

    /**
     * Tạo tài khoản học viên mới
     * @param array $data Mảng chứa name, email, phone, password
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


    public function createStudentAndGetId($data) {
        // 1. Chuẩn bị câu lệnh SQL (Lưu ý các tên cột phải khớp với DB của bạn)
        $sql = "INSERT INTO users (name, email, phone_number, password, role, created_at) 
                VALUES (:name, :email, :phone, :pass, :role, NOW())";
        
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
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ?, password = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone'], $data['password'], $id];
        } else {
            // Đổi phone -> phone_number
            $sql = "UPDATE users SET name = ?, email = ?, phone_number = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['phone'], $id];
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


    
}