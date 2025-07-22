<?php
require_once 'Database.php';

class Comment {
    public $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // إضافة تعليق عام (للكتب أو المدونات)
    public function add($user_id, $target_id, $comment, $type='book') {
        if ($type == 'blog') {
            $stmt = $this->db->prepare("INSERT INTO comments (user_id, blog_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([$user_id, $target_id, $comment]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO comments (user_id, book_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([$user_id, $target_id, $comment]);
        }
    }

    // إضافة رد على تعليق
    public function addReply($user_id, $comment_id, $reply) {
        $stmt = $this->db->prepare("INSERT INTO replies (comment_id, user_id, reply, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$comment_id, $user_id, $reply]);
    }

    // جلب كل التعليقات مع الردود لكتاب معيّن
    public function getByBook($book_id) {
        // جلب التعليقات مع اسم المستخدم
        $stmt = $this->db->prepare("SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.book_id=? ORDER BY c.created_at ASC");
        $stmt->execute([$book_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // جلب الردود لكل تعليق
        foreach ($comments as &$comment) {
            $cid = $comment['id'];
            $q = $this->db->prepare("SELECT r.*, u.name as user_name FROM replies r LEFT JOIN users u ON r.user_id = u.id WHERE r.comment_id=? ORDER BY r.created_at ASC");
            $q->execute([$cid]);
            $comment['replies'] = $q->fetchAll(PDO::FETCH_ASSOC);
        }
        return $comments;
    }

    // جلب تعليقات مدونة مع الردود
    public function getByBlog($blog_id) {
        $comments = [];
        $stmt = $this->db->prepare("SELECT comments.*, users.name AS user_name
            FROM comments
            LEFT JOIN users ON users.id = comments.user_id
            WHERE comments.blog_id = ? AND comments.parent_id IS NULL
            ORDER BY comments.created_at ASC");
        $stmt->execute([$blog_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['replies'] = $this->getReplies($row['id']);
            $comments[] = $row;
        }
        return $comments;
    }

    // جلب الردود لتعليق
    public function getReplies($comment_id) {
        $replies = [];
        $stmt = $this->db->prepare("SELECT comments.*, users.name AS user_name
            FROM comments
            LEFT JOIN users ON users.id = comments.user_id
            WHERE comments.parent_id = ?
            ORDER BY comments.created_at ASC");
        $stmt->execute([$comment_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $replies[] = $row;
        }
        return $replies;
    }

    public function all() {
        $q = $this->db->prepare("SELECT * FROM comments ORDER BY id DESC");
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث حالة التعليق (موافقة/رفض)
    public function setStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE comments SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }

    // حذف تعليق مع حذف ردوده لو فيه
    public function delete($id) {
        // احذف الردود التابعة للتعليق (لو عندك جدول replies)
        $stmt = $this->db->prepare("DELETE FROM replies WHERE comment_id=?");
        $stmt->execute([$id]);
        // احذف التعليق نفسه
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
