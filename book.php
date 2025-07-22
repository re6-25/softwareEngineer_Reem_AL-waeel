<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/Favorite.php';
require_once 'classes/Rating.php';
require_once 'classes/Comment.php';

$bookObj = new Book();
$favObj  = new Favorite();
$rateObj = new Rating();
$commentObj = new Comment();

$user_id = $_SESSION['user_id'] ?? null;
$book_id = intval($_GET['id'] ?? 0);
$book = $bookObj->find($book_id);

if (!$book) {
    die('<div style="padding:30px;color:#a00;">الكتاب غير موجود.</div>');
}

$is_fav = $user_id ? $favObj->isFavorite($user_id, $book_id) : false;
$reviews = $rateObj->getByBook($book_id);
$comments = $commentObj->getByBook($book_id);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> - مكتبة الإلهام الساكن</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap">
    <link rel="stylesheet" href="assets/css/book-details.css">
    <style>
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #323232;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            opacity: 0;
            transform: translateY(30px);
            transition: 0.4s ease;
            z-index: 9999;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .fav-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="container">
        <div class="book-header">
            <img src="<?= htmlspecialchars($book['image'] ?? 'assets/img/book.png') ?>" class="book-cover" alt="">
            <div class="book-info">
                <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                <div class="book-meta">
                    المؤلف: <?= htmlspecialchars($book['author'] ?? '-') ?>
                    <span class="badge"><?= htmlspecialchars($book['category'] ?? '-') ?></span>
                </div>
                <div class="book-meta">
                    الصفحات: <?= htmlspecialchars($book['pages'] ?? '-') ?> |
                    السعر: <?= htmlspecialchars($book['price'] ?? '-') ?> ريال
                    <?php if (!empty($book['is_featured'])): ?>
                        <span class="badge" style="background:#7157c7;">مميز</span>
                    <?php endif; ?>
                    <?php if (!empty($book['is_free'])): ?>
                        <span class="badge" style="background:#1dca6c;">مجاني</span>
                    <?php endif; ?>
                </div>
                <div class="book-meta">
                    تاريخ الإصدار: <?= htmlspecialchars($book['published_at'] ?? '-') ?>
                </div>
                <?php if ($user_id): ?>
                    <button class="add-cart" onclick="addToCart(<?= $book_id ?>)">🛒 أضف إلى السلة</button>
                    <a href="read.php?id=<?= $book_id ?>" style="display:inline-block;color:#44a870;">اقرأ الآن</a>
                    <button class="fav-btn" id="favBtn" onclick="toggleFav(<?= $book_id ?>)" title="مفضلة">
                        <?= $is_fav ? '❤' : '🤍' ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="desc"><?= nl2br(htmlspecialchars($book['description'] ?? 'لا يوجد وصف.')) ?></div>

        <div class="section-title"><i class="fa fa-star"></i> آراء المستخدمين</div>
        <?php if ($reviews && count($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <span><?= str_repeat('<span class="star">&#9733;</span>', $r['stars']) . str_repeat('<span class="star">&#9734;</span>', 5 - $r['stars']); ?></span>
                    <span style="margin-right:11px"><?= htmlspecialchars($r['review'] ?? '') ?></span>
                    <div style="color:#aaa;font-size:12px"><?= htmlspecialchars($r['created_at'] ?? '') ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:#888;font-size:1em;">لا توجد تقييمات بعد.</div>
        <?php endif; ?>

        <div class="section-title" id="comments"><i class="fa fa-comments"></i> التعليقات</div>
        <?php if ($user_id): ?>
        <form method="post" class="comment-box">
            <textarea name="comment" required style="width:96%;height:40px;" placeholder="اكتب تعليقك..."></textarea>
            <button name="add_comment">إرسال</button>
        </form>
        <?php endif; ?>

        <?php foreach ($comments as $c): ?>
            <div class="comment-card">
                <b><?= htmlspecialchars($c['user_name'] ?? 'مستخدم') ?>:</b>
                <?= nl2br(htmlspecialchars($c['comment'])) ?>
                <div style="color:#aaa;font-size:12px"><?= htmlspecialchars($c['created_at']) ?></div>
                <?php if (!empty($c['replies'])): ?>
                    <?php foreach ($c['replies'] as $r): ?>
                        <div class="reply-card">
                            <b><?= htmlspecialchars($r['user_name'] ?? 'مستخدم') ?>:</b>
                            <?= nl2br(htmlspecialchars($r['reply'])) ?>
                            <div style="color:#aaa;font-size:11px"><?= htmlspecialchars($r['created_at']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($user_id): ?>
                <form method="post" class="reply-box">
                    <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                    <textarea name="reply" required style="width:92%;height:24px;" placeholder="رد..."></textarea>
                    <button name="add_reply">رد</button>
                </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="toast" class="toast"></div>

    <script>
    function showToast(msg) {
        const toast = document.getElementById("toast");
        toast.textContent = msg;
        toast.classList.add("show");
        setTimeout(() => toast.classList.remove("show"), 3000);
    }

    function addToCart(bookId) {
        fetch("api/add_to_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "book_id=" + bookId
        }).then(() => showToast("✅ تمت إضافة الكتاب للسلة!"));
    }

    function toggleFav(bookId) {
        fetch("api/toggle_fav.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "book_id=" + bookId
        })
        .then(res => res.text())
        .then(icon => {
            document.getElementById("favBtn").innerText = icon;
            showToast("❤️ تم تعديل المفضلة!");
        });
    }
    </script>
</body>
</html>
