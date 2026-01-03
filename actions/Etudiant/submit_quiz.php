<?php
// actions/Etudiant/submit_quiz.php
require_once '../../config/database.php';

// Prevent PHP notices/warnings from being sent to the client and capture any accidental output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = intval($_POST['quiz_id']);
    $responses = $_POST['answers'] ?? []; // Tableau [question_id => option_choisie]
    $userId = $_SESSION['user_id'] ?? null;

    // Basic validations
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
        // 1. Récupérer les bonnes réponses
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

        // 2. Enregistrer le résultat
        $stmtInsert = $pdo->prepare("INSERT INTO results (quiz_id, etudiant_id, score, total_questions, completed_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmtInsert->execute([$quizId, $userId, $score, $total]);

        // --- CHANGE THIS SECTION ---
        // Ensure no prior output corrupts the JSON
        if (ob_get_length()) { ob_end_clean(); }
        echo json_encode([
            'status' => 'success',
            'score' => $score,
            'total' => $total,
            // Since take_quiz.php and result.php are in the same folder (pages/student/), 
            // we just put the filename here:
            'redirect' => 'result.php' 
        ]);
        exit; // stop any further output
        // ---------------------------

    } catch (Exception $e) {
        // Clear any previous output and return a clean JSON error
        if (ob_get_length()) { ob_end_clean(); }
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}