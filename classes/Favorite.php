<?php
require_once 'Database.php';

class Favorite {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إضافة كتاب للمفضلة
    public function add($user_id, $book_id) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO favorites (user_id, book_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $book_id]);
    }

    // حذف كتاب من المفضلة
    public function remove($user_id, $book_id) {
        $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id=? AND book_id=?");
        return $stmt->execute([$user_id, $book_id]);
    }

    // جلب معرفات الكتب المفضلة للمستخدم كمصفوفة أرقام
    public function getUserFavorites($user_id) {
        $sql = "SELECT book_id FROM favorites WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        $ids = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $ids[] = $row['book_id'];
        return $ids;
    }

    // هل الكتاب مفضل للمستخدم؟
    public function isFavorite($user_id, $book_id) {
        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE user_id=? AND book_id=?");
        $stmt->execute([$user_id, $book_id]);
        return $stmt->fetch() ? true : false;
    }

    // جلب كل الكتب المفضلة للمستخدم كمصفوفة
    public function all($user_id) {
        $stmt = $this->db->prepare("SELECT book_id FROM favorites WHERE user_id=?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
