<?php
session_start();

if (!isset($_SESSION['auth_gest']) || $_SESSION['auth_gest'] !== TRUE) {
    header("Location: login.php");
    exit();
}

/* id_bat from the manager, saved to session during login.php */
$id_bat = $_SESSION['id_bat'];

/* Give access to the data base (moved here to have the building's name in the HTML page) */
include("mysql.php");

/* Retrieval of the name of the building managed by this manager */
$id_bat_safe = mysqli_real_escape_string($id_bd, $id_bat);
$requeteBatiment = "SELECT nom_bat FROM batiment WHERE id_bat = '$id_bat_safe'";
$resultatBatiment = mysqli_query($id_bd, $requeteBatiment)
	or die("Execution de la requete impossible : $requeteBatiment");
$batiment = mysqli_fetch_assoc($resultatBatiment);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Gestion</title>
    <link rel="stylesheet" type="text/css" href="./styles/style.css" />
    <meta http-equiv="refresh" content="60" />
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

    <h2>Bienvenue, <?php echo htmlspecialchars(isset($_SESSION['ges_login']) ? $_SESSION['ges_login'] : 'Gestionnaire'); ?> !</h2>
    <p class="alerte-info">
        Vous êtes connecté sur l espace de suivi du <strong><?php echo htmlspecialchars($batiment['nom_bat']); ?></strong>.
    </p>

    <a href="logout.php">Se déconnecter</a>
    <hr>

    <section>
        <h2>Mesures d&eacute;taill&eacute;es</h2>
        <table class="centre">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Capteur</th>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Unit&eacute;</th>
                    <th>Date</th>
                    <th>Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    /* All the measurements of the sensors in the manager’s building */
                    $requete = "
                        SELECT s.nom_salle, c.nom_capt, c.type_capt, c.unite,
                            m.valeur, m.date, m.horaire
                        FROM mesure m
                        INNER JOIN capteur c ON m.nom_capt = c.nom_capt
                        INNER JOIN salle s ON c.nom_salle = s.nom_salle
                        WHERE s.id_bat = '$id_bat_safe'
                        ORDER BY s.nom_salle, c.nom_capt, m.date DESC, m.horaire DESC
                    ";

                    $resultat = mysqli_query($id_bd, $requete)
                        or die("Execution de la requete impossible : $requete");

                    while ($ligne = mysqli_fetch_array($resultat))
                    {
                        extract($ligne);
                        echo "<tr>";
                        echo "<td>$nom_salle</td>";
                        echo "<td>$nom_capt</td>";
                        echo "<td>$type_capt</td>";
                        echo "<td>$valeur</td>";
                        echo "<td>$unite</td>";
                        echo "<td>$date</td>";
                        echo "<td>$horaire</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Moyenne, minimum et maximum par salle</h2>
        <table class="centre">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Capteur</th>
                    <th>Moyenne</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Unit&eacute;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    /*
                     * Calculation of the average, min and max for each sensor
                     * of the rooms belonging to the manager’s building.
                     */
                    $requeteStats = "
                        SELECT s.nom_salle, c.nom_capt, c.unite,
                            AVG(m.valeur) AS moyenne,
                            MIN(m.valeur) AS minimum,
                            MAX(m.valeur) AS maximum
                        FROM mesure m
                        INNER JOIN capteur c ON m.nom_capt = c.nom_capt
                        INNER JOIN salle s ON c.nom_salle = s.nom_salle
                        WHERE s.id_bat = '$id_bat_safe'
                        GROUP BY s.nom_salle, c.nom_capt, c.unite
                        ORDER BY s.nom_salle, c.nom_capt
                    ";

                    $resultatStats = mysqli_query($id_bd, $requeteStats)
                        or die("Execution de la requete impossible : $requeteStats");

                    while ($ligneStats = mysqli_fetch_array($resultatStats))
                    {
                        extract($ligneStats);
                        $moyenne = round($moyenne, 2);
                        echo "<tr>";
                        echo "<td>$nom_salle</td>";
                        echo "<td>$nom_capt</td>";
                        echo "<td>$moyenne</td>";
                        echo "<td>$minimum</td>";
                        echo "<td>$maximum</td>";
                        echo "<td>$unite</td>";
                        echo "</tr>";
                    }

                    mysqli_close($id_bd);
                ?>
            </tbody>
        </table>
    </section>

    <footer>
        <p><a href="index.html">Accueil</a></p>
        <p><a href="login.php">Acc&egrave;s limit&eacute; : Administration de la base de donn&eacute;es</a></p>
    </footer>

</body>
</html>
