<?php
$host = "localhost";   // A remplir
$user = "sae23";   // A remplir
$pass = "sae23";   // A remplir
$dbname = "SAE23"; // A remplir

$connexion = mysqli_connect($host, $user, $pass, $dbname);

if (!$connexion) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>