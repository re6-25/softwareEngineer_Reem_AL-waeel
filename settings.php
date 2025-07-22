<?php
session_start();
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$userObj = new User();
$user = $userObj->getById($_SESSION['user_id']);
$msg = "";

// حفظ التعديلات
if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // رفع صورة
    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $target = 'uploads/';
        if (!is_dir($target)) mkdir($target);
        $nameImg = time().'_'.basename($_FILES['avatar']['name']);
        $path = $target . preg_replace('/[^\w\.-]/', '', $nameImg);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $path);
        $avatar = $path;
    }

    // تغيير كلمة السر إذا تم إدخالها
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($userObj->updateProfile($_SESSION['user_id'], $name, $email, $avatar, $password)) {
        $msg = "✅ تم تحديث البيانات بنجاح";
        $user = $userObj->getById($_SESSION['user_id']); // تحديث العرض
    } else {
        $msg = "❌ حدث خطأ أثناء الحفظ";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات الحساب</title>
    <style>
        body { font-family: Tahoma; background: #f9f9fc; padding: 30px; }
        .settings-box { max-width: 500px; margin: auto; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 0 15px #ccc; }
        h2 { color: #5d3fd3; text-align: center; }
        input, label { display: block; width: 100%; margin-bottom: 12px; font-size: 0.95rem; }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
            padding: 8px; border: 1px solid #ccc; border-radius: 6px;
        }
        .btn { background: #5d3fd3; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; width: 100%; }
        .btn:hover { background: #432cc0; }
        .alert { background: #f9c846; color: #000; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="settings-box">
        <h2>إعدادات الحساب</h2>
        <?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>الاسم:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>البريد الإلكتروني:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>صورة شخصية جديدة (اختياري):</label>
            <input type="file" name="avatar" accept="image/*">

            <label>كلمة مرور جديدة (اختياري):</label>
            <input type="password" name="password" placeholder="اتركها فارغة إذا لا تريد تغييرها">

            <button class="btn" name="save">💾 حفظ التغييرات</button>
        </form>
    </div>
</body>
</html>
