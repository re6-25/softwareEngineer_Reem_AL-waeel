<?php
session_start();
require_once 'classes/Blog.php';
$user_id = $_SESSION['user_id'] ?? null;
$blogObj = new Blog();
$allBlogs = $blogObj->getApproved();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_blog']) && $user_id) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if ($blogObj->add($user_id, $title, $content)) {
        $msg = "✅ تم إرسال المدونة، في انتظار الموافقة من الإدارة.";
    } else {
        $msg = "❌ حدث خطأ أثناء إضافة المدونة!";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كل المدونات - مكتبة الإلهام الساكن</title>
    <link rel="stylesheet" href="assets/css/blogs.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <?php if (!$user_id): ?>
            <div class="alert" style="color:#c33;padding:15px;background:#fff3f3;margin:15px 0;border-radius:7px;">
                يجب <a href="login_register.php" style="color:#a390e4;">تسجيل الدخول</a> لإضافة مدونة جديدة.
            </div>
        <?php endif; ?>

        <?php if ($user_id): ?>
            <form method="post" action="blogs.php" class="blog-add-form">
                <input type="text" name="title" placeholder="عنوان المدونة" required>
                <textarea name="content" placeholder="محتوى المدونة..." required></textarea>
                <button type="submit" name="add_blog">إضافة المدونة</button>
            </form>
        <?php endif; ?>

        <?php if (isset($msg)): ?>
            <div class="success-msg"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="section-title">كل المدونات</div>
        <?php foreach ($allBlogs as $blog): ?>
            <div class="blog-card">
                <div class="blog-title">
                    <a href="blog.php?id=<?= $blog['id'] ?>" style="color:#a390e4;text-decoration:none;">
                        <?= htmlspecialchars($blog['title']) ?>
                    </a>
                </div>
                <div class="blog-meta">
                    بقلم: <?= htmlspecialchars($blog['user_name'] ?? '-') ?>
                    <?php if (!empty($blog['created_at'])): ?>
                        | <span style="color:#999;"><?= htmlspecialchars(date('Y-m-d', strtotime($blog['created_at']))) ?></span>
                    <?php endif; ?>
                </div>
                <div class="blog-content"><?= nl2br(htmlspecialchars(mb_substr(strip_tags($blog['content']), 0, 120))) . ' ...' ?></div>
                <a href="blog.php?id=<?= $blog['id'] ?>" class="btn" style="background:#f9c846;">قراءة المزيد</a>
            </div>
        <?php endforeach; if (empty($allBlogs)): ?>
            <div style="color:#aaa;margin:10px 0;">لا توجد مدونات بعد.</div>
        <?php endif; ?>
    </div>
</body>
</html>
