<?php
require_once 'Database.php';

class Report {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function topBooks($limit=5) {
        $sql = "
            SELECT b.*, COUNT(d.id) as total
            FROM books b LEFT JOIN downloads d ON b.id=d.book_id
            GROUP BY b.id ORDER BY total DESC LIMIT $limit
        ";
        $q = $this->db->query($sql);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function topUsers($limit=5) {
        $sql = "
            SELECT u.*, COUNT(d.id) as total
            FROM users u LEFT JOIN downloads d ON u.id=d.user_id
            GROUP BY u.id ORDER BY total DESC LIMIT $limit
        ";
        $q = $this->db->query($sql);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
