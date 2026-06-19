<?php
/**
 * consultation.php
 * Page publique affichant les derniers relevés de chaque capteur.
 * - Met en valeur les 4 capteurs cibles (E208, E101, B113, B103).
 * - Affiche le tableau global de l'intégralité des capteurs du système (issu du prototype).
 *
 * Accessible à tous sans connexion.
 * Rafraîchissement automatique : toutes les 30 secondes.
 */

$page_title   = 'Consultation – SAE23 IUT Blagnac';
$current_page = 'consultation';

require_once 'mysql.php';
require_once 'header.php';

// Fonction pour récupérer le tout dernier relevé d'un capteur spécifique (pour les fiches cibles)
function get_latest_measure($conn, $salle, $type) {
    $salle_esc = mysqli_real_escape_string($conn, $salle);
    $type_esc  = mysqli_real_escape_string($conn, $type);

    $sql = "SELECT 
                c.nom_capteur, 
                m.valeur, 
                c.unite, 
                CONCAT(m.date_mesure, ' ', m.heure_mesure) AS horodatage
            FROM Mesure m
            JOIN Capteur c ON m.id_capteur = c.id_capteur
            JOIN Salle s    ON c.id_salle   = s.id_salle
            WHERE s.nom_salle = '$salle_esc' 
              AND c.type_capteur = '$type_esc'
            ORDER BY m.date_mesure DESC, m.heure_mesure DESC
            LIMIT 1";

    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}

// Définition des 4 capteurs cibles demandés
$capteurs_cibles = [
    ['salle' => 'E208', 'type' => 'temperature', 'label' => 'Température – Salle E208'],
    ['salle' => 'E101', 'type' => 'co2',         'label' => 'Taux de CO₂ – Salle E101'],
    ['salle' => 'B113', 'type' => 'humidite',    'label' => 'Humidité – Salle B113'],
    ['salle' => 'B103', 'type' => 'luminosite',  'label' => 'Luminosité – Salle B103'],
];

/* * Requête globale (Prototype consult.php converti aux normes SQL finales)
 * Pour chaque capteur de la base, on extrait le dernier relevé enregistré.
 */
$requete_globale = "
    SELECT 
        b.nom_batiment, 
        s.nom_salle, 
        c.nom_capteur, 
        c.type_capteur,
        c.unite, 
        m.valeur, 
        m.date_mesure, 
        m.heure_mesure
    FROM Capteur c
    JOIN Salle s    ON c.id_salle    = s.id_salle
    JOIN Batiment b ON s.id_batiment = b.id_batiment
    JOIN Mesure m   ON m.id_capteur  = c.id_capteur
    WHERE m.id_mesure = (
        SELECT MAX(m2.id_mesure)
        FROM Mesure m2
        WHERE m2.id_capteur = c.id_capteur
    )
    ORDER BY b.nom_batiment, s.nom_salle, c.nom_capteur";

$resultat_global = mysqli_query($conn, $requete_globale);
?>

<script>
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>

<main>

    <section>
        <h2>Dernières données des capteurs phares</h2>
        <p>Ci-dessous les indicateurs principaux suivis en temps réel au sein de l'IUT.</p>
        
        <div class="Grille-consultation">
            <?php 
            foreach ($capteurs_cibles as $cible): 
                $data = get_latest_measure($conn, $cible['salle'], $cible['type']);
            ?>
            <article class="Carte-capteur">
                <h3><?php echo htmlspecialchars($cible['label']); ?></h3>
                <?php if ($data): ?>
                    <p>Capteur : <em><?php echo htmlspecialchars($data['nom_capteur']); ?></em></p>
                    <p class="valeur-principale"><strong><?php echo htmlspecialchars($data['valeur']); ?> <?php echo htmlspecialchars($data['unite']); ?></strong></p>
                    <p class="horodatage">Relevé le : <?php echo htmlspecialchars($data['horodatage']); ?></p>
                <?php else: ?>
                    <p class="alerte-info">Aucune mesure disponible pour le moment.</p>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2>Vue d'ensemble de l'ensemble du parc de capteurs</h2>
        <p>Ce tableau répertorie la dernière valeur reçue pour l'intégralité des modules IoT déployés.</p>
        
        <?php if ($resultat_global && mysqli_num_rows($resultat_global) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Bâtiment</th>
                    <th>Salle</th>
                    <th>Capteur</th>
                    <th>Type</th>
                    <th>Dernière valeur</th>
                    <th>Unité</th>
                    <th>Date</th>
                    <th>Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ligne = mysqli_fetch_assoc($resultat_global)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ligne['nom_batiment']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['nom_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['type_capteur']); ?></td>
                    <td><strong><?php echo htmlspecialchars($ligne['valeur']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ligne['unite']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['date_mesure']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['heure_mesure']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="alerte-info">Aucun capteur ou aucune mesure n'a été détecté dans la base de données.</p>
        <?php endif; ?>
    </section>

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>