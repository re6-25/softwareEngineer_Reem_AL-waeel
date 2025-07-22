<?php
session_start();
require_once 'classes/Order.php';
require_once 'classes/Book.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login_register.php");
    exit;
}

$orderObj = new Order();
$bookObj = new Book();
$orders = $orderObj->getByUser($user_id);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلباتي - مكتبة الإلهام الساكن</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .order-box {
        background:#fff;
        padding:18px;
        border-radius:10px;
        box-shadow:0 1px 6px #ccc;
        margin:15px 0;
      }
      .order-header {font-weight:bold;margin-bottom:5px;}
      .order-books li {margin-right:10px;}
      .order-status {font-weight:bold;color:#7157c7;}
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <h2>طلباتي</h2>

    <?php foreach ($orders as $order): ?>
        <div class="order-box">
            <div class="order-header">طلب رقم: <?= $order['id'] ?> - <span class="order-status"><?= htmlspecialchars($order['status']) ?></span></div>
            <div><b>بتاريخ:</b> <?= $order['created_at'] ?></div>
            <div><b>المبلغ النهائي:</b> <?= $order['final'] ?> ريال</div>
            <div><b>نقاط مستخدمة:</b> <?= $order['discount'] ?> نقطة</div>
            <div><b>الكتب:</b>
                <ul class="order-books">
                    <?php
                    $bookIds = [];
                    // دعم الحفظ كـ JSON أو مفصولة بفواصل
                    if ($order['books'][0] === '[') {
                        $bookIds = json_decode($order['books'], true);
                    } else {
                        $bookIds = explode(',', $order['books']);
                    }

                    foreach ($bookIds as $bookId):
                        $book = $bookObj->get(trim($bookId));
                        if ($book):
                    ?>
                        <li><?= htmlspecialchars($book['title']) ?></li>
                    <?php endif; endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($orders)): ?>
        <p style="color:#888;">لا توجد طلبات حتى الآن.</p>
    <?php endif; ?>
</div>
</body>
</html>
