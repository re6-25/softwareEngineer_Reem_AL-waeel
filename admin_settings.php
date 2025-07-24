<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/User.php';
require_once 'classes/Upload.php';
$userObj = new User();
$admin = $userObj->get($_SESSION['user_id']);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];
    $avatarPath = null;

    // معالجة رفع الصورة
    if (isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
        $avatarPath = Upload::image($_FILES['avatar']);
    }

    // تحديث الملف الشخصي (اسم، نبذة، صورة)
    if ($name) {
        $userObj->updateProfile($admin['id'], $name, $bio, $avatarPath);
        $msg = "تم تحديث البيانات بنجاح!";
    }

    // تحديث كلمة المرور
    if ($pass1 && $pass1 === $pass2) {
        $userObj->updatePassword($admin['id'], $pass1);
        $msg .= "<br>تم تحديث كلمة المرور!";
    } elseif ($pass1 && $pass1 !== $pass2) {
        $msg .= "<br>كلمتا المرور غير متطابقتان!";
    }

    // تحديث بيانات المستخدم بعد التعديل
    $admin = $userObj->get($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات الأدمن</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body { background: #f4f7fa; font-family: 'Cairo', Arial, sans-serif; min-height: 100vh;}
        .main-wrapper { display: flex; min-height: 100vh; }
        .settings-content {
            background: #fff;
            margin: 50px auto;
            padding: 38px 55px;
            border-radius: 17px;
            box-shadow: 0 2px 18px #a390e420;
            max-width: 520px;
            width: 100%;
        }
         .content {
            flex: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 220px;
        }
        .settings-title { font-size: 1.5em; color: #a390e4; margin-bottom: 22px;}
        .settings-row { margin-bottom: 21px;}
        .settings-label { color: #a390e4; font-weight: bold;}
        .settings-input, .settings-textarea {
            padding: 10px; border-radius: 10px; border: 1px solid #ececec; width: 100%;
            font-size: 1em; background: #f8f7fa;
        }
        .settings-btn {
            background: #a390e4; color: #fff; border: none;
            padding: 10px 30px; border-radius: 13px; font-weight: bold; cursor: pointer;
            margin-top: 18px; font-size: 1.1em;
        }
        .settings-success { color: #44a870; margin-bottom: 17px; font-weight: bold; }
        .avatar-preview { margin-top: 10px; }
        .avatar-preview img { border-radius: 50%; max-width: 80px; border: 2px solid #eee;}
        .center-form { min-height: 80vh; display: flex; align-items: center; justify-content: center;}
        .dash-title { font-size: 2rem; color: #7157c7; font-weight: bold; margin-bottom: 34px; }
        @media (max-width:900px){ .main-wrapper{flex-direction:column;} .settings-content{padding:15px 7px;}}
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'sidebar_admin.php'; ?>
    <div class="content center-form">
        <div class="settings-content">
            <div class="dash-title">إعدادات الأدمن</div>
            <?php if ($msg): ?><div class="settings-success"><?= $msg ?></div><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="settings-row">
                    <label class="settings-label">الاسم:</label>
                    <input class="settings-input" type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
                    </div>
                <div class="settings-row">
                    <label class="settings-label">النبذة:</label>
                    <textarea class="settings-input settings-textarea" name="bio" rows="3"><?= htmlspecialchars($admin['bio'] ?? '') ?></textarea>
                </div>
                <div class="settings-row">
                    <label class="settings-label">الصورة الشخصية:</label>
                    <input class="settings-input" type="file" name="avatar" accept="image/*">
                    <?php if (!empty($admin['avatar'])): ?>
                        <div class="avatar-preview">
                            <img src="<?= $admin['avatar'] ?>" alt="الصورة">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="settings-row">
                    <label class="settings-label">كلمة المرور الجديدة:</label>
                    <input class="settings-input" type="password" name="password">
                </div>
                <div class="settings-row">
                    <label class="settings-label">تأكيد كلمة المرور:</label>
                    <input class="settings-input" type="password" name="password2">
                </div>
                <button type="submit" class="settings-btn"><i class="fa fa-save"></i> حفظ التعديلات</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>