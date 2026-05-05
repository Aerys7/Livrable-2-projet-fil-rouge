<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

require_once "src/Database.php";

$pdo = Database::getConnection();

$userId = $_SESSION["user"]["id"];
$id = $_GET["id"] ?? null;

// USER
$stmt = $pdo->prepare("SELECT nom, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// FORMATION
$stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
$stmt->execute([$id]);
$formation = $stmt->fetch();

if (!$formation) {
    die("Service introuvable.");
}

$erreurs = [];

// Pour UX : créneaux déjà pris selon la date sélectionnée
$heuresPrises = [];

if (!empty($_POST["date_rdv"])) {

    $stmt = $pdo->prepare("
        SELECT heure 
        FROM inscriptions
        WHERE date = ? AND status != 'refuse'
    ");

    $stmt->execute([$_POST["date_rdv"]]);

    $heuresPrises = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $date = $_POST["date_rdv"] ?? "";
    $heure = $_POST["heure_rdv"] ?? "";

    // VALIDATIONS

    if (empty($nom)) {
        $erreurs["nom"] = "Le nom est obligatoire.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs["email"] = "Email invalide.";
    }

    if (empty($date)) {
        $erreurs["date"] = "Date obligatoire.";
    } elseif ($date < date("Y-m-d")) {
        $erreurs["date"] = "Date invalide.";
    }

    if (empty($heure)) {
        $erreurs["heure"] = "Choisir une heure.";
    }

    // Vérification conflit
    if (empty($erreurs)) {

        $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM inscriptions
    WHERE date = ? AND heure = ? AND status != 'refuse'
");

        $stmt->execute([$date, $heure]);

        $conflit = $stmt->fetchColumn() > 0;
    }

    // Enregistrement
    if (empty($erreurs)) {

        $stmt = $pdo->prepare("
    INSERT INTO inscriptions 
    (nom, email, date, heure, formation_id, user_id, status)
    VALUES (?, ?, ?, ?, ?, ?, 'en_attente')
");

        $stmt->execute([
            $nom,
            $email,
            $date,
            $heure,
            $formation["id"],
            $_SESSION["user"]["id"]
        ]);

        header("Location: view-user.php?id=" . $formation["id"] . "&success=1");
        exit;
    }
}

include "partials/header.php";
?>

<h2><?= htmlspecialchars($formation["titre"]) ?></h2>

<?php if (isset($_GET["success"])): ?>
    <div class="alert alert-success">Inscription enregistrée !</div>
<?php endif; ?>

<form method="POST">

    <!-- NOM -->
    <div class="mb-3">
        <label class="form-label">Nom</label>

        <input type="text" name="nom"
            value="<?= htmlspecialchars($_POST["nom"] ?? $user["nom"] ?? "") ?>"
            class="form-control bg-light text-muted <?= isset($erreurs["nom"]) ? 'is-invalid' : '' ?>"
            readonly>

        <?php if (isset($erreurs["nom"])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($erreurs["nom"]) ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- EMAIL -->
    <div class="mb-3">
        <label class="form-label">Email</label>

        <input type="email" name="email"
            value="<?= htmlspecialchars($_POST["email"] ?? $user["email"] ?? "") ?>"
            class="form-control bg-light text-muted <?= isset($erreurs["email"]) ? 'is-invalid' : '' ?>"
            readonly>

        <?php if (isset($erreurs["email"])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($erreurs["email"]) ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- DATE -->
    <div class="mb-3">
        <label class="form-label">Date du rendez-vous</label>

        <input type="date" name="date_rdv" id="date_rdv"
            value="<?= htmlspecialchars($_POST["date_rdv"] ?? "") ?>"
            class="form-control <?= isset($erreurs["date"]) ? 'is-invalid' : '' ?>"
            min="<?= date('Y-m-d') ?>">

        <?php if (isset($erreurs["date"])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($erreurs["date"]) ?>
            </div>
        <?php endif; ?>

        <small class="text-muted">
            Choisissez une date future.
        </small>

    </div>

    <!-- HEURE -->
    <div class="mb-3">
        <label class="form-label">Heure du rendez-vous</label>

        <select name="heure_rdv" id="heure_rdv"
            class="form-control <?= isset($erreurs["heure"]) ? 'is-invalid' : '' ?>">

            <option value="">Choisir une plage horaire</option>

            <?php
            $creneaux = [
                "09:00-10:00",
                "10:00-11:00",
                "11:00-12:00",
                "13:00-14:00",
                "14:00-15:00",
                "15:00-16:00"
            ];

            foreach ($creneaux as $c):
                $disabled = in_array($c, $heuresPrises);
            ?>

                <option value="<?= $c ?>"
                    <?= $disabled ? "disabled" : "" ?>
                    <?= (($_POST["heure_rdv"] ?? "") === $c) ? "selected" : "" ?>>

                    <?= str_replace("-", " - ", $c) ?>
                    <?= $disabled ? " (Complet)" : "" ?>

                </option>

            <?php endforeach; ?>

        </select>

        <?php if (isset($erreurs["heure"])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($erreurs["heure"]) ?>
            </div>
        <?php endif; ?>

        <small class="text-muted">
            Les créneaux complets sont désactivés automatiquement.
        </small>

    </div>

    <button class="btn btn-primary">
        Envoyer la demande
    </button>

</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const dateInput = document.getElementById("date_rdv");
        const selectHeure = document.getElementById("heure_rdv");

        const creneaux = [
            "09:00-10:00",
            "10:00-11:00",
            "11:00-12:00",
            "13:00-14:00",
            "14:00-15:00",
            "15:00-16:00"
        ];

        dateInput.addEventListener("change", function() {

            const date = this.value;

            if (!date) return;

            selectHeure.innerHTML = "<option>Chargement...</option>";

            fetch("get-heures.php?date=" + date)
                .then(res => res.json())
                .then(heuresPrises => {

                    selectHeure.innerHTML = '<option value="">Choisir une plage horaire</option>';

                    creneaux.forEach(c => {

                        const option = document.createElement("option");
                        option.value = c;

                        let label = c.replace("-", " - ");

                        if (heuresPrises.includes(c)) {
                            option.disabled = true;
                            label += " (Complet)";
                        }

                        option.textContent = label;

                        selectHeure.appendChild(option);
                    });

                })
                .catch(err => console.error("Erreur AJAX:", err));

        });

    });
</script>

<?php include "partials/footer.php"; ?>