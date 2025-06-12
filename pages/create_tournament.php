<?php
    $title = 'Crée un tournoi';
    include_once('../function/head.php');

    if (isset($_SESSION["currentUser"]['role']) == 'admin') {
        if (isset($_POST["nameTournament"]) && isset($_POST["startAt"]) && isset($_POST["userLimit"]) && isset($_POST["prize"])) {
            $nameTournament = trim(strtolower(htmlspecialchars($_POST["nameTournament"])));
            $startAt = trim(htmlspecialchars($_POST["startAt"]));
            $userLimit = trim(htmlspecialchars($_POST["userLimit"]));
            $prize = trim(strtolower(htmlspecialchars($_POST["prize"])));

            $requestExistTournament = $bdd->prepare(
                'SELECT nameTournament
                FROM tournament
                WHERE nameTournament = :nameTournament
            ');
            $requestExistTournament->execute(['nameTournament' => $nameTournament]);
            $resultExist = $requestExistTournament->fetch();
            
            if (!$resultExist) {
                $requestCreate = $bdd->prepare(
                    'INSERT INTO tournament(nameTournament,startAt,userLimit,prize) 
                    VALUES (:nameTournament,:startAt,:userLimit,:prize)
                ');
                $requestCreate->execute([
                    'nameTournament'=>$nameTournament,
                    'startAt'=>$startAt,
                    'userLimit'=>$userLimit,
                    'prize'=>$prize
                ]);
                header('location:'.BASE_URL.'/index.php?message=success');
            } else {
                echo '<p>Ceux tournoi existe deja.</p>';
            }
        }
    } else {
        header('location:'.BASE_URL.'/index.php');
    }
?>

<body>
    <?php include_once('../layout/header.php'); ?>
    <main>
        <div>
            <h2>Créer un compte Stellarleague</h2>
            <form action="create_user.php" method="post">
                <input placeholder="Entrez votre pseudo" type="text" name="pseudo" required>
                <input placeholder="Entrez votre email" type="email" name="email" required>
                <input placeholder="Entrez votre mots de passe" type="password" name="password" required>
                <input id="submit" type="submit" value="Rejoindre Stellar League">
            </form>
        </div>
    </main>
    <?php include_once('../function/scripts.php');?>
</body>