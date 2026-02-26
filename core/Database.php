<?php
class Database {
    protected $db;

    public function __construct() {
        if ($this->db === null) {
            $host = 'localhost';
            $db_name = 'tavanchinh.com';
            $username = 'root';
            $password = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
            
            try {
                $this->db = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
            } catch (PDOException $e) {
                die("Kết nối database thất bại: " . $e->getMessage());
            }
        }
    }

    // Các phương thức bổ trợ dùng chung cho tất cả các Model sau này
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}