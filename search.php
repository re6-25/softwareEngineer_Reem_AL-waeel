<?php
require_once 'classes/Book.php';
$bookObj = new Book();
$q = $_GET['q'] ?? '';
$results = $q ? $bookObj->search($q) : [];
?>

<?php include 'sidebar.php'; ?>

<?php if ($q): ?>
    <h2>نتائج البحث عن: <?= htmlspecialchars($q) ?></h2>
    <div class="books-row">
        <?php foreach ($results as $book): ?>
            <div class="book-card">
                <img src="<?= htmlspecialchars($book['image'] ?? 'assets/img/book-default.png') ?>" alt="">
                <div class="book-title"><?= htmlspecialchars($book['title'] ?? '') ?></div>
                <div class="book-author">المؤلف: <?= htmlspecialchars($book['author'] ?? '') ?></div>
                <div class="book-cat">التصنيف: <?= htmlspecialchars($book['category_name'] ?? '') ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
            <div>لا توجد نتائج مطابقة.</div>
        <?php endif; ?>
    </div>
<?php elseif ($q !== ''): ?>
    <div>يرجى إدخال كلمة بحث.</div>
<?php endif; ?>
