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
        // Riceve:
        // 1: pathname del file xml da gestire
        // 2: modalita' di validazione del file: dtd (0) schema (1)
        // 3: pathname del file schema nel caso in cui sia questa la modalita' scelta (altrimenti ignorato)
        function __construct($file, $modalita_validazione, $pathname_schema)
        {
            // Tentativo di apertura del documento
            if ( file_exists($file) )
            {
                $this->pathname = $file;
                $xmlString = "";
                foreach ( file($file) as $node )
                    $xmlString .= trim($node);

                // Istanziazione dell'oggetto DOM
                $this->oggettoDOM = new DOMDocument();
                $this->oggettoDOM->loadXML($xmlString);
                
                // Validazione del contenuto XML
                if (($modalita_validazione == 0 && $this->oggettoDOM->validate()) ||
                        ($modalita_validazione == 1 && file_exists($pathname_schema) && $this->oggettoDOM->schemaValidate($pathname_schema)))
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