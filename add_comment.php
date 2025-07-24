<?php
session_start();
require_once 'classes/Comment.php';

$user_id = $_SESSION['user_id'] ?? null;
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$content = trim($_POST['comment'] ?? '');

if (!$user_id || !$book_id || !$content) {
    echo json_encode(['status'=>'error', 'msg'=>'يجب تسجيل الدخول وكتابة تعليق!']);
    exit;
}

$commentObj = new Comment();
$commentObj->add($user_id, $book_id, $content);

// اسم المستخدم (يمكنك تعديله لجلب الاسم من قاعدة البيانات)
$user_name = $_SESSION['user_name'] ?? "مستخدم";

echo json_encode([
    'status' => 'success',
    'msg' => 'تم إضافة تعليقك!',
    'comment' => [
        'user_name' => htmlspecialchars($user_name),
        'comment'   => nl2br(htmlspecialchars($content))
    ]
]);
exit;
