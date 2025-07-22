<?php
session_start();
require_once 'classes/Blog.php';
require_once 'classes/Comment.php';

$blogObj = new Blog();
$commentObj = new Comment();

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'زائر';

// جلب تفاصيل المدونة
$blog_id = intval($_GET['id'] ?? 0);
$blog = $blogObj->find($blog_id);
if (!$blog) die('<div style="padding:30px;color:#a00;">المدونة غير موجودة.</div>');

// إضافة تعليق جديد أو رد
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_comment']) && $user_id) {
        $content = trim($_POST['comment']);
        // نوع التعليق "blog"
        $commentObj->add($user_id, $blog_id, $content, 'blog');
        $msg = "تم إضافة التعليق!";
    }
    if (isset($_POST['add_reply']) && $user_id) {
        $reply = trim($_POST['reply']);
        $comment_id = intval($_POST['comment_id']);
        $commentObj->addReply($user_id, $comment_id, $reply);
        $msg = "تم إضافة الرد!";
    }
}

// جلب كل التعليقات مع الردود
$comments = $commentObj->getByBlog($blog_id);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($blog['title']) ?> - مكتبة الإلهام الساكن</title>
    <link rel="stylesheet" href="assets/css/blogs.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .comment-card {
            background: #f8f7fd;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
        }
        .reply-card {
            background: #f4f2fa;
            border-radius: 8px;
            padding: 8px 15px;
            margin: 6px 0 0 35px;
            color: #7157c7;
            font-size: .97em;
        }
        .comment-box, .reply-box {
            margin: 12px 0;
        }
        .blog-title {
            font-size: 2em;
            color: #7157c7;
            font-weight: bold;
            margin: 22px 0 6px 0;
        }
        .blog-meta {
            color: #888;
            margin-bottom: 18px;
        }
        .blog-content {
            font-size: 1.11em;
            background: #fff;
            border-radius: 16px;
            padding: 19px 20px;
            margin-bottom: 17px;
            box-shadow: 0 3px 15px #e0d3fa1a;
        }
        .btn {
            background: #a390e4;
            color: #fff !important;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            padding: 6px 19px;
            margin-top: 9px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background .15s;
        }
        .btn:hover { background: #7157c7;}
        .section-title {
            font-size: 1.15em;
            font-weight: bold;
            margin: 22px 0 9px 0;
            color: #7157c7;
        }
        @media (max-width:700px) {
            .blog-title { font-size: 1.25em;}
            .blog-content { font-size: .97em; padding: 9px 4vw;}
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <div class="blog-title"><?= htmlspecialchars($blog['title']) ?></div>
        <div class="blog-meta">
            بقلم: <?= htmlspecialchars($blog['user_name'] ?? '-') ?>
            <?php if (!empty($blog['created_at'])): ?>
                | <span style="color:#999;"><?= htmlspecialchars(date('Y-m-d', strtotime($blog['created_at']))) ?></span>
            <?php endif; ?>
        </div>
        <div class="blog-content">
            <?= nl2br(htmlspecialchars($blog['content'] ?? $blog['body'] ?? '')) ?>
        </div>
        <a href="blogs.php" class="btn">رجوع للمدونات</a>

        <hr>
        <div class="section-title" id="comments"><i class="fa fa-comments"></i> التعليقات</div>
        <?php if ($msg): ?>
            <div style="color:green"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($user_id): ?>
            <form method="post" class="comment-box">
                <textarea name="comment" required style="width:96%;height:38px;" placeholder="اكتب تعليقك..."></textarea>
                <button name="add_comment" class="btn">إرسال</button>
            </form>
        <?php else: ?>
            <div style="color:#888;">سجل دخولك لتتمكن من إضافة تعليق.</div>
        <?php endif; ?>

        <?php foreach ($comments as $c): ?>
            <div class="comment-card">
                <b><?= htmlspecialchars($c['user_name'] ?? 'مستخدم') ?>:</b>
                <?= nl2br(htmlspecialchars($c['comment'] ?? $c['content'] ?? '')) ?>
                <div style="color:#aaa;font-size:12px"><?= htmlspecialchars($c['created_at']) ?></div>
                <!-- الردود -->
                <?php if (!empty($c['replies'])): ?>
                    <?php foreach ($c['replies'] as $r): ?>
                        <div class="reply-card">
                            <b><?= htmlspecialchars($r['user_name'] ?? 'مستخدم') ?>:</b>
                            <?= nl2br(htmlspecialchars($r['reply'] ?? $r['comment'] ?? '')) ?>
                            <div style="color:#aaa;font-size:11px"><?= htmlspecialchars($r['created_at']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- نموذج رد -->
                <?php if ($user_id): ?>
                    <form method="post" class="reply-box">
                        <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                        <textarea name="reply" required style="width:92%;height:24px;" placeholder="رد..."></textarea>
                        <button name="add_reply" class="btn">رد</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (empty($comments)): ?>
            <div style="color:#aaa;">لا توجد تعليقات بعد.</div>
        <?php endif; ?>
    </div>
</body>
</html>
