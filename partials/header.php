<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Conseiller Financier</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

  <?php

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">

    <div class="container">

      <!-- LOGO -->
      <a class="navbar-brand fw-bold" href="index.php">
        💼 Conseiller Financier Fournier
      </a>

      <!-- BURGER MOBILE -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="nav">

        <ul class="navbar-nav ms-auto align-items-center">

          <li class="nav-item me-3">
            <a class="nav-link" href="index.php">Accueil</a>
          </li>

          <?php if (isset($_SESSION["user"])): ?>

            <li class="nav-item me-3">
              <a class="nav-link" href="profile.php">Mon profil</a>
            </li>

            <?php if ($_SESSION["user"]["role"] === "admin"): ?>
              <li class="nav-item me-3">
                <a class="nav-link text-warning" href="view-admin.php">Admin</a>
              </li>
            <?php endif; ?>

            <li class="nav-item">
              <a href="logout.php" class="btn btn-outline-light btn-sm">
                Déconnexion
              </a>
            </li>

          <?php else: ?>

            <li class="nav-item me-2">
              <a href="login.php" class="btn btn-outline-light btn-sm px-3">
                Connexion
              </a>
            </li>

            <li class="nav-item">
              <a href="register.php" class="btn btn-primary btn-sm px-3">
                Inscription
              </a>
            </li>

          <?php endif; ?>

        </ul>

      </div>

    </div>

  </nav>

  <div class="container mt-4">