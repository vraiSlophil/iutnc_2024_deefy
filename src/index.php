<?php
session_set_cookie_params(3600);
session_start();

require_once '../vendor/autoload.php';
(Dotenv\Dotenv::createImmutable(__DIR__ . '/../'))->load();

$_SESSION['debug'] = (isset($_ENV) && $_ENV['APP_DEBUG'] === 'true') ?? false;

if ($_SESSION['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use iutnc\deefy\auth\Authn;
use iutnc\deefy\dispatch\Dispatcher;

$user = Authn::getAuthenticatedUser();
//
//if ($user == null) {
//    header('Location: ?action=logout');
//    exit();
//}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../public/style/style.css">
    <title>Iutnc - Deefy</title>
</head>
<body>
<nav>
    <h1>
        Iutnc - Deefy
    </h1>
    <form method="get" action="">
        <button type="submit" name="action" value="default">Accueil</button>
        <button type="submit" name="action" value="playlist">Afficher mes playlists</button>
        <button type="submit" name="action" value="add-playlist">Créer une nouvelle playlist</button>
        <?php
        if ($user !== null && $user->hasAccess(100)) {
            echo '<button type="submit" name="action" value="admin">Admin Panel</button>';
        }
        ?>
    </form>
    <form method="get" action="">

        <?php
        if ($user !== null) {
            echo '
                <p>
                    Connecté en tant que <b>' . $user->getUserName() . '</b>
                </p>
                <button type="submit" name="action" value="logout">Déconnexion</button>';

        } else {
            echo '
                <button type="submit" name="action" value="login">Connexion</button>
                <button type="submit" name="action" value="register">Enregistrement</button>';
        }
        ?>
    </form>

</nav>
<main>
    <?php
    try {
        $dispatcher = new Dispatcher();
        $dispatcher->run();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</main>
<script src="../public/script/script.js"></script>
</body>
</html>
