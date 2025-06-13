<?php
    include_once('../environnement.php');
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET["id"]);
        $requestSelectTournament = $bdd->prepare(
            'SELECT *
            FROM tournament
            WHERE id = :id
        ');
        $requestSelectTournament->execute(['id'=>$id]);
        $tournament = $requestSelectTournament->fetch();

        $requestSelectClassement = $bdd->prepare(
            'SELECT one.id AS classement_id, e.id AS user_id, e.pseudo AS pseudo, u.id AS user_id_stop, u.pseudo AS pseudo_stop
            FROM classement one
            JOIN users e ON one.user_id_continue = e.id
            LEFT JOIN users u ON one.user_id_stop = u.id
            WHERE one.tournament_id = :tournament_id
        ');
        $requestSelectClassement->execute(['tournament_id'=>$id]);
        $classement = $requestSelectClassement->fetchAll();

        date_default_timezone_set('Europe/Paris');
        $defaultStartAt = date('Y-m-d\TH:i', $tournament['startAt']);

        $title = 'Tournoi, '.$tournament['nameTournament'];
        include_once('../function/head.php');

        if (isset($_SESSION["currentUser"]['role']) == 'admin') {
            if (isset($_POST['start_fight'])) {
                $requestActiveUsers = $bdd->prepare(
                    'SELECT user_id_continue FROM classement
                    WHERE tournament_id = :tournament_id
                    AND user_id_continue IS NOT NULL AND user_id_continue != 0'
                );
                $requestActiveUsers->execute(['tournament_id' => $id]);
                $users = $requestActiveUsers->fetchAll(PDO::FETCH_COLUMN);

                if (count($users) >= 2) {
                    shuffle($users);
                    $round = 1;

                    $insertGame = $bdd->prepare(
                        'INSERT INTO game (user_1_id, user_2_id, tournament_id, round_number)
                        VALUES (:user1, :user2, :tournament_id, :round)'
                    );

                    for ($i = 0; $i < count($users) - 1; $i += 2) {
                        $insertGame->execute([
                            'user1' => $users[$i],
                            'user2' => $users[$i + 1],
                            'tournament_id' => $id,
                            'round' => $round
                        ]);
                    }

                    if (count($users) % 2 === 1) {
                        $last = end($users);
                        $autoWin = $bdd->prepare(
                            'INSERT INTO game (user_1_id, user_2_id, tournament_id, round_number, winner_id)
                            VALUES (:user, NULL, :tournament_id, :round, :winner)'
                        );
                        $autoWin->execute([
                            'user' => $last,
                            'tournament_id' => $id,
                            'round' => $round,
                            'winner' => $last
                        ]);
                    }

                    header('Location: ' . BASE_URL . '/pages/tournament.php?id=' . $id . '&round=1');
                    exit;
                }
            }
            $requestSelectAllUsers = $bdd->prepare(
                'SELECT *
                FROM users
                WHERE userRole = :userRole
            ');
            $requestSelectAllUsers->execute(['userRole'=>'user']);
            $allUsers = $requestSelectAllUsers->fetchAll();

            $requestSelectAllPendingUsers = $bdd->prepare(
                'SELECT u.id, u.pseudo
                FROM pending_list one
                JOIN usersPending_list e ON e.pending_list_id = one.id
                JOIN users u ON u.id = e.user_id
                WHERE one.tournament_id = :tournament_id
            ');
            $requestSelectAllPendingUsers->execute(['tournament_id'=>$id]);
            $allPendingUsers = $requestSelectAllPendingUsers->fetchAll();
            if (isset($_POST["nameTournament"]) && isset($_POST["startAt"]) && isset($_POST["userLimit"]) && isset($_POST["prize"])) {
                $nameTournament = trim(strtolower(htmlspecialchars($_POST["nameTournament"])));
                $date = trim(htmlspecialchars($_POST["startAt"]));
                $userLimit = trim(htmlspecialchars($_POST["userLimit"]));
                $prize = trim(strtolower(htmlspecialchars($_POST["prize"])));

                $startAt = strtotime($date);
                $requestUpdateTournament = $bdd->prepare(
                    'UPDATE tournament
                    SET
                    nameTournament = :nameTournament,
                    startAt        = :startAt,
                    userLimit      = :userLimit,
                    prize          = :prize
                    WHERE id = :id;

                ');
                $requestUpdateTournament->execute([
                    'id' =>$id,
                    'nameTournament'=>$nameTournament,
                    'startAt'=>$startAt,
                    'userLimit'=>$userLimit,
                    'prize'=>$prize
                ]);
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
            if (isset($_POST["userToAdd"])) {
                $addUserId = $_POST["userToAdd"];

                $requestInsertUserTournament = $bdd->prepare(
                    'INSERT INTO usersTournament(user_id, tournament_id)
                    VALUES (:user_id, :tournament_id)
                ');

                $requestInsertUserClassement = $bdd->prepare(
                    'INSERT INTO classement(tournament_id, user_id_continue)
                    VALUES (:tournament_id, :user_id_continue)
                ');

                $checkDuplicate = $bdd->prepare(
                    'SELECT user_id_continue
                    FROM classement
                    WHERE user_id_continue = :user_id_continue AND tournament_id = :tournament_id
                ');

                foreach ($addUserId as $content) {
                    $checkDuplicate->execute([
                        'tournament_id' => $id,
                        'user_id_continue' => (int)$content,
                    ]);
                    $alreadyInClassement = $checkDuplicate->fetch();
                    if ($alreadyInClassement) {
                        continue;
                    }
                    $requestInsertUserTournament->execute([
                        'tournament_id'=>$id,
                        'user_id'=>(int)$content
                    ]);
                    $requestInsertUserClassement->execute([
                        'tournament_id'=>$id,
                        'user_id_continue'=>(int)$content
                    ]);
                }
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
            if (isset($_POST["validate_user_id"])) {
                $validateUserId = $_POST["validate_user_id"];

                if (!is_array($validateUserId)) {
                    $validateUserId = [$validateUserId];
                }

                $requestInsertUserTournament = $bdd->prepare(
                    'INSERT INTO usersTournament(user_id, tournament_id)
                    VALUES (:user_id, :tournament_id)'
                );

                $requestInsertUserClassement = $bdd->prepare(
                    'INSERT INTO classement (tournament_id, user_id_continue)
                    VALUES (:tournament_id, :user_id_continue)'
                );

                $checkDuplicate = $bdd->prepare(
                    'SELECT user_id_continue
                    FROM classement
                    WHERE user_id_continue = :user_id_continue AND tournament_id = :tournament_id
                ');

                foreach ($validateUserId as $userId) {
                    $checkDuplicate->execute([
                        'tournament_id' => $id,
                        'user_id_continue' => (int)$userId,
                    ]);
                    $alreadyInClassement = $checkDuplicate->fetch();
                    if ($alreadyInClassement) {
                        continue;
                    }
                    $requestInsertUserTournament->execute([
                        'tournament_id' => $id,
                        'user_id' => (int)$userId
                    ]);

                    $requestInsertUserClassement->execute([
                        'tournament_id' => $id,
                        'user_id_continue' => (int)$userId
                    ]);
                }
                header('location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
            if (isset($_POST["classement_user_id_continue"])) {
                $userIdContinue = $_POST["classement_user_id_continue"];

                $moveUserClassement = $bdd->prepare(
                    'UPDATE classement
                    SET user_id_stop = :user_id_stop,
                        user_id_continue = :user_id_continue
                    WHERE user_id_continue = :condition_user_id
                ');
                $moveUserClassement->execute([
                    'condition_user_id'=>(int)$userIdContinue,
                    'user_id_stop'=>(int)$userIdContinue,
                    'user_id_continue'=>null
                ]);

                header('Location:'.BASE_URL.'/pages/tournament.php?id='.$id);
            }
            }
            $requestActiveUsers = $bdd->prepare(
                'SELECT user_id_continue FROM classement WHERE tournament_id = :tournament_id AND user_id_continue IS NOT NULL AND user_id_continue != 0'
            );
            $requestActiveUsers->execute(['tournament_id' => $id]);
            $users = $requestActiveUsers->fetchAll(PDO::FETCH_COLUMN);

            // Récupération du round actuel
            $currentRoundStmt = $bdd->prepare('SELECT MAX(round_number) FROM game WHERE tournament_id = :tournament_id');
            $currentRoundStmt->execute(['tournament_id' => $id]);
            $currentRound = $currentRoundStmt->fetchColumn() ?: 1;

            // Récupération des matchs du round actuel
            $requestGames = $bdd->prepare(
                'SELECT g.id AS game_id, g.user_1_id, u1.pseudo AS pseudo1, g.user_2_id, u2.pseudo AS pseudo2, g.winner_id
                FROM game g
                LEFT JOIN users u1 ON g.user_1_id = u1.id
                LEFT JOIN users u2 ON g.user_2_id = u2.id
                WHERE g.tournament_id = :tournament_id AND g.round_number = :round'
            );
            $requestGames->execute(['tournament_id' => $id, 'round' => $currentRound]);
            $games = $requestGames->fetchAll();

            // Traitement des vainqueurs
            if (isset($_POST['validate_winners']) && isset($_POST['winner'])) {
                $winners = $_POST['winner'];

                $updateGame = $bdd->prepare('UPDATE game SET winner_id = :winner_id WHERE id = :game_id');
                foreach ($winners as $gameId => $winnerId) {
                    $stmt = $bdd->prepare('SELECT user_1_id, user_2_id FROM game WHERE id = :id');
                    $stmt->execute(['id' => $gameId]);
                    $match = $stmt->fetch();
                    if (!$match) continue;

                    $loserId = ($match['user_1_id'] == $winnerId) ? $match['user_2_id'] : $match['user_1_id'];
                    $updateGame->execute(['winner_id' => $winnerId, 'game_id' => $gameId]);

                    $bdd->prepare(
                        'UPDATE classement SET user_id_continue = 0, user_id_stop = :loser_id
                        WHERE tournament_id = :tournament_id AND user_id_continue = :loser_id'
                    )->execute(['tournament_id' => $id, 'loser_id' => $loserId]);
                }

                // Génération du prochain round
                $requestNextPlayers = $bdd->prepare(
                    'SELECT winner_id FROM game WHERE tournament_id = :tournament_id AND round_number = :round AND winner_id IS NOT NULL'
                );
                $requestNextPlayers->execute(['tournament_id' => $id, 'round' => $currentRound]);
                $nextPlayers = $requestNextPlayers->fetchAll(PDO::FETCH_COLUMN);

                if (count($nextPlayers) >= 1) {
                    shuffle($nextPlayers);
                    $nextRound = $currentRound + 1;

                    $insertGame = $bdd->prepare(
                        'INSERT INTO game (user_1_id, user_2_id, tournament_id, round_number)
                        VALUES (:user1, :user2, :tournament_id, :round)'
                    );

                    for ($i = 0; $i < count($nextPlayers) - 1; $i += 2) {
                        $insertGame->execute([
                            'user1' => $nextPlayers[$i],
                            'user2' => $nextPlayers[$i + 1],
                            'tournament_id' => $id,
                            'round' => $nextRound
                        ]);
                    }

                    if (count($nextPlayers) % 2 === 1) {
                        $last = end($nextPlayers);
                        $autoWin = $bdd->prepare(
                            'INSERT INTO game (user_1_id, user_2_id, tournament_id, round_number, winner_id)
                            VALUES (:user, NULL, :tournament_id, :round, :winner)'
                        );
                        $autoWin->execute([
                            'user' => $last,
                            'tournament_id' => $id,
                            'round' => $nextRound,
                            'winner' => $last
                        ]);
                    }
                }

                header('Location:' . BASE_URL . '/pages/tournament.php?id=' . $id . '&round=' . $nextRound);
            }
        }else {
        header('location:'.BASE_URL.'/index.php');
    }
?>
<body>
    <?php include_once('../layout/header.php');?>
    <main id="main-tournament">
        <?php if ($_SESSION["currentUser"]['role'] === 'admin'): ?>
            <section class="tournament-form">
                <h2>Modifier les informations du tournoi</h2>
                <form action="tournament.php?id=<?= $id ?>" method="post">
                    <div>
                        <label for="nameTournament">Nom du tournoi</label>
                        <input type="text" name="nameTournament" value="<?= htmlspecialchars($tournament['nameTournament']) ?>" required>
                    </div>

                    <div>
                        <label for="startAt">Date de début</label>
                        <input type="datetime-local" name="startAt" value="<?= $defaultStartAt ?>" required>
                    </div>

                    <div>
                        <label for="userLimit">Nombre maximum de joueurs</label>
                        <input type="number" name="userLimit" min="1" required
                            value="<?= htmlspecialchars($tournament['userLimit']) ?>">
                    </div>

                    <div>
                        <label for="prize">Récompense</label>
                        <input type="text" name="prize" value="<?= $tournament['prize'] ?>" required>
                    </div>

                    <button type="submit">Mettre à jour</button>
                </form>
            </section>
            <section class="tournament-form">
                <h2>Ajouter un utilisateur au tournoi</h2>
                <form action="tournament.php?id=<?= $id ?>" method="post">
                    <select name="userToAdd[]" multiple>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= $user['pseudo'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Ajouter au tournoi">
                </form>
            </section>
            <section class="tournament-form">
                <h2>Valider les joueurs en attente</h2>
                <form action="tournament.php?id=<?= $id ?>" method="post">
                    <?php foreach ($allPendingUsers as $user): ?>
                        <div>
                            <label>
                                <input type="checkbox" name="validate_user_id[]" value="<?= (int)$user['id'] ?>">
                                <?= $user['pseudo'] ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <input type="submit" value="Valider les joueurs sélectionnés">
                </form>
            </section>
            <section class="classement">
                <h2>Classement (joueurs encore en lice)</h2>
                <?php if (count($classement) > 0): ?>
                    <ul>
                        <?php foreach ($classement as $user_continue): ?>
                            <li>
                                <?= $user_continue['pseudo'] ?>
                                <form method="post">
                                    <input type="hidden" name="classement_user_id_continue" value="<?= $user_continue['user_id'] ?>">
                                    <input type="submit" value="Retirer">
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun joueur dans le classement.</p>
                <?php endif; ?>
            </section>
        <?php endif ?>
        <section class="classement-matchs">
            <h2>Matchs du tournoi</h2>
            <?php if (count($classement) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Joueur qualifié</th>
                            <th>Joueur éliminé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classement as $match): ?>
                            <tr>
                                <td><?= $match['pseudo'] ?></td>
                                <td>
                                    <?= $match['pseudo_stop'] 
                                        ? $match['pseudo_stop'] 
                                        : '<em>En attente</em>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun match enregistré pour ce tournoi.</p>
            <?php endif; ?>
        </section>
<?php $currentRound = isset($_GET['round'])
    ? (int) $_GET['round'] 
    : 1; ?>
        <h2>Valider les matchs du round <?= $currentRound ?></h2>
        <form action="tournament.php?id=<?= $id ?>" method="post">
            <?php foreach ($games as $game): ?>
                <div>
                    <strong>Match <?= $game['game_id'] ?> :</strong>
                    <?= htmlspecialchars($game['pseudo1']) ?> vs <?= htmlspecialchars($game['pseudo2']) ?><br>

                    <label>
                        <input type="radio" name="winner[<?= $game['game_id'] ?>]" value="<?= $game['user_1_id'] ?>" required>
                        <?= htmlspecialchars($game['pseudo1']) ?>
                    </label>
                    <label>
                        <input type="radio" name="winner[<?= $game['game_id'] ?>]" value="<?= $game['user_2_id'] ?>" required>
                        <?= htmlspecialchars($game['pseudo2']) ?>
                    </label>
                </div>
                <hr>
            <?php endforeach; ?>
            <button type="submit" name="validate_winners">Valider les vainqueurs</button>
        </form>

        

        
        <form method="post">
            <input type="hidden" name="start_fight" value="1">
            <button type="submit">Lancer le tournoi</button>
        </form>

        <?php
            // Récupération de tous les matchs pour affichage du bracket
            $requestAllGames = $bdd->prepare(
                'SELECT g.*, 
                        u1.pseudo AS pseudo1, 
                        u2.pseudo AS pseudo2 
                FROM game g
                LEFT JOIN users u1 ON g.user_1_id = u1.id
                LEFT JOIN users u2 ON g.user_2_id = u2.id
                WHERE g.tournament_id = :tournament_id
                ORDER BY g.round_number ASC, g.id ASC'
            );
            $requestAllGames->execute(['tournament_id' => $id]);
            $allGames = $requestAllGames->fetchAll();

            // Organiser les matchs par round
            $rounds = [];
            foreach ($allGames as $game) {
                $rounds[$game['round_number']][] = $game;
            }
        ?>
        <div class="tournament-bracket">
        <?php foreach ($rounds as $roundNumber => $roundMatches): ?>
            <div class="round">
                <h3>Round <?= $roundNumber ?></h3>
                <?php foreach ($roundMatches as $match): ?>
                    <div class="match">
                        <div class="player <?= $match['winner_id'] == $match['user_1_id'] ? 'winner' : '' ?>">
                            <?= htmlspecialchars($match['pseudo1']) ?? '—' ?>
                        </div>
                        <div class="player <?= $match['winner_id'] == $match['user_2_id'] ? 'winner' : '' ?>">
                            <?= htmlspecialchars($match['pseudo2']) ?? '—' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        </div>
    </main>
    <?php include_once('../function/scripts.php');?>
</body>