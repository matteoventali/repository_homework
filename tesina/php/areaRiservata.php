<?php
    require_once "lib/libreria.php";
    require_once "gestoriXML/gestoreRichiesteRicariche.php";

    // Variabili di gestione popup
    $mostraPopup = false; $msg = ''; $err = false;

    // Verifico se vi e' una sessione esistente per un account attivo
    // In caso positivo l'utente rimane nella sua area riservata
    // altrimenti viene reindirizzato alla homepage
    require_once 'lib/verificaSessioneAttiva.php';
    if ( !$sessione_attiva )
    {
        require_once 'lib/cancellaSessione.php';
        header("Location: homepage.php");
    }
    else
    {
        // Se sono loggato come admin verifico se vi siano delle richieste
        // di ricarica da gestire. In questo caso viene mostrato
        // un popup di alert all'admin
        if ( $_SESSION["ruolo"] == "A" )
        {
            $gestoreRichieste = new GestoreRichiesteRicariche();
            if ( count($gestoreRichieste->ottieniRichiestiRicaricheDaGestire()) > 0 )
            {
                // Set dei parametri del popup in modo che venga mostrato
                $mostraPopup = true;
                $msg = 'Ci sono delle richieste di ricarica da gestire!';
                $err = true;
            }
        }
    }
        
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileAccessoCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body onload="">
        <?php
            if ( $sessione_attiva )
            {
                // Import della navbar
                $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
                $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
                echo $nav ."\n";
                
                // Import del frammento di accesso al catalogo
                echo creaPopup($mostraPopup, $msg, $err) . "\n\n";
                
                $cat = file_get_contents("../html/frammentoAccessoCatalogo.html");
                $cat = str_replace("%URL_SFONDO_CASUALE%", ottieniURLSfondo(), $cat);
                echo $cat . "\n";

                // Import della sidebar e mostro le opzioni per l'utente loggato
                // Il ruolo dell'utente e' prelevato dalle variabili di sessione
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
                echo $sidebar . "\n";
            }
        ?>
    </body>
</html>