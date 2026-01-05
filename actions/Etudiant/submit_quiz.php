<?php
require_once '../../config/database.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = intval($_POST['quiz_id']);
    $responses = $_POST['answers'] ?? [];
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        if (ob_get_length()) { ob_end_clean(); }
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
        exit;
    }
    if ($quizId <= 0) {
        if (ob_get_length()) { ob_end_clean(); }
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Identifiant du quiz invalide']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, correct_option FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $score = 0;
        $total = count($questions);

        foreach ($questions as $q) {
            if (isset($responses[$q['id']]) && intval($responses[$q['id']]) === intval($q['correct_option'])) {
                $score++;
            }
        }

        $stmtInsert = $pdo->prepare("INSERT INTO results (quiz_id, etudiant_id, score, total_questions, completed_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmtInsert->execute([$quizId, $userId, $score, $total]);

        if (ob_get_length()) { ob_end_clean(); }
        echo json_encode([
            'status' => 'success',
            'score' => $score,
            'total' => $total,
            'redirect' => 'result.php' 
        ]);
        exit;

    } catch (Exception $e) {
        if (ob_get_length()) { ob_end_clean(); }
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}