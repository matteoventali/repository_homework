<?php
    require_once "lib/libreria.php";

    // Verifico se vi e' una sessione esistente per un account attivo
    // In caso positivo l'utente va nella sua area riservata
    // altrimenti rimane nella homepage
    require_once 'lib/verificaSessioneAttiva.php';

    if ( $sessione_attiva )
        header("Location: area_riservata.php");
    else 
        require_once 'lib/cancellaSessione.php';
    
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
            // Mostro il bottone per accedere alla pagina di registrazione e per quella di login
            $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
            $nav = str_replace("%OPZIONE_DISPLAY_REGISTRATI%", "block", $nav);
            $nav = str_replace("%OPZIONE_DISPLAY_ACCEDI%", "block", $nav);
            echo $nav ."\n";

            // Import del frammento di accesso al catalogo
            $cat = file_get_contents("../html/frammentoAccessoCatalogo.html");
            $cat = str_replace("%URL_SFONDO_CASUALE%", ottieniURLSfondo(), $cat);
            echo $cat . "\n";

            // Import della sidebar e le opzioni del visitatore
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu('V'), $sidebar);
            echo $sidebar . "\n";
        ?>
    </body>
</html>