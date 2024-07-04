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

            // Parametro di visibilita' per visualizzare il bottone per eliminare
            // un intervento (domanda o risposta)
            $visibilita_elimina = "none";

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

                // Import della sidebar
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
                echo $sidebar . "\n";

                // L'opzione di aggiungere una nuova risposta deve essere fornita
                // all'utente loggato che non sia proprietario della domanda
                if ( $_SESSION['id_utente'] != $domanda->id_utente )
                    $visibilita_bottone = "block";

                // Genero due paragrafi nascosti per memorizzare nella pagina
                // l'id dell'utente e la sua reputazione in modo da effettuare la valutazione
                // La reputazione potrebbe essere cambiata dal momento in cui l'utente si e' loggato
                // pertanto e' opportuno effettuare una nuova query al database
                require 'lib/connection.php';
                if ( $connessione )
                {
                    $utente = ottieniUtente($_SESSION['id_utente'], $handleDB);
                    echo '<p style="display:none;" id="id_utente">' . $_SESSION['id_utente'] . '</p>' . "\n";
                    echo '<p style="display:none;" id="reputazione_utente">' . $utente->reputazione . '</p>' . "\n";
                    $handleDB->close();
                }

                // Se il ruolo dell'utente e' gestore o admin fornisco la possibilita' di eliminare
                // gli interventi
                if ( $_SESSION['ruolo'] == 'A' || $_SESSION['ruolo'] == 'G' )
                    $visibilita_elimina = 'block';
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

        <div id="sezioneCentrale">
            <div id="sezioneDomanda">
                <?php
                    // Mi connetto al database per estrarre successivamente le informazioni
                    // degli utenti
                    require 'lib/connection.php';
                    
                    // Prelevo un frammento di intervento vuoto
                    $frammento_vuoto = file_get_contents('../html/frammentoIntervento.html');
                    
                    // Struttura per contenere le info di un utente
                    $utente = ottieniUtente($domanda->id_utente, $handleDB);

                    // Inizializzo le stelline
                    $id_intervento = 1; 
                    $container_padre = 'int_' . $id_intervento;
                    $frammento_stelline_statiche = initStelline(calcolaMediaRating($domanda->valutazioni), 'blue', false, $container_padre);
                    $frammento_stelline_dinamiche = initStelline(0, 'blue', true, $container_padre);

                    // Popolo la sezione domande (intervento 1 della pagina)
                    $domanda_html = str_replace("%CONTENUTO%", $domanda->contenuto, $frammento_vuoto);
                    $domanda_html = str_replace("%DATA_INTERVENTO%", date('d-m-Y', strtotime($domanda->data)), $domanda_html);
                    $domanda_html = str_replace("%USERNAME%", $utente->username, $domanda_html);
                    $domanda_html = str_replace("%STELLINE_STATICHE%", $frammento_stelline_statiche, $domanda_html);
                    $domanda_html = str_replace("%ID_INTERVENTO%", $id_intervento, $domanda_html);
                    $domanda_html = str_replace("%ID_INTERVENTO_XML%", $domanda->id, $domanda_html);
                    $domanda_html = str_replace("%TIPO_INTERVENTO%", 'domanda', $domanda_html);
                    $domanda_html = str_replace("%OPZIONE_DISPLAY_ELIMINA%", $visibilita_elimina, $domanda_html);
                    $id_intervento++;

                    // Le stelline dinamiche per valutare la domanda sono visibili
                    // se l'utente e' loggato, non e' il proprietario della domanda e non ha gia' valutato la domanda
                    // In caso l'utente non sia il proprietario ma ha valutato la domanda appaiono le stelline statiche
                    // per mostrare la valutazione gia' effettuata
                    $opt_display_dinamiche = "none";
                    if ( $sessione_attiva && $_SESSION["id_utente"] != $domanda->id_utente )
                    {
                        // Verifico se esiste una valutazione dell'utente in oggetto
                        $val = $gestore_domande->ottieniValutazione($domanda->id, $_SESSION["id_utente"]);
                        if ( $val != null ) // Esiste gia' una valutazione
                        {
                            $frammento_stelline_statiche = initStelline($val->rating, 'blue', false, $container_padre);
                            $domanda_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_statiche, $domanda_html);
                        }
                        else
                            $domanda_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_dinamiche, $domanda_html);
                        
                        $opt_display_dinamiche = "block";
                    }
                    $domanda_html = str_replace("%VISUALIZZA_DINAMICHE%", $opt_display_dinamiche, $domanda_html);
                    echo $domanda_html . "\n";

                    // Popolo la sezione delle risposte
                    $risposte_html = "";
                    $n_risp = count($risposte);
                    if ( $n_risp == 0 )
                    {
                        $risposte_html = "<p style='font-size: 150%'>Nessuna risposta presente</p>";
                        $gap = 'gap: 0px;';
                    }
                    else
                    {
                        for ( $i=0; $i<$n_risp; $i++ )
                        {
                            $utente = ottieniUtente($risposte[$i]->id_utente, $handleDB);
                            $container_padre = 'int_' . $id_intervento;
                            $frammento_stelline_statiche = initStelline(calcolaMediaRating($risposte[$i]->valutazioni), '#00FFFF', false, $container_padre);
                            $frammento_stelline_dinamiche = initStelline(0, '#00FFFF', true, $container_padre);
                            
                            // Replace
                            $risposta_html = str_replace("%CONTENUTO%", $risposte[$i]->contenuto, $frammento_vuoto);
                            $risposta_html = str_replace("%DATA_INTERVENTO%", date('d-m-Y', strtotime($risposte[$i]->data)), $risposta_html);
                            $risposta_html = str_replace("%USERNAME%", $utente->username, $risposta_html);
                            $risposta_html = str_replace("%STELLINE_STATICHE%", $frammento_stelline_statiche, $risposta_html);
                            $risposta_html = str_replace("%ID_INTERVENTO%", $id_intervento, $risposta_html);
                            $risposta_html = str_replace("%ID_INTERVENTO_XML%", $risposte[$i]->id, $risposta_html);
                            $risposta_html = str_replace("%TIPO_INTERVENTO%", 'risposta', $risposta_html);
                            $risposta_html = str_replace("%OPZIONE_DISPLAY_ELIMINA%", $visibilita_elimina, $risposta_html);

                            // Le stelline dinamiche per valutare la risposta sono visibili
                            // se l'utente e' loggato e non e' il proprietario della risposta
                            $opt_display_dinamiche = "none";
                            if ( $sessione_attiva && $_SESSION["id_utente"] != $risposte[$i]->id_utente )
                            {
                                // Verifico se esiste gia' una valutazione effettuata da quell'utente
                                $val = $gestore_risposte->ottieniValutazione($risposte[$i]->id, $_SESSION["id_utente"]);
                                if ( $val != null ) // L'ho trovata
                                {
                                    $frammento_stelline_statiche = initStelline($val->rating, '#00FFFF', false, $container_padre);
                                    $risposta_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_statiche, $risposta_html);
                                }
                                else
                                    $risposta_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_dinamiche, $risposta_html);
                                
                                $opt_display_dinamiche = "block";
                            }
                                
                            $risposta_html = str_replace("%VISUALIZZA_DINAMICHE%", $opt_display_dinamiche, $risposta_html);

                            $id_intervento++;

                            $risposte_html .= $risposta_html . "\n";
                        }

                        $gap = 'gap: 30px;'; // Il gap deve essere visualizzato solo in caso di risposte presenti
                    }
                ?>
            </div>

            <div id="sezioneRisposte" style="<?php echo $gap; ?>">
                <form class="parteButton" action="inserisciRisposta.php" method="post">
                    <div style="display: <?php echo $visibilita_bottone; ?>">
                        <input type="submit" value="Inserisci nuova risposta" name="btnInserisci" />
                        <input type="hidden" name="id_domanda" value="<?php echo $_GET['id_domanda']; ?>" />
                        <input type="hidden" name="contenuto_domanda" value="<?php echo $domanda->contenuto; ?>" />
                    </div>
                </form>
                
                <?php
                    // Mostro le risposte nella pagina
                    echo $risposte_html . "\n";

                    // Chiudo la connessione col database
                    $handleDB->close();
                ?>
            </div>
        </div>
    </body>
</html>