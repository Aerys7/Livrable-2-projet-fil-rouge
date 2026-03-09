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

    $rdv = $_POST["rdv_datetime"];

    $inscriptionsRepo->add([
    "nom" => $nom,
    "email" => $email,
    "rdv_datetime" => $rdv,
    "date" => date("Y-m-d"),
    "formation_id" => $formation["id"],
    "status" => "en_attente"
]);
    header("Location: view-user.php?id=".$formation["id"]."&success=1");
    exit;
}

include "partials/header.php";

?>

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
<label class="form-label">Date et heure du rendez-vous</label>

<input
type="datetime-local"
name="rdv_datetime"
class="form-control"
required>

</div>

<button class="btn btn-primary">

Envoyer la demande

</button>

</form>

<?php include "partials/footer.php"; ?>