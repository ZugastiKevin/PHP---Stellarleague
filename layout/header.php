<?php //include_once('../function/head.php'); ?>

<header>
    <nav>
        <ul>
            <li><a class="nav"  href="<?= BASE_URL?>/index.php">Accueil</a></li>
            <li><a  class="nav" href="<?= BASE_URL?>/pages/tournament.php">Tournois</a></li>
            <li><a  class="nav" href="<?= BASE_URL?>/pages/classement.php">Classement</a></li>
<?php if (isset($_SESSION['currentUser']['role']) 
          && $_SESSION['currentUser']['role'] === 'admin'): ?>
  <li>
    <a class="nav" href="<?= BASE_URL ?>/pages/admin.php">
      Gestion Admin
    </a>
  </li>
<?php endif; ?>

            <div class="searchBar">
                <form action="">
                    <input id="searchInput" type="text" method="Post" placeholder="Chercher">
                </form>
                <i id="magnifyer" class="fa-solid fa-magnifying-glass"></i>
            </div>
            <?php
$allTournaments = $bdd
  ->query("SELECT id, nameTournament FROM tournament ORDER BY nameTournament ASC")
  ->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
const tournaments = <?= json_encode($allTournaments, JSON_HEX_TAG) ?>;
const baseURL     = '<?= rtrim(BASE_URL, "/") ?>';

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('searchInput');
  const icon  = document.getElementById('magnifyer');

  // function to attempt redirect
  function goToTournament() {
    const q = input.value.trim();
    if (!q) return;
    // find an exact name match
    const match = tournaments.find(t => t.nameTournament === q);
    if (match) {
      window.location.href = `${baseURL}/pages/tournament.php?id=${match.id}`;
    } else {
      // optional: show a not-found hint
      console.warn('Tournoi non trouvé:', q);
    }
  }

  // 3a) on Enter key in the input
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      goToTournament();
    }
  });

  // 3b) on clicking the magnifier icon
  icon.addEventListener('click', goToTournament);
});
</script>
            <div>
                <li><a href="/PHP---Stellarleague/pages/create_user.php"><i class="fa-solid fa-user-plus"></i></a></li>
                <li><a href="<?= BASE_URL?>/pages/login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li>
                <li><a href="<?= BASE_URL?>/function/logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </div>
        </ul>
    </nav>
</header>