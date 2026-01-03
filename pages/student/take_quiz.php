<?php
require_once '../../config/database.php';
require_once '../../classes/Etudiant/Quiz.php';
require_once '../../classes/Etudiant/Question.php';

if (!isset($pdo) && isset($db)) { $pdo = $db; }

if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

$quizId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quizId) { header("Location: quizzes.php"); exit(); }

$questions = Question::getAllByQuiz($pdo, $quizId);
if (empty($questions)) { die("Ce quiz ne contient aucune question."); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session d'Examen | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .slide-in { animation: slideIn 0.4s ease-out forwards; }
        @keyframes slideIn { 
            from { opacity: 0; transform: translateX(10px); } 
            to { opacity: 1; transform: translateX(0); } 
        }
    </style>
</head>
<body class="bg-[#FAFAFA] text-black">

<div class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <span class="bg-black text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Examen en cours</span>
            <h1 class="font-bold text-sm hidden md:block">Session Qodex v2</h1>
        </div>
        
        <div class="flex items-center gap-8">
            <div class="text-right">
                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-tighter">Temps Restant</span>
                <span id="timer" class="text-lg font-black tabular-nums">30:00</span>
            </div>
            <div class="h-10 w-[1px] bg-gray-100"></div>
            <button onclick="if(confirm('Abandonner l\'examen ?')) window.location.href='quizzes.php'" class="text-gray-400 hover:text-black transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    <div class="w-full h-1 bg-gray-50">
        <div id="progressBar" class="h-full bg-black transition-all duration-500" style="width: 0%"></div>
    </div>
</div>

<div class="pt-32 pb-20 min-h-screen">
    <div class="max-w-3xl mx-auto px-6">
        <form id="quizForm">
            <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
            
            <?php foreach ($questions as $index => $q): ?>
                <div class="question-slide <?= $index === 0 ? '' : 'hidden' ?> slide-in" data-q-index="<?= $index ?>">
                    <div class="mb-10 text-center">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Étape <?= $index + 1 ?> / <?= count($questions) ?></span>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-10 md:p-16 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-50 rounded-full opacity-50"></div>

                        <h2 class="text-3xl md:text-4xl font-bold mb-12 tracking-tight leading-tight relative z-10">
                            <?= htmlspecialchars($q->getQuestionText()) ?>
                        </h2>
                        
                        <div class="space-y-4 relative z-10">
                            <?php foreach ($q->getOptions() as $val => $text): ?>
                                <label class="group block relative cursor-pointer">
                                    <input type="radio" name="answers[<?= $q->getId() ?>]" value="<?= $val ?>" class="hidden peer" required>
                                    <div class="p-6 border-2 border-gray-50 rounded-[1.5rem] transition-all duration-300 flex items-center group-hover:border-gray-200 peer-checked:border-black peer-checked:bg-black peer-checked:text-white peer-checked:shadow-xl peer-checked:scale-[1.02]">
                                        <span class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center mr-6 text-xs font-bold transition-colors group-hover:border-black peer-checked:border-white/30 peer-checked:text-white">
                                            <?= chr(64 + $val) ?>
                                        </span>
                                        <span class="text-lg font-medium"><?= htmlspecialchars($text) ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-16 flex justify-between items-center">
                            <button type="button" onclick="prevQ()" class="group flex items-center font-bold text-gray-400 hover:text-black transition-colors <?= $index === 0 ? 'invisible' : '' ?>">
                                <i class="fas fa-arrow-left mr-3 transform group-hover:-translate-x-1 transition-transform"></i>
                                Retour
                            </button>

                            <?php if ($index === count($questions) - 1): ?>
                                <button type="submit" class="bg-black text-white px-12 py-5 rounded-full font-black text-xs uppercase tracking-widest hover:bg-gray-800 transition-all hover:shadow-2xl active:scale-95">
                                    Soumettre l'examen
                                </button>
                            <?php else: ?>
                                <button type="button" onclick="nextQ()" class="group bg-black text-white px-12 py-5 rounded-full font-black text-xs uppercase tracking-widest hover:bg-gray-800 transition-all flex items-center shadow-lg hover:shadow-xl">
                                    Suivant
                                    <i class="fas fa-arrow-right ml-3 transform group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.question-slide');
const progressBar = document.getElementById('progressBar');

function updateUI() {
    slides.forEach((s, i) => {
        s.classList.toggle('hidden', i !== currentSlide);
    });
    const progress = ((currentSlide + 1) / slides.length) * 100;
    progressBar.style.width = progress + '%';
}

function nextQ() {
    const currentInputs = slides[currentSlide].querySelectorAll('input:checked');
    if (currentInputs.length === 0) {
        alert("Veuillez sélectionner une réponse.");
        return;
    }
    if (currentSlide < slides.length - 1) {
        currentSlide++;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevQ() {
    if (currentSlide > 0) {
        currentSlide--;
        updateUI();
    }
}

let timeLeft = 1800;
const timerDisplay = document.getElementById('timer');
const countdown = setInterval(() => {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    if (timeLeft <= 0) {
        clearInterval(countdown);
        document.getElementById('quizForm').dispatchEvent(new Event('submit'));
    }
    timeLeft--;
}, 1000);

document.getElementById('quizForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Calcul...';
    btn.disabled = true;

    const formData = new FormData(e.target);
    const response = await fetch('../../actions/Etudiant/submit_quiz.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    });

    // If server returned non-OK, handle errors (401 -> redirect to login)
    if (!response.ok) {
        let data = null;
        try { data = await response.json(); } catch (err) { /* ignore non-JSON */ }

        if (response.status === 401) {
            alert('Session expirée — veuillez vous reconnecter.');
            window.location.href = '../../pages/auth/login.php';
            return;
        }

        const msg = (data && data.message) ? data.message : ('Erreur serveur: ' + response.status);
        alert("Erreur: " + msg);
        btn.disabled = false;
        btn.textContent = "Soumettre l'examen";
        return;
    }

    const result = await response.json();
    if (result.status === 'success') {
        window.location.href = result.redirect + '?score=' + result.score + '&total=' + result.total;
    } else {
        alert("Erreur: " + result.message);
        btn.disabled = false;
        btn.textContent = "Soumettre l'examen";
    }
};
updateUI();
</script>

</body>
</html>