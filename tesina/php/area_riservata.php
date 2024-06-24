<?php
    require_once "lib/libreria.php";

    // Verifico se vi e' una sessione esistente per un account attivo
    // In caso positivo l'utente rimane nella sua area riservata
    // altrimenti viene reindirizzato alla homepage
    require_once 'lib/verificaSessioneAttiva.php';
    if ( !$sessione_attiva )
    {
        require_once 'lib/cancellaSessione.php';
        header("Location: homepage.php");
    }
        
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body onload="">
        <?php 
            // Import della navbar
            $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
            $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
            echo $nav ."\n";
            
            // Import del frammento di accesso al catalogo
            $cat = file_get_contents("../html/frammentoAccessoCatalogo.html");
            $cat = str_replace("%URL_SFONDO_CASUALE%", ottieniURLSfondo(), $cat);
            echo $cat . "\n";

            // Import della sidebar e mostro le opzioni per l'utente loggato
            // Il ruolo dell'utente e' prelevato dalle variabili di sessione
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
            echo $sidebar . "\n";
        ?>
    </body>
</html>