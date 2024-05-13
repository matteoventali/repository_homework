<?php
    // Oggetto per la gestione di un file XML con DOM
    class GestoreXMLDOM
    {
        // Attributi della classe
        // Pathname del file (privato)
        // Oggetto DOM per utilizzo del documento XML
        // Flag per check errori
        protected $pathname = "";
        protected $oggettoDOM = null;
        protected $errori = true;

        // Costruttore
        function __construct($str)
        {
            // Tentativo di apertura del documento
            if ( file_exists($str) )
            {
                $this->pathname = $str;
                $xmlString = "";
                foreach ( file($str) as $node ) 
                    $xmlString .= trim($node);
                
                // Istanziazione dell'oggetto DOM
                $this->oggettoDOM = new DOMDocument();
                $this->oggettoDOM->loadXML($xmlString);

                // Validazione del contenuto XML
                if ($this->oggettoDOM->validate())
                    $this->errori = false;
            }
        }

        // Metodo per verificare usabilita' oggetto DOM
        function checkValidita()
        {
            return !$this->errori;
        }

        // Metodi getter
        function getOggettoDOM()
        {
            if ( $this->checkValidita() )
                return $this->oggettoDOM;
            else
                return null;
        }
        
        // Metodo per salvare il contenuto nel file XML
        // Se non viene passato il pathname (stringa vuota) viene sfruttato quello
        // da cui e' stato creato l'oggetto DOM
        function salvaXML($path)
        {
            if ( $this->checkValidita() )
            {
                if ( strlen($path) == 0 )
                    $path = $this->pathname;
                $this->oggettoDOM->save($path);
            }
        }
    }

    class GestoreXMLDOMSquadre extends GestoreXMLDOM
    {
        // Costruttore che fa riferimento alla superclasse
        function __construct($str)
        {
            parent::__construct($str);
        }

        // Metodo per ottenere la lista di squadre
        // dal file XML
        function getListaSquadre()
        {
            if ( $this->checkValidita() )
            {
                return $this->getOggettoDOM()->documentElement->childNodes;
            }
            else
                return null;
        }
    }
?>