<?php

require_once "src/JsonRepository.php";

$formationsRepo = new JsonRepository("data/formations.json");
$inscriptionsRepo = new JsonRepository("data/inscriptions.json");

// Mois en français
$mois = [
    "January" => "janvier",
    "February" => "février",
    "March" => "mars",
    "April" => "avril",
    "May" => "mai",
    "June" => "juin",
    "July" => "juillet",
    "August" => "août",
    "September" => "septembre",
    "October" => "octobre",
    "November" => "novembre",
    "December" => "décembre"
];

// CREATE (ajout formation)
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

// DELETE formation
if (isset($_GET["delete"])) {

    $formationsRepo->delete((int)$_GET["delete"]);

    header("Location: view-admin.php");
    exit;
}

// Charger données
$formations = $formationsRepo->all();
$inscriptions = $inscriptionsRepo->all();

// Trier par date DESC
usort($inscriptions, function($a, $b) {
    return strtotime($b["date"]) - strtotime($a["date"]);
});

// Mapping formations (id → titre)
$formationsMap = [];
foreach ($formations as $f) {
    $formationsMap[$f["id"]] = $f["titre"];
}
// CONFIRMER
if (isset($_GET["confirm"])) {

    $inscriptionsRepo->update((int)$_GET["confirm"], [
        "status" => "confirme"
    ]);

    header("Location: view-admin.php");
    exit;
}

// REFUSER
if (isset($_GET["refuse"])) {

    $inscriptionsRepo->update((int)$_GET["refuse"], [
        "status" => "refuse"
    ]);

    header("Location: view-admin.php");
    exit;
}

include "partials/header.php";

?>

<h2>Administration</h2>


<div class="card shadow-sm mb-4">
<div class="card-body">
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
</div>
</div>

<div class="card shadow-sm mb-4">
<div class="card-body">
<h3>Inscriptions</h3>

<table class="table">

<tr>
<th>Nom</th>
<th>Email</th>
<th>Rendez-vous</th>
<th>Formation</th>
<th>Status</th>
</tr>

<?php foreach($inscriptions as $i): ?>

<tr>

<td><?= htmlspecialchars($i["nom"]) ?></td>

<td><?= htmlspecialchars($i["email"]) ?></td>

<td>
<?php
// Format date FR
$dateFormattee = date("d F Y", strtotime($i["date"]));
$dateFormattee = strtr($dateFormattee, $mois);

// Format heure
$heure = $i["heure"] ?? "";
$heureFormattee = str_replace(":", "h", $heure);
$heureFormattee = str_replace("-", " à ", $heure);
?>

<?= htmlspecialchars(
    $heureFormattee 
        ? $dateFormattee . " — " . $heureFormattee 
        : $dateFormattee
) ?>
</td>

<td><?= htmlspecialchars($formationsMap[$i["formation_id"]] ?? "Inconnue") ?></td>

<td>

<?php
$status = $i["status"];

$color = match($status) {
    "en_attente" => "warning",
    "confirme" => "success",
    "refuse" => "danger",
    default => "secondary"
};
?>

<span class="badge bg-<?= $color ?>">
<?= htmlspecialchars($status) ?>
</span>

<br><br>

<?php if($status === "en_attente"): ?>

<a href="?confirm=<?= $i["id"] ?>" class="btn btn-success btn-sm">
Confirmer
</a>

<a href="?refuse=<?= $i["id"] ?>" class="btn btn-danger btn-sm">
Refuser
</a>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</table>
</div>
</div>

<div class="card shadow-sm mb-4">
<div class="card-body">
<h3>Ajouter un service</h3>

<form method="POST" class="mb-4">

<input name="titre" placeholder="Titre" class="form-control mb-2" required>

<input name="duree" placeholder="Durée" class="form-control mb-2" required>

<button class="btn btn-success">Ajouter</button>

</form>
</div>
</div>

<?php include "partials/footer.php"; ?>