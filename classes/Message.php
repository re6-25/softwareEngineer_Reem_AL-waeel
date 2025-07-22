<?php
require_once 'Database.php';

class Message {
    public $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إرسال رسالة جديدة من المستخدم
    public function send($user_id, $name, $email, $msg) {
        $stmt = $this->db->prepare("INSERT INTO messages (user_id, name, email, content, sender, status, created_at) VALUES (?, ?, ?, ?, 'user', 'unread', NOW())");
        return $stmt->execute([$user_id, $name, $email, $msg]);
    }

    // تعليم الرسالة كمحذوفة للمستخدم فقط
    public function markUserDelete($id, $user_id) {
        $stmt = $this->db->prepare("UPDATE messages SET is_deleted_by_user = 1 WHERE id=? AND user_id=?");
        return $stmt->execute([$id, $user_id]);
    }

    // عرض فقط الرسائل التي لم يتم حذفها من المستخدم
    public function byUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE user_id=? AND is_deleted_by_user = 0 ORDER BY created_at ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب جميع الرسائل (للإدارة)
    public function all() {
        $q = $this->db->query("SELECT * FROM messages ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    // حذف رسالة
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id=?");
        return $stmt->execute([$id]);
    }

    // تعليم كمقروءة
    public function setRead($id) {
        $stmt = $this->db->prepare("UPDATE messages SET status='read' WHERE id=?");
        return $stmt->execute([$id]);
    }

    // تخزين الرد داخل الرسالة الأصلية
    public function setReply($id, $reply) {
        $stmt = $this->db->prepare("UPDATE messages SET reply=?, reply_at=NOW() WHERE id=?");
        return $stmt->execute([$reply, $id]);
    }

    // إضافة رسالة جديدة من الإدارة للمستخدم
    public function replyAsAdmin($user_id, $reply) {
        $stmt = $this->db->prepare("INSERT INTO messages (user_id, sender, content, status, created_at) VALUES (?, 'admin', ?, 'read', NOW())");
        return $stmt->execute([$user_id, $reply]);
    }

    // جلب رسالة واحدة حسب ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // عداد الرسائل
    public function count() {
        $q = $this->db->query("SELECT COUNT(*) as c FROM messages");
        return $q->fetch(PDO::FETCH_ASSOC)['c'];
    }

    public function markAdminDelete($id) {
        $stmt = $this->db->prepare("UPDATE messages SET is_deleted_by_admin = 1 WHERE id=?");
        return $stmt->execute([$id]);
    }

    // استرجاع الرسالة
    public function unmarkAdminDelete($id) {
        $stmt = $this->db->prepare("UPDATE messages SET is_deleted_by_admin = 0 WHERE id=?");
        return $stmt->execute([$id]);
    }

    // جلب الرسائل الظاهرة فقط
    public function forAdmin() {
        $q = $this->db->query("SELECT * FROM messages WHERE is_deleted_by_admin = 0 ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب الرسائل المخفية فقط
    public function getDeletedByAdmin() {
        $q = $this->db->query("SELECT * FROM messages WHERE is_deleted_by_admin = 1 ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
