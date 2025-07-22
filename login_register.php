<?php
session_start();
require_once 'classes/User.php';
require_once 'classes/Upload.php';

$user = new User();
$msg = '';
$tab = 'login';

// استقبال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tab = $_POST['mode'] ?? 'login';

  // --- التسجيل ---
  if ($tab === 'register') {
    $name = trim($_POST['reg_name'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $password2 = $_POST['reg_password2'] ?? '';
    $birthday = $_POST['reg_birthday'] ?? null;
    $avatar_path = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
      $avatar_path = Upload::image($_FILES['avatar']);
    }
    if (!$name || !$email || !$password || !$password2 || !$birthday) {
      $msg = "يرجى تعبئة جميع الحقول!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $msg = "البريد الإلكتروني غير صحيح!";
    } elseif ($password !== $password2) {
      $msg = "كلمتا المرور غير متطابقتان!";
    } elseif ($user->emailExists($email)) {
      $msg = "البريد الإلكتروني مستخدم مسبقاً!";
    } else {
      // مرر الميلاد لدالة التسجيل
      if ($user->register($name, $email, $password, $avatar_path, $birthday)) {
        $msg = "تم إنشاء الحساب بنجاح! سجل دخولك الآن.";
        $tab = "login";
      } else {
        $msg = "حدث خطأ أثناء التسجيل!";
      }
    }
  }
  // --- تسجيل الدخول ---
  else {
    $email = trim($_POST['log_email'] ?? '');
    $password = $_POST['log_password'] ?? '';
    $login = $user->login($email, $password);
    if ($login) {
      $_SESSION['user_id'] = $login['id'];
      $_SESSION['user_name'] = $login['name'];
      $_SESSION['user_avatar'] = $login['avatar'] ?? '';
      $_SESSION['user_role']   = $login['role'] ?? 'user';
      if ($_SESSION['user_role'] == 'admin') {
        header("refresh:2;url=dashboard_admin.php");
        exit;
      } else {
        header("Location: index.php");
        exit;
      }
    } else {
      $msg = "البريد الإلكتروني أو كلمة المرور غير صحيحة!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول / التسجيل - مكتبة الإلهام الساكن</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="container-auth">
    <!-- يمين/يسار: صورة -->
    <div class="auth-img">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="صورة تسجيل/دخول">
    </div>
    <!-- يسار/يمين: الفورم -->
    <div class="auth-forms">
      <div class="tabs-switch">
        <button class="tab-btn <?= $tab == 'login' ? 'active' : '' ?>" data-tab="login">تسجيل دخول</button>
        <button class="tab-btn <?= $tab == 'register' ? 'active' : '' ?>" data-tab="register">تسجيل حساب</button>
      </div>
      <?php if ($msg): ?>
        <div class="msg-error"><?= $msg; ?></div>
      <?php endif; ?>
      <!-- فورم تسجيل الدخول -->
      <form id="loginForm" class="<?= $tab == 'login' ? 'active' : '' ?>" method="post" autocomplete="off">
        <input type="hidden" name="mode" value="login">
        <div class="field">
          <label>البريد الإلكتروني</label>
          <input type="email" name="log_email" required>
        </div>
        <div class="field">
          <label>كلمة المرور</label>
          <input type="password" name="log_password" required>
        </div>
        <button class="submit-btn" type="submit">دخول</button>
        <a href="index.php" class="browse-btn">دخول كزائر (تصفح فقط)</a>
      </form>
      <!-- فورم التسجيل -->
      <form id="registerForm" class="<?= $tab == 'register' ? 'active' : '' ?>" method="post" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="register">
        <div class="field">
          <label>الاسم الكامل</label>
          <input type="text" name="reg_name" required>
        </div>
        <div class="field">
          <label>البريد الإلكتروني</label>
          <input type="email" name="reg_email" required>
        </div>
        <div class="field">
          <label>كلمة المرور</label>
          <input type="password" name="reg_password" required>
        </div>
        <div class="field">
          <label>تأكيد كلمة المرور</label>
          <input type="password" name="reg_password2" required>
        </div>
        <div class="field">
          <label>صورة شخصية (اختياري)</label>
          <input type="file" name="avatar" accept="image/*">
        </div>
        <div class="field">
          <label>تاريخ الميلاد</label>
          <input type="date" name="reg_birthday" required>
        </div>
        <button class="submit-btn" type="submit">تسجيل</button>
        <a href="index.php" class="browse-btn">دخول كزائر (تصفح فقط)</a>
      </form>
    </div>
  </div>
  <script src="assets/js/login.js"></script>
</body>
</html>
