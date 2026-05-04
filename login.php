<?php

session_start();

require_once "src/Database.php";

$pdo = Database::getConnection();

$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // VALIDATION
    if (empty($email)) {
        $erreurs[] = "Email requis.";
    }

    if (empty($password)) {
        $erreurs[] = "Mot de passe requis.";
    }

    // Vérification en base
    if (empty($erreurs)) {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user["password"])) {
            $erreurs[] = "Identifiants invalides.";
        }
    }

    // Connexion réussie
    if (empty($erreurs)) {

        $_SESSION["user"] = [
            "id" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];

        // Redirection selon rôle
        if ($user["role"] === "admin") {
            header("Location: view-admin.php");
        } else {
            header("Location: profile.php");
        }

        exit;
    }
}

include "partials/header.php";
?>

<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mb-4 text-center">Connexion</h2>

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
            <label class="form-label">Email</label>
            <input type="email" name="email"
                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password"
                class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">
            Se connecter
        </button>

        <div class="text-center mt-3">
            <a href="register.php" class="btn btn-outline-secondary w-100">
                Créer un compte
            </a>
        </div>

    </form>

</div>

<?php include "partials/footer.php"; ?>