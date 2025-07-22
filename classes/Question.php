<?php
require_once 'Database.php';

class Question {

    private $db;

    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    // جلب أسئلة عشوائية حسب المستوى مع استبعاد أسئلة تم عرضها
    public function getRandomByLevel($level, $limit, $excludeIds = []) {
        $sql = "SELECT * FROM questions WHERE level = ?";
        $params = [$level];

        if (!empty($excludeIds)) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql .= " AND id NOT IN ($placeholders)";
            $params = array_merge($params, $excludeIds);
        }

        $sql .= " ORDER BY RAND() LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // إضافة سؤال جديد إلى قاعدة البيانات
    public function add($question, $choice1, $choice2, $choice3, $choice4, $correct_choice, $level) {
        $stmt = $this->db->prepare("
            INSERT INTO questions (question, choice1, choice2, choice3, choice4, correct_choice, level)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $question,
            $choice1,
            $choice2,
            $choice3,
            $choice4,
            $correct_choice,
            $level
        ]);
    }
}
