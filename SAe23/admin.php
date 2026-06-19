
<?php
session_start();

// Vérifie le bon nom de session
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== TRUE) {
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
				<li><a href="consultation.php">Consultation</a></li>
				<li><a href="MentionsLegales.html">Mention légales</a></li>
				<li><a href="gestion_projet.html">Gestion du projet</a></li>
				<li><a href="login.php" >Se connecter</a></li>
		</ul> 
	</nav>
 </header>
</head>
<body>
<h2>Gestion des bâtiments</h2>

        <h3>Ajouter un bâtiment</h3>
        <form method="post" action="admin.php">
            <fieldset>
                <legend>Nouveau bâtiment</legend>
                <input type="hidden" name="action" value="ajouter_batiment">

                <label for="nom_batiment">Nom du bâtiment :</label>
                <input type="text" id="nom_batiment" name="nom_batiment" required placeholder="Ex : Bâtiment RT">

                <label for="id_compte">Gestionnaire :</label>
                <select id="id_compte" name="id_compte" required>
                    <option value="">-- Choisir un gestionnaire --</option>
                    <?php while ($cg = mysqli_fetch_assoc($comptes_gest)): ?>
                    <option value="<?php echo $cg['id_compte']; ?>">
                        <?php echo htmlspecialchars($cg['login']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <input type="submit" value="Ajouter le bâtiment">
            </fieldset>
        </form>

        <h3>Liste des bâtiments</h3>
        <table>
            <thead>
                <tr><th>Nom</th><th>Gestionnaire</th><th>Supprimer</th></tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($batiments)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['nom_batiment']); ?></td>
                    <td><?php echo htmlspecialchars($b['gestionnaire']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_batiment">
                            <input type="hidden" name="id_batiment" value="<?php echo $b['id_batiment']; ?>">
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
                <input type="text" id="capacite" name="capacite" required placeholder="Ex : 30">

                <label for="id_batiment_salle">Bâtiment :</label>
                <select id="id_batiment_salle" name="id_batiment_salle" required>
                    <option value="">-- Choisir un bâtiment --</option>
                    <?php while ($bs = mysqli_fetch_assoc($batiments_select)): ?>
                    <option value="<?php echo $bs['id_batiment']; ?>">
                        <?php echo htmlspecialchars($bs['nom_batiment']); ?>
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
                    <td><?php echo htmlspecialchars($s['nom_batiment']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_salle">
                            <input type="hidden" name="id_salle" value="<?php echo $s['id_salle']; ?>">
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
                <input type="text" id="nom_capteur" name="nom_capteur" required placeholder="Ex : capteur_E208_temp">

                <label for="type_capteur">Type :</label>
                <select id="type_capteur" name="type_capteur" required>
                    <option value="">-- Choisir un type --</option>
                    <option value="temperature">Température</option>
                    <option value="co2">CO₂</option>
                    <option value="humidite">Humidité</option>
                    <option value="luminosite">Luminosité</option>
                </select>

                <label for="unite">Unité :</label>
                <input type="text" id="unite" name="unite" required placeholder="Ex : °C, ppm, %, lux">

                <label for="id_salle_capteur">Salle :</label>
                <select id="id_salle_capteur" name="id_salle_capteur" required>
                    <option value="">-- Choisir une salle --</option>
                    <?php while ($sc = mysqli_fetch_assoc($salles_select)): ?>
                    <option value="<?php echo $sc['id_salle']; ?>">
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
                    <td><?php echo htmlspecialchars($c['nom_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($c['type_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($c['unite']); ?></td>
                    <td><?php echo htmlspecialchars($c['nom_salle']); ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="action" value="supprimer_capteur">
                            <input type="hidden" name="id_capteur" value="<?php echo $c['id_capteur']; ?>">
                            <input type="submit" value="Supprimer">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>


</body>
</html>
