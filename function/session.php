<?php
    function deleteToken($id, $bdd) {
        $requestRemoveToken = $bdd->prepare(
            "UPDATE users
            SET token = :token, tokenValidate = :tokenValidate
            WHERE id = :id
        ");
        $requestRemoveToken->execute(['token'=>null, 'tokenValidate'=>null, 'id'=>$id]);
        setcookie('token-user', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'lax'
        ]);
        session_unset();
        session_destroy();
        header('location:'.BASE_URL.'/index.php');
    }

    function updateToken($id, $bdd) {
        $token = bin2hex(random_bytes(32));
        $time = time() + (7 * 24 * 60 * 60);
        $requestUpdateToken = $bdd->prepare(
            "UPDATE users 
            SET token = :token, tokenValidate = :tokenValidate
            WHERE id = :id
        ");
        $requestUpdateToken->execute(['id'=>$id, 'token'=>sha1($token), 'tokenValidate'=>$time]);

        setcookie('token-user', $token, [
            'expires' => $time,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'lax'
        ]);
    }

    function setSession($id, $pseudo, $role, $bdd) {
        $requestTournaments = $bdd->prepare(
            'SELECT one.tournament_id AS tournament_id, e.nameTournament AS nameTournament 
            FROM usersTournament one
            JOIN tournament e TO one.tournament_id = e.id
            WHERE user_id = :user_id
        ');
        $requestTournaments->execute(['user_id' => $id]);
        $tournaments = $requestTournaments->fetch();
        $_SESSION["currentUser"] = ['id'=>$id, 'pseudo'=>$pseudo, 'tournaments'=>$tournaments, 'role'=>$role];
    }

    function createSessionUserWithRemember($id, $pseudo, $role) {
        include_once(BASE_URL.'/environnement.php');
        setSession($id, $pseudo, $role, $bdd);
        updateToken($id, $bdd);
    }