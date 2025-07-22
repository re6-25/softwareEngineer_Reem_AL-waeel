<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/User.php';
require_once 'classes/Category.php';
require_once 'classes/Favorite.php';
require_once 'classes/Rating.php';
require_once 'classes/Notification.php';

$bookObj   = new Book();
$userObj   = new User();
$catObj    = new Category();
$favObj    = new Favorite();
$rateObj   = new Rating();
$notifObj  = new Notification();

$user_id   = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;

$user_name = null;
if ($user_id) {
    $user = $userObj->getById($user_id); 
    $user_name = $user['name'] ?? '';
}


$categories = $catObj->all();
$latest     = $bookObj->latestByYear(8, 2025);
$mostRead   = $bookObj->mostDownloaded(8);
$topRated   = $bookObj->topRated(4);
$reviews    = $rateObj->libraryReviews(4);
$favorites  = $user_id ? $favObj->getUserFavorites($user_id) : [];
$notifs     = $user_id ? $notifObj->unread($user_id) : [];
$points     = $user_id ? $userObj->getPoints($user_id) : 0;

$games = [
    ["id" => 1, "title" => "لعبة الذاكرة", "desc" => "اختبر ذاكرتك!", "icon" => "fa-brain", "url" => "play_game.php?g=memory"],
    ["id" => 2, "title" => "سودوكو", "desc" => "سودوكو الكلاسيكية", "icon" => "fa-th", "url" => "play_game.php?g=sudoku"],
];

$services = [
    ["img" => "uploads/img_68514bc435289.jpg", "title" => "خدمة التوصيل", "desc" => "توصيل الكتب خلال 48 ساعة"],
    ["img" => "uploads/img_68514bc435289.jpg", "title" => "الإهداءات", "desc" => "أهدِ كتبًا لأحبّائك"],
    ["img" => "uploads/img_68514bc435289.jpg", "title" => "اشتراك شهري", "desc" => "اقرأ بدون حدود بـ 9.99 فقط"],
];
$poems = [
    'user' => [
        'quote' => 'مرحبًا بك يا {name} في مكتبتك!<br>
        <b>
        وقفتُ ببابِ العلمِ والكتبُ حولي<br>
        وفي رفِّها سرُّ الزمانِ وعِطرُهُ<br>
        ومن يأنسِ الكتبَ لا يعرفُ وحشةً<br>
        ولا يُمسِ ليلًا في الحياةِ مُظلِمُهْ
        </b>',
    ],
    'admin' => [
        'quote' => 'مرحبًا أيها الإداري الحكيم<br>
        <b>
        أأحلامُنا في الكتبِ تُنسَجُ دومًا<br>
        ومن فكرِكمْ تنمو الحياةُ وتزدهرُ<br>
        قُدوتُنا في الحزمِ والعزمِ دائمًا<br>
        فسِرْ، فالنجومُ لطموحِكَ تُنتظَرُ
        </b>',
    ],
    'guest' => [
        'quote' => 'مرحبًا أيها الزائر الكريم<br>
        <b>
        جئتَ للمعرفةِ ضيفًا عزيزًا كريمًا<br>
        وفي رحابِ الكتبِ تشرقُ الأنوارُ<br>
        سلْ المكتبة، تعطيك من سرِّها<br>
        وتحملك الحروفُ إلى سِعةِ الديارِ
        </b>',
    ]
];

if ($user_role === 'admin') {
    $welcome = str_replace('{name}', htmlspecialchars($user_name), $poems['admin']['quote']);
} elseif ($user_name) {
    $welcome = str_replace('{name}', htmlspecialchars($user_name), $poems['user']['quote']);
} else {
    $welcome = $poems['guest']['quote'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>مكتبة الإلهام الساكن</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  
   <link rel="stylesheet" href="assets\css\style.css"/>
</head>

<body class="index">

    <?php include 'sidebar.php'; ?>

    <!-- مكان عرض الشعر الترحيبي في الصفحة الرئيسية -->
    <div style="background:#f3f0fa;padding:16px 8px 12px 8px;border-radius:13px;text-align:center;color:#7157c7;font-size:1.11em;margin-bottom:25px;">
        <?= $welcome ?>
    </div>
    <div class="main-wrapper">
        <div style="flex:1;">
            <div class="content">

                <!-- Slider رئيسي للكتب المميزة -->
                <div class="main-slider">
                    <div class="slide">
                        <img src="uploads/منارات.jpg" alt="منارات">
                    </div>
                    <div class="slide">
                        <img src="uploads/اقتباسات.jpg" alt="اقتباسات">
                    </div>
                    <div class="slide">
                        <!-- عدل مسار الصورة حسب الصورة الصحيحة -->
                        <img src="uploads/our-offers.jpg" alt="عروضنا">
                    </div>
                </div>

                <!-- خدمات مختصرة/مميزات -->
                <div class="section-title"><i class="fa fa-bolt"></i> خدماتنا</div>
                <div class="category-row">
                    <?php foreach ($services as $service): ?>
                        <div class="category-card">
                            <img src="<?= htmlspecialchars($service['img']) ?>" alt="" style="width:50px;height:50px;border-radius:50%;background:#f6f3fe;">
                            <div class="category-title"><?= htmlspecialchars($service['title']) ?></div>
                            <div style="color:#888;font-size:.96em;"><?= htmlspecialchars($service['desc']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- التصنيفات -->
                <div class="section-title"><i class="fa fa-layer-group"></i> التصنيفات</div>
                <div class="category-row">
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-card">
                            <img src="<?= htmlspecialchars($cat['icon'] ?? 'assets/img/cat.png') ?>" alt="">
                            <div class="category-title"><?= htmlspecialchars($cat['name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- أحدث الإصدارات -->
                <div class="section-title"><i class="fa fa-bolt"></i> أحدث الإصدارات</div>
                <div class="book-row">
                    <?php foreach ($latest as $b): ?>
                        <div class="book-card">
                            <?php if (!empty($b['is_featured'])): ?><span class="book-badge">جديد</span><?php endif; ?>
                            <img src="<?= htmlspecialchars($b['image'] ?? 'assets/img/book.png') ?>" alt="">
                            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                            <a href="book.php?id=<?= $b['id'] ?>" class="hero-btn" style="padding:8px 16px;font-size:.98em;">تفاصيل</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- الأكثر قراءة -->
                <div class="section-title"><i class="fa fa-fire"></i> الأكثر قراءة</div>
                <div class="book-row">
                    <?php foreach ($mostRead as $b): ?>
                        <div class="book-card">
                            <img src="<?= htmlspecialchars($b['image'] ?? 'assets/img/book.png') ?>" alt="">
                            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                            <a href="book.php?id=<?= $b['id'] ?>" class="hero-btn" style="padding:8px 16px;font-size:.98em;">تفاصيل</a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- الأعلى تقييماً -->
                <div class="section-title"><i class="fa fa-star"></i> الأعلى تقييماً</div>
                <div class="book-row">
                    <?php foreach ($topRated as $b): ?>
                        <div class="book-card">
                            <img src="<?= htmlspecialchars($b['image'] ?? 'assets/img/book.png') ?>" alt="">
                            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                            <a href="book.php?id=<?= $b['id'] ?>" class="hero-btn" style="padding:8px 16px;font-size:.98em;">تفاصيل</a>
                        </div>
                    <?php endforeach; ?>
                </div>


                <!-- تقييمات وآراء القراء -->
                <div class="section-title"><i class="fa fa-star"></i> تقييمات وآراء القراء</div>
                <?php foreach ($reviews as $r): ?>
                    <div class="review-card">
                        <span><?= str_repeat('<span class="star">&#9733;</span>', $r['rating']) . str_repeat('<span class="star">&#9734;</span>', 5 - $r['rating']); ?></span>
                        <span style="color:#393e46; margin-right:12px"><?= htmlspecialchars($r['review']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-shortcuts">
            <a href="about.php"><i class="fa fa-info-circle"></i> عن المكتبة</a>
            <a href="contact.php"><i class="fa fa-envelope"></i> تواصل معنا</a>
            <?php if ($user_role === 'admin'): ?>
                <a href="admin_panel.php"><i class="fa fa-tools"></i> لوحة الإدارة</a>
            <?php endif; ?>
        </div>
        <div class="footer-contact">
            البريد: <a href="mailto:eng.rim.taher@gmail.com" style="color:#ffd369;">eng.rim.taher@gmail.com</a>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.main-slider').slick({
                slidesToShow: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                rtl: true,
                arrows: false,
                dots: true,
                fade: true
            });
        });
    </script>
</body>
</html>
