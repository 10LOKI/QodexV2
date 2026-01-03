<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'qodexv2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    $pdo = $db; // On définit $pdo ici directement pour être tranquille
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

// CONFIGURATION UNIQUE DE LA SESSION
if (session_status() === PHP_SESSION_NONE) {
    // On définit les paramètres AVANT de démarrer
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Mettre à 1 en production (HTTPS)
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 1800);
    session_set_cookie_params(1800);
    
    session_start();
}

// Sécurité : Régénération de l'ID
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}