<?php

require_once "src/Database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = Database::getConnection();

require_once "src/Database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = Database::getConnection();

$stmt = $pdo->query("
    SELECT i.*, f.titre 
    FROM inscriptions i
    JOIN formations f ON i.formation_id = f.id
    ORDER BY i.date DESC
");

$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT * FROM formations
    ORDER BY id DESC
");

$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

require_once "src/FormationRepository.php";
require_once "src/InscriptionRepository.php";

// ACTIONS STATUS
if (isset($_GET["confirm"])) {

    $id = (int)$_GET["confirm"];

    $stmt = $pdo->prepare("
        UPDATE inscriptions 
        SET status = 'confirme' 
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: view-admin.php");
    exit;
}

if (isset($_GET["refuse"])) {

    $id = (int)$_GET["refuse"];

    $stmt = $pdo->prepare("
        UPDATE inscriptions 
        SET status = 'refuse' 
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: view-admin.php");
    exit;
}

// FILTRE
$filtre = $_GET["status"] ?? "all";

if ($filtre !== "all") {
    $inscriptions = array_filter($inscriptions, fn($i) => $i["status"] === $filtre);
}

//TRIAGE
usort($inscriptions, function ($a, $b) {

    // Combiner date + heure début
    $dateA = $a["date"] . " " . explode("-", $a["heure"])[0];
    $dateB = $b["date"] . " " . explode("-", $b["heure"])[0];

    return strtotime($dateA) - strtotime($dateB);
});

// STATS
$total = count($inscriptions);
$attente = count(array_filter($inscriptions, fn($i) => $i["status"] === "en_attente"));
$confirme = count(array_filter($inscriptions, fn($i) => $i["status"] === "confirme"));
$refuse = count(array_filter($inscriptions, fn($i) => $i["status"] === "refuse"));

include "partials/header.php";
?>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4 class="mb-4">Admin</h4>

            <a href="#" class="d-block text-white mb-2">Dashboard</a>
            <a href="#services" class="d-block text-white mb-2">Services</a>
            <a href="#inscriptions" class="d-block text-white">Inscriptions</a>

        </div>

        <!-- CONTENU -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">
                Dashboard
                <span class="badge bg-secondary">Administrateur</span>
            </h2>

            <!-- STATS -->
            <div class="row mb-4 text-center">

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h5>Total</h5>
                        <h3><?= $total ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h5>En attente</h5>
                        <h3><?= $attente ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h5>Confirmés</h5>
                        <h3><?= $confirme ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h5>Refusés</h5>
                        <h3><?= $refuse ?></h3>
                    </div>
                </div>

            </div>

            <!-- AJOUT SERVICE -->
            <div id="services" class="card shadow-sm mb-4">
                <div class="card-body">

                    <h4>Ajouter un service</h4>

                    <form method="POST" class="row g-2">

                        <div class="col-md-5">
                            <input name="titre" class="form-control" placeholder="Titre" required>
                        </div>

                        <div class="col-md-5">
                            <input name="duree" class="form-control" placeholder="Durée" required>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-success w-100">Ajouter</button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- TABLE SERVICES -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <h4>Services</h4>

                    <table class="table table-hover align-middle">

                        <tr>
                            <th>Titre</th>
                            <th>Durée</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach ($formations as $f): ?>

                            <tr>
                                <td><?= htmlspecialchars($f["titre"]) ?></td>
                                <td><?= htmlspecialchars($f["duree"]) ?></td>

                                <td>
                                    <a href="?delete=<?= $f["id"] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Supprimer ?')">
                                        Supprimer
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>
            </div>

            <!-- FILTRE -->
            <div id="inscriptions" class="mb-3">

                <a href="?status=all" class="btn btn-sm <?= ($filtre == "all") ? "btn-dark" : "btn-secondary" ?>">Tous</a>
                <a href="?status=en_attente" class="btn btn-sm <?= ($filtre == "en_attente") ? "btn-dark" : "btn-warning" ?>">En attente</a>
                <a href="?status=confirme" class="btn btn-sm <?= ($filtre == "confirme") ? "btn-dark" : "btn-success" ?>">Confirmés</a>
                <a href="?status=refuse" class="btn btn-sm <?= ($filtre == "refuse") ? "btn-dark" : "btn-danger" ?>">Refusés</a>

            </div>

            <!-- TABLE INSCRIPTIONS -->
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4>Inscriptions</h4>

                    <table class="table table-hover align-middle">

                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach ($inscriptions as $i): ?>

                            <tr>

                                <td><?= htmlspecialchars($i["nom"]) ?></td>
                                <td><?= htmlspecialchars($i["email"]) ?></td>
                                <td><?= htmlspecialchars($i["date"]) ?></td>
                                <td><?= htmlspecialchars($i["heure"] ?? "") ?></td>

                                <td>
                                    <?= htmlspecialchars($i["titre"] ?? "Inconnu") ?>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $i["status"] === "confirme" ? "success" : ($i["status"] === "refuse" ? "danger" : "warning") ?>">
                                        <?= htmlspecialchars($i["status"]) ?>
                                    </span>
                                </td>

                                <td>

                                    <?php if ($i["status"] === "en_attente"): ?>

                                        <a href="?confirm=<?= $i["id"] ?>" class="btn btn-success btn-sm">✔</a>
                                        <a href="?refuse=<?= $i["id"] ?>" class="btn btn-danger btn-sm">✖</a>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>
            </div>

        </div>

    </div>

</div>

include "partials/header.php";

<?php include "partials/footer.php"; ?>