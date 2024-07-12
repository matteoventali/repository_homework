<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreCategorie.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';

    // Gestore categorie
    $gestoreCategorie = new GestoreCategorie();
    $categorie = $gestoreCategorie->ottieniCategorie();

    // Variabili utili all'identificazione dell'errore
    $mostraPopup = false; $msg = ''; $err = true;

    // Verifico la consistenza dei parametri di ricerca ricevuti
    $id_categoria = '';
    $id_tipologia = '';
    $contenuto_ricerca = '';
    if ( isset($_POST["id_categoria"]) && $_POST["id_categoria"] != '0' )
        $id_categoria = $_POST["id_categoria"];
    if ( isset($_POST["id_tipologia"]) && $_POST["id_tipologia"] != '0')
        $id_tipologia = $_POST["id_tipologia"];
    if ( isset($_POST["contenutoRicerca"]) )
        $contenuto_ricerca = trim($_POST["contenutoRicerca"]);

    // Verifico che i parametri di ricerca non siano vuoti
    if ( $id_categoria == '' && $id_tipologia == '' && $contenuto_ricerca == '' )
    {
        // Qui bisogna mostrare il popup
        $mostraPopup = true;
        $msg = 'Parametri di ricerca non specificati';
    }
    else
    {
        // Eseguo la ricerca tramite apposita metodo
        $gestore_catalogo = new GestoreCatalogoProdotti();
        $lista_prodotti = $gestore_catalogo->ricercaProdotti($id_categoria, $id_tipologia, $contenuto_ricerca);
    }
    
    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
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

        <div id="sezioneCentrale">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <div id="sezioneRicerca">
                <form id="ricercaProdotti" action="catalogo.php" method="post">
                    <fieldset><p>Categoria</p>
                        <select name="id_categoria" onchange="ottieniTipologie(this)">
                            <option value='0' selected="selected">Seleziona categoria</option>
                            <?php
                                // Stampa delle categorie disponibili
                                $categorie = $gestoreCategorie->ottieniCategorie();
                                $n_categorie = count($categorie);

                                // Popolo la tendina
                                for ( $i=0; $i<$n_categorie; $i++ )
                                {
                                    $nome_cat = $categorie[$i]->nome_categoria;
                                    $id_cat = $categorie[$i]->id_categoria;
                                    echo "<option value=\"$id_cat\">$nome_cat</option>" . "\n";
                                }
                            ?>
                        </select>
                    </fieldset>
                    <fieldset><p>Tipologia</p>
                        <select name="id_tipologia" id="tendinaTipologia">
                            <option value='0' selected="selected">Seleziona tipologia</option>
                        </select> 
                    </fieldset>
                    <fieldset><p>Ricerca</p><input type="text" name="contenutoRicerca" /></fieldset>
                    <fieldset><input type="submit" name="btnRicerca" value="Cerca &#128269;" /></fieldset>
                    <fieldset><input type="reset" name="btnIndietro" onclick="azzeraRicercaProdotti();" value="Reset &#8634;" /></fieldset>
                </form>
            </div>

            <div id="sezioneRisultati">
                <div id="sezioneOrdinamento">
                    <form id="formOrdinamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset>
                            <input type="hidden" name="id_categoria" value="" />
                            <input type="hidden" name="id_tipologia" value="" />
                            <input type="hidden" name="contenutoRicerca" value="" />
                        </fieldset>
                        <fieldset>
                            <p>
                                Ordina per:     
                                <input type="radio" name="ordinamento" value="crescente" />
                                prezzo crescente
                                <input type="radio" name="ordinamento" value="decrescente" />
                                prezzo decrescente
                                <input type="radio" name="ordinamento" value="no" />
                                nessun ordinamento
                            </p>
                        </fieldset>
                    </form>
                </div>

                <div id="sezioneProdotti">
                    <?php
                        // Composizione dei risultati della ricerca
                        $n_prodotti = count($lista_prodotti);
                        $data_oggi = strtotime(date('Y-m-d'));

                        if ( $n_prodotti == 0 )
                            echo '<p style="font-size: 150%; width:100%; text-align:center;">Nessun prodotto soddisfa i criteri di ricerca</p>';
                        else
                        {
                            $contenuto_html = '';
                            $frammento_vuoto = file_get_contents('../html/frammentoTesseraProdotto.html');

                            // Calcolo della percentuale di sconto fisso per il cliente (vedi documento)
                            // In caso invece non siamo loggati o si utilizza un account gestore/admin
                            // viene mostrato il prezzo di listino
                            if ( $sessione_attiva && ($_SESSION["ruolo"] == 'A' || $_SESSION["ruolo"] == 'B')
                                        || !$sessione_attiva )
                                $sconto_fisso = 0;
                            else
                                $sconto_fisso = calcolaScontoFisso($_SESSION['id_utente'], $_SESSION['reputazione'], $_SESSION['data_registrazione']);
                            
                            // Creazione di una tessera per ogni prodotto
                            for ( $i=0; $i < $n_prodotti; $i++ )
                            {
                                // Applico lo sconto fisso
                                $prezzo = applicaSconto($lista_prodotti[$i]->prezzo_listino, $sconto_fisso);

                                // Fill del frammento
                                $cat = $lista_prodotti[$i]->id_categoria;
                                $tipi = $gestoreCategorie->ottieniTipi($cat);

                                $frammento_pieno = str_replace("%NOME_PRODOTTO%", $lista_prodotti[$i]->nome, $frammento_vuoto);
                                $frammento_pieno = str_replace("%ID_PRODOTTO%", $lista_prodotti[$i]->id, $frammento_pieno);
                                $frammento_pieno = str_replace("%PATH_IMMAGINE%", $lista_prodotti[$i]->percorso_immagine, $frammento_pieno);
                                $frammento_pieno = str_replace("%CATEGORIA_PRODOTTO%", $categorie[$cat-1]->nome_categoria, $frammento_pieno);
                                $frammento_pieno = str_replace("%TIPOLOGIA_PRODOTTO%",$tipi[$lista_prodotti[$i]->id_tipo - 1]->nome_tipo, $frammento_pieno);
                                $frammento_pieno = str_replace("%PREZZO_PRODOTTO%",$prezzo, $frammento_pieno);

                                // Se il prodotto ha un'offerta speciale in corso mostro il paragrafo adeguato
                                if ( $lista_prodotti[$i]->offerta_speciale != NULL )
                                {
                                    // Verifico validita' del periodo associato all'offerta
                                    $data_inizio = strtotime($lista_prodotti[$i]->offerta_speciale->data_inizio);
                                    $data_fine = strtotime($lista_prodotti[$i]->offerta_speciale->data_fine);

                                    if ( $data_oggi >= $data_inizio && $data_oggi <= $data_fine )
                                    {
                                        $frammento_pieno = str_replace("%DISPLAY_OFFERTA_SPECIALE%", 'block', $frammento_pieno);
                                        $frammento_pieno = str_replace("%PREZZO_PRODOTTO_OFFERTA%", 
                                                                applicaSconto($lista_prodotti[$i]->prezzo_listino, $lista_prodotti[$i]->offerta_speciale->percentuale), 
                                                                $frammento_pieno);
                                    }
                                    else // Offerta scaduta
                                        $frammento_pieno = str_replace("%DISPLAY_OFFERTA_SPECIALE%", 'none', $frammento_pieno);    
                                }
                                else
                                    $frammento_pieno = str_replace("%DISPLAY_OFFERTA_SPECIALE%", 'none', $frammento_pieno);
                                
                                $contenuto_html .= $frammento_pieno . "\n";
                            }

                            echo $contenuto_html . "\n";
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>