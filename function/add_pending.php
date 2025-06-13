<?php
    include_once('../environnement.php');
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET["id"]);
        $user_id = $_SESSION["currentUser"]['id'];
        
        $requestCheckPendingList = $bdd->prepare(
            'SELECT id 
            FROM pending_list 
            WHERE tournament_id = :tournament_id
        ');
        $requestCheckPendingList->execute(['tournament_id' => $id]);
        $pendingList = $requestCheckPendingList->fetch();

        if (!$pendingList) {
            $requestCreatePendingList = $bdd->prepare(
                'INSERT INTO pending_list(tournament_id)
                VALUES (:tournament_id)
            ');
            $requestCreatePendingList->execute(['tournament_id' => $id]);

            $requestSelectPendingList = $bdd->prepare(
                'SELECT id 
                FROM pending_list 
                WHERE tournament_id = :tournament_id
            ');
            $requestSelectPendingList->execute(['tournament_id' => $id]);
            $pendingListId = $requestSelectPendingList->fetch();
        } else {
            $pendingListId = $pendingList['id'];
        }

        $requestCreatePendingUser = $bdd->prepare(
            'INSERT INTO usersPending_list(user_id, pending_list_id)
            VALUES (:user_id,:pending_list_id)
        ');
        $requestCreatePendingUser->execute([
            'user_id' => $user_id,
            'pending_list_id' => $pendingListId
        ]);
        header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
    }