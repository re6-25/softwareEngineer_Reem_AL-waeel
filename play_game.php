<?php
session_start();
require_once 'classes/Game.php';
require_once 'classes/Question.php';
$gameObj = new Game();
$questionObj = new Question();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die('<div style="text-align:center;padding:40px;font-size:1.2em;color:#a00;">يجب عليك <a href="login_register.php">تسجيل الدخول</a> أولاً للعب اللعبة.</div>');
}

if (!isset($_SESSION['quiz'])) {
    $_SESSION['quiz'] = [
        'step' => 0,
        'score' => 0,
        'level' => 1,
        'asked' => [],
    ];
}

$quiz = &$_SESSION['quiz'];
$questionsPerLevel = 5;
$totalQuestions = 100;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_and_exit'])) {
    $gameObj->addScore($user_id, $quiz['score']);
    header("Location: game.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['save_and_exit'])) {
    $answers = $_POST['answers'] ?? [];
    $curQuestions = $_SESSION['cur_questions'] ?? [];
    $correct = 0;

    foreach ($curQuestions as $i => $q) {
        if (isset($answers[$i]) && $answers[$i] == $q['correct_choice']) {
            $correct++;
        }
    }

    $quiz['score'] += $correct;
    $quiz['step'] += $questionsPerLevel;
    if ($quiz['step'] % 10 == 0 && $quiz['level'] < 3) $quiz['level']++;
    unset($_SESSION['cur_questions']);
}

$remaining = $totalQuestions - $quiz['step'];
if ($remaining > 0) {
    $level = $quiz['level'];
    $questions = $questionObj->getRandomByLevel($level, $questionsPerLevel, $quiz['asked']);
    $_SESSION['cur_questions'] = $questions;
    foreach ($questions as $q) $quiz['asked'][] = $q['id'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لعبة ثقافية - مكتبة الإلهام الساكن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .quiz-box {background:#fff;padding:28px 33px;border-radius:15px;box-shadow:0 2px 14px #7157c72b;max-width:470px;margin:38px auto;}
      .question {margin:15px 0;}
      .btn {background:#a390e4;color:#fff;border:none;padding:9px 24px;border-radius:9px;font-weight:bold;cursor:pointer;}
      .btn:hover {background:#f9c846;color:#393e46;}
      .result {background:#e4f8ec;color:#258850;padding:15px;margin-top:17px;border-radius:8px;font-size:1.2em;text-align:center;}
      .quiz-title {text-align:center;font-size:1.3em;margin-bottom:16px;}
      @media(max-width:700px){.quiz-box{padding:16px 5px;}}
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="quiz-box">
        <div class="quiz-title">
            <?= $quiz['step'] < $totalQuestions ? "مجموعة أسئلة جديدة (المستوى: {$quiz['level']})" : "انتهت اللعبة" ?>
        </div>
        <?php if ($quiz['step'] >= $totalQuestions): ?>
            <?php $gameObj->addScore($user_id, $quiz['score']); $_SESSION['quiz'] = null; ?>
            <div class="result">
                مبروك! أكملت كل الأسئلة.<br>
                مجموع نقاطك: <?= $quiz['score'] ?> من <?= $totalQuestions ?>
                <br><a href="?reset=1" class="btn">إعادة اللعب</a>
                <br><a href="games.php" class="btn" style="background:#f9c846;color:#393e46;margin-top:10px;">العودة للألعاب</a>
            </div>
        <?php else: ?>
            <form method="post">
                <?php foreach ($_SESSION['cur_questions'] as $i => $q): ?>
                    <div class="question">
                        <?= ($quiz['step']+$i+1) . ". " . htmlspecialchars($q['question']) ?><br>
                        <?php
                        $choices = [
                            '1' => $q['choice1'],
                            '2' => $q['choice2'],
                            '3' => $q['choice3'],
                            '4' => $q['choice4']
                        ];
                        foreach ($choices as $key => $text):
                            if (!empty($text)):
                        ?>
                            <label>
                                <input type="radio" name="answers[<?= $i ?>]" value="<?= $key ?>" required>
                                <?= htmlspecialchars($text) ?>
                            </label><br>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                <?php endforeach; ?>
                <button class="btn" type="submit">إرسال الإجابات</button>
                <button class="btn" type="submit" name="save_and_exit" value="1" style="background:#f9c846;color:#393e46;margin-right:10px;">حفظ ومتابعة لاحقاً</button>
            </form>
            <div style="margin-top:12px;text-align:center;">
                <b>النقاط الحالية:</b> <?= $quiz['score'] ?> / <?= $totalQuestions ?><br>
                <b>عدد الأسئلة التي أجبتها:</b> <?= $quiz['step'] ?> / <?= $totalQuestions ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if (isset($_GET['reset'])) {
    unset($_SESSION['quiz'], $_SESSION['cur_questions']);
    header("Location: play_game.php");
    exit;
}
?>
