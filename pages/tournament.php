<?php
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET["id"]);
        $requestSelectTournament = $bdd->prepare(
            "SELECT *
            FROM tournament one
            JOIN pending_list e ON 
            WHERE id = :id
        ");
        $requestSelectTournament->execute(['id'=>$id]);
        $tournament = $requestSelectTournament->fetch();

        $title = $tournament['nameTournament'];
        include_once('../function/head.php');

        if (isset($_SESSION["currentUser"]['role']) == 'admin') {
            $requestSelectAllUsers = $bdd->prepare(
                "SELECT *
                FROM users
                WHERE userRole = :userRole
            ");
            $requestSelectAllUsers->execute(['userRole'=>'user']);
            $allUsers = $requestSelectAllUsers->fetch();

            $requestSelectAllPendingUsers = $bdd->prepare(
                "SELECT u.id, u.pseudo
                FROM pending_list one
                JOIN usersPending_list e ON e.pending_list_id = one.id
                JOIN users u ON u.id = e.user_id
                WHERE one.tournament_id = :tournament_id
            ");
            $requestSelectAllPendingUsers->execute(['tournament_id'=>$id]);
            if (isset($_POST["nameTournament"]) && isset($_POST["startAt"]) && isset($_POST["userLimit"]) && isset($_POST["prize"])) {
                $nameTournament = trim(strtolower(htmlspecialchars($_POST["nameTournament"])));
                $startAt = trim(htmlspecialchars($_POST["startAt"]));
                $userLimit = trim(htmlspecialchars($_POST["userLimit"]));
                $prize = trim(strtolower(htmlspecialchars($_POST["prize"])));

                $requestUpdateTournament = $bdd->prepare(
                    'UPDATE tournament
                    SET (:nameTournament,:startAt,:userLimit,:prize)
                    WHERE id = :id
                ');
                $requestUpdateTournament->execute([
                    'id' => $id,
                    'nameTournament'=>$nameTournament,
                    'startAt'=>$startAt,
                    'userLimit'=>$userLimit,
                    'prize'=>$prize
                ]);
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
            if (isset($_POST["validate_user"]) && isset($_POST["validated_user_id"])) {
                $validatedUserId = htmlspecialchars((int)$_POST["validated_user_id"]);

                $requestInsertUserTournament = $bdd->prepare(
                    'INSERT INTO usersTournament(user_id, tournament_id)
                    VALUES (:user_id, :tournament_id)
                ');
                $requestInsertUserTournament->execute([
                    'user_id' => $validatedUserId,
                    'tournament_id' => $id
                ]);

                $deleteFromPending = $bdd->prepare(
                    'DELETE usersPending_list
                    FROM usersPending_list one
                    JOIN pending_list e ON one.pending_list_id = e.id
                    WHERE one.user_id = :user_id AND e.tournament_id = :tournament_id
                ');
                foreach ($validatedUserId as $userId) {
                    $deleteFromPending->execute([
                        'user_id' => $validatedUserId,
                        'tournament_id' => $id
                    ]);
                }
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
        }
    } else {
        header('location:'.BASE_URL.'/index.php');
    }
?>
<body>
    <?php include_once('../layout/header.php');?>
    <main id="main-tournament">
        <ul>
            <li class="coming-tournament">Tournois 1
                <div class="round1">
                    <ul>
                        <li>
                            Joueurs Match 1
                        </li>
                        <li></li>
                    </ul>
                </div>
                <div class="round2">
                    <ul>
                        <li>
                            Joueurs Match 2
                        </li>
                        <li></li>
                    </ul>
                </div>
            </li>

            <li class="coming-tournament">Tournois 1
                <div class="round1">
                    <ul>
                        <li>
                            Joueurs Match 1
                        </li>
                        <li></li>
                    </ul>
                </div>
                <div class="round2">
                    <ul>
                        <li>
                            Joueurs Match 2
                        </li>
                        <li></li>
                    </ul>
                </div>
            </li>
            </li>
            <li class="past-tournament">Tournois 1
                <div class="round1">
                    <ul>
                        <li>
                            Joueurs Match 1
                        </li>
                        <li></li>
                    </ul>
                </div>
                <div class="round2">
                    <ul>
                        <li>
                            Joueurs Match 2
                        </li>
                        <li></li>
                    </ul>
                </div>
            </li>
            </li>
        </ul>
    </main>
    <?php include_once('../function/scripts.php');?>
</body>