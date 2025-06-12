<?php
    $title = 'Connection';
    include_once('../environnement.php');
    include_once('../function/head.php');

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim(strtolower(htmlspecialchars($_POST["email"])));
        $requestPrepareUser = $bdd->prepare(
            "SELECT id, pseudo, email, pass, user_role
            FROM users
            WHERE email = :email
        ");
        $requestPrepareUser->execute(['email'=>$email]);
        $data = $requestPrepareUser->fetch();
        $encryption = trim(htmlspecialchars($_POST["password"]));
        if (password_verify($encryption, $data['pass'])) {
            if (isset($_POST['remember-me']) == true) {
                createSessionUserWithRemember($data['id'], $data['pseudo'], $data['userRole']);
                header('location:'.BASE_URL.'/index.php');
            } else {
                setSession($data['id'], $data['pseudo'], $data['userRole']);
                header('location:'.BASE_URL.'/index.php');
            }
        } else {
            echo 'Mots de passe ou email incorrects';
        }
    }
?>

<body>
    <?php include_once('../layout/header.php'); ?>
    <main>
        <div>
            <form action="login.php" method="post">
                <label for="email">Votre email</label>
                <input type="email" name="email" required>
                <label for="password">Votre mots de passe</label>
                <input type="password" name="password" required>
                <label for="remember-me">
                    Se souvenir de moi ?
                    <input type="checkbox" name="remember-me" value="true">
                </label>
                <input type="submit" value="Connection">
            </form>
        </div>
    </main>
    <?php include_once('../function/scripts.php'); ?>
</body>