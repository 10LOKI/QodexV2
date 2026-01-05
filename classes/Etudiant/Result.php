<?php
class Result
{
    private int $id;
    private int $quiz_id;
    private int $etudiant_id;
    private int $score;
    private int $total_questions;
    private string $completed_at;

    public function getId(): int { return $this->id; }
    public function getScore(): int { return $this->score; }
    public function getTotal(): int { return $this->total_questions; }

    public static function getByStudent(PDO $db, int $studentId): array
    {
        $sql = "SELECT r.*, q.titre as quiz_title
                FROM results r
                JOIN quiz q ON r.quiz_id = q.id
                WHERE r.etudiant_id = :studentId
                ORDER BY r.completed_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':studentId' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function save(PDO $db, int $quizId, int $studentId, int $score, int $total): bool
    {
        $sql = "INSERT INTO results (quiz_id, etudiant_id, score, total_questions, completed_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$quizId, $studentId, $score, $total]);
    }

    public static function getStats(PDO $db, int $studentId): array
    {
        $sql = "SELECT COUNT(*) as total_quiz, AVG(score / total_questions * 100) as moyenne, MAX(score / total_questions * 100) as meilleur_score
                FROM results WHERE etudiant_id = :studentId";
        $stmt = $db->prepare($sql);
        $stmt->execute([':studentId' => $studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>