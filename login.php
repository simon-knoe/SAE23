<!-- test -->
<?php
session_start();
require_once("db.php");

$error_message = "";

// Redirection after login
$redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Also get the redirect target
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php';

    $sql = "SELECT * FROM utilisateurs WHERE login = '$username' AND mdp = '$password'";
    $resultat = mysqli_query($connexion, $sql);

    if ($resultat && mysqli_num_rows($resultat) > 0) {
        $utilisateur = mysqli_fetch_assoc($resultat);

        $_SESSION['user_name'] = $utilisateur['login'];
        $_SESSION['user_role'] = $utilisateur['role']; 
        $_SESSION['user_building'] = $utilisateur['id_bat']; 

        // Redirection from where the user came
        header("Location: " . $redirect_to);
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - SAÉ 23</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <article>
        <h2>Connexion</h2>
        
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_to); ?>"> <!-- Pour garder la variable quand on rafraichi la page -->

            <p>
                <label>Utilisateur :</label><br>
                <input type="text" name="username" required>
            </p>
            <p>
                <label>Mot de passe :</label><br>
                <input type="password" name="password" required>
            </p>
            <button type="submit">Se connecter</button>
        </form>
    </article>
</body>
</html>