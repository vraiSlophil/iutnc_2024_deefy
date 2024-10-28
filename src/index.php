<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;


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
</nav>
<main>
    <form method="get" action="" class="form-index">
        <button type="submit" name="action" value="default">Default Action</button>
        <button type="submit" name="action" value="playlist">Display Playlist Action</button>
        <button type="submit" name="action" value="add-playlist">Add Playlist Action</button>
<!--        <button type="submit" name="action" value="add-track">Add Podcast Track Action</button>-->
    </form>
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
