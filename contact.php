<?php
session_start();
require_once 'classes/Message.php';
require_once 'classes/Notification.php';

$user_id  = $_SESSION['user_id'] ?? null;
$name     = $_SESSION['user_name'] ?? 'زائر';
$email    = $_SESSION['user_email'] ?? ($_POST['email'] ?? 'no-reply@example.com');

$msgObj   = new Message();
$notifObj = new Notification();
$msg      = "";

// ✅ حذف داخلي (Ajax) من المستخدم فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $user_id) {
    $delete_id = intval($_POST['delete_id']);
    $msgObj->markUserDelete($delete_id, $user_id);
    exit('OK');
}

// ✅ إرسال الرسالة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content && $email) {
        $msgObj->send($user_id, $name, $email, $content);
        $notifObj->send('admin', "رسالة جديدة من $name ($email)");
        $msg = " سيتم الرد على بريدك الإلكتروني";
    }
}

// ✅ جلب الرسائل والإشعارات فقط للمستخدمين المسجلين
$messages = $user_id ? $msgObj->byUser($user_id) : [];
$notifs   = $user_id ? $notifObj->unread($user_id, 5) : [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الدعم الفني</title>
     <link rel="stylesheet" href="assets/css/contcat.css">
     <style>
        .message {margin-bottom:10px;padding:10px;border-radius:10px;max-width:70%;clear:both;}
        .user {background:#e6e6fa;margin-left:auto;text-align:right;}
        .admin {background:#d1f0d1;margin-right:auto;text-align:left;}
        .meta {font-size:12px;color:#777;margin-top:5px;}
        .notif {background:#fff7cc;padding:8px;border-radius:8px;margin:8px 0;}
        .success {color:green;margin:10px 0;}
     </style>
</head>
<body>
     <?php include 'sidebar.php'; ?>
<div class="container">
    <div class="title">راسل الإدارة</div>

    <?php if ($msg): ?>
        <div class="success"><?= $msg ?></div>
    <?php endif; ?>

    <?php foreach ($notifs as $n): ?>
        <div class="notif">🔔 <?= htmlspecialchars($n['message']) ?></div>
    <?php endforeach; ?>

    <form method="post">
        <?php if (!$user_id): ?>
            <input type="email" name="email" placeholder="بريدك الإلكتروني" required style="width:100%;margin-bottom:8px;padding:8px;border-radius:6px;">
            <small style="color:#888">سيتم الرد على بريدك الإلكتروني</small>
        <?php endif; ?>
        <textarea name="content" placeholder="اكتب رسالتك هنا..." required></textarea>
        <button type="submit">إرسال</button>
    </form>

    <?php if ($user_id): ?>
    <hr style="margin:20px 0;">
    <div class="title" style="font-size:16px;">رسائلك السابقة</div>
    <?php foreach ($messages as $m): ?>
        <div class="message <?= (isset($m['sender']) && $m['sender'] === 'admin') ? 'admin' : 'user' ?>" id="msg-<?= $m['id'] ?>">
            <b><?= (isset($m['sender']) && $m['sender'] === 'admin') ? 'رد الإدارة:' : 'أنت:' ?></b><br>
            <?= htmlspecialchars($m['content']) ?>
            <div class="meta">
                <?= htmlspecialchars($m['created_at']) ?>
                <?php if ($m['sender'] !== 'admin'): ?>
                    | <a class="delete-link" data-id="<?= $m['id'] ?>">🗑 حذف</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.delete-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.dataset.id;
        if (confirm('هل تريد حذف هذه الرسالة؟')) {
            fetch("", {
                method: "POST",
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'delete_id=' + id
            })
            .then(res => res.text())
            .then(resp => {
                if (resp.trim() === 'OK') {
                    document.getElementById('msg-' + id).remove();
                } else {
                    alert('فشل الحذف!');
                }
            });
        }
    });
});
</script>
</body>
</html>
