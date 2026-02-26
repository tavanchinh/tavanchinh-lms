<?php
require_once __DIR__ . '/../../core/Database.php';

class CategoryModel extends Database {
    
    // Lấy tất cả danh mục để đổ vào Dropdown/Select
    public function getAllCategories() {
        $sql = "SELECT id, name FROM categories ORDER BY name ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}