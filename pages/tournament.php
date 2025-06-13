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
                $startAt = trim(htmlspecialchars($_POST["startAt"]));
                $userLimit = trim(htmlspecialchars($_POST["userLimit"]));
                $prize = trim(strtolower(htmlspecialchars($_POST["prize"])));

                $requestUpdateTournament = $bdd->prepare(
                    'UPDATE tournament
                    SET (:nameTournament,:startAt,:userLimit,:prize)
                    WHERE id = :id
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
                foreach ($addUserId as $content) {
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
    } else {
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