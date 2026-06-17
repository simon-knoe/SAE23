<!-- accessible uniquement par les Gestionnaires.
Affichage des mesures des capteurs de leur bâtiment uniquement.
Affichage des moyennes, min et max des salles de leur bâtiment.-->
<!-- Si utilisaeur non connecté/non role gestion, redirection vers login.php avec paramètre redirect=gestion.php -->
<?php
session_start();
// Si utilisateur non connecté ou non role "gestion"
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'gestion') {
    
    $current_page = basename($_SERVER['SCRIPT_NAME']); 
        header("Location: login.php?redirect=" . urlencode($current_page));
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$user_building = $_SESSION['user_building'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <header>
        <h1>Page de gestion</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="consultation.php">Consultation des données</a></li>
                <li><a href="gestion.php" class="active">Gestion</a></li>
                <li><a href="administration.php">Administration</a></li>
                <li><a href="gestion-projet.php">Gestion de projet</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <article>
            <h2>Bienvenue <?php echo $user_name; ?> sur votre page de gestion</h2>
            <p>Vous pouvez consulter les mesures des capteurs du bâtiment <?php echo $user_building; ?></p>
        </article>
    </body>
</html>

