<?php
session_start();
require_once 'classes/Game.php';
require_once 'classes/User.php';
$gameObj = new Game();
$userObj = new User();
$user_id = $_SESSION['user_id'] ?? null;

// جلب الألعاب من قاعدة البيانات (أو اتركها مصفوفة فاضية إذا مافي ألعاب)
$games = $gameObj->all();
$userPoints = $user_id ? $gameObj->getUserTotalScore($user_id) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ألعاب الذكاء</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/games.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="game-wrapper">
        <h2 style="color:#a390e4;">🧠 ألعاب الذكاء والتحدي</h2>
        <?php if($user_id): ?>
            <div class="points-box">رصيد نقاطك الحالي: <b><?= $userPoints ?></b> نقطة</div>
        <?php else: ?>
            <div class="points-box" style="background:#fee4e4;color:#e75e5e;">سجل دخولك لتجميع النقاط والاستمتاع!</div>
        <?php endif; ?>
        <div class="games-grid">
            <?php foreach($games as $g): ?>
                <div class="game-card">
                    <div class="game-icon"><?= htmlspecialchars($g['icon'] ?? '🧩') ?></div>
                    <div class="game-title"><?= htmlspecialchars($g['title']) ?></div>
                    <div class="game-desc"><?= htmlspecialchars($g['description']) ?></div>
                    <a href="play_game.php?id=<?= $g['id'] ?>" class="start-btn">ابدأ اللعبة</a>
                </div>
            <?php endforeach; ?>
            <?php if(empty($games)): ?>
                <div class="game-card">
                    <div class="game-icon">🧩</div>
                    <div class="game-title">اختبار ثقافة عامة</div>
                    <div class="game-desc">جاوب على أسئلة ثقافية متنوعة واكسب نقاط!</div>
                    <a href="play_game.php?type=quiz" class="start-btn">ابدأ اللعبة</a>
                </div>
                <div class="game-card">
                    <div class="game-icon">🧠</div>
                    <div class="game-title">لعبة الذاكرة</div>
                    <div class="game-desc">اختبر ذاكرتك بمطابقة البطاقات بسرعة.</div>
                    <a href="play_game.php?type=memory" class="start-btn">ابدأ اللعبة</a>
                </div>
                <div class="game-card">
                    <div class="game-icon">🤔</div>
                    <div class="game-title">ألغاز منطقية</div>
                    <div class="game-desc">حل ألغاز منوعة واربح جوائز.</div>
                    <a href="play_game.php?type=logic" class="start-btn">ابدأ اللعبة</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
