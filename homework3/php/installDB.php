<?php
    require_once 'configurazione.php';

    // Connessione al dbms
    $handleDB = new mysqli($ip_dbms, $user_dbms, $pass_dbms);

    // Verifico errori
    $connessione = false;
    if ( !$handleDB->errno )
        $connessione = true;

    // Se la connessione e' avvenuta eseguo lo script
    // di creazione del database
    $queryEseguite = "";
    if ( $connessione )
    {
        // Lettura del file contenente le istruzioni sql
        // per creazione e popolazione database
        $istruzioni = file_get_contents("../sql/championsLeague.sql");
        $listaQuery = explode(";\n", $istruzioni);
        
        for ( $i=0; $i < count($listaQuery); $i++ )
        {
            $queryEseguite .= $listaQuery[$i] . "<br />";
            $listaQuery[$i] = trim($listaQuery[$i]);
            $handleDB->query($listaQuery[$i]);
        }
    }
?>