<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCarrelli.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // A questa pagina possono accedervi solo i clienti
    // Nel caso in cui l'utente non fosse cliente o fosse bannato,
    // viene ridirezionato
    if ( !$sessione_attiva || $_SESSION["ruolo"] != "C" )
        header("Location: homepage.php");

    // Verifico se c'è da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCarrello.css" type="text/css" />
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

        <div id="sezioneCarrello">
            <form id="formCarrello" action="finalizzaAcquisto.php" method="post">
                <div id="sezioneProdotti">
                    <?php
                        // Prelevo i prodotti dal carrello dell'utente loggato
                        if ( $sessione_attiva && $_SESSION["ruolo"] == 'C' )
                        {
                            // Variabile per tenere traccia del totale provvisorio del carrello
                            $totale_provvisorio = 0;
                            
                            // Gestore carrelli
                            $gestoreCarrelli = new GestoreCarrelli();
                            $lista_prodotti = $gestoreCarrelli->ottieniProdottiCarrello($_SESSION["id_utente"]);

                            // Gestore catalogo
                            $gestoreCatalogo = new GestoreCatalogoProdotti();
                            
                            // Per ogni prodotto nel carrello compongo un frammento da mostrare
                            $frammento_vuoto = file_get_contents('../html/frammentoProdottoCarrello.html');
                            $n_prod = count($lista_prodotti);

                            // Flag per segnalare la presenza di prodotti nel carrello (si considerano solo quelli non nascosti)
                            $prod_presenti = false;

                            // Calcolo lo sconto fisso per il cliente loggato
                            $sconto_fisso = calcolaScontoFisso($_SESSION['id_utente'], $_SESSION['reputazione'], $_SESSION['data_registrazione']);

                            $contenuto_html = '';
                            
                            for ( $i=0; $i < $n_prod; $i++ )
                            {
                                $prod = $gestoreCatalogo->ottieniProdotto($lista_prodotti[$i]);
                                
                                // Se il prodotto e' mostrato nel catalogo lo mostro nel carrello
                                // in questo modo, si evita di far acquistare un prodotto non presente
                                // attualmente nel catalogo
                                if ( $prod->mostra == 'true' )
                                {
                                    // Almeno un prodotto e' presente
                                    $prod_presenti = true;
                                    
                                    // Frammento prodotto
                                    $frammento = str_replace('%NOME_PRODOTTO%', $prod->nome, $frammento_vuoto);
                                    $frammento = str_replace('%ID_PRODOTTO%', $prod->id, $frammento);
                                    $frammento = str_replace('%ID_CLIENTE%', $_SESSION["id_utente"], $frammento);
                                    $frammento = str_replace('%PATH_IMMAGINE%', $prod->percorso_immagine, $frammento);

                                    // Se vi e' un'offerta speciale mostro il prezzo dell'offerta
                                    // altrimenti viene mostrato quello di listino applicando lo sconto fisso (vedi documento)
                                    $prezzo_da_mostrare = 0; $crediti_bonus = 0;
                                    if ( $prod->offerta_speciale != null && strtotime($prod->offerta_speciale->data_fine) >= time() )
                                    {
                                        $prezzo_da_mostrare = applicaSconto($prod->prezzo_listino, $prod->offerta_speciale->percentuale);
                                        $crediti_bonus = $prod->offerta_speciale->crediti;
                                    }
                                    else
                                        $prezzo_da_mostrare = applicaSconto($prod->prezzo_listino, $sconto_fisso);

                                    $frammento = str_replace('%PREZZO_PRODOTTO%', $prezzo_da_mostrare, $frammento);
                                    $frammento = str_replace('%PREZZO_ACQUISTO%', $prezzo_da_mostrare, $frammento);
                                    $frammento = str_replace('%CREDITI_BONUS%', $crediti_bonus, $frammento);

                                    // Incremento del totale provvisorio
                                    $totale_provvisorio += intval($prezzo_da_mostrare);

                                    $contenuto_html .= $frammento . "\n";
                                }
                            }

                            // Se il numero di prodotti e' nullo messaggio di notifica all'utente
                            if ( !$prod_presenti )
                                $contenuto_html = '<p style="font-weight: bold; text-align:center; font-size: 130%;">Nessun prodotto nel carrello!</p>';
                            
                            echo $contenuto_html . "\n\n";
                        }
                    ?>
                </div>

                <fieldset>
                    <p>Totale provvisorio: <?php if(isset($totale_provvisorio)) echo $totale_provvisorio; ?></p>
                    <?php
                        if ( isset($prod_presenti) && $prod_presenti > 0 )
                            echo '<input type="submit" name="btnFinalizza" value="Procedi all\'acquisto" />';
                    ?>
                </fieldset>
            </form>
        </div>
    </body>
</html>