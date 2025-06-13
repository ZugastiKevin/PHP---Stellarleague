<?php
    include_once('../environnement.php');
    if (isset($_GET['id'])) {
        if (isset($_SESSION["currentUser"]['role']) == 'user') {
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

            $checkDuplicate = $bdd->prepare(
                'SELECT user_id
                FROM usersPending_list
                WHERE user_id = :user_id AND pending_list_id = :pending_list_id
            ');
            $checkDuplicate->execute([
                'user_id' => $user_id,
                'pending_list_id' => $pendingListId
            ]);

            $alreadyPending = $checkDuplicate->fetch();
            if ($alreadyPending) {
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
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
        }else {
            header('location:'.BASE_URL.'/index.php');
        }
    } else {
        header('location:'.BASE_URL.'/index.php');
    }