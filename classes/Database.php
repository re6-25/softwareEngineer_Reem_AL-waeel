<?php
class Database {
    private $host = "localhost";
    private $db   = "الإلهام الساكن";
    private $user = "root";
    private $pass = "";
    private $port = 3307;
    public $conn;

    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;port=$this->port;charset=utf8mb4";
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            // تفعيل نمط الأخطاء الاستثنائية
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function close() {
        $this->conn = null;
    }
}
?>
