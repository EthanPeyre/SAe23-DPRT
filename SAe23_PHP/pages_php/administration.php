<?php
/* administration.php
 * Administration page accessible only by the site administrator.
 * Allows adding and deleting buildings, rooms, and sensors via forms.
 */

$page_title   = 'Administration – SAE23 IUT Blagnac';
$current_page = 'admin';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access restricted to admin role only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'db_connect.php';
require_once 'header.php';

$message = '';
$erreur  = '';

/* =================== POST actions =================== */

$action = $_POST['action'] ?? '';

/* ----- Add a building ----- */
if ($action === 'ajouter_batiment') {
    $nom = trim($_POST['nom_batiment'] ?? '');
    $id_compte = (int)($_POST['id_compte'] ?? 0);
    if ($nom === '' || $id_compte === 0) {
        $erreur = 'Nom de bâtiment et compte gestionnaire obligatoires.';
    } else {
        $nom_esc = mysqli_real_escape_string($conn, $nom);
        $sql = "INSERT INTO Batiment (nom_batiment, id_compte) VALUES ('$nom_esc', $id_compte)";
        if (mysqli_query($conn, $sql)) {
            $message = 'Bâtiment ajouté avec succès.';
        } else {
            $erreur = 'Erreur lors de l\'ajout : ' . mysqli_error($conn);
        }
    }
}

/* ----- Delete a building ----- */
if ($action === 'supprimer_batiment') {
    $id = (int)($_POST['id_batiment'] ?? 0);
    if ($id > 0) {
        $sql = "DELETE FROM Batiment WHERE id_batiment = $id";
        if (mysqli_query($conn, $sql)) {
            $message = 'Bâtiment supprimé.';
        } else {
            $erreur = 'Erreur : ' . mysqli_error($conn);
        }
    }
}

/* ----- Add a room ----- */
if ($action === 'ajouter_salle') {
    $nom      = trim($_POST['nom_salle']   ?? '');
    $type     = trim($_POST['type_salle']  ?? '');
    $capacite = (int)($_POST['capacite']   ?? 0);
    $id_bat   = (int)($_POST['id_batiment_salle'] ?? 0);
    if ($nom === '' || $type === '' || $capacite === 0 || $id_bat === 0) {
        $erreur = 'Tous les champs salle sont obligatoires.';
    } else {
        $nom_esc  = mysqli_real_escape_string($conn, $nom);
        $type_esc = mysqli_real_escape_string($conn, $type);
        $sql = "INSERT INTO Salle (nom_salle, type_salle, capacite, id_batiment)
                VALUES ('$nom_esc', '$type_esc', $capacite, $id_bat)";
        if (mysqli_query($conn, $sql)) {
            $message = 'Salle ajoutée avec succès.';
        } else {
            $erreur = 'Erreur : ' . mysqli_error($conn);
        }
    }
}

/* ----- Delete a room ----- */
if ($action === 'supprimer_salle') {
    $id = (int)($_POST['id_salle'] ?? 0);
    if ($id > 0) {
        $sql = "DELETE FROM Salle WHERE id_salle = $id";
        if (mysqli_query($conn, $sql)) {
            $message = 'Salle supprimée.';
        } else {
            $erreur = 'Erreur : ' . mysqli_error($conn);
        }
    }
}

/* ----- Add a sensor ----- */
if ($action === 'ajouter_capteur') {
    $nom   = trim($_POST['nom_capteur']  ?? '');
    $type  = trim($_POST['type_capteur'] ?? '');
    $unite = trim($_POST['unite']        ?? '');
    $id_salle = (int)($_POST['id_salle_capteur'] ?? 0);
    if ($nom === '' || $type === '' || $unite === '' || $id_salle === 0) {
        $erreur = 'Tous les champs capteur sont obligatoires.';
    } else {
        $nom_esc   = mysqli_real_escape_string($conn, $nom);
        $type_esc  = mysqli_real_escape_string($conn, $type);
        $unite_esc = mysqli_real_escape_string($conn, $unite);
        $sql = "INSERT INTO Capteur (nom_capteur, type_capteur, unite, id_salle)
                VALUES ('$nom_esc', '$type_esc', '$unite_esc', $id_salle)";
        if (mysqli_query($conn, $sql)) {
            $message = 'Capteur ajouté avec succès.';
        } else {
            $erreur = 'Erreur : ' . mysqli_error($conn);
        }
    }
}

/* ----- Delete a sensor ----- */
if ($action === 'supprimer_capteur') {
    $id = (int)($_POST['id_capteur'] ?? 0);
    if ($id > 0) {
        $sql = "DELETE FROM Capteur WHERE id_capteur = $id";
        if (mysqli_query($conn, $sql)) {
            $message = 'Capteur supprimé.';
        } else {
            $erreur = 'Erreur : ' . mysqli_error($conn);
        }
    }
}

/* ====================== Fetch data for listing  ========================= */

// Manager accounts (for the add-building form)
$comptes_gest = mysqli_query($conn,
    "SELECT id_compte, login FROM Compte WHERE role = 'gestionnaire' ORDER BY login");

// All buildings
$batiments = mysqli_query($conn,
    "SELECT b.id_batiment, b.nom_batiment, c.login AS gestionnaire
     FROM Batiment b JOIN Compte c ON b.id_compte = c.id_compte
     ORDER BY b.nom_batiment");

// All rooms (with building)
$salles = mysqli_query($conn,
    "SELECT s.id_salle, s.nom_salle, s.type_salle, s.capacite, b.nom_batiment
     FROM Salle s JOIN Batiment b ON s.id_batiment = b.id_batiment
     ORDER BY b.nom_batiment, s.nom_salle");

// All buildings (for room-add select)
$batiments_select = mysqli_query($conn,
    "SELECT id_batiment, nom_batiment FROM Batiment ORDER BY nom_batiment");

// All sensors (with room)
$capteurs = mysqli_query($conn,
    "SELECT c.id_capteur, c.nom_capteur, c.type_capteur, c.unite, s.nom_salle
     FROM Capteur c JOIN Salle s ON c.id_salle = s.id_salle
     ORDER BY s.nom_salle, c.nom_capteur");

// All rooms (for sensor-add select)
$salles_select = mysqli_query($conn,
    "SELECT id_salle, nom_salle FROM Salle ORDER BY nom_salle");
?>

<main>

    <?php if ($message !== ''): ?>
        <p class="alerte-succes"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($erreur !== ''): ?>
        <p class="alerte-erreur"><?php echo htmlspecialchars($erreur); ?></p>
    <?php endif; ?>

    <!-- ===== Buildings ===== -->
    <section>
        <h2>Gestion des bâtiments</h2>

        <h3>Ajouter un bâtiment</h3>
        <form method="post" action="administration.php">
            <fieldset>
                <legend>Nouveau bâtiment</legend>
                <input type="hidden" name="action" value="ajouter_batiment">

                <label for="nom_batiment">Nom du bâtiment :</label>
                <input type="text" id="nom_batiment" name="nom_batiment" required
                       placeholder="Ex : Bâtiment RT">

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
                        <form method="post" action="administration.php">
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

    <!-- ===== Rooms ===== -->
    <section>
        <h2>Gestion des salles</h2>

        <h3>Ajouter une salle</h3>
        <form method="post" action="administration.php">
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
                        <form method="post" action="administration.php">
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

    <!-- ===== Sensors ===== -->
    <section>
        <h2>Gestion des capteurs</h2>

        <h3>Ajouter un capteur</h3>
        <form method="post" action="administration.php">
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
                        <form method="post" action="administration.php">
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

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>
