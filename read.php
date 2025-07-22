<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/User.php';
require_once 'classes/Comment.php';

$bookObj = new Book();
$userObj = new User();
$commentObj = new Comment();

$user_id = $_SESSION['user_id'] ?? null;
$role    = $_SESSION['user_role'] ?? '';
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book    = $bookObj->find($book_id);

if (!$book) {
    echo "<h2>الكتاب غير موجود.</h2>";
    exit;
}
// جلب التعليقات والردود
$comments = $commentObj->getByBook($book_id);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> | قراءة كتاب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/read.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
  <?php include 'sidebar.php'; ?>
<body>
<div class="container">
    <?php if (!empty($book['image'])): ?>
        <img src="<?= htmlspecialchars($book['image']) ?>" style="max-width:150px;display:block;margin:0 auto 18px auto;border-radius:8px;">
    <?php endif; ?>
    <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
    <div class="book-author"><b>المؤلف:</b> <?= htmlspecialchars($book['author']) ?></div>
    <?php if (!empty($book['published_at'])): ?>
        <div class="book-meta"><b>تاريخ الإصدار:</b> <?= htmlspecialchars($book['published_at']) ?></div>
    <?php endif; ?>
    <?php if (!empty($book['pages'])): ?>
        <div class="book-meta"><b>عدد الصفحات:</b> <?= htmlspecialchars($book['pages']) ?></div>
    <?php endif; ?>
    <?php if (!empty($book['price'])): ?>
        <div class="book-meta"><b>السعر:</b> <?= htmlspecialchars($book['price']) ?> ريال</div>
    <?php endif; ?>
    <div class="book-desc"><?= nl2br(htmlspecialchars($book['description'])) ?></div>

    <!-- عرض PDF -->
    <?php if (!empty($book['pdf'])): ?>
        <div class="pdf-viewer">
            <h3>قراءة الكتاب PDF</h3>
            <iframe src="<?= htmlspecialchars($book['pdf']) ?>" width="100%" height="600" style="border-radius: 12px; border:1.5px solid #a390e4;"></iframe>
        </div>
    <?php endif; ?>

    <!-- قسم التعليقات والردود -->
    <div class="comments-block">
        <h3>آراء القراء</h3>
        <?php if ($user_id): ?>
            <form id="add-comment-form" class="add-comment-form" style="margin-top:15px;">
                <textarea name="comment" required placeholder="اكتب تعليقك..." style="width:100%;padding:10px;border-radius:8px;"></textarea>
                <button type="submit" class="btn" style="margin-top:7px;">إرسال التعليق</button>
            </form>
            <div id="add-comment-msg"></div>
        <?php else: ?>
            <div style="margin-top:15px;color:#888;">سجّل الدخول لتستطيع إضافة تعليق.</div>
        <?php endif; ?>
        <div id="comments-list">
        <?php if ($comments): foreach ($comments as $c): ?>
            <div class="comment-card">
                <span class="user-name"><?= htmlspecialchars($c['user_name'] ?? "مستخدم") ?>:</span>
                <span><?= nl2br(htmlspecialchars($c['comment'])) ?></span>
                <?php if (!empty($c['replies'])): ?>
                    <?php foreach ($c['replies'] as $reply): ?>
                        <div class="reply-card">
                            <b><?= htmlspecialchars($reply['user_name'] ?? "مستخدم") ?>:</b>
                            <?= nl2br(htmlspecialchars($reply['reply'])) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; else: ?>
            <div style="color:#a390e4;">لا توجد تعليقات بعد.</div>
        <?php endif; ?>
        </div>
    </div>
      <script src="assets/js/read.js"></script>
</div>
</body>
</html>
