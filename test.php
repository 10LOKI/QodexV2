<?php
require_once 'config/database.php';
if (isset($pdo)) {
    echo "✅ Connexion réussie ! L'objet PDO est prêt.";
} else {
    echo "❌ L'objet PDO n'est pas défini.";
}
?>