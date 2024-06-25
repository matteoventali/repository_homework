<?php
    require_once 'gestoreXMLDOM.php';

    // Gestore XML DOM per il file carrello.xml
    class GestoreCarrelli extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file carrelli con validazione tramite schema
            parent::__construct("../xml/carrelli.xml", 1, "../xml/schemaCarrelli.xsd");
        }

        // Metodo per creare un nuovo carrello vuoto nel file
        // riceve l'id del cliente da associare al carrello
        function aggiungiNuovoCarrello($id_cliente)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return $esito;

            // Qui sono sicuro di poter utilizzare il file
            // Creazione del nuovo carrello
            $nuovo_carrello = $this->oggettoDOM->createElement('carrello');
            $nuovo_carrello->setAttribute('id_cliente', $id_cliente);

            // Aggiunta del carrello al file
            $this->oggettoDOM->documentElement->appendChild($nuovo_carrello);

            // Salvataggio delle modifiche sul file
            $this->salvaXML($this->pathname);
            $esito = true;

            return $esito;
        }
    } 
?>