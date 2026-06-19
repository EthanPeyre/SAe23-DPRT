<?php
/**
 * index.php
 * Home page of the SAE23 web application.
 * Displays site description, list of monitored buildings and rooms.
 * Accessible to everyone (no login required).
 */

$page_title   = 'Accueil – SAE23 IUT Blagnac';
$current_page = 'accueil';

require_once 'db_connect.php';
require_once 'header.php';

// Retrieve all buildings and their managers
$sql_batiments = "SELECT b.id_batiment, b.nom_batiment, c.login AS gestionnaire
                  FROM Batiment b
                  JOIN Compte c ON b.id_compte = c.id_compte
                  ORDER BY b.nom_batiment";
$res_batiments = mysqli_query($conn, $sql_batiments);

// Retrieve all equipped rooms
$sql_salles = "SELECT s.nom_salle, s.type_salle, s.capacite, b.nom_batiment
               FROM Salle s
               JOIN Batiment b ON s.id_batiment = b.id_batiment
               ORDER BY b.nom_batiment, s.nom_salle";
$res_salles = mysqli_query($conn, $sql_salles);
?>

<main>

    <section>
        <h2>Présentation du projet</h2>
        <p>
            Cette application web permet de visualiser les données collectées par les capteurs
            installés dans les bâtiments de l'IUT de Blagnac. Elle s'inscrit dans le cadre de
            la SAE23 (Mettre en place une solution informatique pour l'entreprise) du Bachelor
            Universitaire de Technologie – Réseaux et Télécommunications.
        </p>
        <p>
            Les mesures proviennent d'un bus <strong>MQTT</strong> et sont stockées dans une
            base de données <strong>MySQL</strong> via un script planifié (<em>crontab</em>).
            La chaîne de traitement repose sur des conteneurs Docker : Mosquitto, Node-RED,
            InfluxDB et Grafana.
        </p>
    </section>

    <section>
        <h2>Bâtiments gérés</h2>
        <?php if (mysqli_num_rows($res_batiments) === 0): ?>
            <p class="alerte-info">Aucun bâtiment enregistré pour le moment.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Nom du bâtiment</th>
                    <th>Gestionnaire</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bat = mysqli_fetch_assoc($res_batiments)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bat['id_batiment']); ?></td>
                    <td><?php echo htmlspecialchars($bat['nom_batiment']); ?></td>
                    <td><?php echo htmlspecialchars($bat['gestionnaire']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <section>
        <h2>Salles équipées</h2>
        <?php if (mysqli_num_rows($res_salles) === 0): ?>
            <p class="alerte-info">Aucune salle enregistrée pour le moment.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Type</th>
                    <th>Capacité</th>
                    <th>Bâtiment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($salle = mysqli_fetch_assoc($res_salles)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($salle['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($salle['type_salle']); ?></td>
                    <td><?php echo htmlspecialchars($salle['capacite']); ?></td>
                    <td><?php echo htmlspecialchars($salle['nom_batiment']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <section>
        <h2>Mentions légales</h2>
        <p>
            Ce site est réalisé dans un cadre strictement pédagogique par des étudiants de
            l'IUT de Blagnac (département RT) dans le cadre de la SAE23 (2025-2026).
            Les données affichées sont issues de capteurs réels ou simulés et ne peuvent être
            utilisées à d'autres fins. Aucune donnée personnelle n'est collectée.
        </p>
    </section>

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>
