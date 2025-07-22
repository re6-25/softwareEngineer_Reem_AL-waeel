<?php
session_start();
require_once 'classes/Category.php';
require_once 'classes/Book.php';

$catObj  = new Category();
$bookObj = new Book();

$categories = $catObj->all();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تصنيفات الكتب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="assets/css/categories.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'sidebar.php'; ?>
        <div style="flex:1;">
            <div class="content">
                <div class="categories-title"><i class="fa fa-layer-group"></i> كل التصنيفات</div>
                <div class="category-row">
                <?php foreach ($categories as $cat): ?>
                    <div class="category-card">
                        <img src="<?= htmlspecialchars($cat['icon'] ?? 'assets/img/cat.png') ?>" alt="">
                        <div class="category-title"><?= htmlspecialchars($cat['name']) ?></div>
                        <a href="books.php?cat=<?= $cat['id'] ?>" class="category-link">عرض الكتب</a>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>