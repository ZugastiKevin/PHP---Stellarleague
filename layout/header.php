<?php //include_once('../function/head.php'); ?>
<header>
    <nav>
        <ul>
            <li><a class="nav"  href="<?= BASE_URL?>/index.php">Accueil</a></li>
            <li><a  class="nav" href="<?= BASE_URL?>/pages/tournament.php">Tournois</a></li>
            <li><a  class="nav" href="<?= BASE_URL?>/pages/classement.php">Classement</a></li>
            <li><a  class="nav" href="<?= BASE_URL?>/pages/admin.php">Gestion Admin</a></li>
            <div class="searchBar">
                <form action="">
                    <input id="searchInput" type="text" method="Post" placeholder="Chercher">
                </form>
                <i id="magnifyer" class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div>
                <li><a href="/PHP---Stellarleague/pages/create_user.php"><i class="fa-solid fa-user-plus"></i></a></li>
                <li><a href="/PHP---Stellarleague/pages/login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li>
                <li><a href="../function/logout.php">logout</a></li>
            </div>
        </ul>
    </nav>
</header>