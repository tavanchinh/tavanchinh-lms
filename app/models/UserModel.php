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

    public function getAllStudents() {
        $sql = "SELECT id, name, email, phone_number, created_at FROM users WHERE role = 'student'";
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
    
    // Đừng quên hàm kiểm tra email trùng lặp (Rất quan trọng)
    public function checkEmailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        return $this->query($sql, [$email])->fetch();
    }
}