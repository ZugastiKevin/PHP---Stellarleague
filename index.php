<?php 
    $title= 'Accueil';
    include_once 'function/head.php';
?>

<body id="body-index">
    <?php include_once 'layout/header.php'; 
    if(isset($_SESSION["currentUser"])){
        echo 'Bienvenue, ' . $_SESSION["currentUser"]['pseudo'];
    }else{
        echo 'Bienvenue! Vous devez vous inscrire pour rejoindre un tournois!';
    }
    ?>
    
<?php
// 1) On fixe le fuseau
date_default_timezone_set('Europe/Paris');

// 2) On récupère les tournois en cours (endAt IS NULL)
$stmt = $bdd->prepare("
  SELECT id, nameTournament, startAt
  FROM tournament
  WHERE endAt IS NULL
  ORDER BY startAt ASC
  limit 10
");
$stmt->execute();
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table-tournois">
  <tbody>
    <?php if (empty($tournaments)): ?>
      <tr>
        <td colspan="3">Aucun tournoi en cours</td>
      </tr>
    <?php else: foreach($tournaments as $t): ?>
      <?php
        //Conversion en timestamp PHP
        $ts = is_numeric($t['startAt'])
              ? (int)$t['startAt']
              : strtotime($t['startAt']);

        // Pour l'affichage table : format français
        $dateAffichage = date('d/m/Y H:i', $ts);
        $defaultStartAt = date('Y-m-d\TH:i', $ts);
      ?>
      <tr>
        <td>
  <a href="<?= BASE_URL ?>/pages/tournament.php?id=<?= (int)$t['id'] ?>">
    <?= htmlspecialchars($t['nameTournament'], ENT_QUOTES, 'UTF-8') ?>
  </a>
</td>

        <td><?= $dateAffichage ?></td>
<?php
if (
    !empty($_SESSION['currentUser']['role'])
    && $_SESSION['currentUser']['role'] === 'user'
):
?>

  <td>
    <a href="<?= BASE_URL ?>/function/add_pending.php?id=<?= $t['id']?>">Rejoindre<a>
  </td>
</tr>
<?php
endif;
?>

    <?php endforeach; endif; ?>
  </tbody>
</table>


</body>