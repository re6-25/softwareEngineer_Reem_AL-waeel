<?php
require_once 'Database.php';

class Category {
    public $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إضافة تصنيف جديد مع صورة
    public function add($name, $image = null) {
        $stmt = $this->db->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
        return $stmt->execute([$name, $image]);
    }

    // تعديل تصنيف مع صورة
    public function edit($id, $name, $image = null) {
        if ($image) {
            $stmt = $this->db->prepare("UPDATE categories SET name=?, image=? WHERE id=?");
            return $stmt->execute([$name, $image, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE categories SET name=? WHERE id=?");
            return $stmt->execute([$name, $id]);
        }
    }

    // حذف تصنيف
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id=?");
        return $stmt->execute([$id]);
    }

    // جلب تصنيف واحد
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // جلب جميع التصنيفات
    public function all() {
        $result = $this->db->query("SELECT * FROM categories ORDER BY id DESC");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // عداد التصنيفات
    public function count() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM categories");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['total'] : 0;
    }
}
?>
