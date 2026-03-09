<?php

require_once "src/JsonRepository.php";

$formationsRepo = new JsonRepository("data/formations.json");
$inscriptionsRepo = new JsonRepository("data/inscriptions.json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $formationsRepo->add([
        "titre" => $_POST["titre"],
        "duree" => $_POST["duree"],
        "prix" => 0,
        "actif" => true
    ]);

    header("Location: view-admin.php");
    exit;
}

if (isset($_GET["delete"])) {

    $formationsRepo->delete((int)$_GET["delete"]);

    header("Location: view-admin.php");
    exit;
}

$formations = $formationsRepo->all();
$inscriptions = $inscriptionsRepo->all();

include "partials/header.php";

?>

<h2>Administration</h2>

<h3>Ajouter un service</h3>

<form method="POST" class="mb-4">

<input name="titre" placeholder="Titre" class="form-control mb-2" required>

<input name="duree" placeholder="Durée" class="form-control mb-2" required>

<button class="btn btn-success">Ajouter</button>

</form>

<h3>Services</h3>

<table class="table">

<tr>

<th>Titre</th>

<th>Durée</th>

<th>Action</th>

</tr>

<?php foreach($formations as $f): ?>

<tr>

<td><?= htmlspecialchars($f["titre"]) ?></td>

<td><?= htmlspecialchars($f["duree"]) ?></td>

<td>

<a class="btn btn-danger btn-sm"

href="?delete=<?= $f["id"] ?>"
onclick="return confirm('Supprimer ce service ?')">

Supprimer

</a>

</td>

</tr>

<?php endforeach; ?>

</table>

<h3>Inscriptions</h3>

<table class="table">

<tr>

<th>Nom</th>

<th>Email</th>

<th>Date</th>

<th>Formation</th>

<th>Status</th>

</tr>

<?php $formationsMap = [];

foreach ($formations as $f) {
    $formationsMap[$f["id"]] = $f["titre"]; 
}

foreach($inscriptions as $i): ?>

<tr>

<td><?= htmlspecialchars($i["nom"]) ?></td>

<td><?= htmlspecialchars($i["email"]) ?></td>

<td><?= htmlspecialchars($i["date"]) ?></td>

<td><?= htmlspecialchars($formationsMap[$i["formation_id"]] ?? "Inconnue") ?></td>

<td><?= htmlspecialchars($i["status"]) ?></td>

</tr>

<?php endforeach; ?>

</table>

<?php include "partials/footer.php"; ?>