<?php
    require_once 'gestoreXMLDOM.php';

    // Gestore XML DOM per il file portafogliBonus.xml
    class GestorePortafogliBonus extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file portafogli bonus con validazione tramite schema
            parent::__construct("../xml/documenti/portafogliBonus.xml", 1, "../xml/schema/schemaPortafogliBonus.xsd");
        }

        // Metodo per creare un nuovo portafoglio bonus vuoto nel file
        // riceve l'id del cliente da associare al portafoglio bonus
        function aggiungiNuovoPortafoglioBonus($id_cliente)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return $esito;

            // Qui sono sicuro di poter utilizzare il file
            // Creazione del nuovo portafoglio bonus
            $nuovo_portafoglio = $this->oggettoDOM->createElement('portafoglio');
            $nuovo_portafoglio->setAttribute('totale', '0');
            $nuovo_portafoglio->setAttribute('id_cliente', $id_cliente);

            // Inizializzo il portafoglio con un saldo a 0 di crediti bonus
            // per l'anno corrente
            $anno_corrente = date("Y");
            $saldo_anno_corrente = $this->oggettoDOM->createElement('saldo', '0');
            $saldo_anno_corrente->setAttribute('anno', $anno_corrente);

            // Il saldo e' figlio di portafoglio bonus
            $nuovo_portafoglio->appendChild($saldo_anno_corrente);

            // Aggiungo il portafoglio
            $this->oggettoDOM->documentElement->appendChild($nuovo_portafoglio);
            
            // Salvataggio delle modifiche sul file
            $this->salvaXML($this->pathname);
            $esito = true;

            return $esito;
        }
    }
?>