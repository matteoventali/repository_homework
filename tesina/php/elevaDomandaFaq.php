<?php
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreriaDB.php';

    // Variabili utili all'identificazione dell'errore
    $msg = ''; $err = true; $mostraPopup = false;

    // Recupero il numero della domanda, utile per cercarla nel file
    // controllo che vi sia una sessione attiva e valida per admin o gestori
    $id_domanda = null;
    if ( $sessione_attiva && ($_SESSION["ruolo"] == 'A' || $_SESSION["ruolo"] == 'G') )
    {
        // Gestori per domande e risposte e id domanda ricevuto dal post
        $gestore_domande = new GestoreDomande();
        $gestore_risposte = new GestoreRisposte();
        $id_domanda = $_POST["id_domanda"];

        // Verifico se vi sia una richiesta di elevazione a faq
        if ( isset($_POST["btnEleva"]) )
        {
            // Verifico la consistenza dei dati ricevuti. Non posso avere
            // una risposta selezionata e una scritta tramite text area.
            $validi = true;
            $risposta_text = trim($_POST["text_risposta"]);
            if ( isset($_POST['id_risposta_selezionata']) && strlen($risposta_text) > 0 ) 
            {
                $validi = false;
                $msg = '<p>Risposte multiple non consentite</p>';
            }
            else if ( !isset($_POST['id_risposta_selezionata']) && !(strlen($risposta_text) > 0) )
            {
                $validi = false;
                $msg = '<p>Campi vuoti</p>';
            }

            // Se i dati superano il controllo
            if ( $validi )
            {
                $err = false;

                header("Location: dettaglioDomanda.php?id_domanda=$id_domanda");
            }
            else
                $mostraPopup  = true;
        }
        
        if (isset($_POST["id_domanda"]))
        {
            // Ottengo la domanda e le risposte associate per visualizzarle
            $domanda = $gestore_domande->ottieniDomanda($id_domanda);
            $risposte = [];
            if ( $domanda != "" )
                $risposte = $gestore_risposte->ottieniRisposte($id_domanda, "false"); // Ottengo TUTTE le risposte
            else
                header("Location: homepage.php"); // Domanda non trovata
        }
    }
    else
        header("Location: homepage.php");

    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileElevazioneFaq.css" type="text/css" />
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

            // Import della sidebar e mostro solo le opzioni dell'admin
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
            echo $sidebar . "\n";
            
        ?>

        <div id="sezioneCentrale">
            <div id="sezioneDomanda">
                <p> <?php echo $domanda->contenuto; ?> </p>
            </div>

            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <div id="sezioneRisposte">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <fieldset><input type="submit" value="Eleva a FAQ" name="btnEleva" /></fieldset>
                    <?php
                        // Frammento vuoto per includere le risposte
                        $frammento_vuoto = file_get_contents('../html/frammentoRispostaPerSelezione.html');
                        
                        // Mostro le risposte nel form per essere selezionate
                        $n_risposte = count($risposte);
                        $risposte_html = '';
                        for ( $i=0; $i < $n_risposte; $i++ )
                        {
                            // Costruisco il frammento da includere
                            $frammento_pieno = str_replace("%ID_RISPOSTA%", $risposte[$i]->id, $frammento_vuoto);
                            $frammento_pieno = str_replace("%CONTENUTO_RISPOSTA%", $risposte[$i]->contenuto, $frammento_pieno);
                            $risposte_html .= $frammento_pieno;
                        }

                        echo $risposte_html . "\n";
                    ?>
                    <fieldset>
                        <p>Scrivi qui la risposta:</p>
                        <textarea rows="6" cols="90" name="text_risposta"><?php if($err) echo $risposta;?></textarea>
                        <input type="hidden" value="<?php echo $_POST["id_domanda"]; ?>" name="id_domanda" />
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>