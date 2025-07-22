<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/Blog.php';
require_once 'classes/User.php';

$blogObj = new Blog();
$userObj = new User();
$msg = "";

$current_admin_id = $_SESSION['user_id']; // معرف الأدمن الحالي

$forbiddenWords = ['تفوو', 'قذر', 'حمار', 'كلب', '****', 'سيئ', 'زبالة']; 

// 🔹 إضافة مدونة
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $found = false;
    foreach ($forbiddenWords as $bad) {
        if (stripos($title, $bad) !== false || stripos($content, $bad) !== false) {
            $found = true;
            break;
        }
    }

    if ($found) {
        $msg = "❌ لا يمكن نشر المدونة: تحتوي على كلمات غير لائقة.";
    } else {
        $user_id = $current_admin_id;
        if ($blogObj->add($user_id, $title, $content)) {
            $msg = "✅ تمت إضافة المدونة بنجاح.";
        } else {
            $msg = "❌ حدث خطأ أثناء الإضافة!";
        }
    }
}

// 🔹 تعديل مدونة
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $found = false;
    foreach ($forbiddenWords as $bad) {
        if (stripos($title, $bad) !== false || stripos($content, $bad) !== false) {
            $found = true;
            break;
        }
    }

    if ($found) {
        $msg = "❌ لا يمكن تعديل المدونة: تحتوي على كلمات غير لائقة.";
    } else {
        $blog = $blogObj->get($id);
        if ($blog && $blog['user_id'] == $current_admin_id) {
            if ($blogObj->update($id, $title, $content)) {
                $msg = "✅ تم التعديل بنجاح.";
            } else {
                $msg = "❌ حدث خطأ أثناء التعديل!";
            }
        } else {
            $msg = "🚫 غير مسموح لك بتعديل هذه المدونة!";
        }
    }
}

// 🔹 حذف مدونة
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($blogObj->delete($id)) {
        $msg = "🗑️ تم الحذف بنجاح.";
    } else {
        $msg = "❌ حدث خطأ أثناء الحذف!";
    }
}

// 🔹 جلب البيانات
$blogs = $blogObj->all();
$usersArr = [];
foreach($userObj->all() as $u) { $usersArr[$u['id']] = $u['name']; }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المدونات</title>
    <style>
        body { font-family: Tahoma, Arial; background: #f8f7fc; padding: 25px; }
        .container { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 30px #d3c0ef33; max-width: 900px; margin:auto; }
        h1, h3 { color: #a390e4; }
        table { width:100%; border-collapse:collapse; margin-top:20px; font-size:0.95rem; }
        th,td { border:1px solid #ececec; padding:8px 10px; text-align:center; vertical-align:middle; }
        th { background:#f3f0fa; }
        .btn { padding:6px 13px; border-radius:8px; border:none; background:#a390e4; color:#fff; cursor:pointer; margin:2px;}
        .btn:hover { background:#7157c7; }
        .btn-danger { background:#c84646; }
        .msg { background: #f9c846; padding:10px 16px; border-radius:11px; color:#333; margin-bottom:15px; text-align:center; }
        form { margin-bottom: 16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-start; }
        textarea { width: 210px; height: 50px; border-radius:6px; padding:6px; border:1px solid #ddd; }
        input[type="text"] { padding:6px; border-radius:6px; border:1px solid #ccc; width:200px; }
    </style>
</head>
<body>
<div class="container">
    <h1>إدارة المدونات</h1>
    <?php if($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <!-- ✅ إضافة -->
    <h3>إضافة مدونة جديدة</h3>
    <form method="post">
        <input type="text" name="title" placeholder="العنوان" required>
        <textarea name="content" placeholder="المحتوى" required></textarea>
        <button class="btn" type="submit" name="add">➕ إضافة</button>
    </form>

    <!-- ✅ عرض -->
    <h3>كل المدونات</h3>
    <table>
        <tr>
            <th>العنوان</th>
            <th>المحتوى</th>
            <th>الكاتب</th>
            <th>التاريخ</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach($blogs as $blog): ?>
        <tr>
            <td><?= htmlspecialchars($blog['title']) ?></td>
            <td><?= htmlspecialchars(mb_substr($blog['content'],0,50)) ?>...</td>
            <td><?= htmlspecialchars($usersArr[$blog['user_id']] ?? '؟') ?></td>
            <td><?= htmlspecialchars($blog['created_at']) ?></td>
            <td>
                <?php if ($blog['user_id'] == $current_admin_id): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" style="width:90px">
                        <textarea name="content" style="width:120px"><?= htmlspecialchars($blog['content']) ?></textarea>
                        <button class="btn" name="edit">✏️ تعديل</button>
                    </form>
                <?php endif; ?>
                <a href="?delete=<?= $blog['id'] ?>" class="btn btn-danger" onclick="return confirm('تأكيد الحذف؟')">🗑 حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>
