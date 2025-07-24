<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/Category.php';
require_once 'classes/Favorite.php';

$bookObj = new Book();
$catObj  = new Category();
$favObj  = new Favorite();
$user_id = $_SESSION['user_id'] ?? null;

$cat_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
$q = trim($_GET['q'] ?? '');

$books = $q ? $bookObj->search($q) : ($cat_id ? $bookObj->byCategory($cat_id) : $bookObj->all());
$favorites = $user_id ? $favObj->getUserFavorites($user_id) : [];
$categories = $catObj->all();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جميع الكتب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="assets/css/books.css">
</head>
<body>
<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>
    <div style="flex:1;">
        <div class="content">
            <div class="books-header">
                <form action="books.php" method="get" style="display:inline;">
                    <input class="search-box" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ابحث عن كتاب...">
                    <button type="submit">بحث</button>
                </form>
                <div class="cat-filter-bar">
                    <a href="books.php" class="cat-filter <?= $cat_id == 0 ? 'active' : '' ?>">الكل</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="books.php?cat=<?= $cat['id'] ?>" class="cat-filter <?= $cat_id == $cat['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="book-row">
                <?php foreach ($books as $b): ?>
                    <div class="book-card">
                        <?php if (!empty($b['is_free'])): ?>
                            <span class="book-badge">مجاني</span>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($b['image'] ?: 'assets/img/book.png') ?>" alt="غلاف الكتاب">
                        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                        <div class="book-btns">
                            <a href="book.php?id=<?= $b['id'] ?>" class="main-btn">تفاصيل</a>
                            <a href="read.php?id=<?= $b['id'] ?>" class="main-btn green">اقرأ الآن</a>
                        </div>
                        <div class="icon-row">
                            <button class="icon-btn yellow" onclick="addToCart(<?= $b['id'] ?>)" title="أضف للسلة">
                                <i class="fa fa-cart-plus"></i>
                            </button>
                            <button class="icon-btn red" title="أضف للمفضلة">
                                <i class="fa<?= in_array($b['id'], $favorites) ? '' : '-regular' ?> fa-heart"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($books)): ?>
                    <div>لا توجد كتب حالياً.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast">تمت الإضافة للسلة!</div>

<script>
function addToCart(bookId) {
    fetch("cart.php?add=" + bookId)
        .then(() => showToast("✅ تمت إضافة الكتاب للسلة!"));
}
function showToast(msg) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}
</script>
</body>
</html>
