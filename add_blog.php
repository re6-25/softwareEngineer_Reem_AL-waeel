<?php
require_once '../classes/Admin.php';
require_once '../classes/Blog.php';
require_once '../classes/Validator.php';

session_start();
if (!Admin::isLogged()) { header("Location: login.php"); exit; }

$blog = new Blog();
$msg = "";
// إضافة أو تعديل تدوينة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $id = intval($_POST['id'] ?? 0);
    $author_id = $_SESSION['admin_id'];

    if (!$title || !$body) {
        $msg = "يرجى إدخال كل الحقول!";
    } else {
        if ($id) {
            if ($blog->update($id, $title, $body)) $msg = "تم التعديل بنجاح!";
            else $msg = "حدث خطأ أثناء التعديل!";
        } else {
            if ($blog->create($title, $body, $author_id)) $msg = "تم إضافة التدوينة!";
            else $msg = "حدث خطأ أثناء الإضافة!";
        }
    }
}

// حذف تدوينة
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if ($blog->delete($del_id)) $msg = "تم الحذف بنجاح!";
    else $msg = "فشل الحذف!";
}

// جلب جميع التدوينات
$all_blogs = $blog->all();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المدونات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {font-family:'Cairo',Arial,sans-serif;background:#f4f6fa;margin:0;padding:0;}
        .admin-container{max-width:1150px;margin:40px auto 0;background:#fff;border-radius:15px;box-shadow:0 6px 36px #a390e430;padding:32px;}
        h2{color:#a390e4;margin-bottom:18px;}
        .msg{padding:11px 17px;border-radius:11px;font-weight:bold;margin-bottom:20px;}
        .msg{background:#eaf7e4;color:#3a8a39;}
        .msg.error{background:#ffebea;color:#c23c24;}
        table{width:100%;border-collapse:collapse;margin-bottom:30px;}
        th,td{border-bottom:1px solid #eee;padding:12px;text-align:center;}
        th{background:#f7f2fa;}
        tr:hover{background:#f6f6f9;}
        .actions a{margin:0 6px;padding:5px 14px;border-radius:8px;text-decoration:none;}
        .edit{background:#ffd86b;color:#633;}
        .del{background:#ee6c70;color:#fff;}
        form{margin-bottom:25px;}
        .field{margin-bottom:17px;}
        label{font-weight:bold;}
        input[type=text],textarea{width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;background:#f7f7fa;}
        textarea{min-height:72px;}
        .btn{padding:10px 28px;border-radius:10px;border:none;background:#a390e4;color:#fff;font-weight:bold;cursor:pointer;}
        .btn:hover{background:#7157c7;}
    </style>
</head>
<body>
<div class="admin-container">
    <h2>إدارة المدونات</h2>
    <?php if($msg):?><div class="msg"><?=htmlspecialchars($msg)?></div><?php endif;?>
    <!-- نموذج إضافة/تعديل -->
    <form method="post" id="blogForm">
        <input type="hidden" name="id" id="blogId">
        <div class="field">
            <label>عنوان التدوينة:</label>
            <input type="text" name="title" id="title">
        </div>
        <div class="field">
            <label>المحتوى:</label>
            <textarea name="body" id="body"></textarea>
        </div>
        <button type="submit" class="btn">حفظ</button>
    </form>
    <!-- جدول التدوينات -->
    <table>
        <tr>
            <th>#</th>
            <th>العنوان</th>
            <th>المحتوى</th>
            <th>الكاتب</th>
            <th>تاريخ النشر</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach($all_blogs as $b):?>
        <tr>
            <td><?= $b['id']?></td>
            <td><?= htmlspecialchars($b['title'])?></td>
            <td><?= htmlspecialchars(mb_substr($b['body'],0,40))."..."?></td>
            <td><?= htmlspecialchars($b['author_name'])?></td>
            <td><?= $b['created_at']?></td>
            <td class="actions">
                <a href="#" class="edit" data-id="<?= $b['id']?>" data-title="<?=htmlspecialchars($b['title'])?>" data-body="<?=htmlspecialchars($b['body'])?>">تعديل</a>
                <a href="?del=<?=$b['id']?>" class="del" onclick="return confirm('تأكيد الحذف؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('.edit').click(function(e){
    e.preventDefault();
    $('#blogId').val($(this).data('id'));
    $('#title').val($(this).data('title'));
    $('#body').val($(this).data('body'));
    $('html,body').animate({scrollTop:0},350);
});
</script>
</body>
</html>