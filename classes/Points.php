<?php
require_once 'Database.php';

class Points {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إضافة نقاط لمستخدم
    public function add($user_id, $points) {
        $stmt = $this->db->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        return $stmt->execute([$points, $user_id]);
    }

    // جلب نقاط مستخدم
    public function get($user_id) {
        $stmt = $this->db->prepare("SELECT points FROM users WHERE id=?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['points'] : 0;
    }
}
?>
