<?php
    ob_start();
    session_start();

    include('/var/www/html/PHP---Stellarleague/function/call_bdd.php');

    if (!empty($_COOKIE['token-user'])) {
        $token = $_COOKIE['token-user'];
        $requestSelectUser = $bdd->prepare(
            "SELECT id, user_name, user_role
            FROM users
            WHERE token = :token
        ");
        $requestSelectUser->execute(['token'=>sha1($token)]);
        $data = $requestSelectUser->fetch();
        $requestElements = $bdd->prepare(
            'SELECT element_type_id
            FROM usersElements
            WHERE user_id = :user_id
        ');
        $requestElements->execute(['user_id' => $data['id']]);
        $elements = $requestElements->fetch();
        $_SESSION["currentUser"] = ['id'=>$data['id'], 'name'=>$data['user_name'], 'elements'=>$elements, 'role'=>$data['user_role']];
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
        $requestUpdate = $bdd->prepare(
            "UPDATE users 
            SET token = :token, tokenValidate = :tokenValidate
            WHERE id = :id
        ");
        $requestUpdate->execute(['id'=>$id, 'token'=>sha1($token), 'tokenValidate'=>$time]);

        setcookie('token-user', $token, [
            'expires' => $time,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'lax'
        ]);
    }

    function setSession($id, $name, $role) {
        include('/var/www/html/PHP---Stellarleague/function/call_bdd.php');
        $requestElement = $bdd->prepare(
            "SELECT e.id, e.name_element
            FROM usersElements one
            JOIN elements_type e ON one.element_type_id = e.id
            WHERE user_id = :user_id
        ");
        $requestElement->execute(['user_id'=>$id]);
        $resultElement = $requestElement->fetchAll();
        $_SESSION["currentUser"] = ['id'=>$id, 'name'=>$name, 'elements'=>$resultElement, 'role'=>$role];
    }

    function createSessionUserWithRemember($id, $name, $role) {
        setSession($id, $name, $role);
        updateToken($id);
    }