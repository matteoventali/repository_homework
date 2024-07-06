<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';

    // Inizializzazione variabili per gestione popup
    $mostraPopup = false; $err = false; $msg = "";

    // Verifico che vi sia una sessione attiva per un utente admin o gestore
    // altrimenti ridireziono sulla homepage
    if (!( $sessione_attiva && ($_SESSION["ruolo"] == 'A' || $_SESSION["ruolo"] == 'G') ))
        header("Location: homepage.php");
    else if ( isset($_POST["domanda"]) && isset($_POST["risposta"]) ) // Verifico se vi sia una richiesta di inserimento nuova faq
    {
        // Devo mostrare l'esito della richiesta
        $mostraPopup = true; $err = true; $msg = 'Campi vuoti';
        
        // Effettuo il controllo sui campi
        $domanda = trim($_POST["domanda"]);
        $risposta = trim($_POST["risposta"]);
        if ( strlen($domanda) > 0 && strlen($risposta) > 0 )
        {
            // Procedo all'inserimento della domanda e della risposta
            // impostando entrambi da visualizzare nelle faq
            $gestore_domande = new GestoreDomande();
            $gestore_risposte = new GestoreRisposte();
            $id_domanda = $gestore_domande->inserisciDomanda($domanda, $_SESSION["id_utente"], "true");
            if ( $id_domanda != null )
            {
                $gestore_risposte->inserisciRisposta($risposta, $_SESSION["id_utente"], "true", $id_domanda);
                $err = false;
                
                // Ridireziono l'utente sulla pagina delle FAQ
                header("Location: faq.php");
            }
            else // Errore generico causato dai file XML
                $msg = "Errore nell'inserimento della FAQ";
        }
    }

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileInserisciFaq.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Import della navbar
            // Visualizzo nome dell'utente e il tasto "Logout"
            $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
            $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
            echo $nav ."\n";

            // Import della sidebar
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
            echo $sidebar . "\n";
        ?>

        <div id="sezioneForm">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>
            
            <form id="parteCentrale" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <fieldset>
                    <p>Domanda:</p>
                    <textarea rows="4" cols="45" name="domanda"><?php if($err) echo $domanda;?></textarea>
                </fieldset>
                <fieldset>
                    <p>Risposta:</p>
                    <textarea rows="4" cols="45" name="risposta"><?php if($err) echo $risposta;?></textarea>
                </fieldset>
                    
                <div class="parteButton">
                        <input type="submit" value="Invia" name="btnInvia" />
                </div>
            </form>
        </div>
    </body>
</html>