<?php
    ob_start();
    session_start();
    if (isset($_SESSION['currentUser'])) {
        include('./session.php');
        include('./call_bdd.php');
        deleteToken($_SESSION['currentUser']['id'], $bdd);
    } else {
        header('location:http://localhost:8080/PHP---Stellarleague/index.php');
    }