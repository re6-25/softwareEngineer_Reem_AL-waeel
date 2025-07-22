<?php
require_once 'Database.php';

class Download {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // تسجيل تحميل جديد
    public function add($user_id, $book_id) {
        $stmt = $this->db->prepare("INSERT INTO downloads (user_id, book_id, downloaded_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$user_id, $book_id]);
    }

    // جلب كل الكتب التي تم تحميلها من مستخدم
    public function all($user_id) {
        $stmt = $this->db->prepare("SELECT book_id FROM downloads WHERE user_id=? ORDER BY downloaded_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
