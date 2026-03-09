<?php

require_once "src/JsonRepository.php";

$repo = new JsonRepository("data/formations.json");

$formations = $repo->all();

include "partials/header.php";

?>

<h1 class="mb-4">Nos services financiers</h1>

<div class="row">

<?php foreach($formations as $f): ?>

<div class="col-md-4 mb-4">

<div class="card h-100">

<div class="card-body">

<h5 class="card-title">

<?= htmlspecialchars($f["titre"]) ?>

</h5>

<p>Durée : <?= htmlspecialchars($f["duree"]) ?></p>

<a class="btn btn-primary"

href="view-user.php?id=<?= $f["id"] ?>">

Prendre rendez-vous

</a>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

<?php include "partials/footer.php"; ?>