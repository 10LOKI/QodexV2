<?php
// pages/student/profile.php
require_once '../../config/database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userNom = $_SESSION['user_nom'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';

$currentPage = 'profile';
include_once('../partials/header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FAFAFA] text-black min-h-screen">
    <div class="max-w-3xl mx-auto px-6 py-20">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h1 class="text-2xl font-black mb-4">Mon profil</h1>
            <p class="text-gray-500 mb-6">Voici les informations de votre compte.</p>

            <div class="grid grid-cols-1 gap-4">
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="text-xs text-gray-400 uppercase font-bold">Nom</div>
                    <div class="text-lg font-bold"><?= htmlspecialchars($userNom) ?></div>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="text-xs text-gray-400 uppercase font-bold">Email</div>
                    <div class="text-lg font-bold"><?= htmlspecialchars($userEmail) ?></div>
                </div>
            </div>

            <div class="mt-6">
                <a href="dashboard.php" class="inline-block bg-black text-white px-6 py-3 rounded-xl font-bold">Retour</a>
            </div>
        </div>
    </div>
</body>
</html>