<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Admin</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['login']); ?> !</h1>
    <p>Tu es sur la page réservée aux administrateurs.</p>

    <a href="logout.php">Se déconnecter</a>
</body>
</html>