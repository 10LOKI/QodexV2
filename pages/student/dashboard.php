<?php
require_once('../../config/database.php');
include_once('../partials/header.php'); 
try 
{
    $pdo = new PDO("mysql:host=". DB_HOST. ";dbname=". DB_NAME . ";charset=". DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $etudiant_id = $_SESSION['user_id'] ?? 2; 

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE etudiant_id = ?");
    $stmt->execute([$etudiant_id]);
    $total_completes = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT AVG((score / total_questions) * 20) FROM results WHERE etudiant_id = ?");
    $stmt->execute([$etudiant_id]);
    $moyenne = round($stmt->fetchColumn(), 1);

    $stmt = $pdo->prepare("
        SELECT (COUNT(CASE WHEN (score/total_questions) >= 0.5 THEN 1 END) * 100 / NULLIF(COUNT(*), 0)) 
        FROM results WHERE etudiant_id = ?
    ");
    $stmt->execute([$etudiant_id]);
    $taux_reussite = round($stmt->fetchColumn(), 0) ?: 0;

    $stmt = $pdo->prepare("
        SELECT classement FROM (
            SELECT etudiant_id, RANK() OVER (ORDER BY SUM(score) DESC) as classement
            FROM results 
            GROUP BY etudiant_id
        ) AS ranks WHERE etudiant_id = ?
    ");
    $stmt->execute([$etudiant_id]);
    $classement = $stmt->fetchColumn() ?: '-';

    $stmt = $pdo->query("SELECT c.nom, c.description, COUNT(q.id) as nb_quiz FROM categories c LEFT JOIN quiz q ON c.id = q.categorie_id GROUP BY c.id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) 
{
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <div id="studentSpace" class="pt-16">
        
        <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h1 class="text-4xl font-bold mb-4">Espace Étudiant</h1>
                <p class="text-xl text-green-100">Passez des quiz et suivez votre progression</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Quiz Complétés</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $total_completes; ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Moyenne</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $moyenne; ?>/20</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-star text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Taux Réussite</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $taux_reussite; ?>%</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Classement</p>
                            <p class="text-3xl font-bold text-gray-900">#<?php echo $classement; ?></p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-trophy text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mb-12 border-gray-200">

            <h2 class="text-3xl font-bold text-gray-900 mb-8">Catégories Disponibles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($categories as $cat): ?>
                <div onclick="showStudentSection('categoryQuizzes', '<?php echo addslashes($cat['nom']); ?>')" 
                     class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white">
                        <i class="fas fa-code text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($cat['nom']); ?></h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i><?php echo $cat['nb_quiz']; ?> quiz</span>
                            <span class="text-blue-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div> </div> </body>
</html>