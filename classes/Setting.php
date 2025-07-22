<?php
class Setting {
    public $db;
    public function __construct() {
        require_once 'Database.php';
        $this->db = (new Database())->conn;
    }

    // جلب جميع الإعدادات كمصفوفة name => value
    public function all() {
        $q = $this->db->query("SELECT * FROM settings");
        $arr = [];
        while($row = $q->fetch(PDO::FETCH_ASSOC)) $arr[$row['name']] = $row['value'];
        return $arr;
    }

    // تحديث أو إضافة إعداد
    public function set($name, $value) {
        $stmt = $this->db->prepare("REPLACE INTO settings (name, value) VALUES (?, ?)");
        return $stmt->execute([$name, $value]);
    }
}
?>
