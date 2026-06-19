<?php
/**
 * db_connect.php
 * Database connection file for SAE23 project.
 * Establishes a connection to the MySQL database using mysqli.
 *
 * Usage: require_once 'db_connect.php'; => $conn is available
 *
 * @author  SAE23 Group
 * @version 1.0
 */

// Database configuration constants
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'sae23_iut');

// Open connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}

// Set character encoding to UTF-8
mysqli_set_charset($conn, 'utf8');
