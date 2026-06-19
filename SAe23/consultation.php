<!DOCTYPE html>
 <html lang="fr">
  <head>
   <meta charset="UTF-8" />
   <title>Consultation</title>
   <link rel="stylesheet" type="text/css" href="./styles/style.css" />
    <!-- Menu -->
    <header>
		 <nav>
		 <ul> 
				<li><a href="index.html" >Accueil</a></li>
				<li><a href="consultation.php">Consultation</a></li>
				<li><a href="MentionsLegales.html">Mention légales</a></li>
				<li><a href="gestion_projet.html">Gestion du projet</a></li>
				<li><a href="login.php" >Se connecter</a></li>
		</ul> 
	</nav>
 </header>  
 
  </head>
  <body>
   
    <div><h1><br />Consultation des données<br /><br /></h1></div>
    <hr/>
	<h2>Vue d'ensemble de l'ensemble du parc de capteurs</h2>
        <p>Ce tableau répertorie la dernière valeur reçue pour l intégralité des modules IoT déployés.</p>

    <section>
			<table class="centre">
				<thead>
					<tr>
						<th>B&acirc;timent</th>
						<th>Salle</th>
						<th>Capteur</th>
						<th>Type</th>
						<th>Derni&egrave;re valeur</th>
						<th>Unit&eacute;</th>
						<th>Date</th>
						<th>Heure</th>
					</tr>
				</thead>
				<tbody>
					<?php
						/* Acces a la base */
						include ("mysql.php");
 
						/*
						 * Pour chaque capteur, on recupere la derniere mesure enregistree
						 * (la mesure ayant la date/horaire la plus recente), ainsi que
						 * les informations de la salle et du batiment correspondants.
						 */
						$requete = "
							SELECT b.nom_bat, s.nom_salle, c.nom_capt, c.type_capt,
								c.unite, m.valeur, m.date, m.horaire
							FROM capteur c
							INNER JOIN salle s ON c.nom_salle = s.nom_salle
							INNER JOIN batiment b ON s.id_bat = b.id_bat
							INNER JOIN mesure m ON m.nom_capt = c.nom_capt
							WHERE m.id_mes = (
								SELECT m2.id_mes
								FROM mesure m2
								WHERE m2.nom_capt = c.nom_capt
								ORDER BY m2.date DESC, m2.horaire DESC
								LIMIT 1
							)
							ORDER BY b.nom_bat, s.nom_salle, c.nom_capt
						";
 
						$resultat = mysqli_query($id_bd, $requete)
							or die("Execution de la requete impossible : $requete");
 
						while ($ligne = mysqli_fetch_array($resultat))
						{
							extract($ligne);
							echo "<tr>";
							echo "<td>$nom_bat</td>";
							echo "<td>$nom_salle</td>";
							echo "<td>$nom_capt</td>";
							echo "<td>$type_capt</td>";
							echo "<td>$valeur</td>";
							echo "<td>$unite</td>";
							echo "<td>$date</td>";
							echo "<td>$horaire</td>";
							echo "</tr>";
						}
 
						mysqli_close($id_bd);
					?>
   </header>
   <footer>
    <p><a href="index.html">Acceuil</a></p>
    <p><a href="login.php">Acc&egrave;s limit&eacute; : Administration de la base de donn&eacute;es</a></p>
   </footer>
  </body>
</html>
