<?php 
    include_once('environnement.php');
    $title= 'Accueil';
    include_once 'function/head.php';
?>

<body>
    <?php include_once 'layout/header.php'; 
    if(isset($_SESSION["currentUser"])){
        echo 'Bienvenue, ' . $_SESSION["currentUser"]['pseudo'];
        var_dump($_SESSION);
    }else{
        echo 'Hello!';
    }
    ?>
    

    
</body>