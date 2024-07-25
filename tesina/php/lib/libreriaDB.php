<?php
    require_once 'configurazione.php';

    class Utente
    {
        public $id_utente            = "";
        public $nome                 = "";
        public $cognome              = "";
        public $indirizzo            = "";
        public $citta                = "";
        public $cap                  = "";
        public $reputazione          = "";
        public $data_registrazione   = "";
        public $stato                = "";
        public $username             = "";
        public $mail                 = "";
        public $ruolo                = "";
        public $saldo_standard       = "";
    }

    class UtenteTessera
    {
        public $id_utente            = "";
        public $nome                 = "";
        public $cognome              = "";
        public $username             = "";
        public $stato                = "";
    }
    
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

    // Funzione per ottenere la lista di utenti dal database
    // riceve l'handle per il database e eventuali filtri:
    // uno per selezionare solo account attivi
    // uno per selezionare solo account bannati
    // uno per selezionare una corrispondenza nel nome e cognome o username
    function ottieniClienti($handleDB, $flag_attivi, $flag_bannati, $nome_cognome_username)
    {
        global $tb_utenti;
        
        // Creazione di una lista di utenti risultato della ricerca
        $lista_utenti = [];
        
        // Composizione della query
        $q = "select id, nome, cognome, stato, username from $tb_utenti";
        $condizioni = " where ruolo='C' ";
        
        // Se entrambi i flag sono posti a true o entrambi a false non aggiungo condizioni sullo stato
        if (!(($flag_attivi && $flag_bannati) || (!$flag_attivi && !$flag_bannati)))
        {
            if ( $flag_attivi )
                $condizioni .= "and stato='A'";
            else if ( $flag_bannati )
                $condizioni .= "and stato='B'";
        }

        // Aggiungo la condizione sui dati se richiesta
        if ( strlen($nome_cognome_username) > 0 )
        {
            $condizione_dati = "( nome like '%$nome_cognome_username%' or cognome like '%$nome_cognome_username%' or username like '%$nome_cognome_username%' )";
            $condizioni .= " and " . $condizione_dati;
        }

        $q .= $condizioni;

        // Esecuzione della query
        try
        {
            $rs = $handleDB->query($q);

            while ( $riga = $rs->fetch_row() ) 
            {
                // Creazione del nuovo utente
                $utente = new UtenteTessera();
                
                // Fill della struttura
                $utente->id_utente          = $riga[0];
                $utente->nome               = $riga[1];
                $utente->cognome            = $riga[2];
                $utente->stato              = $riga[3];
                $utente->username           = $riga[4];
                
                // Aggiunta della struttura alla lista
                array_push($lista_utenti, $utente);
            }
            
            $rs->close();
        }
        catch (Exception $e){}

        return $lista_utenti;
    }

    // Funzione per incrementare il saldo standard del portafoglio di un cliente
    function incrementaSaldoStandard($handleDB, $id_utente, $incremento)
    {
        global $tb_utenti;
        
        // Creo la query per ottenere il saldo standard corrente
        $q_saldo = "select saldo_standard from $tb_utenti where id=" . $id_utente;

        // Esecuzione della query
        try
        {
            // Eseguo la query per il saldo
            $rs = $handleDB->query($q_saldo);

            if ( $riga = $rs->fetch_row() )  // Se ho trovato una corrispondenza continuo con la computazione
            {
                $saldo_corrente = floatval($riga[0]);

                // Calcolo il nuovo saldo
                $nuovo_saldo = $saldo_corrente + floatval($incremento);
                $nuovo_saldo = strval($nuovo_saldo);

                // Query per aggiornare il saldo
                $q_saldo = "update $tb_utenti set saldo_standard=$nuovo_saldo where id=$id_utente";

                // Eseguo la query
                $handleDB->query($q_saldo);
            }
            
            $rs->close();
        }
        catch (Exception $e){}
    }

    // Funzione per cambiare lo stato dell'account di un cliente
    function cambiaStato($handleDB, $id_utente, $nuovo_stato)
    {
        global $tb_utenti;
        $esito = false;

        // Se ricevo uno stato nullo o non valido l'operazione fallisce
        if ( $nuovo_stato == "" || ($nuovo_stato != 'B' && $nuovo_stato != 'A') )
            return $esito;

        // Creo la query per cambiare lo stato
        $q = "update $tb_utenti set stato='$nuovo_stato' where id=$id_utente and ruolo='C'";

        // Esecuzione della query
        try
        {
            // Eseguo la query
            $esito = $handleDB->query($q);
        }
        catch (Exception $e){}

        return $esito;
    }

    // Funzione per cambiare i dati di un cliente
    function modificaCliente($handleDB, $id_cliente, $nome, $cognome, $citta, $cap, $indirizzo, $reputazione, $ruolo_modificatore)
    {
        global $tb_utenti;
        $esito = false;

        // Creo la query per aggiornare i dati. Considero la reputazione se il modificatore e' admin
        if ( $ruolo_modificatore == 'A' )
            $q = "update $tb_utenti set nome='$nome', cognome='$cognome', citta='$citta', cap='$cap', indirizzo='$indirizzo', reputazione=$reputazione where id=$id_cliente and ruolo='C'";
        else
            $q = "update $tb_utenti set nome='$nome', cognome='$cognome', citta='$citta', cap='$cap', indirizzo='$indirizzo' where id=$id_cliente and ruolo='C'";

        // Esecuzione della query
        try
        {
            // Eseguo la query
            $esito = $handleDB->query($q);
        }
        catch (Exception $e){}

        return $esito;
    }

    // Funzione per aggiornare la reputazione associata ad un cliente
    // Riceve l'id del cliente, il rating della valutazione che provoca l'aggiornamento
    // e il peso dell'utente che ha effettuato la valutazione
    function aggiornaReputazione($handleDB, $id_cliente, $rating, $peso)
    {
        global $tb_utenti;
        $esito = false;
        
        // Calcolo dell'incremento/decremento di reputazione
        $incremento = 0;
        switch ($rating)
        {
            case "1":
                $incremento = -4; break;
            case "2":
                $incremento = -2; break;
            case "3":
                $incremento = 0; break;
            case "4":
                $incremento = 2; break;
            case "5":
                $incremento = 4; break;
            default:
                $incremento = 0; break;
        }
        $incremento = $incremento * intval($peso)/100;

        // Ottengo le informazioni del cliente per ottenere la reputazione corrente
        $cliente = ottieniUtente($id_cliente, $handleDB);

        // Calcolo della nuova reputazione
        $nuova_reputazione = round(intval($cliente->reputazione) + $incremento);
        if ( $nuova_reputazione < 0 )
            $nuova_reputazione = 1; // Valore minimo di reputazione
        if ( $nuova_reputazione > 100 )
            $nuova_reputazione = 100;

        // Query di aggiornamento
        $q = "update $tb_utenti set reputazione=$nuova_reputazione where id=$id_cliente and ruolo='C'";

        // Esecuzione della query
        try
        {
            // Eseguo la query
            $esito = $handleDB->query($q);
        }
        catch (Exception $e){}
 
        return $esito;
    }

    // Funzione per aggiornare il saldo del portafogio standard associato ad un cliente
    // a seguito di un acquisto
    function aggiornaSaldoStandard($handleDB, $id_cliente, $nuovo_saldo)
    {
        global $tb_utenti;
        $esito = false;
        
        // Query di aggiornamento
        $q = "update $tb_utenti set saldo_standard=$nuovo_saldo where id=$id_cliente and ruolo='C'";

        // Esecuzione della query
        try
        {
            // Eseguo la query
            $esito = $handleDB->query($q);
        }
        catch (Exception $e){}
 
        return $esito;
    }
?>