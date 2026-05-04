<?php

session_start();
require_once "src/Database.php";

$pdo = Database::getConnection();

$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";

    // VALIDATION
    if (empty($nom)) {
        $erreurs[] = "Nom requis.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }

    if (strlen($password) < 6) {
        $erreurs[] = "Mot de passe trop court (6 caractères min).";
    }

    if ($password !== $confirm) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si email existe déjà
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erreurs[] = "Cet email est déjà utilisé.";
        }
    }

    // INSERTION
    if (empty($erreurs)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (nom, email, password, role)
            VALUES (?, ?, ?, 'user')
        ");

        $stmt->execute([$nom, $email, $hash]);

        // auto login
        $_SESSION["user"] = [
            "id" => $pdo->lastInsertId(),
            "email" => $email,
            "role" => "user"
        ];

        header("Location: profile.php");
        exit;
    }
}

include "partials/header.php";
?>

<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mb-4 text-center">Inscription</h2>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control"
                value="<?= htmlspecialchars($_POST["nom"] ?? "") ?>">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        </div>

        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Confirmer mot de passe</label>
            <input type="password" name="confirm" class="form-control">
        </div>

        <button class="btn btn-success w-100">
            Créer un compte
        </button>

    </form>

    <div class="text-center mt-3">
        <a href="login.php">Déjà un compte ? Se connecter</a>
    </div>

</div>

<?php include "partials/footer.php"; ?>