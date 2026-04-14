<?php

require_once "src/FormationRepository.php";
require_once "src/InscriptionRepository.php";

$formationsRepo = new FormationRepository();
$inscriptionsRepo = new InscriptionRepository();

$id = $_GET["id"] ?? null;
$formation = $formationsRepo->find((int)$id);

if (!$formation) {
    die("Service introuvable.");
}

$erreurs = [];

// Pour UX : créneaux déjà pris selon la date sélectionnée
$heuresPrises = [];

if (!empty($_POST["date_rdv"])) {
    $inscriptions = $inscriptionsRepo->all();

    foreach ($inscriptions as $i) {
        if (
            $i["date"] === $_POST["date_rdv"] &&
            $i["status"] !== "refuse"
        ) {
            $heuresPrises[] = $i["heure"];
        }
    }
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

        $inscriptions = $inscriptionsRepo->all();

        foreach ($inscriptions as $i) {
            if (
                $i["date"] === $date &&
                $i["heure"] === $heure &&
                $i["status"] !== "refuse"
            ) {
                $erreurs["heure"] = "Ce créneau est déjà réservé.";
                break;
            }
        }
    }

    // Enregistrement
    if (empty($erreurs)) {

        $inscriptionsRepo->add([
            "nom" => $nom,
            "email" => $email,
            "date" => $date,
            "heure" => $heure,
            "formation_id" => $formation["id"],
            "status" => "en_attente",
            "user_id" => $_SESSION["user"]["id"]
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
            value="<?= htmlspecialchars($_POST["nom"] ?? "") ?>"
            class="form-control <?= isset($erreurs["nom"]) ? 'is-invalid' : '' ?>">

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
            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
            class="form-control <?= isset($erreurs["email"]) ? 'is-invalid' : '' ?>">

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