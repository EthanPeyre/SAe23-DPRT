<?php
/* Login script to the data base named sae23 */

  $id_bd = mysqli_connect("localhost","dbrt","mot_de_passe","sae23")
    or die("Connexion au serveur et/ou à la base de données impossible");

  /* Caracter encryption management */
  mysqli_query($id_bd, "SET NAMES 'utf8'");

?>
