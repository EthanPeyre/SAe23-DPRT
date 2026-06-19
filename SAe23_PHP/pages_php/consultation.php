<?php
/**
 * consultation.php
 * Public page displaying the latest sensor reading for every room.
 */

$page_title   = 'Consultation – SAE23 IUT Blagnac';
$current_page = 'consultation';

require_once 'db_connect.php';
require_once 'header.php';

// Retrieve the latest measurement for each sensor (one row per sensor)
$sql = "SELECT
            c.nom_capteur,
            c.type_capteur,
            c.unite,
            s.nom_salle,
            b.nom_batiment,
            m.valeur,
            CONCAT(m.date_mesure, ' ', m.heure_mesure) AS horodatage
        FROM Capteur c
        JOIN Salle   s ON c.id_salle    = s.id_salle
        JOIN Batiment b ON s.id_batiment = b.id_batiment
        JOIN Mesure  m ON m.id_capteur  = c.id_capteur
        WHERE m.id_mesure = (
            SELECT MAX(m2.id_mesure)
            FROM Mesure m2
            WHERE m2.id_capteur = c.id_capteur
        )
        ORDER BY b.nom_batiment, s.nom_salle, c.nom_capteur";

$result = mysqli_query($conn, $sql);
?>

<main>

    <section>
        <h2>Dernières mesures de tous les capteurs</h2>
        <p class="alerte-info">
            Cette page se rafraîchit automatiquement toutes les 30 secondes.
        </p>

        <?php if (!$result || mysqli_num_rows($result) === 0): ?>
            <p class="alerte-erreur">Aucune mesure disponible pour le moment.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Capteur</th>
                    <th>Type</th>
                    <th>Salle</th>
                    <th>Bâtiment</th>
                    <th>Dernière valeur</th>
                    <th>Unité</th>
                    <th>Horodatage</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($row['type_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom_batiment']); ?></td>
                    <td><?php echo htmlspecialchars($row['valeur']); ?></td>
                    <td><?php echo htmlspecialchars($row['unite']); ?></td>
                    <td><?php echo htmlspecialchars($row['horodatage']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <section>
        <h2>Capteurs surveillés – Vue détaillée</h2>
        <p>Mesures en temps réel des quatre capteurs principaux du projet.</p>

        <?php
        function get_latest_measure(mysqli $conn, string $nom_salle, string $type): ?array {
            $nom_salle = mysqli_real_escape_string($conn, $nom_salle);
            $type      = mysqli_real_escape_string($conn, $type);

            $sql = "SELECT
                        m.valeur,
                        c.unite,
                        c.nom_capteur,
                        CONCAT(m.date_mesure, ' ', m.heure_mesure) AS horodatage
                    FROM Mesure  m
                    JOIN Capteur c ON m.id_capteur  = c.id_capteur
                    JOIN Salle   s ON c.id_salle    = s.id_salle
                    WHERE s.nom_salle    = '$nom_salle'
                      AND c.type_capteur = '$type'
                    ORDER BY m.date_mesure DESC, m.heure_mesure DESC
                    LIMIT 1";

            $res = mysqli_query($conn, $sql);
            if ($res && mysqli_num_rows($res) > 0) {
                return mysqli_fetch_assoc($res);
            }
            return null;
        }

        $capteurs_cibles = [
            ['salle' => 'E208', 'type' => 'temperature', 'label' => 'Température – Salle E208'],
            ['salle' => 'E101', 'type' => 'co2',         'label' => 'Taux de CO₂ – Salle E101'],
            ['salle' => 'B113', 'type' => 'humidite',    'label' => 'Humidité – Salle B113'],
            ['salle' => 'B103', 'type' => 'luminosite',  'label' => 'Luminosité – Salle B103'],
        ];

        foreach ($capteurs_cibles as $cible):
            $data = get_latest_measure($conn, $cible['salle'], $cible['type']);
        ?>
        <article>
            <h3><?php echo htmlspecialchars($cible['label']); ?></h3>
            <?php if ($data): ?>
                <p>Capteur : <em><?php echo htmlspecialchars($data['nom_capteur']); ?></em></p>
                <p>Dernière valeur : <strong><?php echo htmlspecialchars($data['valeur']); ?> <?php echo htmlspecialchars($data['unite']); ?></strong></p>
                <p>Relevé le : <?php echo htmlspecialchars($data['horodatage']); ?></p>
            <?php else: ?>
                <p class="alerte-erreur">Aucune donnée disponible pour ce capteur.</p>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>

    </section>

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>