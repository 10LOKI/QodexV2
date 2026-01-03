<?php
require_once('../../config/database.php');

// On définit la page active pour la navbar
$currentPage = 'dashboard';
include_once('../partials/header.php'); 

try 
{
    $pdo = new PDO("mysql:host=". DB_HOST. ";dbname=". DB_NAME . ";charset=". DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $etudiant_id = $_SESSION['user_id'] ?? 2; 

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE etudiant_id = ?");
    $stmt->execute([$etudiant_id]);
    $total_completes = $stmt->fetchColumn();

    // 2. Stats : Moyenne
    $stmt = $pdo->prepare("SELECT AVG((score / total_questions) * 20) FROM results WHERE etudiant_id = ?");
    $stmt->execute([$etudiant_id]);
    $moyenne = round($stmt->fetchColumn(), 1) ?: 0;

    // 3. Stats : Taux de réussite
    $stmt = $pdo->prepare("
        SELECT (COUNT(CASE WHEN (score/total_questions) >= 0.5 THEN 1 END) * 100 / NULLIF(COUNT(*), 0)) 
        FROM results WHERE etudiant_id = ?
    ");
    $stmt->execute([$etudiant_id]);
    $taux_reussite = round($stmt->fetchColumn(), 0) ?: 0;

    // 4. Stats : Classement
    $stmt = $pdo->prepare("
        SELECT classement FROM (
            SELECT etudiant_id, RANK() OVER (ORDER BY SUM(score) DESC) as classement
            FROM results 
            GROUP BY etudiant_id
        ) AS ranks WHERE etudiant_id = ?
    ");
    $stmt->execute([$etudiant_id]);
    $classement = $stmt->fetchColumn() ?: '-';

    $stmt = $pdo->query("SELECT c.id, c.nom, c.description, COUNT(q.id) as nb_quiz FROM categories c LEFT JOIN quiz q ON c.id = q.categorie_id GROUP BY c.id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} 
catch (PDOException $e) 
{
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-black">

    <div id="studentSpace" class="pt-24 pb-12">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="relative overflow-hidden bg-black rounded-[2rem] p-8 md:p-12 shadow-2xl">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-gray-800 rounded-full opacity-20 blur-3xl"></div>
                
                <div class="relative z-10">
                    <span class="inline-block px-4 py-1.5 mb-4 text-xs font-bold tracking-widest text-gray-400 uppercase bg-white/10 rounded-full backdrop-blur-md">
                        Espace Étudiant
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">
                        Ravi de vous revoir.
                    </h1>
                    <p class="text-gray-400 text-lg max-w-xl">
                        Prêt à relever de nouveaux défis ? Explorez les catégories et améliorez votre classement.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                
                <?php
                $stats = [
                    ['Quiz Complétés', $total_completes, 'fa-check-circle', 'bg-white'],
                    ['Moyenne Générale', $moyenne . '/20', 'fa-star', 'bg-white'],
                    ['Taux de Réussite', $taux_reussite . '%', 'fa-chart-pie', 'bg-white'],
                    ['Votre Rang', '#' . $classement, 'fa-trophy', 'bg-black text-white']
                ];

                foreach($stats as $stat):
                    $isBlack = $stat[3] === 'bg-black text-white';
                ?>
                <div class="<?= $stat[3] ?> border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="<?= $isBlack ? 'text-gray-400' : 'text-gray-500' ?> text-xs font-bold uppercase tracking-wider mb-1"><?= $stat[0] ?></p>
                            <p class="text-3xl font-black"><?= $stat[1] ?></p>
                        </div>
                        <div class="<?= $isBlack ? 'bg-white/10' : 'bg-gray-50' ?> p-3 rounded-2xl">
                            <i class="fas <?= $stat[2] ?> <?= $isBlack ? 'text-white' : 'text-black' ?> text-xl"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-black tracking-tight">Catégories</h2>
                    <p class="text-gray-500 mt-1">Choisissez une discipline pour commencer le quiz</p>
                </div>
                <div class="h-px flex-grow mx-8 bg-gray-100 hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($categories as $cat): ?>
                <a href="quizzes.php?id=<?= $cat['id']; ?>" class="group bg-white border border-gray-100 rounded-[2rem] p-8 hover:border-black transition-all duration-500 cursor-pointer shadow-sm hover:shadow-xl overflow-hidden relative">
                    <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-gray-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-black rounded-2xl flex items-center justify-center mb-6 group-hover:rotate-6 transition-transform">
                            <i class="fas fa-terminal text-white text-xl"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-black mb-3"><?= htmlspecialchars($cat['nom']); ?></h3>
                        <p class="text-gray-500 leading-relaxed mb-6 h-12 overflow-hidden text-sm">
                            <?= htmlspecialchars($cat['description']);?>
                        </p>
                        
                        <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">
                                <i class="fas fa-layer-group mr-2"></i><?= $cat['nb_quiz']; ?> modules
                            </span>
                            <span class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center group-hover:bg-black group-hover:text-white transition-all">
                                <i class="fas fa-arrow-right text-sm"></i>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

        </div> 
    </div>

    <script>
        function showStudentSection(section, catName) {
            // Logique de redirection vers quizzes.php avec la catégorie
            window.location.href = 'quizzes.php?category=' + encodeURIComponent(catName);
        }
    </script>

</body>
</html>