<!-- accessible uniquement par les Gestionnaires.
Affichage des mesures des capteurs de leur bâtiment uniquement.
Affichage des moyennes, min et max des salles de leur bâtiment.-->
<!-- Si utilisaeur non connecté/non role gestion, redirection vers login.php avec paramètre redirect=gestion.php -->
<?php
session_start();
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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
        <style>
            .dashboard {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-top: 20px;
            }
            .bloc-capteur {
                border: 2px solid #333;
                padding: 15px;
                width: 240px;
                text-align: center;
                background-color: #fafafa;
            }
            .valeur-grosse {
                font-size: 32px;
                font-weight: bold;
                margin: 15px 0;
            }
            .stats-ligne {
                font-size: 12px;
                border-top: 1px dashed #333;
                padding-top: 5px;
            }
        </style>
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

            <div class="dashboard">
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='bloc-capteur'>";
                            echo "<strong>Salle : " . $row['salle'] . "</strong><br>";
                            echo "<small>" . $row['capt_type'] . " (" . $row['capteur'] . ")</small>";
                            
                            echo "<div class='valeur-grosse'>";
                                echo $row['valeur_actuelle'] . " " . $row['unite'];
                            echo "</div>";
                            
                            echo "<div class='stats-ligne'>";
                                echo "Min: " . $row['min_val'] . " | ";
                                echo "Moy: " . $row['avg_val'] . " | ";
                                echo "Max: " . $row['max_val'];
                            echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Aucun capteur ou aucune mesure disponible pour votre bâtiment.</p>";
                }
                ?>
            </div>
        </section>
    </body>
</html>

