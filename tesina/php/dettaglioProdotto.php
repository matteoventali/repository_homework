<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    require_once 'gestoriXML/gestoreRecensioni.php';

    // Variabili di gestione popup
    $mostraPopup = false; $msg = ''; $err = false;

    // Verifico se provengo dall'inserimento al carrello del prodotto
    if ( isset($_GET["esito_carrello"]) && $_GET["esito_carrello"] == 'true' )
    {
        // Inizializzo le variabili per il popup in modo che venga mostrato il messaggio di conferma
        $mostraPopup = true;
        $msg = 'Prodotto aggiunto al carrello!';
        $err = false;
    }
    
    // Verifico se sia pervenuto l'id del prodotto da visualizzare
    // dal post
    $prodotto = null;
    if ( isset($_GET["id_prodotto"]) )
    {
        $id_prodotto = $_GET["id_prodotto"];

        // Prelevo le informazioni del prodotto dal catalogo
        $gestoreCatalogo = new GestoreCatalogoProdotti();
        $prodotto = $gestoreCatalogo->ottieniProdotto($id_prodotto);

        // Se non trovo il prodotto redireziono l'utente alla homepage
        // (nel flusso usuale dell'applicazione cio' non puo' accadere)
        if ( $prodotto == null )
            header("Location: homepage.php");
    }
    else
        header("Location: homepage.php");
    
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileDettaglioProdotto.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Bisogna controllare se l'utente è loggato oppure no
            // In base a questo avrà diversi tipi di visualizzazione
            if( $sessione_attiva )
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

                // Mostro il popup
                echo creaPopup($mostraPopup, $msg, $err) . "\n\n";
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

        <div id="sezioneDettagli">
            <div id="sezioneCentrale">
                <form action="catalogo.php" method="post">
                    <fieldset><input type="submit" value="Indietro &#8617;" name="btnIndietro" /></fieldset>
                    <fieldset>
                        <input type="hidden" name="id_categoria" value="<?php echo $_GET["id_categoria"]; ?>" />
                        <input type="hidden" name="id_tipologia" value="<?php echo $_GET["id_tipologia"]; ?>" />
                        <input type="hidden" name="contenutoRicerca" value="<?php echo $_GET["contenutoRicerca"]; ?>" />
                    </fieldset>
                </form>

                <div id="sezioneTitolo" class="riga">
                    <div id="sezioneImmagine">
                        <img <?php echo "src='$prodotto->percorso_immagine' alt='Immagine del $prodotto->nome'"; ?> />
                    </div>
                    <div id="sezioneNome">
                        <h1> <?php echo $prodotto->nome; ?></h1>
                        <h3> <?php echo $prodotto->descrizione; ?></h3>
                    </div>
                </div>

                <div id="sezioneSpecifichePrezzo" class="riga">
                    <div id="specifiche">
                        <?php echo nl2br($prodotto->specifiche); ?>
                    </div>
                    <div id="prezzo">
                        <?php 
                            // Calcolo della percentuale di sconto fisso per il cliente (vedi documento)
                            // In caso invece non siamo loggati o si utilizza un account gestore/admin
                            // viene mostrato il prezzo di listino
                            if ( $sessione_attiva && ($_SESSION["ruolo"] == 'A' || $_SESSION["ruolo"] == 'G')
                                        || !$sessione_attiva )
                                $sconto_fisso = 0;
                            else
                                $sconto_fisso = calcolaScontoFisso($_SESSION['id_utente'], $_SESSION['reputazione'], $_SESSION['data_registrazione']);
                        
                            // Applico lo sconto fisso
                            $prezzo = applicaSconto($prodotto->prezzo_listino, $sconto_fisso);
                        
                            // Se presente un'offerta speciale la mostro altrimenti mostro il prezzo
                            // con applicato lo sconto fisso
                            if ( $prodotto->offerta_speciale != null )
                            {
                                // Verifico validita' del periodo associato all'offerta
                                $data_oggi = strtotime(date('Y-m-d'));
                                $data_inizio = strtotime($prodotto->offerta_speciale->data_inizio);
                                $data_fine = strtotime($prodotto->offerta_speciale->data_fine);

                                if ( $data_oggi >= $data_inizio && $data_oggi <= $data_fine )
                                {
                                    $offerta = applicaSconto($prodotto->prezzo_listino, $prodotto->offerta_speciale->percentuale);
                                    echo "<p style=\"background-color:red; color:white; text-align: center;\">Offerta speciale: $offerta crediti!</p>";    
                                }
                                else
                                    echo "<p>Prezzo: $prezzo crediti</p>";
                            }
                            else
                                echo "<p>Prezzo: $prezzo crediti</p>";
                        ?>
                    </div>
                </div>

                <div id="sezioneOpzioni" class="riga">
                    <?php 
                        // Campi hidden che contengono informazioni nascoste per permettere il flusso
                        // corretto all'interno dell'applicazione
                        $id_categoria = $_GET['id_categoria']; $id_tipologia = $_GET['id_tipologia'];
                        $contenuto_ricerca = $_GET['contenutoRicerca'];
                        $campi_hidden = "<input type=\"hidden\" value=\"$id_categoria\" name=\"id_categoria\" />
                                            <input type=\"hidden\" value=\"$id_tipologia\" name=\"id_tipologia\" />
                                            <input type=\"hidden\" value=\"$contenuto_ricerca\" name=\"contenutoRicerca\" />";
                        
                        // Se l'utente e' loggato come gestore mostro il form con le 3 opzioni di modifica, eliminazione e inserimento offerta speciale
                        if ( $sessione_attiva && $_SESSION["ruolo"] == 'G' )
                        {
                            // Se il prodotto e' mostrato fornisco l'opzione per nasconderlo, e viceversa
                            if ( $prodotto->mostra == 'true')
                                $campo_attivazione =  "<input type=\"submit\" value=\"Nascondi prodotto\" name=\"btnNascondi\" />";
                            else
                                $campo_attivazione =  "<input type=\"submit\" value=\"Mostra prodotto\" name=\"btnMostra\" />";

                            
                            echo "<form id=\"formModifica\" action=\"modificaProdotto.php\" method=\"post\">
                                    <fieldset>
                                        <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_prodotto\" />
                                        $campi_hidden
                                        <input type=\"submit\" value=\"Modifica prodotto\" name=\"btnModifica\" />
                                    </fieldset>
                                    </form>". "\n\n". 
                                    "<form id=\"formAttivazione\" action=\"attivaDisattivaProdotto.php\" method=\"post\">
                                        <fieldset>
                                            <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_prodotto\" />
                                            $campi_hidden
                                            $campo_attivazione
                                        </fieldset>
                                    </form>" . "\n\n" .
                                    "<form id=\"formOffertaSpeciale\" action=\"inserisciOffertaSpeciale.php\" method=\"post\">
                                        <fieldset>
                                            <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_prodotto\" />
                                            $campi_hidden
                                            <input type=\"submit\" value=\"Aggiungi offerta speciale\" name=\"btnAggiungiOffertaSpeciale\" />
                                        </fieldset>
                                    </form>";
                        }
                        else if ( $sessione_attiva && $_SESSION["ruolo"] == 'C' ) // Fornisco l'opportunita' al cliente di aggiungere al carrello il prodotto
                            echo "<form id=\"formCarrello\" action=\"aggiungiAlCarrello.php\" method=\"post\">
                                    <fieldset>
                                        <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_prodotto\" />
                                        $campi_hidden
                                        <input type=\"submit\" value=\"Aggiungi al carrello\" name=\"btnAggiungiCarrello\" />
                                    </fieldset>
                                    </form>" . "\n\n". 
                                    "<form id=\"formRecensione\" action=\"inserisciRecensione.php\" method=\"post\">
                                        <fieldset>
                                            <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_prodotto\" />
                                            $campi_hidden
                                            <input type=\"submit\" value=\"Aggiungi recensione\" name=\"btnAggiungiCarrello\" />
                                        </fieldset>
                                    </form>";
                    ?>
                </div>

                <div id="sezioneRecensioni" class="riga">
                    <h2>Recensioni</h2>
                    <?php
                        // Connessione al database
                        require 'lib/connection.php';
                        if ( $connessione )
                        {
                            $frammento_vuoto = file_get_contents('../html/frammentoIntervento.html');
                            $contenuto_html = '';

                            // Alloco il gestore recensioni
                            $gestoreRecensioni = new GestoreRecensioni();
                            $recensioni = $gestoreRecensioni->ottieniRecensioni($prodotto->id);
                            $n_recensioni = count($recensioni);
                            $id_intervento = 1;

                            if ( $n_recensioni == 0 )
                                echo '<p>Nessuna recensione presente</p>';

                            for ( $i=0; $i < $n_recensioni; $i++ )
                            {
                                // Prelevo le informazioni dell'utente tramite il database
                                $utente = ottieniUtente($recensioni[$i]->id_utente, $handleDB);
                                $container_padre = 'int_' . $id_intervento;
                                $frammento_stelline_statiche = initStelline(calcolaMediaRating($recensioni[$i]->valutazioni), '#00FFFF', false, $container_padre);
                                $frammento_stelline_dinamiche = initStelline(0, '#00FFFF', true, $container_padre);
                                
                                // Replace delle informazioni all'interno del frammento
                                $recensione_html = str_replace("%CONTENUTO%", $recensioni[$i]->contenuto, $frammento_vuoto);
                                $recensione_html = str_replace("%DATA_INTERVENTO%", date('d-m-Y', strtotime($recensioni[$i]->data)), $recensione_html);
                                $recensione_html = str_replace("%USERNAME%", $utente->username, $recensione_html);
                                $recensione_html = str_replace("%STELLINE_STATICHE%", $frammento_stelline_statiche, $recensione_html);
                                $recensione_html = str_replace("%ID_INTERVENTO%", $id_intervento, $recensione_html);
                                $recensione_html = str_replace("%ID_INTERVENTO_XML%", $recensioni[$i]->id, $recensione_html);
                                $recensione_html = str_replace("%TIPO_INTERVENTO%", 'recensione', $recensione_html);
                                $recensione_html = str_replace("%OPZIONE_FAQ%", 'none', $recensione_html);

                                // Se l'utente e' loggato come gestore o admin fornisco l'opzione di eliminare la recensione
                                if ( $sessione_attiva && ($_SESSION["ruolo"] == 'G' || $_SESSION["ruolo"] == 'A') )
                                    $recensione_html = str_replace("%OPZIONE_DISPLAY_ELIMINA%", 'block', $recensione_html);
                                else
                                    $recensione_html = str_replace("%OPZIONE_DISPLAY_ELIMINA%", 'none', $recensione_html);

                                // Le stelline dinamiche per valutare la recensione sono visibili
                                // se l'utente e' loggato e non e' il proprietario della recensione
                                $opt_display_dinamiche = "none";
                                if ( $sessione_attiva && $_SESSION["id_utente"] != $recensioni[$i]->id_utente )
                                {
                                    // Verifico se esiste gia' una valutazione effettuata da quell'utente
                                    $val = $gestoreRecensioni->ottieniValutazione($recensioni[$i]->id, $_SESSION["id_utente"]);
                                    if ( $val != null ) // L'ho trovata
                                    {
                                        $frammento_stelline_statiche = initStelline($val->rating, '#00FFFF', false, $container_padre);
                                        $recensione_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_statiche, $recensione_html);
                                    }
                                    else
                                        $recensione_html = str_replace("%STELLINE_DINAMICHE%", $frammento_stelline_dinamiche, $recensione_html);
                                    
                                    $opt_display_dinamiche = "block";
                                }
                                    
                                $recensione_html = str_replace("%VISUALIZZA_DINAMICHE%", $opt_display_dinamiche, $recensione_html);

                                
                                $contenuto_html .= $recensione_html . "\n";
                                $id_intervento++;
                            }

                            echo $contenuto_html . "\n";
                            $handleDB->close();
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>