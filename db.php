<!--Connection information to the DATA BASE-->
<?php
$host = "localhost";
$user = "sae23";
$pass = "sae23";
$dbname = "SAE23";

$connexion = mysqli_connect($host, $user, $pass, $dbname);

if (!$connexion) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
