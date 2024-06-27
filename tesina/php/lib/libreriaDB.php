<?php
    require_once 'configurazione.php';
    
    // Funzione per ottenere i dati dell'utente dal database
    // riceve il suo id e l'handle per il database
    function ottieniUtente($id_utente, $handleDB)
    {
        global $tb_utenti;
        
        // Creazione del record utente
        $utente = new Utente();
        
        // Query per ottenere l'utente
        $q = "select * from $tb_utenti where id=$id_utente";

        // Esecuzione della query
        try
        {
            $rs = $handleDB->query($q);

            if ( $riga = $rs->fetch_row() ) // Corrispondenza trovata
            {
                // Fill della struttura
                $utente->id_utente          = $riga[0];
                $utente->nome               = $riga[1];
                $utente->cognome            = $riga[2];
                $utente->indirizzo          = $riga[3];
                $utente->citta              = $riga[4];
                $utente->cap                = $riga[5];
                $utente->reputazione        = $riga[6];
                $utente->data_registrazione = $riga[7];
                $utente->stato              = $riga[8];
                $utente->username           = $riga[9];
                $utente->mail               = $riga[10];
                $utente->ruolo              = $riga[12];
                $utente->saldo_standard     = $riga[13];
            }
            
            $rs->close();
        }
        catch (Exception $e){}

        return $utente;
    }
?>