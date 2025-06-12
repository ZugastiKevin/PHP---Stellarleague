<?php
    include('../environnement.php');
    if (isset($_SESSION['currentUser'])) {
        include('./session.php');
        deleteToken($_SESSION['currentUser']['id'], $bdd);
    } else {
        header('location:'.BASE_URL.'/index.php');
    }