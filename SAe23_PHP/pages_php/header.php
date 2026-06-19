<?php
// header.php
// common header for every page
// adapted fo each session

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'SAE23 - IUT Blagnac'); ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <?php if (($current_page ?? '') === 'consultation'): ?>
    <meta http-equiv="refresh" content="30">
    <?php endif; ?>
</head>
<body>

<header>
    <h1>SAE23 – Monitoring IoT – IUT Blagnac</h1>
    <p>Département Réseaux et Télécommunications</p>
</header>

<nav>
    <ul>
        <li><a href="index.php" <?php if (($current_page ?? '') === 'accueil') echo 'class="active"'; ?>>Accueil</a></li>
        <li><a href="consultation.php" <?php if (($current_page ?? '') === 'consultation') echo 'class="active"'; ?>>Consultation</a></li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire'): ?>
        <li><a href="gestion.php" <?php if (($current_page ?? '') === 'gestion') echo 'class="active"'; ?>>Gestion</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li><a href="administration.php" <?php if (($current_page ?? '') === 'admin') echo 'class="active"'; ?>>Administration</a></li>
        <?php endif; ?>
        <li><a href="gestion_projet.php" <?php if (($current_page ?? '') === 'projet') echo 'class="active"'; ?>>Gestion de projet</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="logout.php">Déconnexion (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
        <?php else: ?>
        <li><a href="login.php" <?php if (($current_page ?? '') === 'login') echo 'class="active"'; ?>>Connexion</a></li>
        <?php endif; ?>
    </ul>
</nav>