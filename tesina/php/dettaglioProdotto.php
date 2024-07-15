<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    require_once 'gestoriXML/gestoreRecensioni.php';
    
    // Verifico se sia pervenuto l'id del prodotto da visualizzare
    // dal post
    $prodotto = null;
    if ( isset($_POST["id_prodotto"]) )
    {
        $id_prodotto = $_POST["id_prodotto"];

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

        <div id="sezioneDettagli">
            <div id="sezioneCentrale">
                <form action="catalogo.php" method="post">
                    <fieldset><input type="submit" value="Indietro &#8617;" name="btnIndietro" /></fieldset>
                    <fieldset>
                        <input type="hidden" name="id_categoria" value="<?php echo $_POST["id_categoria"]; ?>" />
                        <input type="hidden" name="id_tipologia" value="<?php echo $_POST["id_tipologia"]; ?>" />
                        <input type="hidden" name="contenutoRicerca" value="<?php echo $_POST["contenutoRicerca"]; ?>" />
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
                        ?>

                        <p>Prezzo: <?php echo $prezzo . " "; ?>crediti</p>

                        <?php
                            // Se presente un'offerta speciale la mostro
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
                            }
                        ?>
                    </div>
                </div>

                <div id="sezioneOpzioni" class="riga">
                    <?php 
                        // Se l'utente e' loggato come gestore mostro il form con le 3 opzioni di modifica, eliminazione e inserimento offerta speciale
                        if ( $_SESSION["ruolo"] == 'G' )
                            echo "<form id=\"formOpzioni\" action=\"modificaCliente.php\" method=\"post\" style=\"$visibilita_bottone\">
                                    <fieldset>
                                        <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_cliente\" />
                                        <input type=\"submit\" value=\"Modifica prodotto\" name=\"btnModifica\" />
                                        <input type=\"submit\" value=\"Elimina prodotto\" name=\"btnElimina\" />
                                        <input type=\"submit\" value=\"Aggiungi offerta speciale\" name=\"btnAggiungiOffertaSpeciale\" />
                                    </fieldset>
                                </form>";
                        else if ( $_SESSION["ruolo"] == 'C' ) // Fornisco l'opportunita' al cliente di aggiungere al carrello il prodotto
                        echo "<form id=\"formOpzioni\" action=\"modificaCliente.php\" method=\"post\" style=\"$visibilita_bottone\">
                                    <fieldset>
                                        <input type=\"hidden\" value=\"$prodotto->id\" name=\"id_cliente\" />
                                        <input type=\"submit\" value=\"Aggiungi al carrello\" name=\"btnAggiungiCarrello\" />
                                    </fieldset>
                                </form>";
                    ?>
                </div>

                <div id="sezioneRecensioni" class="riga">
                    <h2>Recensioni</h2>
                    <?php
                        $frammento_vuoto = file_get_contents('../html/frammentoIntervento.html');
                        $contenuto_html = '';

                        // Alloco il gestore recensioni
                        $gestoreRecensioni = new GestoreRecensioni();
                        $recensioni = $gestoreRecensioni->ottieniRecensioni($prodotto->id);
                        $n_recensioni = count($recensioni);
                        
                        for ( $i=0; $i < $n_recensioni; $i++ )
                        {
                            /****************/
                        }

                        echo $contenuto_html . "\n";
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>