<?php
require_once 'Database.php';

class Book
{
    public $db;
    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    // جلب كل الكتب
    public function all()
    {
        $q = $this->db->query("SELECT * FROM books ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    // إضافة كتاب
    public function add($title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date)
    {
        $stmt = $this->db->prepare("INSERT INTO books (title, author, category_id, image, pdf, description, price, is_free, is_featured, release_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date]);
    }

    // تحديث كتاب
    public function update($id, $title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date)
    {
        $stmt = $this->db->prepare("UPDATE books SET title=?, author=?, category_id=?, image=?, pdf=?, description=?, price=?, is_free=?, is_featured=?, release_date=? WHERE id=?");
        return $stmt->execute([$title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date, $id]);
    }

    // جلب كتاب واحد
    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function latest($limit = 8)
    {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function topRated($limit = 4)
    {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY rating DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mostRead($limit = 8)
    {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY read_count DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function freeBooks($limit = 8)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE is_free=1 ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRead($user_id, $book_id)
    {
        // سجل القراءة
        $this->db->prepare("INSERT INTO downloads (user_id, book_id) VALUES (?, ?)")->execute([$user_id, $book_id]);
        // زِد النقاط
        $this->db->prepare("UPDATE users SET points = points+5 WHERE id = ?")->execute([$user_id]);
    }

    public function count()
    {
        $q = $this->db->query("SELECT COUNT(*) as c FROM books");
        return $q->fetch(PDO::FETCH_ASSOC)['c'];
    }

    public function suggestRandom($count = 4)
    {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY RAND() LIMIT ?");
        $stmt->bindValue(1, $count, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        // 1. حذف الردود المرتبطة بتعليقات الكتاب
        $this->db->prepare("DELETE FROM replies WHERE comment_id IN (SELECT id FROM comments WHERE book_id=?)")->execute([$id]);
        // 2. حذف التعليقات المرتبطة بالكتاب
        $this->db->prepare("DELETE FROM comments WHERE book_id=?")->execute([$id]);
        // 3. حذف من المفضلة
        $this->db->prepare("DELETE FROM favorites WHERE book_id=?")->execute([$id]);
        // 4. حذف الكتاب نفسه
        $stmt = $this->db->prepare("DELETE FROM books WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function recommendForUser($user_id, $limit = 6)
    {
        $sql = "SELECT * FROM books WHERE category_id IN (
                SELECT category_id FROM favorites WHERE user_id=?
            ) OR id IN (
                SELECT book_id FROM downloads WHERE user_id=?
            ) ORDER BY RAND() LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($term)
    {
        $term = "%" . $term . "%";
        $sql = "SELECT b.*, c.name AS category_name 
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.title LIKE ? OR b.author LIKE ? OR c.name LIKE ?
            ORDER BY b.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byCategory($cat_id, $limit = 20)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE category_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $cat_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function featured($limit = 5)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE is_featured=1 ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mostDownloaded($limit = 8)
    {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY downloads DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function latestByYear($limit, $year = 2025)
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE YEAR(published_at) >= ? ORDER BY published_at DESC LIMIT ?");
        $stmt->bindValue(1, $year, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIds($ids)
    {
        $ids = array_map('intval', $ids);
        $in = implode(',', $ids);
        $sql = "SELECT * FROM books WHERE id IN ($in)";
        $res = $this->db->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
