<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/Favorite.php';

$user_id  = $_SESSION['user_id'] ?? null;
$bookObj  = new Book();
$favObj   = new Favorite();
$favorites_ids = [];

// للمسجلين
if ($user_id) {
    $favorites_ids = $favObj->getUserFavorites($user_id);
} else {
    // للزوار
    $favorites_ids = $_SESSION['guest_fav'] ?? [];
}

// جلب بيانات الكتب
$fav_books = [];
foreach ($favorites_ids as $bid) {
    $book = $bookObj->get($bid);
    if ($book) $fav_books[] = $book;
}

// إزالة من المفضلة
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if ($user_id) {
        $favObj->remove($user_id, $remove_id);
    } else {
        $_SESSION['guest_fav'] = array_diff($_SESSION['guest_fav'] ?? [], [$remove_id]);
    }
    header("Location: favorites.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>المفضلة - مكتبة الإلهام الساكن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body {
            background: #f4f7fa;
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
        }

        .content {
            padding: 30px 15px;
            margin-right: 220px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 32px 0 15px 0;
            color: #7157c7;
        }

        .book-row {
            display: flex;
            gap: 23px;
            flex-wrap: wrap;
        }

        .book-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px #a390e413;
            padding: 15px 10px;
            width: 145px;
            text-align: center;
            position: relative;
        }

        .book-card img {
            width: 90px;
            height: 130px;
            border-radius: 7px;
            box-shadow: 0 2px 8px #ccc2f321;
        }

        .book-title {
            font-weight: 600;
            font-size: 1.02rem;
            color: #a390e4;
        }

        .remove-btn {
            display: block;
            margin: 10px auto 0 auto;
            color: #e75e5e;
            background: none;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
        }

        @media (max-width:700px) {
            .content {
                margin-right: 0;
                padding: 10px 2px;
            }

            .book-row {
                gap: 9px;
            }

            .book-card {
                width: 90vw;
                min-width: 140px;
                max-width: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'sidebar.php'; ?>
        <div style="flex:1;">
            <div class="content">
                <div class="section-title"><i class="fa fa-heart"></i> الكتب المفضلة</div>
                <div class="book-row">
                    <?php if ($fav_books): foreach ($fav_books as $b): ?>
                        <div class="book-card">
                            <img src="<?= htmlspecialchars($b['image'] ?? 'assets/img/book.png') ?>" alt="">
                            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                            <a href="book.php?id=<?= $b['id'] ?>" style="color:#7157c7;font-size:.95em;">تفاصيل</a>
                            <a href="?remove=<?= $b['id'] ?>" class="remove-btn" title="إزالة من المفضلة"><i class="fa fa-trash"></i> إزالة</a>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="color:#888;font-size:1.15em;">لا يوجد كتب في المفضلة بعد.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
