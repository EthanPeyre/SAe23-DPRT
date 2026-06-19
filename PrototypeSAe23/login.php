<?php
session_start();

// Redirection si déjà connecté
if (isset($_SESSION['auth']) && $_SESSION['auth'] === TRUE) {
    header("Location: admin.php");
    exit();
}
if (isset($_SESSION['auth_gest']) && $_SESSION['auth_gest'] === TRUE) {
    header("Location: gest.php");
    exit();
}

$_SESSION["auth"]      = FALSE;
$_SESSION["auth_gest"] = FALSE;

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $type   = $_REQUEST["type"];
    $motdep = $_REQUEST["mdp"];

    if ($type === "admin")
    {
        if (empty($motdep))
            header("Location: login_error.php");
        else
        {
            include("mysql.php");

            $requete  = "SELECT mdp FROM Administration WHERE login = 'admin'";
            $resultat = mysqli_query($id_bd, $requete)
                or die("Execution de la requete impossible : $requete");

            $ligne = mysqli_fetch_row($resultat);
            if ($ligne && $motdep == $ligne[0])
            {
                $_SESSION["auth"] = TRUE;
                mysqli_close($id_bd);
                echo "<script type='text/javascript'>document.location.replace('admin.php');</script>";
            }
            else
            {
                $_SESSION = array();
                session_destroy();
                unset($_SESSION);
                mysqli_close($id_bd);
                echo "<script type='text/javascript'>document.location.replace('login_error.php');</script>";
            }
        }
    }
    elseif ($type === "gest")
    {
        $login = $_REQUEST["login"];

        if (empty($login) || empty($motdep))
            header("Location: login_error.php");
        else
        {
            include("mysql.php");

            $login  = mysqli_real_escape_string($id_bd, $login);
            $motdep = mysqli_real_escape_string($id_bd, $motdep);

            /* On recupere egalement id_bat, necessaire pour que gest.php
               sache de quel batiment afficher les mesures */
            $requete  = "SELECT ges_login, id_bat FROM batiment WHERE ges_login = '$login' AND ges_mdp = '$motdep'";
            $resultat = mysqli_query($id_bd, $requete)
                or die("Execution de la requete impossible : $requete");

            $ligne = mysqli_fetch_row($resultat);
            if ($ligne)
            {
                $_SESSION["auth_gest"] = TRUE;
                $_SESSION["ges_login"] = $login;
                $_SESSION["id_bat"]    = $ligne[1];
                mysqli_close($id_bd);
                echo "<script type='text/javascript'>document.location.replace('gest.php');</script>";
            }
            else
            {
                $_SESSION = array();
                session_destroy();
                unset($_SESSION);
                mysqli_close($id_bd);
                echo "<script type='text/javascript'>document.location.replace('login_error.php');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="./styles/style.css" />
</head>
<body>

    <!-- Menu -->
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="login.php">Se connecter</a></li>
                <li><a href="consult.html">Consultation</a></li>
                <li><a href="gestion.html">Gestion du projet</a></li>
            </ul>
        </nav>
    </header>

    <h1>Connexion</h1>

    <form method="POST" action="login.php">

        <label>Type de connexion :</label><br>
        <select name="type" id="type" onchange="toggleLogin()">
            <option value="admin">Administrateur</option>
            <option value="gest">Gestionnaire</option>
        </select><br><br>

        <div id="champ_login" style="display:none;">
            <label>Login :</label><br>
            <input type="text" name="login"><br><br>
        </div>

        <label>Mot de passe :</label><br>
        <input type="password" name="mdp" required><br><br>

        <button type="submit">Se connecter</button>

    </form>

    <script>
        function toggleLogin() {
            var type = document.getElementById('type').value;
            var champLogin = document.getElementById('champ_login');
            if (type === 'gest')
                champLogin.style.display = 'block';
            else
                champLogin.style.display = 'none';
        }
    </script>

</body>
</html>