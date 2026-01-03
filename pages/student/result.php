<?php
// pages/Etudiant/result.php
session_start();

// Security check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Get parameters from URL (sent by the JS redirect)
$score = filter_input(INPUT_GET, 'score', FILTER_VALIDATE_INT) ?? 0;
$total = filter_input(INPUT_GET, 'total', FILTER_VALIDATE_INT) ?? 0;

// Calculate Percentage
$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;

// Determine Status
$passed = $percentage >= 50; // You can change this threshold
$statusTitle = $passed ? "Félicitations !" : "Ne lâchez rien !";
$statusMessage = $passed 
    ? "Vous avez réussi cet examen avec brio." 
    : "Vous n'avez pas atteint le seuil de réussite. Révisez et réessayez.";

$colorTheme = $passed ? 'green' : 'red';
$textClass = $passed ? 'text-green-600' : 'text-red-600';
$bgClass = $passed ? 'bg-green-50' : 'bg-red-50';
$borderClass = $passed ? 'border-green-100' : 'border-red-100';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat de l'examen | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Circular Progress Animation */
        .circle-chart__circle {
            animation: circle-chart-fill 2s reverse; 
            transform: rotate(-90deg); 
            transform-origin: center;
        }
        @keyframes circle-chart-fill {
            to { stroke-dasharray: 0 100; }
        }
        .pop-in { animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="bg-[#FAFAFA] text-black min-h-screen flex flex-col">

    <div class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="bg-black text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Résultats</span>
                <h1 class="font-bold text-sm hidden md:block">Session Qodex v2</h1>
            </div>
            <a href="dashboard.php" class="text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-black transition-colors">
                Quitter
            </a>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6 pt-24 pb-12">
        <div class="max-w-md w-full bg-white rounded-[2.5rem] p-8 md:p-12 shadow-xl border border-gray-100 text-center pop-in relative overflow-hidden">
            
            <div class="absolute -top-24 -left-24 w-48 h-48 <?= $bgClass ?> rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-48 h-48 <?= $bgClass ?> rounded-full opacity-50 blur-3xl"></div>

            <div class="relative z-10 mb-8">
                <div class="w-20 h-20 mx-auto <?= $bgClass ?> rounded-full flex items-center justify-center mb-6">
                    <?php if($passed): ?>
                        <i class="fas fa-trophy text-3xl <?= $textClass ?>"></i>
                    <?php else: ?>
                        <i class="fas fa-chart-line text-3xl <?= $textClass ?>"></i>
                    <?php endif; ?>
                </div>
                <h2 class="text-3xl font-black mb-2 tracking-tight"><?= $statusTitle ?></h2>
                <p class="text-gray-500 text-sm font-medium"><?= $statusMessage ?></p>
            </div>

            <div class="relative w-48 h-48 mx-auto mb-10">
                <svg viewBox="0 0 36 36" class="w-full h-full block">
                    <path class="text-gray-100" 
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                        fill="none" stroke="currentColor" stroke-width="2.5" />
                    <path class="<?= $textClass ?>" 
                        stroke-dasharray="<?= $percentage ?>, 100" 
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" 
                        class="circle-chart__circle" />
                </svg>
                <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
                    <span class="text-4xl font-black tracking-tighter"><?= $percentage ?>%</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                        Score <?= $score ?>/<?= $total ?>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-10">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="block text-xs text-gray-400 font-bold uppercase mb-1">Correctes</span>
                    <span class="block text-xl font-black text-green-600"><?= $score ?></span>
                </div>
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="block text-xs text-gray-400 font-bold uppercase mb-1">Incorrectes</span>
                    <span class="block text-xl font-black text-red-500"><?= $total - $score ?></span>
                </div>
            </div>

            <div class="space-y-3 relative z-10">
                <a href="dashboard.php" class="block w-full bg-black text-white py-4 rounded-xl font-bold text-sm uppercase tracking-wider hover:bg-gray-900 transition-all hover:shadow-lg transform hover:-translate-y-1">
                    Retour au Dashboard
                </a>
                <a href="quizzes.php" class="block w-full bg-white text-black border-2 border-gray-100 py-4 rounded-xl font-bold text-sm uppercase tracking-wider hover:border-black transition-all">
                    Autre Examen
                </a>
            </div>
        </div>
    </div>

    <?php if($passed): ?>
    <script>
        // Trigger confetti on load if passed
        window.addEventListener('load', () => {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            var random = function(min, max) { return Math.random() * (max - min) + min; };

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        });
    </script>
    <?php endif; ?>

</body>
</html>