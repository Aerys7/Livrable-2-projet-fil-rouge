<?php

session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session
session_destroy();

// Optionnel : supprimer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirection vers la page d'accueil
header("Location: index.php");
exit;
