<!-- accessible uniquement par l’Administrateur du site (login/mdp). Ajout/suppression de bâtiments, salles et capteurs.-->
<!-- Si utilisaeur non connecté/non role admin, redirection vers login.php avec paramètre redirect=administration.php -->
<?php
session_start();
// Si utilisateur non connecté ou non role "admin"
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'admin') {
    
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
        <title>Administration — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <header>
        <h1>Page d'administration</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="consultation.php">Consultation des données</a></li>
                <li><a href="gestion.php">Gestion</a></li>
                <li><a href="administration.php" class="active">Administration</a></li>
                <li><a href="gestion-projet.php">Gestion de projet</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <article>
            <h2>Bienvenue <?php echo $user_name; ?> sur votre page d'administration</h2>
            <p>Vous pouvez gérer différents bâtiments existants en créer de nouveaux, gérer les salles et capteurs du site.</p>
        </article>
    </body>
</html>
