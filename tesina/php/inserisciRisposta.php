<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreRisposte.php';

    // Inizializzazione variabili per gestione popup
    $mostraPopup = false; $err = false; $msg = "";

    // Verifico che vi sia una sessione attiva per un utente e la domanda a cui rispondere
    // altrimenti ridireziono sulla homepage
    if (! ($sessione_attiva && isset($_POST['id_domanda'])) )
        header("Location: homepage.php");
    else if ( isset($_POST["risposta"])  ) // Verifico se vi sia una richiesta di inserimento nuova risposta
    {
        // Devo mostrare l'esito della richiesta
        $mostraPopup = true; $err = true; $msg = 'Campi vuoti';
        
        // Effettuo il controllo sui campi
        $risposta = trim($_POST["risposta"]);
        if ( strlen($risposta) > 0 )
        {
            // Procedo all'inserimento della risposta
            // ponendo il flag faq a false
            $gestore_risposte = new GestoreRisposte();
            $gestore_risposte->inserisciRisposta($risposta, $_SESSION["id_utente"], "false", $_POST['id_domanda']);
            $err = false;
            $msg = 'Inserimento risposta avvenuto con successo';
        }
    }

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileInserisciRisposta.css" type="text/css" />
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
            
            <div id="parteCentrale"> 
                <p style="font-size: 150%;"> <?php echo '<i>' . $_POST['contenuto_domanda'] . '</i>'; ?> </p>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <fieldset>
                        <p>Risposta:</p>
                        <textarea rows="6" cols="45" name="risposta"><?php if($err) echo $risposta;?></textarea>
                        <input type="hidden" value="<?php echo $_POST["id_domanda"]; ?>" name="id_domanda" />
                        <input type="hidden" value="<?php echo $_POST["contenuto_domanda"]; ?>" name="contenuto_domanda" />
                    </fieldset>
                        
                    <div class="parteButton">
                        <input type="submit" value="Aggiungi risposta" name="btnAggiungi" />
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>