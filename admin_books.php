<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/Book.php';
require_once 'classes/Category.php';

$bookObj = new Book();
$catObj  = new Category();
$msg = "";

// إضافة كتاب
if (isset($_POST['add'])) {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $is_free     = isset($_POST['is_free']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $release_date = $_POST['release_date'];

    // رفع الغلاف
    $image = 'assets/default_book.png';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imgName = time() . '_cover_' . basename($_FILES['image']['name']);
        $imgName = preg_replace('/[^\w\.-]/', '', $imgName);
        $imgPath = $targetDir . $imgName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);
        $image = $imgPath;
    }

    // رفع PDF
    $pdf = '';
    if (!empty($_FILES['pdf']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $pdfName = time() . '_book_' . basename($_FILES['pdf']['name']);
        $pdfName = preg_replace('/[^\w\.-]/', '', $pdfName);
        $pdfPath = $targetDir . $pdfName;
        move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath);
        $pdf = $pdfPath;
    }

    if ($bookObj->add($title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date)) {
        $msg = "تمت إضافة الكتاب بنجاح";
    } else {
        $msg = "حدث خطأ أثناء الإضافة!";
    }
}

// تعديل كتاب
if (isset($_POST['edit'])) {
    $id          = intval($_POST['id']);
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $is_free     = isset($_POST['is_free']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $release_date = $_POST['release_date'];

    // الغلاف
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imgName = time() . '_cover_' . basename($_FILES['image']['name']);
        $imgName = preg_replace('/[^\w\.-]/', '', $imgName);
        $imgPath = $targetDir . $imgName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);
        $image = $imgPath;
    } else {
        $image = $_POST['old_image'] ?? 'assets/default_book.png';
    }

    // PDF
    if (!empty($_FILES['pdf']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $pdfName = time() . '_book_' . basename($_FILES['pdf']['name']);
        $pdfName = preg_replace('/[^\w\.-]/', '', $pdfName);
        $pdfPath = $targetDir . $pdfName;
        move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath);
        $pdf = $pdfPath;
    } else {
        $pdf = $_POST['old_pdf'] ?? '';
    }

    if ($bookObj->update($id, $title, $author, $category_id, $image, $pdf, $description, $price, $is_free, $is_featured, $release_date)) {
        $msg = "تم التعديل بنجاح";
    } else {
        $msg = "حدث خطأ أثناء التعديل!";
    }
}

// حذف كتاب
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($bookObj->delete($id)) {
        $msg = "تم الحذف بنجاح";
    } else {
        $msg = "حدث خطأ أثناء الحذف!";
    }
}

$q = $_GET['q'] ?? '';
if ($q !== '') {
    $books = $bookObj->search($q);
} else {
    $books = $bookObj->all();
}

$categories = $catObj->all();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة الكتب</title>
    <link rel="stylesheet" href="assets/css/admin_books.css">
</head>

<body>
    <div class="container">
        <h1>إدارة الكتب</h1>

        <?php if ($msg): ?>
            <div class="msg"><?= $msg ?></div>
        <?php endif; ?>

        <form method="get" style="margin-bottom: 20px;">
            <input type="text" name="q" placeholder="ابحث عن كتاب..." value="<?= htmlspecialchars($q) ?>">
            <button type="submit" class="btn">بحث</button>
        </form>

        <?php if ($q !== ''): ?>
            <h2>نتائج البحث عن: <?= htmlspecialchars($q) ?></h2>
            <div class="books-row" style="display:flex; flex-wrap: wrap; gap: 15px;">
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-card" style="border:1px solid #ccc; padding:10px; width:180px; border-radius:8px;">
                            <img src="<?= htmlspecialchars($book['image'] ?? 'assets/default_book.png') ?>" alt="" style="width:100%; height:200px; object-fit:cover; border-radius:4px;">
                            <h4><?= htmlspecialchars($book['title']) ?></h4>
                            <p>المؤلف: <?= htmlspecialchars($book['author']) ?></p>
                            <p>التصنيف: <?= htmlspecialchars($book['category_name'] ?? '') ?></p>
                            <a href="<?= htmlspecialchars($book['pdf'] ?? '#') ?>" target="_blank" class="btn" style="padding: 3px 8px; display: inline-block; margin-top: 5px;">📄 PDF</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>لا توجد نتائج مطابقة.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <h3>كل الكتب</h3>
            <table>
                <tr>
                    <th>الغلاف</th>
                    <th>الاسم</th>
                    <th>المؤلف</th>
                    <th>السعر</th>
                    <th>تاريخ الإصدار</th>
                    <th>التصنيف</th>
                    <th>الوصف</th>
                    <th>مجاني؟</th>
                    <th>مميز؟</th>
                    <th>PDF</th>
                    <th>إجراءات</th>
                </tr>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $book['id'] ?>">
                            <input type="hidden" name="old_image" value="<?= htmlspecialchars($book['image'] ?? 'assets/default_book.png') ?>">
                            <input type="hidden" name="old_pdf" value="<?= htmlspecialchars($book['pdf'] ?? '') ?>">
                            <td>
                                <img src="<?= htmlspecialchars($book['image'] ?? 'assets/default_book.png') ?>" class="cover"><br>
                                <input type="file" name="image" accept="image/*" style="width:120px;">
                            </td>
                            <td><input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>"></td>
                            <td><input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>"></td>
                            <td><input type="number" name="price" value="<?= $book['price'] ?>" step="0.01"></td>
                            <td><input type="date" name="release_date" value="<?= htmlspecialchars($book['release_date']) ?>" style="width:130px;"></td>
                            <td>
                                <select name="category_id">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $book['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="description" value="<?= htmlspecialchars($book['description']) ?>"></td>
                            <td><input type="checkbox" name="is_free" <?= !empty($book['is_free']) ? 'checked' : '' ?>></td>
                            <td><input type="checkbox" name="is_featured" <?= !empty($book['is_featured']) ? 'checked' : '' ?>></td>
                            <td>
                                <?php if (!empty($book['pdf'])): ?>
                                    <a href="<?= htmlspecialchars($book['pdf']) ?>" target="_blank" class="btn" style="padding:3px 8px;">📄 PDF</a>
                                <?php endif; ?>
                                <input type="file" name="pdf" accept=".pdf" style="width:110px;">
                            </td>
                            <td class="actions">
                                <button class="btn" name="edit">تعديل</button>
                                <a href="?delete=<?= $book['id'] ?>" class="btn btn-danger" onclick="return confirm('تأكيد الحذف؟')">حذف</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <?php include 'sidebar_admin.php'; ?>
    </div>
</body>

</html>
