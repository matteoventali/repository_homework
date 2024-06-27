<?php
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreriaDB.php';

    // Recupero il numero della domanda, utile per cercarla nel file
    $id_domanda = null;
    if ( isset($_GET["id_domanda"]))
        $id_domanda = $_GET["id_domanda"];  

    // Gestori per domande e risposte
    $gestore_domande = new GestoreDomande();
    $gestore_risposte = new GestoreRisposte();
    
    // Ottengo la domanda e le risposte associate
    $domanda = $gestore_domande->ottieniDomanda($id_domanda);
    $risposte = [];
    if ( $domanda != "" )
        $risposte = $gestore_risposte->ottieniRisposte($id_domanda, "false"); // Ottengo TUTTE le risposte
    else
        header("Location: homepage.php"); // Domanda non trovata

    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileDettaglioDomanda.css" type="text/css" />
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

                // L'opzione di aggiungere una nuova risposta deve essere fornita
                // all'utente loggato
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

        <!-- FORM DI REGISTRAZIONE -->
        <div id="sezioneCentrale">
            <div id="sezioneDomanda">
                <?php
                    // Prelevo un frammento di intervento vuoto
                    $frammento_vuoto = file_get_contents('../html/frammentoIntervento.html');

                    // Struttura per contenere le info di un utente
                    $utente = ottieniUtente($domanda->id_utente);

                    // Popolo la sezione domande
                    $domanda_html = str_replace("%CONTENUTO%", $domanda->contenuto, $frammento_vuoto);
                    $domanda_html = str_replace("%DATA_INTERVENTO%", date('d-m-Y', strtotime($domanda->data)), $domanda_html);
                    $domanda_html = str_replace("%USERNAME%", $utente->username, $domanda_html);
                    echo $domanda_html . "\n";
                ?>
            </div>

            <div id="sezioneRisposte">
                <form class="parteButton" action="inserisciRisposta.php" method="post">
                    <div style="display: <?php echo $visibilita_bottone; ?>">
                        <input type="submit" value="Inserisci nuova risposta" name="btnInserisci" />
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>