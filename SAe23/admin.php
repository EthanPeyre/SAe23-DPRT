<?php
session_start();

// Checks if the user's identity is comptible (administrator in this case)
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== TRUE) {
    header("Location: login.php");
    exit();
}

/* Give access to the data base */
include("mysql.php");

/*
 * Form action choice treatment (Add/Delete).
 * We treat the data before generating the HTML page, to redirect accordingly after each action is made
 * (to avoid having doubles once the refresh is acted out).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']))
{
	$action = $_POST['action'];

	switch ($action)
	{
		case 'ajouter_batiment':
			/* mysqli_real_escape_string : This function is used to create a legal SQL string that you can use in an SQL statement. 
			The given string is encoded to produce an escaped SQL string, 
			taking into account the current character set of the connection. */
			$nom_bat = mysqli_real_escape_string($id_bd, $_POST['nom_batiment']);
			$ges_login = mysqli_real_escape_string($id_bd, $_POST['ges_login']);
			$ges_mdp = mysqli_real_escape_string($id_bd, $_POST['ges_mdp']);

			/* Automatic calculation of the next id_bat available */
			$requeteMax = "SELECT MAX(id_bat) AS max_id FROM batiment";
			$resultatMax = mysqli_query($id_bd, $requeteMax);
			$ligneMax = mysqli_fetch_assoc($resultatMax);
			$nouvel_id = $ligneMax['max_id'] ? $ligneMax['max_id'] + 1 : 1000;
			
			/* Inser the new data collection into the data base in order to create a new building */
			$requete = "
				INSERT INTO batiment (id_bat, nom_bat, ges_login, ges_mdp)
				VALUES ('$nouvel_id', '$nom_bat', '$ges_login', '$ges_mdp')
			";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete"); /* Error occuring */
			break;

		case 'supprimer_batiment':
			$id_bat = mysqli_real_escape_string($id_bd, $_POST['id_batiment']);
			$requete = "DELETE FROM batiment WHERE id_bat = '$id_bat'";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete");
			break;

		case 'ajouter_salle':
			$nom_salle = mysqli_real_escape_string($id_bd, $_POST['nom_salle']);
			$type_salle = mysqli_real_escape_string($id_bd, $_POST['type_salle']);
			$capacite = mysqli_real_escape_string($id_bd, $_POST['capacite']);
			$id_bat = mysqli_real_escape_string($id_bd, $_POST['id_batiment_salle']);

			$requete = "
				INSERT INTO salle (id_bat, nom_salle, type_salle, capacite)
				VALUES ('$id_bat', '$nom_salle', '$type_salle', '$capacite')
			";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete");
			break;

		case 'supprimer_salle':
			$nom_salle = mysqli_real_escape_string($id_bd, $_POST['nom_salle_suppr']);
			$requete = "DELETE FROM salle WHERE nom_salle = '$nom_salle'";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete");
			break;

		case 'ajouter_capteur':
			$nom_capteur = mysqli_real_escape_string($id_bd, $_POST['nom_capteur']);
			$type_capteur = mysqli_real_escape_string($id_bd, $_POST['type_capteur']);
			$unite = mysqli_real_escape_string($id_bd, $_POST['unite']);
			$nom_salle = mysqli_real_escape_string($id_bd, $_POST['nom_salle_capteur']);

			$requete = "
				INSERT INTO capteur (nom_salle, nom_capt, type_capt, unite)
				VALUES ('$nom_salle', '$nom_capteur', '$type_capteur', '$unite')
			";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete");
			break;

		case 'supprimer_capteur':
			$nom_capt = mysqli_real_escape_string($id_bd, $_POST['nom_capteur_suppr']);
			$requete = "DELETE FROM capteur WHERE nom_capt = '$nom_capt'";
			mysqli_query($id_bd, $requete)
				or die("Execution de la requete impossible : $requete");
			break;
	}

	/* Redirection to the same page to avoid resubmitting the form during the refresh (F5) */
	header("Location: admin.php");
	exit();
}

/* Display requests: list of buildings (table + select for the room form) */
$requeteBatiments = "SELECT id_bat, nom_bat, ges_login FROM batiment ORDER BY nom_bat";
$batiments = mysqli_query($id_bd, $requeteBatiments)
	or die("Execution de la requete impossible : $requeteBatiments");

$batiments_select = mysqli_query($id_bd, $requeteBatiments)
	or die("Execution de la requete impossible : $requeteBatiments");

/* List of rooms, with the name of the associated building */
$requeteSalles = "
	SELECT s.nom_salle, s.type_salle, s.capacite, b.nom_bat
	FROM salle s
	INNER JOIN batiment b ON s.id_bat = b.id_bat
	ORDER BY b.nom_bat, s.nom_salle
";
$salles = mysqli_query($id_bd, $requeteSalles)
	or die("Execution de la requete impossible : $requeteSalles");

$requeteSallesSelect = "SELECT nom_salle FROM salle ORDER BY nom_salle";
$salles_select = mysqli_query($id_bd, $requeteSallesSelect)
	or die("Execution de la requete impossible : $requeteSallesSelect");

/* Sensors list */
$requeteCapteurs = "SELECT nom_capt, type_capt, unite, nom_salle FROM capteur ORDER BY nom_salle, nom_capt";
$capteurs = mysqli_query($id_bd, $requeteCapteurs)
	or die("Execution de la requete impossible : $requeteCapteurs");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Admin</title>
    <link rel="stylesheet" type="text/css" href="./styles/style.css" />
</head>
<body>

    <!-- Menu -->
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="consultation.php">Consultation</a></li>
                <li><a href="MentionsLegales.html">Mention légales</a></li>
                <li><a href="gestion_projet.html">Gestion du projet</a></li>
                <li><a href="login.php">Se connecter</a></li>
            </ul>
        </nav>
    </header>

    <h1>Bienvenue, <?php echo htmlspecialchars(isset($_SESSION['login']) ? $_SESSION['login'] : 'admin'); ?> !</h1>
    <p>Tu es sur la page réservée aux administrateurs.</p>

    <a href="logout.php">Se déconnecter</a>

    <section>
        <h2>Gestion des bâtiments</h2>

        <h3>Ajouter un bâtiment</h3>
        <form method="post" action="admin.php">
            <fieldset>
                <legend>Nouveau bâtiment</legend>
                <input type="hidden" name="action" value="ajouter_batiment">

                <label for="nom_batiment">Nom du bâtiment :</label>
                <input type="text" id="nom_batiment" name="nom_batiment" required placeholder="Ex : Bâtiment RT">

                <label for="ges_login">Login du gestionnaire :</label>
                <input type="text" id="ges_login" name="ges_login" required placeholder="Ex : gest_rt">

                <label for="ges_mdp">Mot de passe du gestionnaire :</label>
                <input type="password" id="ges_mdp" name="ges_mdp" required>

                <input type="submit" value="Ajouter le bâtiment">
            </fieldset>
        </form>

        <h3>Liste des bâtiments</h3>
        <table>
            <thead>
                <tr><th>Nom</th><th>Login gestionnaire</th><th>Supprimer</th></tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($batiments)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['nom_bat']); ?></td>
                    <td><?php echo htmlspecialchars($b['ges_login']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_batiment">
                            <input type="hidden" name="id_batiment" value="<?php echo htmlspecialchars($b['id_bat']); ?>">
                            <input type="submit" value="Supprimer">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Gestion des salles</h2>

        <h3>Ajouter une salle</h3>
        <form method="post" action="admin.php">
            <fieldset>
                <legend>Nouvelle salle</legend>
                <input type="hidden" name="action" value="ajouter_salle">

                <label for="nom_salle">Nom de la salle :</label>
                <input type="text" id="nom_salle" name="nom_salle" required placeholder="Ex : E208">

                <label for="type_salle">Type :</label>
                <input type="text" id="type_salle" name="type_salle" required placeholder="Ex : Amphi, TP, Bureau">

                <label for="capacite">Capacité :</label>
                <input type="number" id="capacite" name="capacite" required placeholder="Ex : 30">

                <label for="id_batiment_salle">Bâtiment :</label>
                <select id="id_batiment_salle" name="id_batiment_salle" required>
                    <option value="">-- Choisir un bâtiment --</option>
                    <?php mysqli_data_seek($batiments_select, 0); ?>
                    <?php while ($bs = mysqli_fetch_assoc($batiments_select)): ?>
                    <option value="<?php echo htmlspecialchars($bs['id_bat']); ?>">
                        <?php echo htmlspecialchars($bs['nom_bat']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <input type="submit" value="Ajouter la salle">
            </fieldset>
        </form>

        <h3>Liste des salles</h3>
        <table>
            <thead>
                <tr><th>Salle</th><th>Type</th><th>Capacité</th><th>Bâtiment</th><th>Supprimer</th></tr>
            </thead>
            <tbody>
                <?php while ($s = mysqli_fetch_assoc($salles)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($s['type_salle']); ?></td>
                    <td><?php echo htmlspecialchars($s['capacite']); ?></td>
                    <td><?php echo htmlspecialchars($s['nom_bat']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_salle">
                            <input type="hidden" name="nom_salle_suppr" value="<?php echo htmlspecialchars($s['nom_salle']); ?>">
                            <input type="submit" value="Supprimer">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Gestion des capteurs</h2>

        <h3>Ajouter un capteur</h3>
        <form method="post" action="admin.php">
            <fieldset>
                <legend>Nouveau capteur</legend>
                <input type="hidden" name="action" value="ajouter_capteur">

                <label for="nom_capteur">Nom du capteur :</label>
                <input type="text" id="nom_capteur" name="nom_capteur" required placeholder="Ex : Capt_Temp_E208">

                <label for="type_capteur">Type :</label>
                <select id="type_capteur" name="type_capteur" required>
                    <option value="">-- Choisir un type --</option>
                    <option value="temperature">Température</option>
                    <option value="co2">CO₂</option>
                    <option value="humidity">Humidité</option>
                    <option value="illumination">Luminosité</option>
                </select>

                <label for="unite">Unité :</label>
                <input type="text" id="unite" name="unite" required placeholder="Ex : °C, ppm, %, lux">

                <label for="nom_salle_capteur">Salle :</label>
                <select id="nom_salle_capteur" name="nom_salle_capteur" required>
                    <option value="">-- Choisir une salle --</option>
                    <?php while ($sc = mysqli_fetch_assoc($salles_select)): ?>
                    <option value="<?php echo htmlspecialchars($sc['nom_salle']); ?>">
                        <?php echo htmlspecialchars($sc['nom_salle']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <input type="submit" value="Ajouter le capteur">
            </fieldset>
        </form>

        <h3>Liste des capteurs</h3>
        <table>
            <thead>
                <tr><th>Capteur</th><th>Type</th><th>Unité</th><th>Salle</th><th>Supprimer</th></tr>
            </thead>
            <tbody>
                <?php while ($c = mysqli_fetch_assoc($capteurs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['nom_capt']); ?></td>
                    <td><?php echo htmlspecialchars($c['type_capt']); ?></td>
                    <td><?php echo htmlspecialchars($c['unite']); ?></td>
                    <td><?php echo htmlspecialchars($c['nom_salle']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_capteur">
                            <input type="hidden" name="nom_capteur_suppr" value="<?php echo htmlspecialchars($c['nom_capt']); ?>">
                            <input type="submit" value="Supprimer">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <?php mysqli_close($id_bd); ?>
   <footer>
    <p><a href="index.html">Acceuil</a></p>
    <p><a href="login.php">Acc&egrave;s limit&eacute; : Administration de la base de donn&eacute;es</a></p>
   </footer>
</body>
</html>
