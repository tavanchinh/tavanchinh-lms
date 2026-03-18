<?php
require_once '../core/Database.php';

class OrderModel extends Database {
    
    public function __construct() {
        parent::__construct(); // Kết nối cơ sở dữ liệu từ lớp cha
    }

    /**
     * Tạo đơn hàng mới (Trạng thái mặc định: pending)
     * Gọi hàm này TRƯỚC KHI gửi sang PayOS
     */
    public function createOrder($data) {
        $sql = "INSERT INTO orders (order_code, user_id, course_id, amount, phone_number, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        try {
            // Sử dụng hàm query có sẵn trong Database.php của anh
            return $this->query($sql, [
                $data['order_code'],
                $data['user_id'],
                $data['course_id'],
                $data['amount'],
                $data['phone_number']
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi Create Order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm đơn hàng theo mã order_code (Dùng để đối soát khi Webhook gọi về)
     */
    public function findByOrderCode($orderCode) {
        $sql = "SELECT * FROM orders WHERE order_code = ? LIMIT 1";
        return $this->query($sql, [$orderCode])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng (Dùng khi thanh toán thành công)
     */
    public function updateStatus($orderCode, $status, $paymentId = null) {
        $sql = "UPDATE orders SET status = ?, payment_id = ?, updated_at = NOW() WHERE order_code = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $paymentId, $orderCode]);
        } catch (PDOException $e) {
            error_log("Lỗi Update Order Status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra trạng thái đơn hàng để phản hồi cho Ajax (checkStatus)
     */
    public function checkStatus($orderCode) {
        if (!$orderCode) return null;
        
        $sql = "SELECT status FROM orders WHERE order_code = ? LIMIT 1";
        $result = $this->query($sql, [$orderCode])->fetch(PDO::FETCH_ASSOC);
        
        return $result['status'] ?? null;
    }

    /**
     * Lấy danh sách đơn hàng của một người dùng
     */
    public function getOrdersByUserId($userId) {
        $sql = "SELECT o.*, c.name as course_name 
                FROM orders o 
                JOIN courses c ON o.course_id = c.id 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC";
        return $this->query($sql, [$userId])->fetchAll(PDO::FETCH_ASSOC);
    }
}