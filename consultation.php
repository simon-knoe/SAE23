<!-- accessible à tous qui affiche la dernière mesure de toutes les salles. -->
<?php
// Connexion à la base de données de ton LAMPP
require_once("db.php");

// Requête SQL optimisée pour récupérer la TOUTE DERNIÈRE mesure de chaque capteur avec ses infos
$sql = "SELECT m.capteur, m.date, m.horaire, m.valeur, c.salle, c.capt_type, c.unite 
        FROM mesures m
        INNER JOIN (
            SELECT capteur, MAX(CONCAT(date, ' ', horaire)) AS max_datetime
            FROM mesures
            GROUP BY capteur
        ) latest ON m.capteur = latest.capteur AND CONCAT(m.date, ' ', m.horaire) = latest.max_datetime
        LEFT JOIN capteurs c ON m.capteur = c.capteur
        ORDER BY c.salle, c.capt_type";

$result = mysqli_query($connexion, $sql);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Consultation des données — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <body>
        <header>
            <h1>Page de consultation</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="consultation.php" class="active">Consultation des données</a></li>
                    <li><a href="gestion.php">Gestion</a></li>
                    <li><a href="administration.php">Administration</a></li>
                    <li><a href="gestion-projet.php">Gestion de projet</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <article>
                <h2>Bienvenue sur la page de consultation</h2>
                <p>Trouvez ci-dessous les dernières données de tous les capteurs du site :</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Salle</th>
                            <th>Type de Capteur</th>
                            <th>Dernière Valeur</th>
                            <th>Date & Heure de capture</th>
                            <th>ID Capteur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Vérification et boucle d'affichage des lignes de données
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Formatage de la date en version française (JJ/MM/AAAA)
                                $date_fr = date("d/m/Y", strtotime($row['date']));
                                $heure_fr = $row['horaire'];
                                
                                // Gestion des valeurs par défaut si le capteur n'est pas encore lié proprement
                                $salle = !empty($row['salle']) ? $row['salle'] : "Inconnue";
                                $type = !empty($row['capt_type']) ? ucfirst($row['capt_type']) : "Inconnu";
                                $unite = !empty($row['unite']) ? $row['unite'] : "";

                                echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($salle) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($type) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['valeur']) . " " . htmlspecialchars($unite) . "</td>";
                                    echo "<td class='date-time'>Le " . $date_fr . " à " . $heure_fr . "</td>";
                                    echo "<td><code>" . htmlspecialchars($row['capteur']) . "</code></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>Aucune mesure n'a encore été enregistrée dans la base de données.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </article>
        </main>
    </body>
</html>
