<?php
    require_once "lib/libreria.php";
    
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileContatti.css" type="text/css" />
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

            // Import della sidebar e mostro le opzioni per l'utente loggato
            // Il ruolo dell'utente e' prelevato dalle variabili di sessione
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu('A'), $sidebar);
            echo $sidebar . "\n";
        ?>

        <div id="sezioneContatti">
            <div id="presentazione">
                <h1>UNI-TECNO <br> Negozio virtuale per la vendita di dispositivi tecnologici</h1>
            </div>

            <div id="profili">
                <div class="profilo">
                    <div class="imgProfilo">
                        <img src="../img/contatti/contattoMatteo.jpg" alt="foto Matteo Ventali" />
                    </div>
                    <div class="descrizione">
                        <h2> Matteo Ventali </h2>
                        <p> Fondatore di UNI-TECNO <br> Telefono: 3463462160 <br> Email: ventali.1985026@studenti.uniroma1.it</p>
                    </div>
                </div>
                <div class="profilo">
                    <div class="imgProfilo">
                        <img src="../img/contatti/contattoStefano.jpg" alt="foto Stefano Rosso" />
                    </div>
                    <div class="descrizione">
                        <h2> Stefano Rosso </h2>
                        <p> Fondatore di UNI-TECNO <br> Telefono: 3801932038 <br> Email: rosso.2001015@studenti.uniroma1.it</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>