<?php
    $title = 'Créer un tournoi';
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
                    'INSERT INTO tournament(nameTournament,startAt,userLimit,prize,imgAvatar) 
                    VALUES (:nameTournament,:startAt,:userLimit,:prize,:imgAvatar)
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
            <h2>Créer un tournois</h2>
            <form action="tournament.php" method="post">
                <input placeholder="Entrez le nom du tournois" type="text" name="nameTournament" required>
                <input placeholder="Entrez la date du début" type="date" name="startAt" required>
                <input placeholder="Entrez le nombre maximum de joueurs" type="number" name="userLimit" required>
                <input placeholder="Entrez le Prix" type="text" name="prize" required>
                <input id="submit" type="submit" value="Créer le tournois">
            </form>
        </div>
    </main>
    <?php include_once('../function/scripts.php');?>
</body>