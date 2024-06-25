<?php
    require_once "lib/libreria.php";
    require_once "lib/verificaSessioneAttiva.php";
    
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
            // Bisogna controllare se l'utente è loggato oppure no
            // In base a questo avrà diversi tipi di visualizzazione
            if($sessione_attiva)
            {
                // In questo caso l'utente è loggato

                // Import della navbar
                // Visualizzo nome dell'utente e il tasto "Logout"
                $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
                $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
                echo $nav ."\n";

                // Import della sidebar e mostro solo le opzioni del visitatore
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
                echo $sidebar . "\n";
            }
            else 
            {
                // Qui l'utente non è loggato

                // Import della navbar
                $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
                echo $nav ."\n";

                // Import della sidebar e mostro solo le opzioni del visitatore
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu('V'), $sidebar);
                echo $sidebar . "\n";
            }
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
                        <p> Fondatore di UNI-TECNO <br> Telefono: 3463462160 <br> Email: ventali@unitecno.it</p>
                    </div>
                </div>
                <div class="profilo">
                    <div class="imgProfilo">
                        <img src="../img/contatti/contattoStefano.jpg" alt="foto Stefano Rosso" />
                    </div>
                    <div class="descrizione">
                        <h2> Stefano Rosso </h2>
                        <p> Fondatore di UNI-TECNO <br> Telefono: 3801932038 <br> Email: rosso@unitecno.it</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>