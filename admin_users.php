<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin'])) {
    header("Location: login_register.php");
    exit;
}

require_once 'classes/User.php';
$userObj = new User();
$msg = "";

// حذف مستخدم
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($userObj->delete($id)) {
        $msg = "تم حذف المستخدم بنجاح!";
    } else {
        $msg = "حدث خطأ أثناء الحذف!";
    }
}

// تغيير الدور
if (isset($_POST['role_change'])) {
    $id = intval($_POST['id']);
    $role = $_POST['role'];
    if ($userObj->changeRole($id, $role)) {
        $msg = "تم تغيير الدور بنجاح!";
    } else {
        $msg = "حدث خطأ أثناء التغيير!";
    }
}

// جلب المستخدمين
$users = $userObj->all();
function getRoleName($role) {
    return match($role) {
        'admin' => 'أدمن',
        'library_manager' => 'مدير مكتبة',
        'moderator' => 'مشرف',
        'reader' => 'قارئ',
        default => 'مستخدم'
    };
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين</title>
    <link rel="stylesheet" href="assets/css/admin_users.css">
</head>
<body>
<div class="container">
    <h1>إدارة المستخدمين</h1>
    <?php if($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    <table>
        <tr>
            <th>الصورة</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>الدور الحالي</th>
            <th>تغيير الدور</th>
            <th>الإجراءات</th>
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
            <td>
                <?php if(!empty($user['avatar'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" class="avatar">
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= getRoleName($user['role']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <select name="role">
                        <option value="reader" <?= $user['role']=='reader'?'selected':'' ?>>قارئ</option>
                        <option value="moderator" <?= $user['role']=='moderator'?'selected':'' ?>>مشرف</option>
                        <option value="library_manager" <?= $user['role']=='library_manager'?'selected':'' ?>>مدير مكتبة</option>
                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>أدمن</option>
                    </select>
                    <button class="btn" name="role_change">تغيير</button>
                </form>
            </td>
            <td>
                <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('تأكيد حذف المستخدم؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>
