<?php
    require_once 'gestoreXMLDOM.php';

    class TaglioRicarica
    {
        public $importo;
        public $crediti;
    }

    // Gestore XML DOM per il file tagliRicarica.xml
    class GestoreTagliRicarica extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file tagliRicarica con validazione tramite dtd
            parent::__construct("../xml/documenti/tagliRicarica.xml", 0, "../xml/grammatiche/grammaticaTagliRicarica.xsd");
        }

        // Metodo per ottenere i tagli di ricarica disponibili
        // Li restituisce in un vettore
        function ottieniTagliRicarica()
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di tagli
            $lista_tagli = [];

            // Ottengo la lista di figli della radice, ovvero la lista dei tagli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un taglio di ricarica, estraggo importo e crediti
            for ( $i=0; $i<$n_figli; $i++ )
            {
                // Nuovo taglio 
                $nuovo_taglio = new TaglioRicarica();
                $nuovo_taglio->importo = $figli[$i]->getAttribute('importo');
                $nuovo_taglio->crediti = $figli[$i]->getAttribute('crediti');

                // Aggancio alla lista
                array_push($lista_tagli, $nuovo_taglio);
            }

            return $lista_tagli;
        }
    }

?>