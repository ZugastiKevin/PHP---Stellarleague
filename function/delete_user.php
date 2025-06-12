<?php
    $title = 'Suppresion de compte';
    include_once('../function/head.php');
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET["id"]);
        if (isset($_SESSION["currentUser"]['id']) == $id OR isset($_SESSION["currentUser"]['role']) == 'admin') {
            $requestExistUser = $bdd->prepare(
                'DELETE FROM users
                WHERE id = :id
            ');
            $requestExistUser->execute(['id' => $id]);
            deleteToken($_SESSION['currentUser']['id'], $bdd);
        } else {
            header('location:'.BASE_URL.'/index.php');
        }
    } else {
        header('location:'.BASE_URL.'/index.php');
    }