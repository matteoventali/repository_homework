<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    require_once 'gestoriXML/gestorePortafogliBonus.php';
    require_once 'gestoriXML/gestoreCarrelli.php';

    // A questa pagina possono accedervi solo i clienti
    // Nel caso in cui l'utente non fosse cliente o fosse bannato,
    // viene ridirezionato
    if ( !$sessione_attiva || $_SESSION["ruolo"] != "C"  )
        header("Location: homepage.php");

    // Gestori
    $gestoreCatalogo = new GestoreCatalogoProdotti();
    $gestoreCarrelli = new GestoreCarrelli();
    $gestorePortafogliBonus = new GestorePortafogliBonus();

    // Verifico se c'è da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileFinalizzaAcquisto.css" type="text/css" />
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

        <div id="sezioneCentrale">
            <div id="riquadro">
                <div id="sezioneRiepilogo">
                    <h2>Riepilogo</h2>
                    <ul>
                        <?php
                            // Calcolo lo sconto fisso per il cliente loggato
                            $sconto_fisso = calcolaScontoFisso($_SESSION['id_utente'], $_SESSION['reputazione'], $_SESSION['data_registrazione']);

                            // Flag per indicare la presenza di almeno un prodotto
                            // NON in offerta speciale su cui poter poi applicare i crediti bonus
                            // a discrezione dell'utente (sconto variabile, vedi documento)
                            $sconto_variabile = false;

                            // Popolazione lista di riepilogo
                            // e calcolo del totale provvisorio
                            // Prelevo i prodotti dal carrello associato all'utente
                            $prodotti = $gestoreCarrelli->ottieniProdottiCarrello($_SESSION["id_utente"]);
                            $n_prodotti = 0;
                            if ( $prodotti != null )
                                $n_prodotti = count($prodotti);

                            $totale_provvisorio = 0;
                            $totale_provvisorio_senza_offerte = 0;

                            for ( $i=0; $i < $n_prodotti; $i++ )
                            {
                                // Ottengo il prodotto i-esimo del carrello
                                $prodotto = $gestoreCatalogo->ottieniProdotto($prodotti[$i]);
                                
                                echo "<li>$prodotto->nome</li>\n";
                                
                                // Incremento il totale provvisorio
                                if ( $prodotto->offerta_speciale != null && strtotime($prodotto->offerta_speciale->data_fine) + 86400 >= time() )
                                    $prezzo = applicaSconto($prodotto->prezzo_listino, $prodotto->offerta_speciale->percentuale);
                                else
                                {
                                    $prezzo = applicaSconto($prodotto->prezzo_listino, $sconto_fisso);
                                    $sconto_variabile = true;
                                    $totale_provvisorio_senza_offerte += $prezzo;
                                }

                                $totale_provvisorio += $prezzo;
                            }
                        ?>
                    </ul>
                </div>

                
                <form id="sezioneAcquisto" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>"> 
                        <?php
                            if ( $sconto_variabile )
                            {
                                // Ottengo l'ammontare massimo di crediti utilizzabili
                                $crediti_massimi = $gestorePortafogliBonus->ottieniCreditiMassimi($_SESSION["id_utente"], $totale_provvisorio_senza_offerte);
                                $id_cliente = $_SESSION['id_utente'];

                                echo "
                                <h2> Sub-totale: $totale_provvisorio </h2>
                                <fieldset>
                                    <p>Crediti bonus aggiuntivi (max $crediti_massimi): </p>
                                    <input type=\"text\" id=\"casellaCrediti\" name=\"creditiBonus\" /> 
                                </fieldset>" . "\n\n";
                            }
                        ?>
                        
                        <h2> 
                            Totale: <span id="totale"><?php echo $totale_provvisorio; ?></span>
                                    <span onclick="aggiornaTotale(<?php echo $id_cliente ?>);" style="cursor:pointer;">&#10227;</span>
                        </h2>
                        
                        <fieldset>
                            <input type="submit" value="Acquista" name="btnAcquista" />
                        </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>