<?php
/**
 * gestion.php
 * Espace gestionnaire de bâtiment fusionné avec le prototype gestion_batE.php.
 * Affiche les mesures, métriques (min/max/avg) et le graphique pour le bâtiment associé.
 */

$page_title   = 'Gestion – SAE23 IUT Blagnac';
$current_page = 'gestion';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de sécurité combinée (Prototype + Final)
if (
    (!isset($_SESSION['auth_gest']) || $_SESSION['auth_gest'] !== TRUE) &&
    (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['gestionnaire', 'admin']))
) {
    header('Location: login.php');
    exit();
}

require_once 'mysql.php';
require_once 'header.php';

// Récupération de l'ID du bâtiment (soit via session directe du prototype, soit via requête sur le compte)
if (isset($_SESSION['id_bat'])) {
    $id_batiment_gestionnaire = (int)$_SESSION['id_bat'];
} else {
    $id_compte = (int)($_SESSION['user_id'] ?? 0);
    $sql_bat = "SELECT id_batiment FROM Batiment WHERE id_compte = $id_compte LIMIT 1";
    $res_bat = mysqli_query($conn, $sql_bat);
    $bat_data = ($res_bat) ? mysqli_fetch_assoc($res_bat) : null;
    $id_batiment_gestionnaire = $bat_data ? (int)$bat_data['id_batiment'] : 0;
}

// Récupération du nom complet du bâtiment pour l'affichage
$batiment = null;
if ($id_batiment_gestionnaire > 0) {
    $sql_nom_bat = "SELECT id_batiment, nom_batiment FROM Batiment WHERE id_batiment = $id_batiment_gestionnaire LIMIT 1";
    $res_nom_bat = mysqli_query($conn, $sql_nom_bat);
    $batiment = ($res_nom_bat) ? mysqli_fetch_assoc($res_nom_bat) : null;
}

// Valeurs de filtrage par défaut pour les formulaires (Dernières 24 heures)
$date_debut = $_POST['date_debut'] ?? date('Y-m-d\TH:i', strtotime('-24 hours'));
$date_fin   = $_POST['date_fin']   ?? date('Y-m-d\TH:i');
$id_capteur_filtre = isset($_POST['id_capteur']) ? (int)$_POST['id_capteur'] : 0;

// Remplissage de la liste déroulante des capteurs appartenant à ce bâtiment
$liste_capteurs = [];
if ($batiment) {
    $id_bat = (int) $batiment['id_batiment'];
    $sql_cap = "SELECT c.id_capteur, c.nom_capteur, c.type_capteur, s.nom_salle
                FROM Capteur c
                JOIN Salle s ON c.id_salle = s.id_salle
                WHERE s.id_batiment = $id_bat
                ORDER BY s.nom_salle, c.nom_capteur";
    $res_cap = mysqli_query($conn, $sql_cap);
    while ($cap = ($res_cap ? mysqli_fetch_assoc($res_cap) : null)) {
        $liste_capteurs[] = $cap;
    }
}

$mesures   = [];
$labels_js = [];
$values_js = [];
$nom_capteur_graphe = '';

// Récupération de l'historique des relevés (Logique du tableau de la page finale)
if ($batiment) {
    $id_bat   = (int) $batiment['id_batiment'];
    $dt_debut = mysqli_real_escape_string($conn, str_replace('T', ' ', $date_debut));
    $dt_fin   = mysqli_real_escape_string($conn, str_replace('T', ' ', $date_fin));

    $filtre_capteur = ($id_capteur_filtre > 0) ? "AND c.id_capteur = $id_capteur_filtre" : '';

    $sql_mes = "SELECT
                    m.id_mesure,
                    CONCAT(m.date_mesure, ' ', m.heure_mesure) AS horodatage,
                    m.valeur,
                    c.unite,
                    c.nom_capteur,
                    c.type_capteur,
                    s.nom_salle
                FROM Mesure m
                JOIN Capteur c ON m.id_capteur  = c.id_capteur
                JOIN Salle   s ON c.id_salle    = s.id_salle
                WHERE s.id_batiment = $id_bat
                  AND CONCAT(m.date_mesure, ' ', m.heure_mesure) BETWEEN '$dt_debut' AND '$dt_fin'
                  $filtre_capteur
                ORDER BY m.date_mesure DESC, m.heure_mesure DESC
                LIMIT 200";

    $res_mes = mysqli_query($conn, $sql_mes);
    if ($res_mes) {
        while ($row = mysqli_fetch_assoc($res_mes)) {
            $mesures[] = $row;
        }
    }

    // Extraction des données pour générer le graphique dynamique
    foreach (array_reverse($mesures) as $m) {
        $labels_js[] = $m['horodatage'];
        $values_js[] = (float) $m['valeur'];
        $nom_capteur_graphe = $m['nom_capteur'] . ' (' . $m['unite'] . ')';
    }
}

// Calcul des statistiques Min/Max/Moyenne (Logique issue de la fusion fonctionnelle du prototype)
$stats = [];
if ($batiment) {
    $id_bat   = (int) $batiment['id_batiment'];
    $dt_debut = mysqli_real_escape_string($conn, str_replace('T', ' ', $date_debut));
    $dt_fin   = mysqli_real_escape_string($conn, str_replace('T', ' ', $date_fin));

    $sql_stats = "SELECT
                      s.nom_salle,
                      c.nom_capteur,
                      c.type_capteur,
                      c.unite,
                      MIN(m.valeur)  AS min_val,
                      MAX(m.valeur)  AS max_val,
                      ROUND(AVG(m.valeur), 2) AS moy_val
                  FROM Mesure m
                  JOIN Capteur c ON m.id_capteur  = c.id_capteur
                  JOIN Salle   s ON c.id_salle    = s.id_salle
                  WHERE s.id_batiment = $id_bat
                    AND CONCAT(m.date_mesure, ' ', m.heure_mesure) BETWEEN '$dt_debut' AND '$dt_fin'
                  GROUP BY s.nom_salle, c.id_capteur
                  ORDER BY s.nom_salle, c.nom_capteur";

    $res_stats = mysqli_query($conn, $sql_stats);
    if ($res_stats) {
        while ($st = mysqli_fetch_assoc($res_stats)) {
            $stats[] = $st;
        }
    }
}

$labels_json = json_encode($labels_js);
$values_json = json_encode($values_js);
$nom_graphe_json = json_encode($nom_capteur_graphe ?: 'Mesures');
?>

<main>

    <?php if (!$batiment): ?>
        <section>
            <p class="alerte-erreur">Aucun bâtiment associé à votre compte de gestion ou votre session.</p>
        </section>
    <?php else: ?>

    <section class="Zone-utilisateur">
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['gestion'] ?? $_SESSION['username'] ?? 'Gestionnaire'); ?> !</h2>
        <p class="alerte-info">
            Vous êtes connecté sur l'espace de suivi du <strong><?php echo htmlspecialchars($batiment['nom_batiment']); ?></strong>.
        </p>
    </section>

    <section>
        <h2>Filtrer les mesures</h2>
        <form method="post" action="gestion.php">
            <fieldset>
                <legend>Critères de recherche</legend>

                <label for="id_capteur">Capteur :</label>
                <select id="id_capteur" name="id_capteur">
                    <option value="0">-- Tous les capteurs --</option>
                    <?php foreach ($liste_capteurs as $cap): ?>
                    <option value="<?php echo $cap['id_capteur']; ?>"
                        <?php if ($id_capteur_filtre === (int)$cap['id_capteur']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cap['nom_salle'] . ' – ' . $cap['nom_capteur'] . ' (' . $cap['type_capteur'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="date_debut">Date/heure de début :</label>
                <input type="datetime-local" id="date_debut" name="date_debut"
                       value="<?php echo htmlspecialchars($date_debut); ?>">

                <label for="date_fin">Date/heure de fin :</label>
                <input type="datetime-local" id="date_fin" name="date_fin"
                       value="<?php echo htmlspecialchars($date_fin); ?>">

                <input type="submit" value="Afficher les mesures">
            </fieldset>
        </form>
    </section>

    <section>
        <h2>Métriques par salle (période sélectionnée)</h2>
        <?php if (empty($stats)): ?>
            <p class="alerte-info">Aucune donnée statistique calculable sur cette plage horaire.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Capteur</th>
                    <th>Type</th>
                    <th>Minimum</th>
                    <th>Maximum</th>
                    <th>Moyenne</th>
                    <th>Unité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $st): ?>
                <tr>
                    <td><?php echo htmlspecialchars($st['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($st['nom_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($st['type_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($st['min_val']); ?></td>
                    <td><?php echo htmlspecialchars($st['max_val']); ?></td>
                    <td><?php echo htmlspecialchars($st['moy_val']); ?></td>
                    <td><?php echo htmlspecialchars($st['unite']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <?php if (!empty($values_js) && $id_capteur_filtre > 0): ?>
    <section>
        <h2>Graphique – <?php echo htmlspecialchars($nom_capteur_graphe); ?></h2>
        <canvas id="graphiqueMesures" width="900" height="350"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            var labels = <?php echo $labels_json; ?>;
            var values = <?php echo $values_json; ?>;
            var nom    = <?php echo $nom_graphe_json; ?>;

            var ctx = document.getElementById('graphiqueMesures');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: nom,
                        data: values,
                        borderColor: '#2a6496',
                        backgroundColor: 'rgba(42, 100, 150, 0.1)',
                        borderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: {
                            display: true,
                            text: 'Évolution temporelle – ' + nom
                        }
                    },
                    scales: {
                        x: {
                            ticks: { maxTicksLimit: 10, maxRotation: 45 }
                        },
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        })();
        </script>
    </section>
    <?php endif; ?>

    <section>
        <h2>Relevés de mesures détaillés</h2>
        <?php if (empty($mesures)): ?>
            <p class="alerte-info">Aucune mesure brute trouvée pour les critères sélectionnés.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Horodatage</th>
                    <th>Salle</th>
                    <th>Capteur</th>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Unité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mesures as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['horodatage']); ?></td>
                    <td><?php echo htmlspecialchars($m['nom_salle']); ?></td>
                    <td><?php echo htmlspecialchars($m['nom_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($m['type_capteur']); ?></td>
                    <td><?php echo htmlspecialchars($m['valeur']); ?></td>
                    <td><?php echo htmlspecialchars($m['unite']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <?php endif; ?>

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>