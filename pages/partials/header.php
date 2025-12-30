<?php
/**
 * PARTIAL: Navigation Étudiant
 */

// 1. Calculer les initiales de manière sécurisée
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$nameParts = explode(' ', trim($userName));
$firstLetter = mb_substr($nameParts[0], 0, 1);
$lastLetter = (count($nameParts) > 1) ? mb_substr(end($nameParts), 0, 1) : '';
$initials = strtoupper($firstLetter . $lastLetter);

// 2. Gestion propre des chemins
// Si vous incluez ce fichier depuis /pages/student/dashboard.php
// Le chemin vers la racine est ../../
$rootPath = "../../"; 

// 3. Définir la page active pour le menu (si non défini dans la page parente)
$currentPage = $currentPage ?? 'dashboard';
?>

<nav class="bg-white shadow-lg fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-graduation-cap text-3xl text-green-600"></i>
                    <span class="ml-2 text-2xl font-bold text-gray-900">Qodex</span>
                    <span class="ml-3 px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Étudiant</span>
                </div>
                
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="dashboard.php" 
                       class="<?= $currentPage === 'dashboard' ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-home mr-2"></i>Tableau de bord
                    </a>
                    
                    <a href="history.php" 
                       class="<?= $currentPage === 'history' ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-history mr-2"></i>Mon Historique
                    </a>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-right">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($userName) ?></div>
                        <div class="text-xs text-gray-500 text-uppercase">Session Étudiant</div>
                    </div>
                    
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-semibold shadow-inner">
                        <?= $initials ?>
                    </div>
                    
                    <a href="<?= $rootPath ?>auth/logout.php" 
                       onclick="return confirm('Voulez-vous vous déconnecter ?')"
                       class="text-gray-400 hover:text-red-600 transition-colors" title="Déconnexion">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>