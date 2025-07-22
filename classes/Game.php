<?php
require_once 'Database.php';

class Game {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // تسجيل نتيجة فوز المستخدم وإضافة نقاط
    public function win($user_id, $game_name, $points) {
        $stmt = $this->db->prepare("INSERT INTO games (user_id, game_name, points, played_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $game_name, $points]);

        // إضافة النقاط لرصيد المستخدم
        $stmt2 = $this->db->prepare("UPDATE users SET points = points + ? WHERE id=?");
        return $stmt2->execute([$points, $user_id]);
    }

    public function addScore($user_id, $score) {
        $stmt = $this->db->prepare("UPDATE users SET score = score + ? WHERE id=?");
        return $stmt->execute([$score, $user_id]);
    }

    public function all() {
        $q = $this->db->query("SELECT * FROM games ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserTotalScore($user_id) {
        $stmt = $this->db->prepare("SELECT SUM(score) as total FROM game_scores WHERE user_id=?");
        $stmt->execute([$user_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res && $res['total'] !== null ? $res['total'] : 0;
    }

    // جلب كل نتائج الألعاب لمستخدم
    public function history($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE user_id=? ORDER BY played_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
