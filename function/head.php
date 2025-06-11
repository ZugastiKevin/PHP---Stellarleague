<?php
    include('/var/www/html/PHP---Stellarleague/function/session.php');
    if ($title == 'Accueil') {
        $linkStyle = 'assets/css/style.css';
    } else {
        $linkStyle = './../assets/css/style.css';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $linkStyle ?>">
    <script src="https://kit.fontawesome.com/84a235b10e.js" crossorigin="anonymous"></script>
</head>
</html>