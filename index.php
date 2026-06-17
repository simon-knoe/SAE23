<!-- description de l’objectif du site, affichage des bâtiments gérés, des salles équipées, mentions légales. -->
<!DOCTYPE html>
<html>
    <head>
        <title>Supervision IoT — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <header>
        <h1>Supervision IoT — IUT de Blagnac</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="consultation.php">Consultation des données</a></li>
                <li><a href="gestion.php">Gestion</a></li>
                <li><a href="administration.php">Administration</a></li>
                <li><a href="gestion-projet.php">Gestion de projet</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <article><!-- description de l’objectif du site -->
            <h2>Bienvenue sur le site de supervision IoT de l'IUT de Blagnac</h2>
            <p>Ce site a pour objectif de superviser les bâtiments et les salles équipées de l'IUT de Blagnac. Vous pouvez consulter les données des capteurs, gérer les équipements et administrer le système.</p>
            <p>Si vous n'avez pas de compte gestion ou administrateur vous pouvez consulter les dernières données récupérées des capteurs en vous rendant sur la <a href="consultation.php">page de consultation</a>. Pour accéder aux fonctionnalités de gestion et d'administration rendez vous sur les pages correspondantes et connectez-vous.</p>
        </article>
        <article><!-- bâtiments et salles équipées -->
            <h2>Les bâtiments et les salles équipées</h2>
            <?php
            require_once("db.php");
            $sql_build = "SELECT DISTINCT id_bat FROM salles";
            $result_build = mysqli_query($connexion, $sql_build);
            if ($result_build && mysqli_num_rows($result_build) > 0) {
                echo "<ul>";
                while ($row = mysqli_fetch_assoc($result_build)) {
                    $id_build = $row['id_bat'];

                    echo "<li><strong>Bâtiment :</strong> $id_build</li>";
                    
                    $sql_room = "SELECT DISTINCT salle FROM salles WHERE id_bat = '$id_build'";
                    $result_room = mysqli_query($connexion, $sql_room);

                    if ($result_room && mysqli_num_rows($result_room) > 0) {
                        echo "<ul>";
                        while ($row_salle = mysqli_fetch_assoc($result_room)) {
                            $salle = $row_salle['salle'];
                            echo "<li>Salle : $salle</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<ul><li>Aucune salle équipée dans ce bâtiment.</li></ul>";
                    }

                }
                echo "</ul>";
            } else {
                echo "<p>Aucun bâtiment équipé trouvé.</p>";
            }
            ?>
        </article>
        <article><!-- mentions légales -->
            <h2>Mentions légales</h2>
        </article>
    </body>
</html>