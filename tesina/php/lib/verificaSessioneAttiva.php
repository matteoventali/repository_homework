<?php
    session_start();

    // Sessione esistente se vi sono dati in $_SESSION
    // Sessione attiva se l'account loggato e' attivo
    $sessione_esistente = false; // L'esistenza e' condizione necessaria per essere attiva
    $sessione_attiva = false;

    // Verifico che sia presente una sessione valida
    // per un account attivo.
    // E' necessario quindi eseguire una connessione al database
    // per evitare che un cliente bannato possa continuare ad utilizzare il sito.
    // Se questo controllo non fosse eseguito in questo modo 
    // l'utente con account disattivato rimarrebbe loggato e la disattivazione
    // sarebbe effettiva solo se effettua il logout.
    // Inoltre viene effettua la sincronizzazione della reputazione utile
    // per il calcolo di sconti fissi nel catalogo.
    if ( isset($_SESSION["id_utente"]) )
    {
        // La sessione esiste
        $sessione_esistente = true;
        
        // Connessione al database per prendere lo stato
        // dell'account loggato
        require 'connection.php';

        if ( $connessione )
        {
            $id = $_SESSION["id_utente"];
            $q = "select stato, reputazione from UTENTI where id=$id"; // Query da eseguire

            // Esecuzione della query
            try
            {
                $rs = $handleDB->query($q);

                if ( $riga = $rs->fetch_row() ) // Corrispondenza trovata
                {
                    if ( $riga[0] == 'A' )
                    {
                        $sessione_attiva = true;
                        $_SESSION['reputazione'] = $riga[1];
                    }
                }
               
                $rs->close();
            }
            catch (Exception $e){}

            $handleDB->close();
        }
    }
?>