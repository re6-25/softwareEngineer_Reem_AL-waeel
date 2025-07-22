<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/Comment.php';
require_once 'classes/User.php';
require_once 'classes/Book.php';

$commentObj = new Comment();
$userObj = new User();
$bookObj = new Book();
$msg = "";

// الموافقة على تعليق
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    if ($commentObj->setStatus($id, 'approved')) {
        $msg = "تمت الموافقة على التعليق.";
    } else {
        $msg = "حدث خطأ أثناء الموافقة!";
    }
}

// رفض تعليق
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    if ($commentObj->setStatus($id, 'rejected')) {
        $msg = "تم رفض التعليق.";
    } else {
        $msg = "حدث خطأ أثناء الرفض!";
    }
}

// حذف تعليق
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($commentObj->delete($id)) {
        $msg = "تم حذف التعليق بنجاح!";
    } else {
        $msg = "حدث خطأ أثناء الحذف!";
    }
}

// جلب جميع التعليقات
$comments = $commentObj->all();
$usersArr = [];
foreach($userObj->all() as $u) { $usersArr[$u['id']] = $u['name']; }
$booksArr = [];
foreach($bookObj->all() as $b) { $booksArr[$b['id']] = $b['title']; }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة التعليقات</title>
    <style>
        body { font-family: Tahoma, Arial; background: #f8f7fc; padding: 25px; }
        .container { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 30px #d3c0ef33; max-width: 1000px; margin:auto; }
        h1 { color: #a390e4; }
        table { width:100%; border-collapse:collapse; margin-top:20px;}
        th,td { border:1px solid #ececec; padding:8px 10px; }
        th { background:#f3f0fa; }
        .btn { padding:6px 13px; border-radius:8px; border:none; background:#a390e4; color:#fff; cursor:pointer; margin-right:5px;}
        .btn:hover { background:#7157c7; }
        .btn-danger { background:#c84646; }
        .btn-approve { background:#4CAF50; }
        .btn-reject { background:#f44336; }
        .msg { background: #f9c846; padding:10px 16px; border-radius:11px; color:#333; margin-bottom:15px;}
        .status-approved { color:green; font-weight:bold;}
        .status-rejected { color:#f44336; font-weight:bold;}
        .status-pending  { color:orange; font-weight:bold;}
    </style>
</head>
<body>
<div class="container">
    <h1>إدارة التعليقات</h1>
    <?php if($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    <table>
        <tr>
            <th>المستخدم</th>
            <th>الكتاب</th>
            <th>التعليق</th>
            <th>الحالة</th>
            <th>التاريخ</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach($comments as $c): ?>
        <tr>
            <td><?= htmlspecialchars($usersArr[$c['user_id']] ?? '-') ?></td>
            <td><?= htmlspecialchars($booksArr[$c['book_id']] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['comment']) ?></td>
            <td>
                <?php
                    if($c['status']=='approved')
                        echo '<span class="status-approved">موافق عليه</span>';
                    elseif($c['status']=='rejected')
                        echo '<span class="status-rejected">مرفوض</span>';
                    else
                        echo '<span class="status-pending">بانتظار المراجعة</span>';
                ?>
            </td>
            <td><?= htmlspecialchars($c['created_at']) ?></td>
            <td>
                <?php if($c['status']!='approved'): ?>
                    <a href="?approve=<?= $c['id'] ?>" class="btn btn-approve">موافقة</a>
                <?php endif; ?>
                <?php if($c['status']!='rejected'): ?>
                    <a href="?reject=<?= $c['id'] ?>" class="btn btn-reject">رفض</a>
                <?php endif; ?>
                <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('تأكيد حذف التعليق؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>