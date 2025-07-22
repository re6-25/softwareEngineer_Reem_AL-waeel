<?php
session_start();
session_unset();  // يمسح جميع متغيرات الجلسة
session_destroy(); // ينهي الجلسة
header("Location: login_register.php"); // يرجعك لصفحة تسجيل الدخول
exit;
?>