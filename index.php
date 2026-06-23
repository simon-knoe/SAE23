<!-- description of the site's goals, display managed buildings, equiped rooms, legal mentions. -->
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
        <article><!-- description of the site's goals -->
            <h2>Bienvenue sur le site de supervision IoT de l'IUT de Blagnac</h2>
            <p>Ce site a pour objectif de superviser les bâtiments et les salles équipées de l'IUT de Blagnac. Vous pouvez consulter les données des capteurs, gérer les équipements et administrer le système.</p>
            <p>Si vous n'avez pas de compte gestion ou administrateur vous pouvez consulter les dernières données récupérées des capteurs en vous rendant sur la <a href="consultation.php">page de consultation</a>. Pour accéder aux fonctionnalités de gestion et d'administration rendez vous sur les pages correspondantes et connectez-vous.</p>
        </article>
        <article><!-- buildings and equiped rooms -->
            <h2>Les bâtiments et les salles équipées</h2>
            <?php
            require_once("db.php");

            $sql = "SELECT s.id_bat, s.salle
                    FROM salles s
                    ORDER BY s.id_bat, s.salle";

            $result = mysqli_query($connexion, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $data = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $id_build = $row['id_bat'];
                    $salle = $row['salle'];

                    if (!isset($data[$id_build])) {
                        $data[$id_build] = [];
                    }
                    $data[$id_build][] = $salle;
                }

                echo "<ul>";
                foreach ($data as $id_build => $salles) {
                    echo "<li><strong>Bâtiment :</strong> " . $id_build . "</li>";
                    
                    if (!empty($salles)) {
                        echo "<ul>";
                        foreach ($salles as $salle) {
                            echo "<li>Salle : " . $salle . "</li>";
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
        <article><!-- legal mentions -->
            <h2>Mentions légales</h2>
        </article>
    </body>
</html>
