<!-- accessible à tous qui affiche la dernière mesure de toutes les salles. -->
<!DOCTYPE html>
<html>
    <head>
        <title>Consultation des données — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
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
    <body>
        <article>
            <h2>Bienvenue sur la page de consultation</h2>
            <p>Trouvez ci dessous les dernières données de tout les capteurs du site : </p>
            <?php
                require_once("db.php");

                $sql = "SELECT s.id_bat, s.salle, c.capteur, m.valeur, m.unite, m.date, m.horaire
                        FROM salles s
                        LEFT JOIN capteurs c ON s.salle = c.salle
                        LEFT JOIN mesures m ON c.capteur = m.capteur AND m.id_mesure = (
                            SELECT m2.id_mesure 
                            FROM mesures m2 
                            WHERE m2.capteur = c.capteur 
                            ORDER BY m2.date DESC, m2.horaire DESC 
                            LIMIT 1
                        )
                        ORDER BY s.id_bat, s.salle, c.capteur";

                $result = mysqli_query($connexion, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    // 2. DATA STRUCTURING
                    $data = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $b = $row['id_bat'];
                        $s = $row['salle'];
                        $c = $row['capteur'];
                        
                        if (!isset($data[$b])) {
                            $data[$b] = [];
                        }
                        if (!isset($data[$b][$s])) {
                            $data[$b][$s] = [];
                        }
                        
                        if ($c !== null) {
                            // Store the sensor name along with its last measurement details
                            $data[$b][$s][] = [
                                'nom'     => $c,
                                'valeur'  => $row['valeur'],
                                'unite'   => $row['unite'],
                                'date'    => $row['date'],
                                'horaire' => $row['horaire']
                            ];
                        }
                    }

                    // 3. HTML RENDER
                    echo "<ul>";
                    foreach ($data as $id_build => $salles) {
                        echo "<li><strong>Bâtiment :</strong> " . $id_build . "</li>";
                        
                        if (!empty($salles)) {
                            echo "<ul>";
                            foreach ($salles as $salle => $capteurs) {
                                echo "<li>Salle : " . $salle . "</li>";
                                
                                echo "<ul>";
                                if (!empty($capteurs)) {
                                    foreach ($capteurs as $capteurData) {
                                        $nom_capteur = $capteurData['nom'];
                                        
                                        // Check if a measurement exists for this sensor
                                        if ($capteurData['valeur'] !== null) {
                                            $valeur  = $capteurData['valeur'];
                                            $unite   = $capteurData['unite'];
                                            $date    = $capteurData['date'];
                                            
                                            // Format the time to show only hours and minutes (HH:mm)
                                            $heure_formattee = date('H:i', strtotime($capteurData['horaire']));

                                            echo "<li>$nom_capteur — <em>Dernière mesure : $valeur $unite (le $date à $heure_formattee)</em></li>";
                                        } else {
                                            echo "<li>$nom_capteur — <em>Aucune mesure enregistrée</em></li>";
                                        }
                                    }
                                } else {
                                    echo "<li>Aucun capteur équipé dans cette salle.</li>";
                                }
                                echo "</ul>";
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
    </body>
</html>
