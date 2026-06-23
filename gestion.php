<!-- Accesible only for managers.
Displays data from buildings they manage only.
Displays average, min et max from their buildings' rooms.-->
<!-- If not connected/no manager role User, redirection to login.php with parameter redirect=gestion.php -->
<?php
session_start();

// Page's protection : redirection if user isn't a manager
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'gestion') {
    $current_page = basename($_SERVER['SCRIPT_NAME']); 
    header("Location: login.php?redirect=" . urlencode($current_page));
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$user_building = $_SESSION['user_building'];

require_once("db.php");


$sql = "SELECT 
            c.capteur, 
            c.salle, 
            c.capt_type,
            m_stat.min_val,
            m_stat.max_val,
            m_stat.avg_val,
            m_actuelle.valeur AS valeur_actuelle,
            m_actuelle.unite
        FROM capteurs c
        INNER JOIN salles s ON s.salle = c.salle
        INNER JOIN (
            SELECT capteur, MIN(valeur) AS min_val, MAX(valeur) AS max_val, ROUND(AVG(valeur), 1) AS avg_val
            FROM mesures
            GROUP BY capteur
        ) m_stat ON m_stat.capteur = c.capteur
        INNER JOIN (
            SELECT m1.capteur, m1.valeur, m1.unite
            FROM mesures m1
            WHERE m1.id_mesure = (
                SELECT m2.id_mesure 
                FROM mesures m2 
                WHERE m2.capteur = m1.capteur 
                ORDER BY m2.date DESC, m2.horaire DESC 
                LIMIT 1
            )
        ) m_actuelle ON m_actuelle.capteur = c.capteur
        WHERE s.id_bat = '$user_building'
        ORDER BY c.salle, c.capteur";

$result = mysqli_query($connexion, $sql);
echo $result
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <body>
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

        <section>
            <article>
                <h2>Bienvenue <?php echo $user_name; ?> sur votre page de gestion</h2>
                <p>Vous pouvez consulter les mesures des capteurs du bâtiment <?php echo $user_building; ?></p>
            </article>

            <div>
                <?php
                // REQUEST 1 : Get all rooms in the manager's building
                $sql_salles = "SELECT salle FROM salles WHERE id_bat = '" . mysqli_real_escape_string($connexion, $user_building) . "' ORDER BY salle";
                $result_salles = mysqli_query($connexion, $sql_salles);

                if ($result_salles && mysqli_num_rows($result_salles) > 0) {
                    
                    // loop of each room
                    while ($salle_row = mysqli_fetch_assoc($result_salles)) {
                        $salle_actuelle = $salle_row['salle'];
                        
                        echo "<h3>Salle : " . $salle_actuelle . "</h3>";

                        // REQUEST 2 : For the current room, get all the sensors linked
                        $sql_capteurs = "SELECT capteur, capt_type, unite FROM capteurs WHERE salle = '" . mysqli_real_escape_string($connexion, $salle_actuelle) . "' ORDER BY capt_type";
                        $result_capteurs = mysqli_query($connexion, $sql_capteurs);

                        if ($result_capteurs && mysqli_num_rows($result_capteurs) > 0) {
                            
                            // loop of each sensor of the room
                            while ($capteur_row = mysqli_fetch_assoc($result_capteurs)) {
                                $id_capteur = $capteur_row['capteur'];
                                $unite = $capteur_row['unite'];

                                // REQUEST 3 : Compute MIN, MAX, AVG and get the last value for this sensor
                                $sql_stats = "SELECT 
                                                MIN(valeur) AS min_val, 
                                                MAX(valeur) AS max_val, 
                                                ROUND(AVG(valeur), 1) AS avg_val,
                                                (SELECT valeur FROM mesures WHERE capteur = '" . mysqli_real_escape_string($connexion, $id_capteur) . "' ORDER BY date DESC, horaire DESC LIMIT 1) AS valeur_actuelle
                                              FROM mesures 
                                              WHERE capteur = '" . mysqli_real_escape_string($connexion, $id_capteur) . "'";
                                
                                $result_stats = mysqli_query($connexion, $sql_stats);
                                $stats = mysqli_fetch_assoc($result_stats);

                                // Default value if no measures exist
                                $val_actuelle = ($stats['valeur_actuelle'] !== null) ? $stats['valeur_actuelle'] : "--";
                                $min_val = ($stats['min_val'] !== null) ? $stats['min_val'] : "--";
                                $max_val = ($stats['max_val'] !== null) ? $stats['max_val'] : "--";
                                $avg_val = ($stats['avg_val'] !== null) ? $stats['avg_val'] : "--";

                                // Raw display of sensor information
                                echo "<div class='bloc-capteur'>";
                                    echo "<div>"
                                        echo "<strong>" . ucfirst($capteur_row['capt_type']) . "</strong> (" . $id_capteur . ") :<br>";
                                    echo "</div>"
                                    echo "<div class='valeur-grosse'>"
                                        echo $val_actuelle . " " . $unite . "<br>";
                                    echo "</div>"
                                    echo "<div class='stats-ligne'>"
                                        echo "Min : " . $min_val . " | Moy : " . $avg_val . " | Max : " . $max_val;
                                    echo "</div>"
                                echo "</div>";
                            }
                        } else {
                            echo "<p>Aucun capteur configuré dans cette salle.</p>";
                        }
                    }
                } else {
                    echo "<p>Aucune salle enregistrée pour votre bâtiment.</p>";
                }
                ?>
            </div>
        </section>
    </body>
</html>
