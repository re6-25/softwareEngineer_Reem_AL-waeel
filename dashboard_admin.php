<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}
require_once 'classes/Book.php';
require_once 'classes/User.php';
require_once 'classes/Category.php';
require_once 'classes/Blog.php';
require_once 'classes/Message.php';
require_once 'classes/Comment.php';

$bookObj = new Book();
$userObj = new User();
$catObj  = new Category();
$blogObj = new Blog();
$msgObj  = new Message();
$commentObj = new Comment();

$totalBooks      = $bookObj->count();
$totalUsers      = $userObj->count();
$totalCategories = $catObj->count();
$totalBlogs      = $blogObj->count();
$totalMessages   = $msgObj->count();
$totalComments = count($commentObj->all());

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الأدمن</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
   
   <link rel="stylesheet" href="assets\css\dashboard_admin.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="main-wrapper">

        <!-- Main content -->
        <div class="content">
            <div class="dash-title">لوحة تحكم الأدمن</div>
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-book"></i></div>
                    <div class="stat-label">عدد الكتب</div>
                    <div class="stat-num"><?= $totalBooks ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-users"></i></div>
                    <div class="stat-label">عدد المستخدمين</div>
                    <div class="stat-num"><?= $totalUsers ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-layer-group"></i></div>
                    <div class="stat-label">عدد التصنيفات</div>
                    <div class="stat-num"><?= $totalCategories ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-blog"></i></div>
                    <div class="stat-label">عدد المدونات</div>
                    <div class="stat-num"><?= $totalBlogs ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-envelope"></i></div>
                    <div class="stat-label">عدد الرسائل</div>
                    <div class="stat-num"><?= $totalMessages ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-comments"></i></div>
                    <div class="stat-label">عدد التعليقات</div>
                    <div class="stat-num"><?= $totalComments ?></div>
                </div>
            </div>
            <a href="index.php" class="btn" style="background:#a390e4;color:#fff;margin:7px 0 15px 0;display:inline-block;">
    <i class="fa fa-home"></i> الذهاب للصفحة الرئيسية
</a>

            <canvas id="adminStatsChart" width="400" height="170" style="margin:20px auto;display:block;max-width:99vw;"></canvas>
            <div class="admin-tips" style="background:#fffbe6;border-radius:12px;padding:13px 11px;margin-top:18px;box-shadow:0 2px 7px #c2b4f610;">
                <b>توصيات المهندس للتطوير:</b>
                <ul style="text-align:right;margin:8px 22px 0 0;color:#aa8c2d;">
                    <li>فصل لوحة التحكم لأدوار: قارئ، مشرف كتب، أدمن، مشرف تعليقات...</li>
                    <li>تفعيل نظام الصلاحيات والمهام لكل رتبة.</li>
                    <li>إظهار إحصاءات متقدمة (أكثر الكتب قراءة/الأعضاء الأكثر تفاعلاً).</li>
                    <li>دعم التنبيهات الذكية للإدمن والمشرفين.</li>
                    <li>إضافة سجل النشاطات لكل مشرف.</li>
                    <li>تحسين التصميم ليكون أسرع وأسهل على الجوالات.</li>
                </ul>
            </div>

        </div>
    </div>
   

    <?php include 'sidebar_admin.php'; ?>
    <script>
        var ctx = document.getElementById('adminStatsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['كتب', 'مستخدمين', 'تصنيفات', 'مدونات', 'رسائل', 'تعليقات'],
                datasets: [{
                    label: 'إحصائيات',
                    data: [
                        <?= $totalBooks ?>,
                        <?= $totalUsers ?>,
                        <?= $totalCategories ?>,
                        <?= $totalBlogs ?>,
                        <?= $totalMessages ?>,
                        <?= $totalComments ?>
                    ],
                    backgroundColor: [
                        '#a390e4',
                        '#f9c846',
                        '#7157c7',
                        '#ffa07a',
                        '#5f9ea0',
                        '#c97c5d'
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>