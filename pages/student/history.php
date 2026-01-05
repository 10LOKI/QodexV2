<?php
require_once '../../config/database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "SELECT r.*, q.titre as quiz_title 
        FROM results r 
        JOIN quiz q ON r.quiz_id = q.id 
        WHERE r.etudiant_id = :user_id 
        ORDER BY r.completed_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Historique | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-black">

<div class="max-w-5xl mx-auto px-6 py-12">
    <div class="flex justify-between items-end mb-12">
        <div>
            <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Performance</span>
            <h1 class="text-4xl font-black tracking-tighter mt-2">Historique des Examens</h1>
        </div>
        <a href="dashboard.php" class="text-sm font-bold text-gray-400 hover:text-black transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Examen</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Date</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Score</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Résultat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-gray-400 font-medium">
                            <i class="fas fa-inbox block text-4xl mb-4 opacity-20"></i>
                            Aucun examen passé pour le moment.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $res): 
                        $pct = ($res['total_questions'] > 0) ? ($res['score'] / $res['total_questions']) * 100 : 0;
                        $passed = $pct >= 50;
                    ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <span class="font-bold text-lg tracking-tight"><?= htmlspecialchars($res['quiz_title']) ?></span>
                            </td>
                            <td class="px-8 py-6 text-center text-gray-500 font-medium text-sm">
                                <?= date('d/m/Y H:i', strtotime($res['completed_at'])) ?>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="font-black text-lg"><?= $res['score'] ?></span>
                                <span class="text-gray-300">/</span>
                                <span class="text-gray-400 font-bold"><?= $res['total_questions'] ?></span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="inline-flex px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest <?= $passed ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-500 border border-red-100' ?>">
                                    <?= $passed ? 'Réussi' : 'Échec' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>