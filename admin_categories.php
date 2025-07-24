<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/Category.php';

$catObj = new Category();
$msg = "";

// إضافة تصنيف
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);

    // رفع صورة جديدة
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'uploads/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $image = $targetFile;
    } else {
        $image = 'assets/default_cat.png'; // صورة افتراضية
    }

    if ($catObj->add($name, $image)) {
        $msg = "تمت إضافة التصنيف بنجاح";
    } else {
        $msg = "حدث خطأ أثناء الإضافة!";
    }
}

// تعديل تصنيف
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);

    // صورة جديدة أو احتفظ بالقديمة
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'upload/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $image = $targetFile;
    } else {
        $image = $_POST['old_image'] ?? 'assets/default_cat.png';
    }

    if ($catObj->edit($id, $name, $image)) {
        $msg = "تم التعديل بنجاح";
    } else {
        $msg = "حدث خطأ أثناء التعديل!";
    }
}

// حذف تصنيف
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($catObj->delete($id)) {
        $msg = "تم الحذف بنجاح";
    } else {
        $msg = "حدث خطأ أثناء الحذف!";
    }
}

// جلب جميع التصنيفات
$categories = $catObj->all();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة التصنيفات</title>
    <style>
        body { font-family: Tahoma, Arial; background: #f8f7fc; padding: 25px; }
        .container { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 30px #d3c0ef33; max-width: 700px; margin:auto; }
        h1 { color: #a390e4; }
        table { width:100%; border-collapse:collapse; margin-top:20px;}
        th,td { border:1px solid #ececec; padding:8px 10px; }
        th { background:#f3f0fa; }
        .btn { padding:6px 13px; border-radius:8px; border:none; background:#a390e4; color:#fff; cursor:pointer; margin-right:5px;}
        .btn:hover { background:#7157c7; }
        .msg { background: #f9c846; padding:10px 16px; border-radius:11px; color:#333; margin-bottom:15px;}
        form { margin-bottom: 16px; }
        img.cat-img { max-width: 40px; border-radius:8px; }
    </style>
</head>
<body>
<div class="container">
    <h1>إدارة التصنيفات</h1>
    <?php if($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <!-- إضافة تصنيف جديد -->
    <h3>إضافة تصنيف جديد</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="اسم التصنيف" required>
        <input type="file" name="image" accept="image/*">
        <button class="btn" type="submit" name="add">إضافة</button>
    </form>

    <!-- جدول التصنيفات -->
    <h3>كل التصنيفات</h3>
    <table>
        <tr>
            <th>الاسم</th>
            <th>صورة</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach($categories as $cat): ?>
        <tr>
            <form method="post" enctype="multipart/form-data" style="display:inline;">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($cat['image'] ?? 'assets/default_cat.png') ?>">
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($cat['image'] ?? 'assets/default_cat.png') ?>" class="cat-img"><br>
                    <input type="file" name="image" accept="image/*" style="width:85px;">
                </td>
                <td>
                    <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" style="width:90px">
                    <button class="btn" name="edit">تعديل</button>
                    <a href="?delete=<?= $cat['id'] ?>" class="btn" style="background:#c84646" onclick="return confirm('تأكيد الحذف؟')">حذف</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>
