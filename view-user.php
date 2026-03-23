<?php

require_once "src/JsonRepository.php";

$formationsRepo = new JsonRepository("data/formations.json");
$inscriptionsRepo = new JsonRepository("data/inscriptions.json");

$id = $_GET["id"] ?? null;

$formation = $formationsRepo->find((int)$id);

if (!$formation) {
    die("Service introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $date = $_POST["date_rdv"];
    $heure = $_POST["heure_rdv"];

    $inscriptions = $inscriptionsRepo->all();
    $conflit = false;

    foreach ($inscriptions as $i) {

        if (
            $i["date"] === $date &&
            $i["heure"] === $heure
        ) {
            $conflit = true;
            break;
        }
    }

    if ($conflit) {
        $erreur = "Ce créneau est déjà réservé.";
    } else {

        $inscriptionsRepo->add([
            "nom" => $nom,
            "email" => $email,
            "date" => $date,
            "heure" => $heure, 
            "formation_id" => $formation["id"],
            "status" => "en_attente"
        ]);

        header("Location: view-user.php?id=".$formation["id"]."&success=1");
        exit;
    }
}

include "partials/header.php";

?>

<?php if(isset($erreur)): ?>

<div class="alert alert-danger">
<?= htmlspecialchars($erreur) ?>
</div>

<?php endif; ?>

<h2><?= htmlspecialchars($formation["titre"]) ?></h2>

<?php if(isset($_GET["success"])): ?>

<div class="alert alert-success">

Inscription enregistrée !

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Nom</label>
<input type="text" name="nom" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Date du rendez-vous</label>
<input type="date"
name="date_rdv"
class="form-control"
min="<?= date('Y-m-d') ?>"
required>
</div>

<div class="mb-3">
<label class="form-label">Heure du rendez-vous</label>

<select name="heure_rdv" class="form-control" required>

<option value="">Choisir une plage horaire</option>

<option value="09:00-10:00">09:00 - 10:00</option>
<option value="10:00-11:00">10:00 - 11:00</option>
<option value="11:00-12:00">11:00 - 12:00</option>
<option value="13:00-14:00">13:00 - 14:00</option>
<option value="14:00-15:00">14:00 - 15:00</option>
<option value="15:00-16:00">15:00 - 16:00</option>

</select>

</div>

<button class="btn btn-primary">
Envoyer la demande
</button>

</form>

<?php include "partials/footer.php"; ?>