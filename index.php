<?php 
    include_once('environnement.php');
    $title= 'Accueil';
    include_once 'function/head.php';
?>

<body>
    <?php include_once 'layout/header.php';
        if(isset($_GET['message']) == 'success'){
        echo '<h1> Inscription réussie!</h1>';
    } ?>
    <main>
        <div class="login-container">
            <h2>Rejoindre un tournois</h2>
            <form action="index.php" method="post">
                <input placeholder="Chercher un tournois" type="tournois" name="tournois" required>
                <input placeholder="Votre email" type="email" name="email" required>
                <input type="text" name="pseudo" placeholder="Votre pseudo">
                <input id="submit" type="submit" value="Rejoindre ce tournois">
            </form>
        </div>
    </main>
    <!-- container général des brackets -->
<div class="bracket">

  <!-- Round of 16 -->
  <div class="round round-16">
    <h2>Round of 16</h2>

    <!-- Match 1 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-denmark"></span>
        <span class="name">MaxPax</span>
        <span class="score">2</span>
      </div>
      <div class="team">
        <span class="flag flag-usa"></span>
        <span class="name">Heaven</span>
        <span class="score">0</span>
      </div>
    </div>

    <!-- Match 2 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-usa"></span>
        <span class="name">Epic</span>
        <span class="score">2</span>
      </div>
      <div class="team">
        <span class="flag flag-brazil"></span>
        <span class="name">MasTeR</span>
        <span class="score">0</span>
      </div>
    </div>

    <!-- ... Les autres matchs du Round of 16 ... -->
  </div>


  <!-- Quarterfinals -->
  <div class="round quarterfinals">
    <h2>Quarterfinals</h2>

    <!-- Match 1 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-denmark"></span>
        <span class="name">MaxPax</span>
        <span class="score">2</span>
      </div>
      <div class="team">
        <span class="flag flag-usa"></span>
        <span class="name">Epic</span>
        <span class="score">0</span>
      </div>
    </div>

    <!-- Match 2 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-usa"></span>
        <span class="name">Astrea</span>
        <span class="score">2</span>
      </div>
      <div class="team">
        <span class="flag flag-canada"></span>
        <span class="name">Scarlett</span>
        <span class="score">1</span>
      </div>
    </div>

    <!-- ... Les autres matchs des Quarts ... -->
  </div>


  <!-- Semi-Finals (Bo5) -->
  <div class="round semifinals">
    <h2>Semi-Finals (Bo5)</h2>

    <!-- Match 1 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-denmark"></span>
        <span class="name">MaxPax</span>
        <span class="score">0</span>
      </div>
      <div class="team team--winner">
        <span class="flag flag-usa"></span>
        <span class="name">Astrea</span>
        <span class="score">3</span>
      </div>
    </div>

    <!-- Match 2 -->
    <div class="match">
      <div class="team team--winner">
        <span class="flag flag-korea"></span>
        <span class="name">Cure</span>
        <span class="score">2</span>
      </div>
      <div class="team">
        <span class="flag flag-usa"></span>
        <span class="name">ByuN</span>
        <span class="score">1</span>
      </div>
    </div>
  </div>

<?php
// Exemple de tableau initial de joueurs (id, pseudo)
$players = [
    ['id'=>1,  'pseudo'=>'MaxPax'],
    ['id'=>2,  'pseudo'=>'Heaven'],
    ['id'=>3,  'pseudo'=>'Epic'],
    ['id'=>4,  'pseudo'=>'MasTeR'],
    ['id'=>5,  'pseudo'=>'Astrea'],
    ['id'=>6,  'pseudo'=>'BerryCrunch'],
    ['id'=>7,  'pseudo'=>'Scarlett'],
    ['id'=>8,  'pseudo'=>'ReBellioN'],
    ['id'=>9,  'pseudo'=>'Cure'],
    ['id'=>10, 'pseudo'=>'HuShang'],
    ['id'=>11, 'pseudo'=>'Percival'],
    ['id'=>12, 'pseudo'=>'Mute'],
    ['id'=>13, 'pseudo'=>'ByuN'],
    ['id'=>14, 'pseudo'=>'PiG'],
    ['id'=>15, 'pseudo'=>'ThatOneDude'],
    ['id'=>16, 'pseudo'=>'FoxeR'],
];

// Nombre de joueurs
$J = count($players);
// Nombre de tours (rounds) nécessaires
$R = (int) ceil(log($J, 2));
// On initialise les paires pour le round 1
$currentRoundPlayers = $players;
?>

<div class="bracket">

  <?php for ($round = 1; $round <= $R; $round++) : 
        // Titres selon le tour
        $roundNames = [
          1      => 'Round of ' . $J,
          2      => 'Quarterfinals',
          $R-1   => 'Semi-Finals (Bo5)',
          $R     => 'Finals (Bo5)',
        ];
        $title = $roundNames[$round] ?? "Round $round";
        $matchCount = count($currentRoundPlayers) / 2;
  ?>
    <div class="round round-<?= $round ?>">
      <h2><?= $title ?></h2>

      <?php for ($m = 0; $m < $matchCount; $m++) :
          $p1 = $currentRoundPlayers[$m*2];
          $p2 = $currentRoundPlayers[$m*2 + 1];
      ?>
        <div class="match">
          <div class="team team--placeholder">
            <span class="name"><?= htmlspecialchars($p1['pseudo']) ?></span>
            <span class="score">–</span>
          </div>
          <div class="team team--placeholder">
            <span class="name"><?= htmlspecialchars($p2['pseudo']) ?></span>
            <span class="score">–</span>
          </div>
        </div>
      <?php endfor; ?>
    </div>

    <?php
      // Prépare les vainqueurs (vide d'abord) pour le tour suivant
      $nextRoundPlayers = array_fill(0, $matchCount, [
        'id'     => null,
        'pseudo' => ''
      ]);
      $currentRoundPlayers = $nextRoundPlayers;
    ?>

  <?php endfor; ?>

</div>


</body>