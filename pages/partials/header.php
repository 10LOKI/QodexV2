<?php
/**
 * PARTIAL: Navigation Étudiant - Design Premium Black & White
 */

// 1. Calculer les initiales
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$nameParts = explode(' ', trim($userName));
$firstLetter = mb_substr($nameParts[0], 0, 1);
$lastLetter = (count($nameParts) > 1) ? mb_substr(end($nameParts), 0, 1) : '';
$initials = strtoupper($firstLetter . $lastLetter);

$rootPath = "../../"; 
$currentPage = $currentPage ?? 'dashboard';

// Charger Security pour le token CSRF (utilisé pour la déconnexion)
require_once $rootPath . 'classes/Security.php';
$logoutToken = Security::generateCSRFToken();

// Helper pour les classes actives
function navClass($page, $current) {
    $base = "relative px-3 py-2 text-sm font-medium transition-all duration-300 ease-in-out ";
    return $page === $current 
        ? $base . "text-black bg-gradient-to-b from-gray-50 to-gray-200 rounded-lg shadow-sm" 
        : $base . "text-gray-500 hover:text-black hover:bg-gray-50 rounded-lg";
}
?>

<nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center group cursor-pointer">
                    <div class="p-2 bg-black rounded-xl group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-graduation-cap text-xl text-white"></i>
                    </div>
                    <div class="ml-3 flex flex-col">
                        <span class="text-xl font-black tracking-tighter text-black uppercase">Qodex</span>
                        <span class="text-[10px] leading-none font-bold text-gray-400 tracking-widest uppercase">Student Portal</span>
                    </div>
                </div>

                <div class="hidden lg:ml-10 lg:flex lg:space-x-2">
                    <a href="dashboard.php" class="<?= navClass('dashboard', $currentPage) ?>">
                        <i class="fas fa-grid-2 mr-2"></i>Dashboard
                    </a>
                    <a href="quizzes.php" class="<?= navClass('quizzes', $currentPage) ?>">
                        <i class="fas fa-layer-group mr-2"></i>Examens
                    </a>
                    <a href="history.php" class="<?= navClass('history', $currentPage) ?>">
                        <i class="fas fa-clock-rotate-left mr-2"></i>Historique
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                
                <div class="hidden md:flex flex-col text-right border-r border-gray-100 pr-6">
                    <span class="text-sm font-bold text-black"><?= htmlspecialchars($userName) ?></span>
                    <span class="text-[11px] text-gray-400 font-medium">Connecté</span>
                </div>

                <div class="relative" id="profileRoot">
                    <button id="profileBtn" aria-haspopup="true" aria-expanded="false" class="w-11 h-11 rounded-xl bg-gradient-to-tr from-black to-gray-700 flex items-center justify-center text-white text-sm font-bold shadow-lg transform hover:scale-105 transition-all focus:outline-none">
                        <?= $initials ?>
                    </button>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-black"></span>
                    </span>

                    <!-- Dropdown profil -->
                    <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                        <div class="px-4 py-3 border-b border-gray-50">
                            <div class="text-sm font-bold"><?= htmlspecialchars($userName) ?></div>
                            <div class="text-xs text-gray-400">Étudiant</div>
                        </div>
                        <a href="<?= $rootPath ?>student/profile.php" class="block px-4 py-3 text-sm hover:bg-gray-50">Mon profil</a>
                        <a href="<?= $rootPath ?>auth/logout.php?token=<?= urlencode($logoutToken) ?>" onclick="return confirm('Souhaitez-vous quitter la session ?')" class="block px-4 py-3 text-sm hover:bg-gray-50 text-red-600">Se déconnecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="h-20"></div>

<script>
// Toggle profile dropdown
(function(){
    const btn = document.getElementById('profileBtn');
    const dd = document.getElementById('profileDropdown');
    const root = document.getElementById('profileRoot');

    if (!btn || !dd) return;

    btn.addEventListener('click', (e) => {
        const isHidden = dd.classList.contains('hidden');
        dd.classList.toggle('hidden', !isHidden);
        btn.setAttribute('aria-expanded', String(!isHidden));
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!root.contains(e.target)) {
            dd.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>