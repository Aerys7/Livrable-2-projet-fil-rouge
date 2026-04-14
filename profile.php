<?php

session_start();

require_once "src/Database.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getConnection();

$userId = $_SESSION["user"]["id"];

$erreurs = [];
$success = "";

// Récupérer utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// MODIFICATION PROFIL
if (isset($_POST["update_profile"])) {

    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);

    if (empty($nom)) {
        $erreurs[] = "Nom requis.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }

    if (empty($erreurs)) {

        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $userId]);

        $success = "Profil mis à jour.";
    }
}

// MODIFICATION MOT DE PASSE
if (isset($_POST["update_password"])) {

    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if (strlen($password) < 6) {
        $erreurs[] = "Mot de passe trop court.";
    }

    if ($password !== $confirm) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($erreurs)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $userId]);

        $success = "Mot de passe mis à jour.";
    }
}

// Récupérer rendez-vous utilisateur
$stmt = $pdo->prepare("
    SELECT i.*, f.titre 
    FROM inscriptions i
    JOIN formations f ON i.formation_id = f.id
    WHERE i.user_id = ?
    ORDER BY i.date DESC
");
$stmt->execute([$userId]);
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "partials/header.php";
?>

<div class="container mt-4">

    <h2>Mon profil</h2>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-danger">
            <?php foreach ($erreurs as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- PROFIL -->
    <div class="card mb-4">
        <div class="card-body">

            <h5>Informations personnelles</h5>

            <form method="POST">

                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" name="nom"
                        value="<?= htmlspecialchars($user["nom"] ?? "") ?>"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email"
                        value="<?= htmlspecialchars($user["email"]) ?>"
                        class="form-control">
                </div>

                <button name="update_profile" class="btn btn-primary">
                    Mettre à jour
                </button>

            </form>

        </div>
    </div>

    <!-- MOT DE PASSE -->
    <div class="card mb-4">
        <div class="card-body">

            <h5>Modifier le mot de passe</h5>

            <form method="POST">

                <div class="mb-3">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Confirmer</label>
                    <input type="password" name="confirm" class="form-control">
                </div>

                <button name="update_password" class="btn btn-warning">
                    Changer le mot de passe
                </button>

            </form>

        </div>
    </div>

    <!-- RENDEZ-VOUS -->
    <div class="card">
        <div class="card-body">

            <h5>Mes rendez-vous</h5>

            <table class="table">

                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($inscriptions as $i): ?>

                    <tr>

                        <td><?= htmlspecialchars($i["date"]) ?></td>
                        <td><?= htmlspecialchars($i["heure"]) ?></td>
                        <td><?= htmlspecialchars($i["titre"]) ?></td>

                        <td>
                            <span class="badge bg-<?=
                                                    $i["status"] === "confirme" ? "success" : ($i["status"] === "refuse" ? "danger" : "warning")
                                                    ?>">
                                <?= htmlspecialchars($i["status"]) ?>
                            </span>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>
    </div>

</div>

<?php include "partials/footer.php"; ?>