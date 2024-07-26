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
        $istruzioni = file_get_contents("../../sql/unitecno.sql");
        $listaQuery = explode(";", $istruzioni);
        
        for ( $i=0; $i < count($listaQuery); $i++ )
        {
            $righe_query = explode("\n", $listaQuery[$i]);
            
            for ( $j=0; $j < count($righe_query); $j++ )
                $queryEseguite .= $righe_query[$j] . '<br />';

            $listaQuery[$i] = trim($listaQuery[$i]);
            if ( $listaQuery[$i] != '' )
                $handleDB->query($listaQuery[$i]);
        }
    }
?>