<?php
// classes/Etudiant/Question.php

class Question {
    private int $id;
    private int $quiz_id;
    private string $question;
    private string $option1;
    private string $option2;
    private string $option3;
    private string $option4;
    private int $correct_option;

    public function getId(): int { return $this->id; }
    public function getQuestionText(): string { return $this->question; }
    public function getOptions(): array {
        return [
            1 => $this->option1,
            2 => $this->option2,
            3 => $this->option3,
            4 => $this->option4
        ];
    }

    public static function getAllByQuiz(PDO $db, int $quizId): array {
        $sql = "SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':quiz_id' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}