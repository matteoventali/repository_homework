<?php
    require_once 'configurazione.php';

    // Connessione al dbms
    $handleDB = new mysqli($ip_dbms, $user_dbms, $pass_dbms);

    // Verifico errori
    $connessione = false;
    if ( !$handleDB->errno )
        $connessione = true;

    // Se la connessione e' avvenuta eseguo lo script
    // di popolazione intermedia del database
    $queryEseguite = "";
    if ( $connessione )
    {
        // Lettura del file contenente le istruzioni sql
        // per creazione e popolazione database
        $istruzioni = file_get_contents("../../sql/dumpIntermedio.sql");
        $listaQuery = explode(";", $istruzioni);
        
        for ( $i=0; $i < count($listaQuery); $i++ )
        {
            $righe_query = explode("\n", $listaQuery[$i]);
            
            for ( $j=0; $j < count($righe_query); $j++ )
                $queryEseguite .= $righe_query[$j] . '<br />';

            $listaQuery[$i] = trim($listaQuery[$i]);
            if ( $listaQuery[$i] != '' )
                $handleDB->query($listaQuery[$i]);
        }
    }

    // Vettore contenente i nomi dei file xml
    $nomi_file = ['acquisti.xml', 'carrelli.xml', 'catalogoProdotti.xml', 
                            'categorieProdotti.xml', 'domande.xml', 'portafogliBonus.xml',
                            'recensioni.xml', 'richiesteRicariche.xml', 'risposte.xml', 'tagliRicarica.xml'];

    // Directory dove individuare i file popolati per installazione intermedia
    $xml_inst = '../../docs/xml_pop/';

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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <title>UNI-TECNO</title>
    </head>

    <body>
        <h1> QUERY SQL </h1>
        <p><?php echo $queryEseguite; ?></p>
        <h1> OPERAZIONI XML </h1>
        <p> <?php echo $operazioni; ?> </p>
        <p><a href="../homepage.php">AVVIA APPLICAZIONE</a></p>
    </body>
</html>