<?php
    // Risultato del caricamento
    $loadSquadre = false;
    
    // Caricamento dal file XML delle squadre 
    $xmlString = "";
    foreach ( file("../xml/squadre.xml") as $node ) {
        $xmlString .= trim($node);
    }
    $squadre = new DOMDocument();
    $squadre->loadXML($xmlString);

    // Validazione del documento
    if ( $squadre->validate() )
        $loadSquadre = true;
?>