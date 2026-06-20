<?php
	// Session start
	session_start();
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
	   <meta charset="UTF-8" />
	   <title>Identification erron&eacute;e</title>
	   <link rel="stylesheet" type="text/css" href="./styles/style.css" />
	 </head>

	<body>
		
		<!-- Menu -->
	<header>
		 <nav>
			<ul>		
				<li><a href="index.html" >Accueil</a></li>
				<li><a href="login.php" >Se connecter</a></li>
				<li><a href="#.html">Consultation</a></li>
				<li><a href="gestion.html">Gestion du projet</a></li>
			</ul> 
		</nav>
 	</header>
 
		<!-- For a dynamic header display -->
		<?php 
			$_SESSION = array(); // Session Table Reset
			session_destroy();   // Session delete
			unset($_SESSION);    // Session table destruction

		?>
		<section>
			<p>
				<br />
				<em><strong>Administration de la base : Acc&egrave;s limit&eacute; aux personnes autoris&eacute;es</strong></em>
				<br />
			</p>
			<br />
			<p class="erreur">Mot de passe non saisi ou erron&eacute; !!!</p>
			<br />
			<hr />
		</section>

	</body>
</html>
