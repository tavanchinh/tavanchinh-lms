<?php

require_once __DIR__ . '/../../core/Database.php';
class FinanceModel extends Database {
    
    /**
     * Thêm một giao dịch mới vào bảng finance_transactions
     */
    public function addTransaction($data) {
        $sql = "INSERT INTO finance_transactions (
                    user_id, 
                    category_id, 
                    order_id, 
                    amount, 
                    payment_method, 
                    note, 
                    transaction_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['user_id'] ?? null,
            $data['category_id'] ?? 1, // Mặc định 1 là học phí
            $data['order_id'] ?? null,
            $data['amount'] ?? 0,
            $data['payment_method'] ?? 'transfer',
            $data['note'] ?? '',
            $data['transaction_date'] ?? date('Y-m-d')
        ];

        return $this->query($sql, $params);
    }

    /**
     * Lấy tổng doanh thu theo khoảng thời gian
     */
    public function getTotalRevenue($fromDate, $toDate) {
        $sql = "SELECT SUM(amount) as total FROM finance_transactions 
                WHERE transaction_date BETWEEN ? AND ?";
        $result = $this->query($sql, [$fromDate, $toDate])->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Lấy danh sách giao dịch có kèm tên User (để làm báo cáo)
     */
    public function getAllTransactions($fromDate, $toDate) {
        $sql = "SELECT ft.*, u.name as user_name 
                FROM finance_transactions ft
                LEFT JOIN users u ON ft.user_id = u.id
                WHERE ft.transaction_date BETWEEN ? AND ?
                ORDER BY ft.transaction_date DESC, ft.id DESC";
        return $this->query($sql, [$fromDate, $toDate])->fetchAll();
    }
}