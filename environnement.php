<?php
    ob_start();
    session_start();
    $bdd = new PDO('mysql:host=mysql;dbname=stellarleague;charset=utf8','root','root');
    $projectRacine = str_replace('\\','/',realpath(__DIR__));
    $documentRoot = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
    $cheminRelatif = str_replace($documentRoot,'', $projectRacine);
    define('BASE_URL', $cheminRelatif);