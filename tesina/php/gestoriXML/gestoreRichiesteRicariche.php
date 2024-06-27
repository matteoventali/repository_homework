<?php
    require_once 'gestoreXMLDOM.php';

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
    }

?>