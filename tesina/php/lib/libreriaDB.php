<?php
    require 'connection.php';
    
    // Funzione per ottenere i dati dell'utente dal database
    // riceve il suo id
    function ottieniUtente($id_utente)
    {
        // Creazione del record utente
        $utente = new Utente();

        global $connessione;
        global $tb_utenti;
        global $handleDB;

        // Verifico di poter usare il database
        if ( $connessione )
        {
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

            $handleDB->close();
        }

        return $utente;
    }

?>