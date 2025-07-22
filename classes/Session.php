<?php
class Session {
    public static function start() {
        if(session_status() == PHP_SESSION_NONE) session_start();
    }
    public static function isLogged() {
        self::start();
        return isset($_SESSION['user_id']);
    }
    public static function logout() {
        self::start();
        session_unset(); session_destroy();
        header("Location: login_register.php");
        exit;
    }
}
?>
