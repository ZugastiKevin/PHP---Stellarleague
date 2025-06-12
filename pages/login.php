<?php
    $title = 'Connection';
    include_once('../function/head.php');
    include_once('../function/session.php');

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim(strtolower(htmlspecialchars($_POST["email"])));
        $requestPrepareUser = $bdd->prepare(
            "SELECT id, pseudo, email, pass, userRole
            FROM users
            WHERE email = :email
        ");
        $requestPrepareUser->execute(['email'=>$email]);
        $data = $requestPrepareUser->fetch();
        $encryption = trim(htmlspecialchars($_POST["password"]));
        if (password_verify($encryption, $data['pass'])) {
            if (isset($_POST['remember-me']) == true) {
                createSessionUserWithRemember($data['id'], $data['pseudo'], $data['userRole'], $bdd);
                header('location:'.BASE_URL.'/index.php');
            } else {
                setSession($data['id'], $data['pseudo'], $data['userRole'], $bdd);
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
        <div class="login-container">
            <h2>Connexion</h2>
            <form action="login.php" method="post">
                <input placeholder="Votre email" type="email" name="email" required>
                <input placeholder="Votre mot de passe" type="password" name="password" required>
                <label for="remember-me">
                    Se souvenir de moi ?
                    <input type="checkbox" name="remember-me" value="true">
                </label>
                <input id="submit" type="submit" value="Connexion">
            </form>
        </div>
    </main>
    <?php include_once('../function/scripts.php'); ?>
</body>