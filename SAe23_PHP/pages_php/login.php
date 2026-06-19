<?php
/* login.php
 * Login page for administrators and building managers.
 * Uses PHP sessions to persist the authenticated user across pages.
 * Passwords are stored as MD5 hashes in the database.
 */

$page_title   = 'Connexion – SAE23 IUT Blagnac';
$current_page = 'login';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect already-logged-in users
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'db_connect.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $login = trim($_POST['login'] ?? '');
    $mdp   = trim($_POST['mdp']   ?? '');

    if ($login === '' || $mdp === '') {
        $erreur = 'Veuillez renseigner le login et le mot de passe.';
    } else {
        // Hash the password with MD5 (as required by the specifications)
        $mdp_md5 = md5($mdp);
        $login_esc = mysqli_real_escape_string($conn, $login);

        $sql = "SELECT c.id_compte, c.login, c.role
                FROM Compte c
                WHERE c.login = '$login_esc'
                  AND c.mdp   = '$mdp_md5'
                LIMIT 1";

        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);

            // Store user info in session
            $_SESSION['user_id']  = $user['id_compte'];
            $_SESSION['username'] = $user['login'];
            $_SESSION['role']     = $user['role'];     // 'admin' or 'gestionnaire'

            // Redirect according to role
            if ($user['role'] === 'admin') {
                header('Location: administration.php');
            } else {
                header('Location: gestion.php');
            }
            exit;
        } else {
            $erreur = 'Login ou mot de passe incorrect.';
        }
    }
}

require_once 'header.php';
?>

<main>

    <section>
        <h2>Connexion</h2>

        <?php if ($erreur !== ''): ?>
            <p class="alerte-erreur"><?php echo htmlspecialchars($erreur); ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <fieldset>
                <legend>Identifiants</legend>

                <label for="login">Login :</label>
                <input type="text" id="login" name="login" required
                       placeholder="Votre identifiant"
                       value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">

                <label for="mdp">Mot de passe :</label>
                <input type="password" id="mdp" name="mdp" required
                       placeholder="Votre mot de passe">

                <input type="submit" value="Se connecter">
            </fieldset>
        </form>

    </section>

</main>

<?php
mysqli_close($conn);
require_once 'footer.html';
?>
