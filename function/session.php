<?php
    ob_start();
    session_start();

    include('/var/www/html/PHP---Stellarleague/function/call_bdd.php');

    if (!empty($_COOKIE['token-user'])) {
        $token = $_COOKIE['token-user'];
        $requestSelectUser = $bdd->prepare(
            "SELECT id, pseudo, userRole
            FROM users
            WHERE token = :token
        ");
        $requestSelectUser->execute(['token'=>sha1($token)]);
        $data = $requestSelectUser->fetch();
        $requestTournaments = $bdd->prepare(
            'SELECT one.tournament_id AS tournament_id, e.nameTournament AS nameTournament 
            FROM usersTournament one
            JOIN tournament e TO one.tournament_id = e.id
            WHERE user_id = :user_id
        ');
        $requestTournaments->execute(['user_id' => $data['id']]);
        $tournaments = $requestTournaments->fetch();
        $_SESSION["currentUser"] = ['id'=>$data['id'], 'pseudo'=>$data['pseudo'], 'tournaments'=>$tournaments, 'role'=>$data['userRole']];
        updateToken($data['id']);
    }

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
        header("location:http://localhost:8080/PHP---Stellarleague/index.php");
    }

    function updateToken($id) {
        include('/var/www/html/PHP---Stellarleague/function/call_bdd.php');
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

    function setSession($id, $pseudo, $role) {
        include('/var/www/html/PHP---Stellarleague/function/call_bdd.php');
        $requestTournaments = $bdd->prepare(
            'SELECT one.tournament_id AS tournament_id, e.nameTournament AS nameTournament 
            FROM usersTournament one
            JOIN tournament e TO one.tournament_id = e.id
            WHERE user_id = :user_id
        ');
        $requestTournaments->execute(['user_id' => $id]);
        $tournaments = $requestTournaments->fetch();
        $_SESSION["currentUser"] = ['id'=>$data['id'], 'pseudo'=>$pseudo, 'tournaments'=>$tournaments, 'role'=>$role];
    }

    function createSessionUserWithRemember($id, $pseudo, $role) {
        setSession($id, $pseudo, $role);
        updateToken($id);
    }