<?php
    require_once 'configurazione.php';
    $nome_db = 'unitecno';
    $tb_utenti = 'UTENTI';

    // Connessione al database
    $handleDB = new mysqli($ip_dbms, $user_dbms, $pass_dbms, $nome_db);

    // Verifico errori
    $connessione = false;
    if ( !$handleDB->errno )
        $connessione = true;
?>