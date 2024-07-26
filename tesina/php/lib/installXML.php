<?php
    // Vettore contenente i nomi dei file xml
    $nomi_file = ['acquisti.xml', 'carrelli.xml', 'catalogoProdotti.xml', 
                            'categorieProdotti.xml', 'domande.xml', 'portafogliBonus.xml',
                            'recensioni.xml', 'richiesteRicariche.xml', 'risposte.xml', 'tagliRicarica.xml'];

    // Directory dove individuare i file puliti per installazione
    $xml_inst = '../../docs/xml_inst/';

    // Directory dove posizionare i file per l'applicazione
    $xml = '../../xml/documenti/';

    // Sposto i file
    $operazioni = '';
    for ( $i=0; $i < count($nomi_file); $i++ )
    {
        $operazioni .= 'Sposto: ' . $xml_inst . $nomi_file[$i] . "\tin\t" . $xml . $nomi_file[$i] . '<br />';
        copy($xml_inst . $nomi_file[$i], $xml . $nomi_file[$i]);
    }
?>