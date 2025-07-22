<?php
require_once 'Database.php';
class Order {
    private $db;
   public function __construct() {
        $this->db = (new Database())->conn;
    }


    public function all() {
        $sql = "SELECT o.*, u.name AS user_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getByUser($user_id) {
    $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function updateStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    public function addNotes($orderId, $notes) {
        $stmt = $this->db->prepare("UPDATE orders SET notes = ? WHERE id = ?");
        return $stmt->execute([$notes, $orderId]);
    }

    public function add($data) {
        $sql = "INSERT INTO orders (user_id, books, total, discount, coupon, coupon_discount, shipping, final, note, created_at, address, payment_method, phone, email)
                VALUES (:user_id, :books, :total, :discount, :coupon, :coupon_discount, :shipping, :final, :note, :created_at, :address, :payment_method, :phone, :email)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':books', $data['books']);
        $stmt->bindParam(':total', $data['total']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':coupon', $data['coupon']);
        $stmt->bindParam(':coupon_discount', $data['coupon_discount']);
        $stmt->bindParam(':shipping', $data['shipping']);
        $stmt->bindParam(':final', $data['final']);
        $stmt->bindParam(':note', $data['note']);
        $stmt->bindParam(':created_at', $data['created_at']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);

        return $stmt->execute();
    }
    public function delete($orderId) {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$orderId]);
    }
}


?>
