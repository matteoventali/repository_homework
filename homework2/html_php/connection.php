<?php
    require_once 'configurazione.php';
    $nome_db = 'champions_league';
    $tb_squadre = 'SQUADRE';
    $tb_utenti = 'UTENTI';
    $tb_partite = 'PARTITE';

    // Connessione al database
    $handleDB = new mysqli($ip_dbms, $user_dbms, $pass_dbms, $nome_db);

    // Verifico errori
    $connessione = false;
    if ( !$handleDB->errno )
        $connessione = true;
?>