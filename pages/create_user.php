<?php
    $title = 'Cree un compte';
    include('../function/head.php');

    if (isset($_POST["pseudo"]) && isset($_POST["email"]) && isset($_POST["password"])) {
        $pseudo = trim(strtolower(htmlspecialchars($_POST["pseudo"])));
        $email = trim(strtolower(htmlspecialchars($_POST["email"])));
        $encryption = password_hash(trim(htmlspecialchars($_POST["password"])), PASSWORD_ARGON2I);

        $requestExistUser = $bdd->prepare(
            'SELECT pseudo, email
            FROM users
            WHERE email = :email
        ');
        $requestExistUser->execute([
            'email' => $email,
        ]);
        $resultExist = $requestExistUser->fetch();

        if (!$resultExist['pseudo']) {
            if (!$resultExist['email']) {
                $requestCreate = $bdd->prepare(
                    'INSERT INTO users(pseudo,email,userRole,pass) 
                    VALUES (:pseudo,:email,:userRole,:pass)
                ');
                $requestCreate->execute([
                    'pseudo'=>$name,
                    'email'=>$email,
                    'userRole'=>'user',
                    'pass'=>$encryption
                ]);
                $requestSelectUser = $bdd->prepare(
                    'SELECT id, pseudo, userRole
                    FROM users
                    WHERE email = :email
                ');
                $requestSelectUser->execute(['email'=>$email]);
                $data = $requestSelectUser->fetch();
                setSession($data['id'], $data['pseudo'], $data['userRole']);
                header('location:http://localhost:8080/PHP---Stellarleague/index.php');
            } else {
                echo 'Cette email existe deja.';
            }
        } else {
            echo 'Ceux pseudo est deja pris.';
        }
    }
?>

<body>
    <?php require_once __DIR__ . '/../layout/header.php'; ?>
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
    <?php require_once __DIR__ . '/../function/scripts.php';?>
</body>