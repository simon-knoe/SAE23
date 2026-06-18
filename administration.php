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

// Messages de suivi pour l'utilisateur
$msg_bat = "";
$msg_salle = "";

require_once("db.php");

// >>>>> New building
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_bat'])) {
    $id_bat = strtoupper(substr(trim($_POST['id_bat']), 0, 1)); 
    $nom_bat = trim($_POST['nom_bat']);

    if (empty($id_bat) || empty($nom_bat)) {
        $msg_bat = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        // SQL Request
        $sql_insert = "INSERT INTO batiments (id_bat, nom) VALUES (?, ?)";
        $stmt_insert = mysqli_prepare($connexion, $sql_insert);
        
        mysqli_stmt_bind_param($stmt_insert, "ss", $id_bat, $nom_bat);

        // Execution
        if (mysqli_stmt_execute($stmt_insert)) {
            $msg_bat = "<p>Le Bâtiment '$id_bat — $nom_bat' a bien été créé !</p>";
        } else {
            $msg_bat = "<p>Erreur lors de l'enregistrement.</p>";
        }

        mysqli_stmt_close($stmt_insert);
    }
}

// >>>>> New room
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_salle'])) {
    $nom_salle = trim($_POST['nom_salle']);
    $nom_type  = trim($_POST['nom_type']);
    $capacite  = intval($_POST['capacite']);
    $id_bat    = $_POST['id_bat_select'];

    if (empty($nom_salle) || empty($nom_type) || empty($id_bat)) {
        $msg_salle = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        // SQL Request
        $sql_insert_salle = "INSERT INTO salles (salle, type, capacite, id_bat) VALUES (?, ?, ?, ?)";
        $stmt_insert_salle = mysqli_prepare($connexion, $sql_insert_salle);
        
        mysqli_stmt_bind_param($stmt_insert_salle, "ssis", $nom_salle, $type, $capacite, $id_bat);

        // Execution
        if (mysqli_stmt_execute($stmt_insert_salle)) {
            $msg_salle = "<p>La salle '$nom_salle' a bien été ajoutée au bâtiment $id_bat !</p>";
        } else {
            $msg_salle = "<p>Erreur lors de l'enregistrement de la salle.</p>";
        }

        mysqli_stmt_close($stmt_insert_salle);
    }
}

// >>>>> New sensor
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_create_capteur'])) {
    $salle = trim($_POST['nom_salle']);
    $nom_type  = trim($_POST['nom_type']);

    if (empty($salle) || empty($type)) {
        $msg_salle = "<p>Tous les champs sont obligatoires.</p>";
    } else {
        // SQL Request
        $sql_insert_capteur = "INSERT INTO capteurs (nom_type, unite, salle) VALUES (?, ?, ?)";
        $stmt_insert_capteur = mysqli_prepare($connexion, $sql_insert_capteur);
        
        mysqli_stmt_bind_param($stmt_insert_capteur, "sss", $nom_type, $unite, $salle);

        // Execution
        if (mysqli_stmt_execute($stmt_insert_capteur)) {
            $msg_salle = "<p>Le capteur de '$nom_type' a bien été ajoutée à la salle $salle !</p>";
        } else {
            $msg_salle = "<p>Erreur lors de l'enregistrement du capteur.</p>";
        }

        mysqli_stmt_close($stmt_insert_capteur);
    }
}

// Getting buildings and rooms
$sql_list_bat = "SELECT id_bat, nom FROM batiments ORDER BY id_bat";
$result_list_bat = mysqli_query($connexion, $sql_list_bat);

$sql_list_salles = "SELECT salle, type, id_bat FROM salles ORDER BY id_bat";
$result_list_salles = mysqli_query($connexion, $sql_list_salles);

$sql_list_capteurs = "SELECT capteur, salle, nom_type, unite FROM salles ORDER BY salle";
$result_list_capteurs = mysqli_query($connexion, $sql_list_salles);
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
        <section>
            <h2>Bienvenue <?php echo $user_name; ?> sur votre page d'administration</h2>
            <p>Vous pouvez gérer différents bâtiments existants en créer de nouveaux, gérer les salles et capteurs du site.</p>
        </section>
        <!-- BATIMENTS -->
        <section>
            <h2>Gestion des bâtiments</h2>
            <article>
                <h3>Ajout de bâtiments</h3>
                <?php echo $msg_bat; ?>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_bat" value="1">

                    <label for="id_bat">Identifiant unique (1 lettre) :</label>
                    <input type="text" id="id_bat" name="id_bat" maxlength="1" placeholder="Ex: A, B, C..." required style="text-transform: uppercase;">

                    <label for="nom_bat">Nom complet du bâtiment :</label>
                    <input type="text" id="nom_bat" name="nom_bat" placeholder="Ex: Bâtiment Informatique" required>

                    <button type="submit">Enregistrer le bâtiment</button>
                </form>
            </article>
            <article>
                <h3>Suppression de bâtiments</h3>
            </article>
        </section>
        <!-- SALLES -->
        <section>
            <h2>Gestion des salles</h2>
            <article>
                <h3>Ajout de la salle</h3>
                <?php echo $msg_salle; ?>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_salle" value="1">

                    <label for="nom_salle">Nom de la salle (Unique) :</label>
                    <input type="text" id="nom_salle" name="nom_salle" placeholder="Ex: E102, Amphi B..." required>

                    <label for="nom_type">Type de salle :</label>
                    <select id="nom_type" name="nom_type" required>
                        <option value="Cours">TD</option>
                        <option value="TP">TP</option>
                        <option value="Amphi">Amphi</option>
                    </select>

                    <label for="capacite">Capacité (Nombre de places) :</label>
                    <input type="number" id="capacite" name="capacite" min="1" placeholder="Ex: 30" required>

                    <label for="id_bat_select">Bâtiment : </label>
                    <select id="id_bat_select" name="id_bat_select" required>
                        <option value="">-- Choisir un bâtiment --</option>
                        <?php 
                        // Automatic list of building
                        if ($result_list_bat && mysqli_num_rows($result_list_bat) > 0) {
                            while ($bat = mysqli_fetch_assoc($result_list_bat)) {
                                echo "<option value='" . $bat['id_bat'] . "'>";
                                echo $bat['id_bat'] . " — " . $bat['nom'];
                                echo "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Ajouter la salle</button>
                </form>
            </article>
            <article>
                <h3>Suppression de salles</h3>
            </article>
        </section>
        <!-- CAPTEURS -->
        <section>
            <h2>Gestion des capteurs</h2>
            <article>
                <h3>Ajout de capteur</h3>
                <?php echo $msg_capteur; ?>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="hidden" name="action_create_capteur" value="1">

                    <label for="nom_type">Type de capteur :</label>
                    <select id="nom_type" name="nom_type" required>
                        <option value="Temperature">Temperature</option>
                        <option value="Humidite">Humidite</option>
                        <option value="CO2">CO2</option>
                        <option value="Luminosite">Luminosite</option>
                    </select>

                    <label for="salle_select">Salle: </label>
                    <select id="salle_select" name="salle_select" required>
                        <?php 
                        // Automatic list of rooms
                        if ($result_list_salles && mysqli_num_rows($result_list_salles) > 0) {
                            while ($salle = mysqli_fetch_assoc($result_list_salles)) {
                                echo "<option value='" . $salle['salle'] . "'>";
                                echo $bat['salle'] . " — " . $salle['id-bat'];
                                echo "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Ajouter la salle</button>
                </form>
            </article>
            <article>
                <h3>Suppression de capteurs</h3>
            </article>
        </section>
    </body>
</html>
