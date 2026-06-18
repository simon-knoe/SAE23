<?php
session_start();

if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

$msg_bat = "";
$msg_salle = "";
$msg_capteur = "";

require_once("db.php");

// New bulding
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_bat'])) {
    $id_bat = strtoupper(substr(trim($_POST['id_bat']), 0, 1)); 
    $nom_bat = trim($_POST['nom_bat']);

    if (empty($id_bat) || empty($nom_bat)) {
        $msg_bat = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        $sql = "INSERT INTO batiments (id_bat, nom) VALUES ('$id_bat', '$nom_bat')";
        if (mysqli_query($connexion, $sql)) {
            $msg_bat = "<p>Le Bâtiment '$id_bat — $nom_bat' a bien été créé !</p>";
        } else {
            $msg_bat = "<p>Erreur lors de l'enregistrement.</p>";
        }
    }
}

// New room
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_salle'])) {
    $nom_salle  = trim($_POST['nom_salle']);
    $salle_type = trim($_POST['salle_type']); 
    $capacite   = intval($_POST['capacite']);
    $id_bat     = $_POST['id_bat_select'];

    if (empty($nom_salle) || empty($salle_type) || empty($id_bat)) {
        $msg_salle = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        $sql = "INSERT INTO salles (salle, salle_type, capacite, id_bat) 
                VALUES ('$nom_salle', '$salle_type', $capacite, '$id_bat')";
        if (mysqli_query($connexion, $sql)) {
            $msg_salle = "<p>La salle '$nom_salle' a bien été ajoutée au bâtiment $id_bat !</p>";
        } else {
            $msg_salle = "<p>Erreur lors de l'enregistrement de la salle.</p>";
        }
    }
}

// New sensor
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_capteur'])) {
    $salle     = $_POST['salle_select'];
    $capt_type = trim($_POST['capt_type']); 

    $unite = "°C"; 
    if ($capt_type == "Humidite") $unite = "%";
    if ($capt_type == "CO2") $unite = "ppm";
    if ($capt_type == "Luminosite") $unite = "lux";

    if (empty($salle) || empty($capt_type)) {
        $msg_capteur = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        $nom_capteur = substr($capt_type, 0, 4) . "_" . $salle;

        $sql = "INSERT INTO capteurs (capteur, capt_type, unite, salle) 
                VALUES ('$nom_capteur', '$capt_type', '$unite', '$salle')";
        if (mysqli_query($connexion, $sql)) {
            $msg_capteur = "<p>Le capteur '$nom_capteur' a bien été ajouté à la salle $salle !</p>";
        } else {
            $msg_capteur = "<p>Erreur lors de l'enregistrement du capteur.</p>";
        }
    }
}

// del bulding
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_delete_bat'])) {
    $id_bat_del = $_POST['id_bat_delete'];
    if (!empty($id_bat_del)) {
        $sql = "DELETE FROM batiments WHERE id_bat = '$id_bat_del'";
        if (mysqli_query($connexion, $sql)) {
            $msg_bat = "<p>Bâtiment $id_bat_del supprimé avec succès !</p>";
        } else {
            $msg_bat = "<p>Impossible de supprimer : vérifiez que des salles ne sont pas encore liées à ce bâtiment.</p>";
        }
    }
}

// del room
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_delete_salle'])) {
    $salle_del = $_POST['salle_delete'];
    if (!empty($salle_del)) {
        $sql = "DELETE FROM salles WHERE salle = '$salle_del'";
        if (mysqli_query($connexion, $sql)) {
            $msg_salle = "<p>Salle $salle_del supprimée avec succès !</p>";
        } else {
            $msg_salle = "<p>Impossible de supprimer : vérifiez que des capteurs ne sont pas encore liés à cette salle.</p>";
        }
    }
}

// del sensor
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_delete_capteur'])) {
    $capteur_del = $_POST['capteur_delete'];
    if (!empty($capteur_del)) {
        $sql = "DELETE FROM meusures WHERE capteur = '$capteur_del';
                DELETE FROM capteurs WHERE capteur = '$capteur_del'";
        if (mysqli_query($connexion, $sql)) {
            $msg_capteur = "<p>Capteur $capteur_del supprimé avec succès !</p>";
        } else {
            $msg_capteur = "<p>Impossible de supprimer : des mesures y sont peut-être associées.</p>";
        }
    }
}

$result_list_bat = mysqli_query($connexion, "SELECT id_bat, nom FROM batiments ORDER BY id_bat");
$result_list_salles = mysqli_query($connexion, "SELECT salle, id_bat FROM salles ORDER BY id_bat");
$result_list_capteurs = mysqli_query($connexion, "SELECT capteur, salle FROM capteurs ORDER BY capteur");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Administration — IUT de Blagnac</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <body>
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
        
        <section>
            <h2>Bienvenue <?php echo $user_name; ?> sur votre page d'administration</h2>
            <p>Vous pouvez gérer les différents éléments du réseau : ajouter ou supprimer des bâtiments, des salles et des capteurs.</p>
        </section>
        
        <section>
            <h2>Gestion des bâtiments</h2>
            <?php echo $msg_bat; ?>
            <article>
                <h3>Ajout de bâtiment</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_bat" value="1">

                    <label for="id_bat">Identifiant unique (1 lettre) :</label>
                    <input type="text" id="id_bat" name="id_bat" maxlength="1" placeholder="Ex: A, B..." required style="text-transform: uppercase;">

                    <label for="nom_bat">Nom complet du bâtiment :</label>
                    <input type="text" id="nom_bat" name="nom_bat" placeholder="Ex: Bâtiment Informatique" required>

                    <button type="submit">Enregistrer le bâtiment</button>
                </form>
            </article>

            <article>
                <h3>Suppression de bâtiment</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_delete_bat" value="1">

                    <label for="id_bat_delete">Choisir le bâtiment à supprimer :</label>
                    <select id="id_bat_delete" name="id_bat_delete" required>
                        <option value="">-- Choisir --</option>
                        <?php 
                        if (mysqli_num_rows($result_list_bat) > 0) {
                            mysqli_data_seek($result_list_bat, 0);
                            while ($bat = mysqli_fetch_assoc($result_list_bat)) {
                                echo "<option value='" . $bat['id_bat'] . "'>" . $bat['nom'] . " (" . $bat['id_bat'] . ")</option>";
                            }
                        }
                        ?>
                    </select>

                    <button type="submit" onclick="return confirm('Supprimer ce bâtiment ?');">Supprimer définitivement</button>
                </form>
            </article>
        </section>
        
        <section>
            <h2>Gestion des salles</h2>
            <?php echo $msg_salle; ?>
            <article>
                <h3>Ajout de salle</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_salle" value="1">

                    <label for="nom_salle">Nom de la salle (Unique) :</label>
                    <input type="text" id="nom_salle" name="nom_salle" placeholder="Ex: E102, Amphi B..." required>

                    <label for="salle_type">Type de salle :</label>
                    <select id="salle_type" name="salle_type" required>
                        <option value="Cours">TD</option>
                        <option value="TP">TP</option>
                        <option value="Amphi">Amphi</option>
                    </select>

                    <label for="capacite">Capacité (Nombre de places) :</label>
                    <input type="number" id="capacite" name="capacite" min="1" placeholder="Ex: 30" required>

                    <label for="id_bat_select">Bâtiment associé : </label>
                    <select id="id_bat_select" name="id_bat_select" required>
                        <option value="">-- Choisir un bâtiment --</option>
                        <?php 
                        if (mysqli_num_rows($result_list_bat) > 0) {
                            mysqli_data_seek($result_list_bat, 0);
                            while ($bat = mysqli_fetch_assoc($result_list_bat)) {
                                echo "<option value='" . $bat['id_bat'] . "'>" . $bat['nom'] . " (" . $bat['id_bat'] . ")</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Ajouter la salle</button>
                </form>
            </article>

            <article>
                <h3>Suppression de salle</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_delete_salle" value="1">

                    <label for="salle_delete">Choisir la salle à supprimer :</label>
                    <select id="salle_delete" name="salle_delete" required>
                        <option value="">-- Choisir --</option>
                        <?php 
                        if (mysqli_num_rows($result_list_salles) > 0) {
                            mysqli_data_seek($result_list_salles, 0);
                            while ($salle = mysqli_fetch_assoc($result_list_salles)) {
                                echo "<option value='" . $salle['salle'] . "'>" . $salle['salle'] . " (Bâtiment " . $salle['id_bat'] . ")</option>";
                            }
                        }
                        ?>
                    </select>

                    <button type="submit" onclick="return confirm('Supprimer cette salle ?');">Supprimer définitivement</button>
                </form>
            </article>
        </section>
        
        <section>
            <h2>Gestion des capteurs</h2>
            <?php echo $msg_capteur; ?>
            <article>
                <h3>Ajout de capteur</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_capteur" value="1">

                    <label for="capt_type">Type de capteur :</label>
                    <select id="capt_type" name="capt_type" required>
                        <option value="Temperature">Temperature</option>
                        <option value="Humidite">Humidite</option>
                        <option value="CO2">CO2</option>
                        <option value="Luminosite">Luminosite</option>
                    </select>

                    <label for="salle_select">Placer dans la salle : </label>
                    <select id="salle_select" name="salle_select" required>
                        <option value="">-- Choisir une salle --</option>
                        <?php 
                        if (mysqli_num_rows($result_list_salles) > 0) {
                            mysqli_data_seek($result_list_salles, 0);
                            while ($salle = mysqli_fetch_assoc($result_list_salles)) {
                                echo "<option value='" . $salle['salle'] . "'>" . $salle['salle'] . " — Bâtiment " . $salle['id_bat'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Ajouter le capteur</button>
                </form>
            </article>

            <article>
                <h3>Suppression de capteur</h3>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_delete_capteur" value="1">

                    <label for="capteur_delete">Choisir le capteur à supprimer :</label>
                    <select id="capteur_delete" name="capteur_delete" required>
                        <option value="">-- Choisir --</option>
                        <?php 
                        if (mysqli_num_rows($result_list_capteurs) > 0) {
                            while ($capt = mysqli_fetch_assoc($result_list_capteurs)) {
                                echo "<option value='" . $capt['capteur'] . "'>" . $capt['capteur'] . " (Salle " . $capt['salle'] . ")</option>";
                            }
                        }
                        ?>
                    </select>

                    <button type="submit" onclick="return confirm('Supprimer ce capteur ?');">Supprimer définitivement</button>
                </form>
            </article>
        </section>
    </body>
</html>