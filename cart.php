<?php
session_start();
require_once 'classes/Book.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';
$user_id  = $_SESSION['user_id'] ?? null;
$bookObj = new Book();
$userObj = new User();
$orderObj = new Order();


// دعم سلة الزوار
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = $user_id ? [] : ($_SESSION['guest_cart'] ?? []);
}

// نقل السلة من زائر إلى عضو بعد تسجيل الدخول
if ($user_id && isset($_SESSION['guest_cart'])) {
    foreach ($_SESSION['guest_cart'] as $id) {
        if (!in_array($id, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $id;
        }
    }
    unset($_SESSION['guest_cart']);
}

// إضافة كتاب
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    if (!in_array($id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $id;
    }
    if (!$user_id) $_SESSION['guest_cart'] = $_SESSION['cart'];
    header("Location: cart.php");
    exit;
}

// إزالة كتاب
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$id]);
    if (!$user_id) $_SESSION['guest_cart'] = $_SESSION['cart'];
    header("Location: cart.php");
    exit;
}

// تفاصيل السلة
$cartBooks = [];
$total = 0;
if ($_SESSION['cart']) {
    $cartBooks = $bookObj->getByIds($_SESSION['cart']);
    foreach ($cartBooks as $b) $total += $b['price'] ?? 0;
}

$userPoints = $user_id ? $userObj->getPoints($user_id) : 0;
$points_discount = $userPoints >= 10 ? round($total * 0.05, 2) : 0;
$totalAfterPoints = $total - $points_discount;

$coupon_discount = 0;
$coupon_code = '';
if (isset($_POST['coupon'])) {
    $coupon_code = trim($_POST['coupon']);
    if (strtolower($coupon_code) === 'insp10') {
        $coupon_discount = round($totalAfterPoints * 0.10, 2);
    }
}
$totalAfterCoupon = $totalAfterPoints - $coupon_discount;

$shipping = ($totalAfterCoupon > 0) ? 10 : 0;
$finalTotal = $totalAfterCoupon + $shipping;

$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order']) && $cartBooks && $user_id) {
    $orderData = [
        'user_id' => $user_id,
        'books' => json_encode(array_map(fn($b) => $b['id'], $cartBooks)),
        'total' => $total,
        'discount' => $points_discount,
        'coupon' => $coupon_code,
        'coupon_discount' => $coupon_discount,
        'shipping' => $shipping,
        'final' => $finalTotal,
        'note' => trim($_POST['note'] ?? ''),
        'created_at' => date('Y-m-d H:i:s'),
        'address' => trim($_POST['address']),
        'payment_method' => $_POST['payment_method'],
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email'])
    ];
    $orderObj->add($orderData);

    // بريد للإدارة
    @mail("youradmin@email.com", "طلب جديد", "طلب من: $user_id\nجوال: ".$orderData['phone']."\nمبلغ: $finalTotal ريال");

    if ($userPoints >= 10) $userObj->deductPoints($user_id, 10);

    $_SESSION['cart'] = [];
    $success = "تمت عملية الشراء بنجاح!";
}

$suggestedBooks = $bookObj->suggestRandom(4);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>السلة - مكتبة الإلهام الساكن</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap">
    <style>
        body {background: #f4f7fa; font-family: 'Cairo', Arial, sans-serif;}
        .cart-container {max-width: 730px; margin: 50px auto 30px; background:#fff; border-radius:13px; box-shadow:0 2px 18px #a390e41a; padding:32px;}
        .cart-title {font-size: 1.7rem; color:#7157c7; margin-bottom:20px;}
        .cart-list {margin-bottom: 20px;}
        .cart-book {display:flex; align-items:center; margin-bottom:13px; background:#f8f8fa; border-radius:8px;}
        .cart-book img {width:48px; height:70px; border-radius:7px; margin:0 13px;}
        .cart-book-title {flex:1; color:#a390e4; font-weight:bold;}
        .cart-book-price {color:#f9c846; font-weight:bold; margin-left: 15px;}
        .cart-remove {background: #e75e5e; color:#fff; border:none; border-radius:7px; font-size:.9em; padding:6px 13px; cursor:pointer;}
        .cart-total {font-size:1.1em; color:#393e46; margin-bottom: 22px;}
        .cart-pay {background: #a390e4; color: #fff; font-weight:bold; border:none; border-radius:11px; padding:13px 37px; font-size:1.18em; cursor:pointer;}
        .cart-success {background:#def9e3; color:#44a870; border-radius:12px; padding:13px 22px; text-align:center; font-weight:bold;}
        .empty-cart {color:#999; margin:23px 0 50px;}
        .form-label {font-weight:bold; margin-top:9px; display:block;}
        .form-input, .form-select {width:99%;padding:7px 9px;border-radius:8px;border:1.2px solid #e6e1f4;margin-bottom:10px;}
        .suggested-row {display:flex;gap:18px;margin:22px 0 0 0;}
        .suggest-card {background:#fff;box-shadow:0 2px 10px #a390e413;border-radius:9px;padding:12px 8px;width:125px;text-align:center;}
        .suggest-card img{width:60px;height:85px;border-radius:5px;}
        .suggest-title{font-size:.95em;color:#7157c7;font-weight:bold;margin:8px 0;}
        .btn-suggest{background:#f9c846;color:#fff;border:none;border-radius:7px;padding:4px 15px;cursor:pointer;font-size:.97em;}
        .btn-suggest:hover{background:#a390e4;}
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include 'sidebar.php'; ?>
        <div style="flex:1;">
            <div class="cart-container">
                <div class="cart-title"><i class="fa fa-shopping-cart"></i> السلة</div>
                <?php if ($success): ?>
                    <div class="cart-success"><?= $success ?></div>
                <?php elseif (!$cartBooks): ?>
                    <div class="empty-cart">السلة فارغة</div>
                <?php else: ?>
                    <div class="cart-list">
                        <?php foreach ($cartBooks as $book): ?>
                            <div class="cart-book">
                                <img src="<?= htmlspecialchars($book['image'] ?? 'assets/img/book.png') ?>">
                                <div class="cart-book-title"><?= htmlspecialchars($book['title']) ?></div>
                                <div class="cart-book-price"><?= htmlspecialchars($book['price'] ?? 0) ?> ريال</div>
                                <a href="cart.php?remove=<?= $book['id'] ?>" class="cart-remove">حذف</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-total">
                        الإجمالي: <span style="color:#a390e4; font-weight:bold;"><?= $total ?> ريال</span>
                        <?php if ($points_discount): ?>
                            <br><span style="color:#44a870;">خصم النقاط (5%): -<?= number_format($points_discount,2) ?> ريال</span>
                        <?php endif; ?>
                        <?php if ($coupon_discount): ?>
                            <br><span style="color:#268bf7;">خصم القسيمة: -<?= number_format($coupon_discount,2) ?> ريال</span>
                        <?php endif; ?>
                        <br><span style="color:#f9c846;">الشحن: <?= $shipping ?> ريال</span>
                        <br><b>المجموع بعد الخصومات: <?= number_format($finalTotal,2) ?> ريال</b>
                    </div>
                    <!-- فورم كوبون -->
                    <form method="post" style="margin-bottom:10px;">
                        <input type="text" name="coupon" placeholder="كود خصم (إن وجد)" style="width:150px;padding:4px 8px;border-radius:7px;border:1.2px solid #eee;">
                        <button type="submit" class="cart-pay" style="padding:6px 17px;font-size:.98em;">تفعيل القسيمة</button>
                    </form>
                    <!-- فورم تأكيد الشراء -->
                    <form method="post" style="margin-top:24px; background:#faf9fd; border-radius:11px; padding:18px 13px;">
                        <label class="form-label">رقم الجوال:</label>
                        <input type="text" name="phone" class="form-input" required placeholder="مثال: 7XXXXXXXX">
                        <label class="form-label">البريد الإلكتروني:</label>
                        <input type="email" name="email" class="form-input" required placeholder="example@email.com">
                        <label class="form-label">العنوان:</label>
                        <input type="text" name="address" class="form-input" required placeholder="مثال: صنعاء - ش الميل - جوار جامع النور">
                        <label class="form-label">طريقة الدفع:</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">دفع عند الاستلام</option>
                            <option value="kuraimi">دفع عبر الكريمي</option>
                        </select>
                        <label class="form-label">ملاحظات الطلب:</label>
                        <textarea name="note" class="form-input" placeholder="أي تفاصيل إضافية (اختياري)"></textarea>
                        <button type="submit" name="confirm_order" class="cart-pay" style="margin-top:16px;">
                            <i class="fa fa-money-bill-wave"></i> تأكيد الطلب
                        </button>
                    </form>
                    <!-- اقتراح كتب -->
                    <div class="section-title" style="margin-top:30px;">كتب مقترحة</div>
                    <div class="suggested-row">
                        <?php foreach ($suggestedBooks as $sbook): ?>
                            <div class="suggest-card">
                                <img src="<?= htmlspecialchars($sbook['image'] ?? 'assets/img/book.png') ?>">
                                <div class="suggest-title"><?= htmlspecialchars($sbook['title']) ?></div>
                                <form method="get" action="cart.php">
                                    <input type="hidden" name="add" value="<?= $sbook['id'] ?>">
                                    <button type="submit" class="btn-suggest">أضف للسلة</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
