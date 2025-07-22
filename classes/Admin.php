<?php
require_once 'Database.php';

class Admin {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function login($email, $password) {
        $stmt = $this->db->conn->prepare("SELECT id, name, email, password FROM admins WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
    public static function isLogged() {
    return (isset($_SESSION['is_admin']) && $_SESSION['is_admin']);
}

}
?>
