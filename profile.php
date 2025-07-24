<?php
session_start();
require_once 'classes/User.php';
require_once 'classes/Book.php';
require_once 'classes/Blog.php';
require_once 'classes/Favorite.php';
require_once 'classes/Download.php';
require_once 'classes/Message.php';
require_once 'classes/Upload.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  header("Location: login_register.php");
  exit;
}

$userObj = new User();
$bookObj = new Book();
$blogObj = new Blog();
$favObj = new Favorite();
$downloadObj = new Download();
$msgObj = new Message();

$msg = "";

// تحديث المعلومات الأساسية
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
  $new_name = trim($_POST['name']);
  $new_email = trim($_POST['email']);
  $new_password = trim($_POST['password']);
  if ($new_name && $new_email) {
    $userObj->updateBasicInfo($user_id, $new_name, $new_email);
    if ($new_password) {
      $userObj->updatePassword($user_id, $new_password);
    }
    $msg = "تم تحديث معلومات الحساب بنجاح!";
  }
}

// تحديث الصورة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_avatar'])) {
  if (isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
    $new_avatar = Upload::image($_FILES['avatar']);
    if ($new_avatar) {
      $userObj->updateAvatar($user_id, $new_avatar);
      $user_avatar = $new_avatar;
      $msg = "تم تحديث صورة الحساب بنجاح!";
    } else {
      $msg = "حدث خطأ أثناء رفع الصورة!";
    }
  }
}

// تحديث النبذة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
  $bio = trim($_POST['bio']);
  if ($user_id) {
    $userObj->updateBio($user_id, $bio);
    $user_bio = $bio;
    $msg = "تم تحديث النبذة بنجاح!";
  }
}

// بيانات المستخدم
$user = $userObj->get($user_id);
$user_avatar = !empty($user['avatar']) ? $user['avatar'] : "assets/img/default-user.png";
$user_name = $user['name'] ?? "";
$user_email = $user['email'] ?? "";
$user_points = $user['points'] ?? 0;
$user_bio = $user['bio'] ?? "";

// المفضلة
$favBooks = $favObj->all($user_id);
$favBooksData = [];
foreach ($favBooks as $fb) {
  $b = $bookObj->get($fb['book_id']);
  if ($b) $favBooksData[] = $b;
}

// المقروءة
$readBooks = $downloadObj->all($user_id);
$readBooksData = [];
foreach ($readBooks as $rb) {
  $b = $bookObj->get($rb['book_id']);
  if ($b) $readBooksData[] = $b;
}

// رسائل الإدارة
$messages = $msgObj->byUser($user_id);
$totalMessages = count($messages);
$recentMessages = array_slice($messages, -5, 5, true);

// مدونات المستخدم
$userBlogs = $blogObj->byUser($user_id);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ملفي الشخصي - مكتبة الإلهام الساكن</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
<header>
  <div class="logo">
    <?php include 'sidebar.php'; ?>
  </div>
</header>
<div class="container">
  <div class="profile-box">
    <div class="profile-img">
      <img src="<?= htmlspecialchars($user_avatar) ?>" alt="صورة المستخدم" style="max-width:120px;max-height:120px;">
      <form method="post" enctype="multipart/form-data" style="margin-top:7px;">
        <input type="file" name="avatar" accept="image/*" required style="margin-bottom:6px;">
        <button type="submit" name="change_avatar" class="change-avatar-btn">تغيير الصورة</button>
      </form>
    </div>

    <div class="profile-info">
      <h2><?= htmlspecialchars($user_name) ?></h2>
      <div class="user-email"><?= htmlspecialchars($user_email) ?></div>
      <div class="user-points">النقاط: <b><?= intval($user_points) ?></b></div>
      <div class="user-bio"><?= $user_bio ? nl2br(htmlspecialchars($user_bio)) : '<span style="color:#aaa">لم تضف نبذة بعد.</span>' ?></div>

      <!-- نموذج تعديل النبذة -->
      <form method="post" class="bio-form">
        <textarea name="bio" placeholder="اكتب نبذة عنك..."><?= htmlspecialchars($user_bio) ?></textarea>
        <button type="submit">حفظ النبذة</button>
      </form>

      <!-- نموذج تعديل البيانات -->
      <form method="post" class="profile-edit-form" style="margin-top:20px;">
        <h3>تعديل معلومات الحساب</h3>
        <label>الاسم:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user_name) ?>" required>
        <label>كلمة المرور الجديدة:</label>
        <input type="password" name="password" placeholder="اتركها فارغة إن لم ترد التغيير">
        <button type="submit" name="update_account">حفظ التعديلات</button>
        <?php if ($msg): ?><div class="msg-success"><?= $msg ?></div><?php endif; ?>
      </form>
    </div>
  </div>

  <div class="section-title">الكتب المفضلة</div>
  <div class="books-row">
    <?php foreach ($favBooksData as $book): ?>
      <div class="book-card" onclick="location.href='book.php?id=<?= $book['id'] ?>'">
        <img src="<?= htmlspecialchars($book['image'] ?? 'https://covers.openlibrary.org/b/id/10519747-L.jpg') ?>" alt="">
        <div class="book-title"><?= htmlspecialchars($book['title'] ?? '') ?></div>
        <div class="book-author"><?= htmlspecialchars($book['author'] ?? '') ?></div>
      </div>
    <?php endforeach;
    if (empty($favBooksData)): ?>
      <div style="color:#aaa;margin:10px 0;">لا توجد كتب مفضلة.</div>
    <?php endif; ?>
  </div>

  <div class="section-title">الكتب التي قرأتها</div>
  <div class="books-row">
    <?php foreach ($readBooksData as $book): ?>
      <div class="book-card" onclick="location.href='book.php?id=<?= $book['id'] ?>'">
        <img src="<?= htmlspecialchars($book['image'] ?? 'https://covers.openlibrary.org/b/id/10519747-L.jpg') ?>" alt="">
        <div class="book-title"><?= htmlspecialchars($book['title'] ?? '') ?></div>
        <div class="book-author"><?= htmlspecialchars($book['author'] ?? '') ?></div>
      </div>
    <?php endforeach;
    if (empty($readBooksData)): ?>
      <div style="color:#aaa;margin:10px 0;">لم تقرأ أي كتاب بعد.</div>
    <?php endif; ?>
  </div>

  <div class="section-title">مدوناتي</div>
  <ul class="blog-list">
    <?php foreach ($userBlogs as $blog): ?>
      <li>
        <a href="blog.php?id=<?= $blog['id'] ?>"><?= htmlspecialchars($blog['title'] ?? '') ?></a>
        <span class="blog-date"><?= date('Y-m-d', strtotime($blog['created_at'] ?? '')) ?></span>
      </li>
    <?php endforeach;
    if (empty($userBlogs)): ?>
      <li>لا توجد مدونات.</li>
    <?php endif; ?>
  </ul>

  <div class="section-title">رسائل الإدارة</div>
  <?php foreach ($recentMessages as $m): ?>
    <div style="background:#f9f7fd; border-radius:8px; padding:12px; margin-bottom:8px;">
      <b>سؤالك:</b> <?= nl2br(htmlspecialchars($m['content'] ?? '')) ?><br>
      <?php if (!empty($m['reply'])): ?>
        <span style="color:green"><b>رد الإدارة:</b> <?= nl2br(htmlspecialchars($m['reply'])) ?></span>
      <?php else: ?>
        <span style="color:#e6903c;">في انتظار الرد...</span>
      <?php endif; ?>
    </div>
  <?php endforeach;
  if ($totalMessages > 5): ?>
    <div style="text-align:center;margin:14px 0;">
      <a href="messages.php" class="change-avatar-btn" style="background:#f9c846;color:#393e46;">عرض كل الرسائل (<?= $totalMessages ?>)</a>
    </div>
  <?php elseif ($totalMessages == 0): ?>
    <div style="color:#aaa;margin:10px 0;">لا توجد رسائل بعد.</div>
  <?php endif; ?>
</div>
<footer>
  " مكتبة الإلهام الساكن" المهندسة/ ريم طاهر الوعيل
</footer>
</body>
</html>
