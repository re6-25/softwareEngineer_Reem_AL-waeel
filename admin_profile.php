<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/User.php';
$userObj = new User();
$admin = $userObj->get($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>الملف الشخصي للأدمن</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body {
            background: #f4f7fa;
            font-family: 'Cairo', Arial, sans-serif;
            min-height: 100vh;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .profile-content {
            background: #fff;
            margin: 60px auto;
            padding: 36px 48px;
            border-radius: 17px;
            box-shadow: 0 2px 18px #a390e420;
            max-width: 430px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .avatar-img {
            width: 108px;
            height: 108px;
            border-radius: 50%;
            border: 3px solid #a390e4;
            margin-bottom: 19px;
            background: #f7f7fa;
            object-fit: cover;
        }

        .profile-row {
            margin-bottom: 18px;
            width: 100%;
        }

        .profile-label {
            color: #a390e4;
            font-weight: bold;
            font-size: 1.04em;
        }

        .profile-val {
            color: #393e46;
            font-size: 1.11em;
            margin-top: 2px;
        }

        .profile-bio {
            color: #666;
            font-size: 1em;
            background: #f8f8fa;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .edit-link {
            margin-top: 14px;
            text-align: center;
            display: block;
        }

        .edit-link a {
            color: #a390e4;
            font-weight: bold;
            text-decoration: none;
            background: #f8f7fc;
            padding: 7px 22px;
            border-radius: 10px;
            transition: background .13s, color .13s;
        }

        .edit-link a:hover {
            background: #a390e4;
            color: #fff;
        }

        .dash-title {
            font-size: 2rem;
            color: #7157c7;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        @media (max-width:900px) {
            .main-wrapper {
                flex-direction: column;
            }

            .profile-content {
                padding: 15px 7px;
            }
        }

        .content {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin-right: center;
            width: 100%;
            /* إذا كان الشريط الجانبي يمين الصفحة وثابت */
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'sidebar_admin.php'; ?>
        <div class="content" style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
            <div class="profile-content">
                <div class="dash-title"><i class="fa fa-user-circle"></i> الملف الشخصي</div>
                <img class="avatar-img" src="<?= $admin['avatar'] && file_exists($admin['avatar']) ? $admin['avatar'] : 'assets/img/default-user.png' ?>" alt="الصورة">
                <div class="profile-row">
                    <div class="profile-label">الاسم:</div>
                    <div class="profile-val"><?= htmlspecialchars($admin['name']) ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">البريد الإلكتروني:</div>
                    <div class="profile-val"><?= htmlspecialchars($admin['email']) ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">النبذة:</div>
                    <div class="profile-bio"><?= nl2br(htmlspecialchars($admin['bio'] ?? 'لا توجد نبذة حالياً.')) ?></div>
                </div>
                <div class="edit-link">
                    <a href="admin_settings.php"><i class="fa fa-edit"></i> تعديل البيانات</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>