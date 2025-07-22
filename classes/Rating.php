<?php
require_once 'Database.php';

class Rating
{
    private $db;
    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    // جلب تقييم مستخدم محدد لكتاب
    public function get($user_id, $book_id)
    {
        $stmt = $this->db->prepare("SELECT stars FROM ratings WHERE user_id=? AND book_id=?");
        $stmt->execute([$user_id, $book_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['stars'] : 0;
    }

    // جلب أحدث التقييمات للكتب
    public function latestReviews($limit = 5)
    {
        $stmt = $this->db->prepare("SELECT * FROM ratings ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب آراء الزوار عن المكتبة
    public function libraryReviews($limit = 4) {
        $stmt = $this->db->prepare("SELECT * FROM library_reviews ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب جميع تقييمات كتاب
    public function getByBook($book_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM ratings WHERE book_id=? ORDER BY created_at DESC");
        $stmt->execute([$book_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // إضافة أو تحديث تقييم
    public function set($user_id, $book_id, $stars)
    {
        $stars = max(1, min(5, intval($stars)));
        $check = $this->db->prepare("SELECT id FROM ratings WHERE user_id=? AND book_id=?");
        $check->execute([$user_id, $book_id]);
        if ($check->fetch()) {
            $stmt = $this->db->prepare("UPDATE ratings SET stars=? WHERE user_id=? AND book_id=?");
            $success = $stmt->execute([$stars, $user_id, $book_id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO ratings (user_id, book_id, stars) VALUES (?, ?, ?)");
            $success = $stmt->execute([$user_id, $book_id, $stars]);
        }
        return $success;
    }

    // عدد التقييمات للكتاب
    public function count($book_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as c FROM ratings WHERE book_id=?");
        $stmt->execute([$book_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['c'] : 0;
    }

    // المتوسط
    public function avg($book_id)
    {
        $stmt = $this->db->prepare("SELECT AVG(stars) as avg FROM ratings WHERE book_id=?");
        $stmt->execute([$book_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['avg'] !== null ? round($row['avg'], 1) : 0;
    }
}
?>
