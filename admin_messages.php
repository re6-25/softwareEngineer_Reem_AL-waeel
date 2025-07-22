<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}

require_once 'classes/Message.php';
require_once 'classes/User.php';

$msgObj = new Message();
$userObj = new User();
$msg = "";

// إخفاء
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $msg = $msgObj->markAdminDelete($id) ? "تم إخفاء الرسالة." : "فشل الحذف.";
}

// استرجاع
if (isset($_GET['restore'])) {
    $id = intval($_GET['restore']);
    $msg = $msgObj->unmarkAdminDelete($id) ? "تم الاسترجاع." : "فشل الاسترجاع.";
}

// الرد
if (isset($_POST['reply_send'])) {
    $id = intval($_POST['msg_id']);
    $reply = trim($_POST['reply']);

    if ($reply) {
        $msgObj->setReply($id, $reply);
        $original = $msgObj->getById($id);
        if ($original && $original['user_id']) {
            $msgObj->replyAsAdmin($original['user_id'], $reply);
        }
        $msg = "تم إرسال الرد.";
    }
}

// الرسائل
$showDeleted = isset($_GET['show']) && $_GET['show'] === 'deleted';
$messages = $showDeleted ? $msgObj->getDeletedByAdmin() : $msgObj->forAdmin();

$users = [];
foreach ($userObj->all() as $u) {
    $users[$u['id']] = $u['name'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الرسائل</title>
    <style>
        body { font-family: Tahoma; background: #f8f7fc; padding: 25px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 0 15px #ccc; }
        .msg-box { border:1px solid #ccc; padding:15px; margin-bottom:18px; border-radius:10px; background: #fdfdfd; }
        .msg-box:hover { background: #fefefe; box-shadow: 0 0 5px #e3e3e3; }
        .admin-reply { background: #fdf1da; color: #a06c00; padding:10px; border-radius:10px; margin-top:10px; }
        .status { margin-top: 8px; font-weight: bold; display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; }
        .status.replied { background: #d4edda; color: #155724; }
        .status.pending { background: #fff3cd; color: #856404; }
        textarea { width: 100%; height: 50px; padding: 8px; margin-top: 5px; border-radius:8px; border:1px solid #ccc; transition: border-color 0.2s ease; }
        textarea:focus { border-color: #6c63ff; }
        .btn { padding: 6px 15px; border: none; border-radius: 6px; background: #7157c7; color: #fff; cursor: pointer; margin-top:5px; font-size: 0.9rem; }
        .btn:hover { background: #5146c4; }
        .btn-danger { background: #e74c3c; }
        .btn-secondary { background: #999; }
        .meta { font-size: 12px; color: #999; margin-top: 5px; }
        h2 { color: #7157c7; }
        .alert { background: #f9c846; padding:10px; border-radius:10px; margin-bottom:15px; color: #000; }
        .toggle-link { margin-bottom:20px; display:inline-block; }
    </style>
</head>

<body>
<div class="container">
    <h2>إدارة رسائل المستخدمين</h2>

    <?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>

    <a class="toggle-link btn btn-secondary" href="?show=<?= $showDeleted ? 'active' : 'deleted' ?>">
        <?= $showDeleted ? '⬅ الرجوع للرسائل الحالية' : '📁 عرض الرسائل المخفية' ?>
    </a>

    <?php foreach ($messages as $m): ?>
        <div class="msg-box">
            <b>من:</b> <?= $users[$m['user_id']] ?? $m['name'] ?> (<?= $m['email'] ?>)<br>
            <b>الرسالة:</b> <?= nl2br(htmlspecialchars($m['content'])) ?>
            <div class="meta"><?= $m['created_at'] ?></div>

            <?php if ($m['reply']): ?>
                <div class="admin-reply">
                    <b>رد الإدارة:</b> <?= nl2br(htmlspecialchars($m['reply'])) ?><br>
                    <small><?= $m['reply_at'] ?></small>
                </div>
                <span class="status replied">✔ تم الرد</span>
            <?php elseif (!$showDeleted): ?>
                <span class="status pending">⌛ بانتظار الرد</span>
                <form method="post">
                    <input type="hidden" name="msg_id" value="<?= $m['id'] ?>">
                    <textarea name="reply" required placeholder="اكتب الرد هنا..."></textarea>
                    <button class="btn" name="reply_send">رد</button>
                </form>
            <?php endif; ?>

            <br>
            <?php if ($showDeleted): ?>
                <a href="?restore=<?= $m['id'] ?>" class="btn">🔄 استرجاع</a>
            <?php else: ?>
                <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger" onclick="return confirm('هل تريد إخفاء الرسالة؟')">🗑 إخفاء</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
<?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>
