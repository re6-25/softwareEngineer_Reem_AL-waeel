<?php
require_once 'Database.php';

class Notification {
    public $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إرسال إشعار لكل المستخدمين الذين لديهم دور معين
    public function send($role, $message) {
        // جلب كل المستخدمين الذين لديهم هذا الدور
        $stmt = $this->db->prepare("SELECT id FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تجهيز أمر الإدخال للإشعارات
        $insert = $this->db->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");

        // إرسال الإشعار لكل مستخدم
        foreach ($users as $row) {
            $insert->execute([$row['id'], $message]);
        }

        return true;
    }

    // جلب كل الإشعارات لمستخدم معين
    public function all($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب الإشعارات غير المقروءة فقط
    public function unread($user_id, $limit = 5) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تعليم إشعار كمقروء
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
