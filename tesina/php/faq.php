<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileFaq.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Parametro di visibilita' bottone aggiunta nuova faq
            $visibilita_bottone = "none";
            
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

                // L'opzione di aggiungere una nuova faq deve essere fornita
                // esclusivamente ad admin e gestori
                if ( $_SESSION["ruolo"] == "A" || $_SESSION["ruolo"] == "G" )
                    $visibilita_bottone = "block";
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

        <div id="sezioneFaq">
            <div id="parteCentrale">
                <div class="parteButton" style="display: <?php echo $visibilita_bottone?>;";>
                    <input type="submit" value="Inserisci nuova FAQ" name="btnInserisci" />
                </div>
                
                <?php
                    // Contenuto di una faq vuota
                    $faq_vuota = file_get_contents("../html/frammentoFaq.html");
                    
                    // Carico le FAQ dai file XML
                    $lista_faq = "";
                    for ( $i=0; $i<8; $i++ )
                    {
                        // Per ogni assegno un id progressivo
                        $faq_piena = str_replace("%DOMANDA%", "Domanda" . $i, $faq_vuota);
                        $faq_piena = str_replace("%ID_DOMANDA%", $i, $faq_piena);
                        $lista_faq .= $faq_piena . "\n";
                    }
                    
                    // Mostro la lista delle faq
                    echo $lista_faq . "\n";
                ?>
            </div>
        </div>
    </body>
</html>