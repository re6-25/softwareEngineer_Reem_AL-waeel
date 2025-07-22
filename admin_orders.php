<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}

require_once 'classes/Order.php';

$orderObj = new Order();
$msg = "";

if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];
    if ($orderObj->updateStatus($id, $status)) {
        $msg = "تم تحديث حالة الطلب بنجاح";
    } else {
        $msg = "حدث خطأ أثناء تحديث الحالة";
    }
}

if (isset($_POST['add_notes'])) {
    $id = intval($_POST['order_id']);
    $notes = trim($_POST['notes'] ?? ''); // ✅ تعديل هنا
    if ($orderObj->addNotes($id, $notes)) {
        $msg = "تم إضافة الملاحظات بنجاح";
    } else {
        $msg = "حدث خطأ أثناء إضافة الملاحظات";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($orderObj->delete($id)) {
        $msg = "تم حذف الطلب بنجاح";
    } else {
        $msg = "حدث خطأ أثناء حذف الطلب";
    }
}

$orders = $orderObj->all();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>إدارة الطلبات</title>
<style>
    body { font-family: Tahoma, Arial; background: #f8f7fc; padding: 25px; }
    .container { max-width: 1000px; margin:auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 15px #ccc; }
    h1 { color: #7157c7; margin-bottom: 20px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background: #f3f0fa; }
    select, textarea { width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #ccc; }
    textarea { resize: vertical; height: 60px; }
    .btn { padding: 6px 15px; border-radius: 6px; border: none; background: #7157c7; color: white; cursor: pointer; }
    .btn-danger { background: #c84646; }
    .msg { background: #f9c846; padding: 10px 15px; border-radius: 10px; color: #333; margin-bottom: 15px; }
    form { margin: 0; }
</style>
</head>
<body>
<div class="container">
    <h1>إدارة الطلبات</h1>
    <?php if ($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <table>
        <tr>
            <th>رقم الطلب</th>
            <th>اسم العميل</th>
            <th>تاريخ الطلب</th>
            <th>الحالة</th>
            <th>الملاحظات</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td><?= $order['created_at'] ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>قيد المعالجة</option>
                        <option value="shipping" <?= $order['status'] == 'shipping' ? 'selected' : '' ?>>قيد التوصيل</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>تم التوصيل</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>ملغى</option>
                    </select>
                    <button type="submit" name="update_status" style="display:none;">حفظ</button>
                </form>
            </td>
            <td>
                <form method="post" style="display:flex; flex-direction: column; gap: 6px;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <textarea name="notes" placeholder="أدخل ملاحظات..."><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                    <button class="btn" type="submit" name="add_notes">حفظ الملاحظات</button>
                </form>
            </td>
            <td>
                <a href="?delete=<?= $order['id'] ?>" class="btn btn-danger" onclick="return confirm('هل تريد حذف هذا الطلب؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
     <?php include 'sidebar_admin.php'; ?>
</div>
</body>
</html>
