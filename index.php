<?php

require_once "src/JsonRepository.php";

$repo = new JsonRepository("data/formations.json");

$formations = $repo->all();

// Mapping des images selon le service
$images = [
    "Assurance vie" => "assets/img/assurance-vie.jpg",
    "Assurance habitation" => "assets/img/assurance-habitation.jpg",
    "Planification financière" => "assets/img/planification-financiere.jpg",
    "Épargne et retraite" => "assets/img/epargne-retraite.jpg",
    "Analyse budgétaire" => "assets/img/analyse-budgetaire.jpg",
    "Assurance invalidité" => "assets/img/assurance-invalidite.jpg"
];

include "partials/header.php";

?>

<!-- HERO SECTION -->
<div class="bg-light text-black text-center py-5 mb-5">
  <div class="container">
    <h1 class="fw-bold">Prenez rendez-vous avec notre conseiller</h1>
    <p class="lead">Des services adaptés à vos besoins financiers</p>
  </div>
</div>

<!-- SERVICES -->
<div class="container">

<h2 class="mb-4 text-center">Nos services</h2>

<div class="row g-4">

<?php foreach($formations as $f): ?>

<?php 
$image = $images[$f["titre"]] ?? "assets/img/default.jpg";
?>

<div class="col-md-6 col-lg-4">

<div class="card h-100 shadow-sm border-0">

<img src="<?= $image ?>"
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

<!-- CALL TO ACTION -->
<div class="bg-light text-center py-5 mt-5">

  <h3 class="fw-bold">Besoin d’un accompagnement ?</h3>

  <p class="text-muted">
     Appellez directement le conseiller
  </p>

 <a href="tel:+14185551234" class="btn btn-primary mt-2">
📞 +1 (418) 555-1234
  </a>

</div>

<?php include "partials/footer.php"; ?>