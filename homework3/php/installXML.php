<?php
    /* Contenuto del file XML */
    $contenutoXML = '<?xml version="1.0" ?><partite xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="partiteSchema.xsd"></partite>';
    
    /* Scrittura del file XML */
    $partite = fopen("../xml/partite.xml", "w");
    fwrite($partite, $contenutoXML);
    fclose($partite);
?>