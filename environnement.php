<?php
    ob_start();
    session_start();
    $bdd = new PDO('mysql:host=mysql;dbname=stellarleague;charset=utf8','root','root');

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
        
        $tokenUpdate = bin2hex(random_bytes(32));
        $time = time() + (7 * 24 * 60 * 60);

        $requestUpdateToken = $bdd->prepare(
            "UPDATE users 
            SET token = :token, tokenValidate = :tokenValidate
            WHERE id = :id
        ");
        $requestUpdateToken->execute(['id'=>$data['id'], 'token'=>sha1($token), 'tokenValidate'=>$time]);

        setcookie('token-user', $tokenUpdate, [
            'expires' => $time,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'lax'
        ]);
    }

    $projectRacine = str_replace('\\','/',realpath(__DIR__));
    $documentRoot = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
    $cheminRelatif = str_replace($documentRoot,'', $projectRacine);
    define('BASE_URL', $cheminRelatif);