<?php
$host = "";   // A remplir
$user = "";   // A remplir
$pass = "";   // A remplir
$dbname = ""; // A remplir

$connexion = mysqli_connect($host, $user, $pass, $dbname);

if (!$connexion) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>