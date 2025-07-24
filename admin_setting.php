<?php
session_start();
//require_once '../classes/Setting.php';

if (!($_SESSION['is_admin'] ?? false)) {
    header("Location: login_register.php");
    exit;
}
$settingObj = new Setting();
$msg = "";

// تحديث الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach(['site_name','site_email','site_phone','site_desc'] as $field) {
        $settingObj->set($field, trim($_POST[$field]));
    }
    $msg = "تم حفظ الإعدادات بنجاح!";
}

$settings = $settingObj->all();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إعدادات الموقع - لوحة تحكم الأدمن</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {font-family:'Cairo',Arial,sans-serif;margin:0;background:#f8f7fc;}
    .container {max-width:700px;margin:38px auto 0 auto;padding:18px;}
    h2 {color:#a390e4;}
    .msg-success {background:#def9e3;color:#44a870;border-radius:12px;padding:10px 15px;margin-bottom:20px;text-align:center;font-size:17px;}
    form {background:#fff;padding:32px;border-radius:16px;box-shadow:0 1px 16px #bbb1eb22;}
    label {font-weight:bold;margin-bottom:5px;display:block;}
    input[type=text],input[type=email],textarea {width:100%;padding:12px;border-radius:11px;border:1px solid #ececec;font-size:1.08rem;margin-bottom:18px;}
    textarea {min-height:60px;}
    .btn {background:#a390e4;color:#fff;padding:13px 36px;border-radius:11px;font-weight:bold;border:none;cursor:pointer;}
    .btn:hover {background:#7157c7;}
    @media(max-width:700px){.container{padding:4px;}}
  </style>
</head>
<body>
<div class="container">
    <h2>إعدادات الموقع</h2>
    <?php if($msg): ?><div class="msg-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post">
        <label>اسم الموقع:</label>
        <input type="text" name="site_name" required value="<?= htmlspecialchars($settings['site_name'] ?? 'مكتبة الإلهام الساكن') ?>">
        <label>البريد الإلكتروني الرسمي:</label>
        <input type="email" name="site_email" required value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
        <label>رقم الهاتف:</label>
        <input type="text" name="site_phone" value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>">
        <label>وصف الموقع/رسالة ترحيب:</label>
        <textarea name="site_desc"><?= htmlspecialchars($settings['site_desc'] ?? '') ?></textarea>
        <button class="btn" type="submit">حفظ الإعدادات</button>
    </form>
    
    <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>