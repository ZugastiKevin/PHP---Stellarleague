<?php 
    include_once('environnement.php');
    $title= 'Accueil';
    include_once 'function/head.php';
    if($_GET['message'] == 'success'){
        echo '<h1> Inscription réussie!</h1>'
    }
?>

<body>
    <?php include_once 'layout/header.php'; ?>
</body>