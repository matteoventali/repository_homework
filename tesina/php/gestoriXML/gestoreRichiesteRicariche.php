<?php
    require_once 'gestoreXMLDOM.php';
    require_once 'lib/libreriaDB.php';

    class RichiestaDiRicarica
    {
        public $id_richiesta;
        public $id_cliente;
        public $data;
        public $crediti_richiesti;
    }

    // Gestore XML DOM per il file richiesteRicariche.xml
    class GestoreRichiesteRicariche extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file richiesteRicariche con validazione tramite schema
            parent::__construct("../xml/documenti/richiesteRicariche.xml", 1, "../xml/schema/schemaRichiesteRicariche.xsd");
        }

        // Funzione per inserire una nuova richiesta di ricarica
        function inserisciNuovaRichiestaRicarica($id_cliente, $crediti)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() && $crediti > 0 )
                return $esito;

            // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultima richiesta di ricarica
            $id_nuova_ricarica = 1;
            $ultima = $this->oggettoDOM->documentElement->lastElementChild;
            if ( $ultima != null ) // Ci sono altre domande
            {
                $id_ultima = $ultima->getAttribute('id');
                $id_ultima = intval($id_ultima);
                $id_nuova_ricarica = ++$id_ultima;
                $id_nuova_ricarica = strval($id_nuova_ricarica);
            }

            // Creazione della nuova richiesta di ricarica
            $nuova_ricarica = $this->oggettoDOM->createElement('ricarica');
            $nuova_ricarica->setAttribute('id', $id_nuova_ricarica);
            $nuova_ricarica->setAttribute('id_cliente', $id_cliente);
            $nuova_ricarica->setAttribute('stato', 'W'); // La ricarica non e' ancora gestita da un admin
            $data_richiesta = $this->oggettoDOM->createElement('data_richiesta', date('Y-m-d'));
            $crediti_richiesti = $this->oggettoDOM->createElement('crediti_richiesti', $crediti);
            $nuova_ricarica->appendChild($data_richiesta);
            $nuova_ricarica->appendChild($crediti_richiesti);

            // Aggiunta della richiesta al file
            $this->oggettoDOM->documentElement->appendChild($nuova_ricarica);

            // Salvataggio delle modifiche sul file
            $this->salvaXML($this->pathname);
            $esito = true;

            return $esito;
        }

        // Funzione per ottenere tutte le richieste di ricarica in stato di waiting
        function ottieniRichiestiRicaricheDaGestire()
        {
            // Lista di richieste
            $lista_richieste = [];
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita()  )
                return $lista_richieste;

            // Ottengo la lista di figli della radice, ovvero la lista delle richieste
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una richiesta, estraggo le informazioni
            for ( $i=0; $i<$n_figli; $i++ )
            {
                // Verifico che la richiesta corrente sia da gestire
                if ( $figli[$i]->getAttribute('stato') == 'W')
                {
                    // Nuova richiesta
                    $nuova_richiesta = new RichiestaDiRicarica();
                    $nuova_richiesta->id_richiesta = $figli[$i]->getAttribute('id');
                    $nuova_richiesta->id_cliente = $figli[$i]->getAttribute('id_cliente');
                    $nuova_richiesta->data = $figli[$i]->firstChild->textContent;
                    $nuova_richiesta->crediti_richiesti = $figli[$i]->firstChild->nextSibling->textContent;

                    // Append nell'array
                    array_push($lista_richieste, $nuova_richiesta);
                }
            }

            return $lista_richieste;
        }
 
        // Funzione per gestire una richiesta di ricarica in stato di waiting
        // Riceve l'id della richiesta da gestire, l'id dell'admin che la gestisce
        // e un flag per accettarla o rifiutarla
        function gestisciRichiestaRicarica($id_richiesta, $id_admin, $flag)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita()  )
                return $esito;

            // Ottengo la lista di figli della radice, ovvero la lista delle richieste
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Scorro il file finche' non raggiungo la richiesta da aggiornare
            $trovata = false;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                if ( $figli[$i]->getAttribute('id') == $id_richiesta )
                    $trovata = true;
            }

            // Aggiorno le informazioni della richiesta se trovata
            if ( $trovata )
            {
                $i--;
                
                // Calcolo del nuovo stato
                if ( $flag )
                {
                    $stato = 'A';

                    // Aggiorno il portafoglio standard del cliente che ha effettuato la richiesta
                    // Prelevo l'id del cliente dalla richiesta in oggetto e l'ammontare di crediti richiesti
                    $id_cliente = $figli[$i]->getAttribute('id_cliente');
                    $crediti_richiesti = $figli[$i]->firstChild->nextSibling->textContent;

                    // Mi connetto al database
                    require 'lib/configurazione.php';
                    require 'lib/connection.php';
                    if ( $connessione )
                    {
                        incrementaSaldoStandard($handleDB, $id_cliente, $crediti_richiesti);
                        $handleDB->close();
                    }
                }
                else
                    $stato = 'R';
                
                $figli[$i]->setAttribute('id_admin', $id_admin);
                $figli[$i]->setAttribute('stato', $stato);

                // Salvo i cambiamenti
                $this->salvaXML($this->pathname);
                $esito = true;
            }

            return $esito;
        }
    }
?>