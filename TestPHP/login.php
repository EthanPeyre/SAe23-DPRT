<?php
session_start();

if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'mysql.php';

    $login = mysqli_real_escape_string($id_bd, $_POST['login']);
    $mdp   = mysqli_real_escape_string($id_bd, $_POST['mdp']);

    $sql       = "SELECT * FROM Administration WHERE login = '$login' AND mdp = '$mdp'";
    $resultat  = mysqli_query($id_bd, $sql);
    $admin     = mysqli_fetch_assoc($resultat);

    if ($admin) {
        $_SESSION['admin'] = true;
        $_SESSION['login'] = $admin['login'];
        header("Location: admin.php");
        exit();
    } else {
                    mysqli_close($id_bd);
            echo "<script type='text/javascript'>document.location.replace('login_error.php');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
</head>
<body>
    <h2>Connexion Administrateur</h2>

    <?php if ($erreur): ?>
        <p style="color:red;"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Login :</label><br>
        <input type="text" name="login" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="mdp" required><br><br>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
