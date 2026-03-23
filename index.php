<?php

require_once "src/JsonRepository.php";

$repo = new JsonRepository("data/formations.json");

$formations = $repo->all();

include "partials/header.php";

?>

<div class="bg-light p-5 rounded text-center mb-5">
  <h1 class="fw-bold">Prenez rendez-vous avec notre conseiller</h1>
  <p class="lead">Des services adaptés à vos besoins financiers</p>
</div>

<div class="container">

<h2 class="mb-4 text-center">Nos services</h2>

<div class="row g-4">

<?php foreach($formations as $f): ?>

<div class="col-md-6 col-lg-4">

<div class="card h-100 shadow-sm border-0">

<img src="assets/img/default.jpg"
     class="card-img-top"
     style="height: 180px; object-fit: cover;">

<div class="card-body text-center">

<h5 class="card-title fw-bold">
<?= htmlspecialchars($f["titre"]) ?>
</h5>

<p class="text-muted">
Durée : <?= htmlspecialchars($f["duree"]) ?>
</p>

<a href="view-user.php?id=<?= $f["id"] ?>"
class="btn btn-primary mt-2">

Prendre rendez-vous

</a>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<div class="bg-light text-center py-5 mt-5">

  <h3 class="fw-bold">Besoin d’un accompagnement ?</h3>

  <p class="text-muted">
    Prenez rendez-vous dès maintenant avec un expert.
  </p>

  <a href="#"
     class="btn btn-primary">
    Voir les services
  </a>

</div>

<?php include "partials/footer.php"; ?>