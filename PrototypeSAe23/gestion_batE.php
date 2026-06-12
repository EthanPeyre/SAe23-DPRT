<?php
session_start();

if (!isset($_SESSION['auth_gest']) || $_SESSION['auth_gest'] !== TRUE) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Admin</title>
    <link rel="stylesheet" type="text/css" href="./styles/style.css" />
        <!-- Menu -->
    <header>
		 <nav>
			<ul>		
				<li><a href="index.html" >Accueil</a></li>
				<li><a href="login.php" >Se connecter</a></li>
				<li><a href="consult.html">Consultation</a></li>
				<li><a href="gestion.html">Gestion du projet</a></li>
		</ul> 
	</nav>
 </header>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['gestion']); ?> !</h1>
    <p>Tu es sur la page réservée a la gestion de base de données au niveau du batiment E.</p>

    <a href="logout.php">Se déconnecter</a>
</body>
</html>
