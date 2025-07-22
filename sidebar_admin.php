<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);

require_once 'classes/Book.php';
require_once 'classes/User.php';
require_once 'classes/Category.php';
require_once 'classes/Blog.php';
require_once 'classes/Message.php';
require_once 'classes/Comment.php';

$bookObj = new Book();
$userObj = new User();
$catObj  = new Category();
$blogObj = new Blog();
$msgObj  = new Message();
$commentObj = new Comment();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الأدمن</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/sidebaradmin.css">
</head>

<body>
    
    <div class="main-wrapper">
        <div style="padding:15px;text-align:center;color:#7a51c3;">
            مرحبًا، <?= htmlspecialchars($_SESSION['user_name'] ?? 'الأدمن') ?>
        </div>
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fa fa-cube"></i> الإلهام الساكن
            </div>

            <div class="sidebar-menu">
                <a href="dashboard_admin.php" class="<?= ($currentPage == 'dashboard_admin.php') ? 'active' : '' ?>"><i class="fa fa-chart-pie"></i>لوحة التحكم</a>
                <a href="admin_books.php" class="<?= ($currentPage == 'admin_books.php') ? 'active' : '' ?>"><i class="fa fa-book"></i>الكتب</a>
                <a href="admin_categories.php" class="<?= ($currentPage == 'admin_categories.php') ? 'active' : '' ?>"><i class="fa fa-layer-group"></i>التصنيفات</a>
                <a href="admin_users.php" class="<?= ($currentPage == 'admin_users.php') ? 'active' : '' ?>"><i class="fa fa-users"></i>المستخدمون</a>
                <a href="admin_blogs.php" class="<?= ($currentPage == 'admin_blogs.php') ? 'active' : '' ?>"><i class="fa fa-blog"></i>المدونات</a>
                <a href="admin_comments.php" class="<?= ($currentPage == 'admin_comments.php') ? 'active' : '' ?>"><i class="fa fa-comments"></i>التعليقات</a>
                <a href="admin_messages.php" class="<?= ($currentPage == 'admin_messages.php') ? 'active' : '' ?>"><i class="fa fa-envelope"></i>الرسائل</a>
                <a href="admin_profile.php" class="<?= ($currentPage == 'admin_profile.php') ? 'active' : '' ?>"><i class="fa fa-user-circle"></i>الملف الشخصي</a>
                <a href="admin_orders.php" class="sidebar-link">
                    <i class="fa fa-shopping-cart"></i> <!-- أيقونة عربة التسوق -->
                    <span>إدارة الطلبات</span>
                </a>
                
                <a href="admin_settings.php" class="<?= ($currentPage == 'admin_settings.php') ? 'active' : '' ?>"><i class="fa fa-cogs"></i>الإعدادات</a>

            </div>
            <div class="sidebar-bottom">
                <a href="logout.php" class="sidebar-menu logout"><i class="fa fa-sign-out-alt"></i>خروج</a>
            </div>
        </div>
    </div>
</body>

</html>