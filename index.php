<?php 
    include_once('environnement.php');
    $title= 'Accueil';
    include_once 'function/head.php';
?>

<body>
    <?php include_once 'layout/header.php';
        if(isset($_GET['message']) == 'success'){
        echo '<h1> Inscription réussie!</h1>';
    } ?>
    <main>
        <div class="login-container">
            <h2>Rejoindre un tournois</h2>
            <form action="index.php" method="post">
                <input placeholder="Chercher un tournois" type="tournois" name="tournois" required>
                <input placeholder="Votre email" type="email" name="email" required>
                <input type="text" name="pseudo" placeholder="Votre pseudo">
                <input id="submit" type="submit" value="Rejoindre ce tournois">
            </form>
        </div>
    </main>

</body>