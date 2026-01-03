<?php
require_once '../../config/database.php';
require_once '../../classes/Etudiant/Quiz.php';

if(!isset($pdo) && isset($db))
{
    $pdo = $db;
}
if(!isset($pdo))
{
    die("There is an error");
}
$categoryId = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
$currentPage = 'quizzes';
if($categoryId && $categoryId > 0)
{
    $view = 'list_quizzes';
    $stmtCat = $pdo -> prepare("SELECT nom, description FROM categories WHERE id = ?");
    $stmtCat -> execute([$categoryId]);
    $category = $stmtCat -> fetch(PDO :: FETCH_ASSOC);

    if(!$category)
    {
        header("Location: quizzes.php");
        exit();
    }
    $quizzes = Quiz::getByCategory($pdo,$categoryId);
}
else
{
    $view = 'list_categories';
    $stmt = $pdo -> query("SELECT c.id,c.nom,c.description,COUNT(q.id) as nb_quiz FROM categories c LEFT JOIN quiz q ON c.id = q.categorie_id AND q.is_active =1 GROUP BY c.id");
    $categories = $stmt -> fetchAll(PDO::FETCH_ASSOC);
}
include_once('../partials/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examens | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .fade-in { animation: fadeInUp 0.6s ease forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#FAFAFA] text-black">

<?php if ($view === 'list_categories'): ?>
    <div class="pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="text-center mb-16">
                <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-2 block">Catalogue</span>
                <h1 class="text-4xl md:text-5xl font-black mb-4">Choisir une matière</h1>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Sélectionnez une catégorie ci-dessous pour accéder aux examens disponibles.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($categories as $cat): ?>
                <a href="quizzes.php?id=<?= $cat['id']; ?>" class="group bg-white border border-gray-100 rounded-[2rem] p-8 hover:border-black transition-all duration-500 cursor-pointer shadow-sm hover:shadow-xl overflow-hidden relative fade-in">
                    <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-gray-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-black rounded-2xl flex items-center justify-center mb-6 group-hover:rotate-6 transition-transform">
                            <i class="fas fa-book text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-black mb-3"><?= htmlspecialchars($cat['nom']); ?></h3>
                        <p class="text-gray-500 leading-relaxed mb-6 h-12 overflow-hidden text-sm">
                            <?= htmlspecialchars($cat['description']);?>
                        </p>
                        <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">
                                <i class="fas fa-layer-group mr-2"></i><?= $cat['nb_quiz']; ?> Quiz
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

<?php elseif ($view === 'list_quizzes'): ?>
    <div id="categoryQuizzes" class="student-section min-h-screen bg-[#FAFAFA]">
        <div class="bg-black text-white relative overflow-hidden">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-gray-800 rounded-full opacity-30 blur-[120px]"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
                <a href="quizzes.php" 
                   class="group flex items-center text-gray-400 hover:text-white transition-colors mb-8 text-sm font-bold uppercase tracking-widest">
                    <i class="fas fa-arrow-left mr-3 transform group-hover:-translate-x-2 transition-transform"></i>
                    Toutes les catégories
                </a>
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <span class="text-gray-500 text-xs font-black uppercase tracking-[0.3em] mb-2 block">Exploration</span>
                        <h1 class="text-5xl font-black mb-2 tracking-tighter"><?= htmlspecialchars($category['nom']); ?></h1>
                        <p class="text-gray-400 text-lg"><?= htmlspecialchars($category['description']); ?></p>
                    </div>
                    <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-4 flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center">
                            <i class="fas fa-layer-group text-black text-xl"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-xl"><?= count($quizzes); ?></p>
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-tighter">Quiz disponibles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if(empty($quizzes)) : ?>
                    <div class="col-span-3 text-center py-12">
                        <div class="inline-block p-4 rounded-full bg-gray-100 mb-4"><i class="fas fa-inbox text-2xl text-gray-400"></i></div>
                        <p class="text-gray-500 font-semibold">Aucun quiz disponible pour cette catégorie.</p>
                    </div>
                <?php else :?>
                    <?php foreach($quizzes as $quiz): ?>
                    <div class="group bg-white border border-gray-100 rounded-[2.5rem] p-2 hover:border-black transition-all duration-500 shadow-sm hover:shadow-2xl fade-in">
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-black transition-colors duration-300">
                                    <i class="fas fa-bolt text-black group-hover:text-white"></i>
                                </div>
                                <span class="px-4 py-1 bg-gray-100 text-[10px] font-black uppercase tracking-widest rounded-full">Quiz</span>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-black mb-3"><?= htmlspecialchars($quiz->getTitle()); ?></h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-8 h-10 overflow-hidden">
                                <?= htmlspecialchars($quiz->getDescription()); ?>
                            </p>
                            
                            <a href="take_quiz.php?id=<?= $quiz->getId() ?>" class="block text-center w-full bg-black text-white py-4 rounded-[2rem] font-bold text-sm tracking-widest uppercase hover:bg-gray-800 transition-all transform group-hover:scale-[0.98]">
                                Démarrer le test
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>