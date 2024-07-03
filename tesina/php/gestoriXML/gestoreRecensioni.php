<?php
    require_once 'gestoreXMLDOM.php';

    // Gestore XML DOM per il file recensioni.xml
    class GestoreRecensioni extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file recensioni con validazione tramite schema
            parent::__construct("../xml/documenti/recensioni.xml", 1, "../xml/schema/schemaRecensioni.xsd");
        }
    }

?>