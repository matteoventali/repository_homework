<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreTagliRicarica.php';
    require_once 'gestoriXML/gestoreRichiesteRicariche.php';

    // Inizializzazione variabili per gestione popup
    $mostraPopup = false; $err = false; $msg = "";

    // Verifico che vi sia una sessione attiva per un cliente
    // altrimenti ridireziono sulla homepage
    if (!( $sessione_attiva && $_SESSION["ruolo"] == 'C'))
        header("Location: homepage.php");
    else if ( isset($_POST["creditiRichiesti"]) ) // Verifico se vi sia una richiesta di ricarica
    {
        // Devo mostrare l'esito della richiesta
        $mostraPopup = true; $err = true; $msg = 'Taglio non selezionato';
        
        // Effettuo il controllo sui campi
        $crediti_richiesti = $_POST["creditiRichiesti"];
        if ( $crediti_richiesti != "0" )
        {
            // Eseguo l'inserimento
            $gestore_richieste = new GestoreRichiesteRicariche();    
            $esito = $gestore_richieste->inserisciNuovaRichiestaRicarica($_SESSION["id_utente"], $crediti_richiesti);
            
            if ( $esito )
            {
                $err = false;
                $msg = 'Inserimento avvenuto con successo';
            }
            else
                $msg = 'Inserimento fallito';
        }
    }

    // Ottengo i tagli di ricarica disponibili
    $gestore_tagli = new GestoreTagliRicarica();
    $tagli = $gestore_tagli->ottieniTagliRicarica();
    $n_tagli = count($tagli);

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileRichiestaRicarica.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
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

        <div id="sezioneForm">
            <div id="sezionePopup">
                <?php 
                    // Stampo il popup se necessario
                    echo creaPopup($mostraPopup, $msg, $err) . "\n";
                ?>
            </div>
            
            <div id="parteCentrale">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <fieldset>
                        <p>Seleziona taglio:</p>
                        <select name="creditiRichiesti">
                            <option value="0">Taglio</option>
                            <?php
                                for ( $i=0; $i<$n_tagli; $i++ )
                                {
                                    // Creo una nuova opzione
                                    echo '<option value="' . $tagli[$i]->crediti. '">' . "\n";
                                    echo $tagli[$i]->importo . "&euro; &rarr; " . $tagli[$i]->crediti . " crediti\n";
                                    echo '</option>';
                                }
                            ?>
                        </select>
                    </fieldset>
                        
                    <div class="parteButton">
                            <input type="submit" value="Invia" name="btnInvia" />
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>