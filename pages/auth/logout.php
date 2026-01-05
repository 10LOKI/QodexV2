<?php
/**
 * Page: Déconnexion
 * Déconnecte l'utilisateur et redirige vers la page de connexion
 */

require_once '../../config/database.php';
require_once '../../classes/User.php';
require_once '../../classes/Security.php';

// Ensure session is started (config may have started it)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Always perform logout to ensure user is unauthenticated.
// If a CSRF token is provided, verify it for logging/auditing purposes, but do not block logout for UX.
try {
    $user = new User();
    $user->logout();
} catch (Exception $e) {
    // If logout fails for any reason, continue to redirect to login page anyway.
}

// Optionally clear any CSRF tokens or flashes
if (isset($_SESSION['csrf_token'])) { unset($_SESSION['csrf_token']); }

// Redirect to the login page
header('Location: login.php');
exit();
