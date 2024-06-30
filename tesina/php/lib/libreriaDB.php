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
?>