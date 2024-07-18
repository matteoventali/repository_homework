<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // Variabili utili all'identificazione dell'errore
    $mostraPopup = false; $msg = ''; $err = false;
    $popolazione_preliminare = false;
    $gestoreCatalogo = new GestoreCatalogoProdotti();

    // Verifico che vi sia una sessione attiva per un gestore e chi vi sia
    // un prodotto di riferimento
    if ( $sessione_attiva && $_SESSION["ruolo"] == 'G' && $_POST["id_prodotto"] )
    {
        // Verifico se sia stato premuto il tasto indietro
        if ( isset($_POST["btnIndietro"]) )
        {
            // Ridireziono l'utente sulla pagina del prodotto
            $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
            $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
            $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca";
            header("Location: dettaglioProdotto.php?$query_string");
        }
        else
        {
            // Verifico se c'e' una richiesta di inserimento offerta speciale
            if ( isset($_POST["btnInserisci"]) && isset($_POST["dataInizio"]) && isset($_POST["dataFine"]) 
                        && isset($_POST["percentuale"]) && isset($_POST["crediti"]) )
            {
                // Prelevo i dati del post
                $data_inizio = trim($_POST["dataInizio"]);
                $data_fine = trim($_POST["dataFine"]);
                $percentuale = trim($_POST["percentuale"]);
                $crediti = trim($_POST["crediti"]);
                
                // Verifico che tutti i dati siano presenti
                if ( strlen($data_inizio) > 0 && strlen($data_fine) > 0 && strlen($percentuale) > 0 && strlen($crediti) > 0 )
                {
                    // Verifico che i dati siano validi
                    
                    // Tentativo di convertire la data
                    $obj_data_inizio_inglese = DateTimeImmutable::createFromFormat('d-m-Y', $data_inizio);
                    $obj_data_fine_inglese = DateTimeImmutable::createFromFormat('d-m-Y', $data_fine);

                    // Tentativo di conversione percentuale e crediti in numeri
                    $percentuale_convertiti = intval($percentuale);
                    $crediti_convertiti = intval($crediti);

                    if ( $obj_data_inizio_inglese && $obj_data_fine_inglese && $percentuale_convertiti > 0 && $crediti_convertiti > 0 )
                    {
                        // Conversione delle date in timestamp per effettuare il confronto di validita'
                        $time_inizio = strtotime(date_format($obj_data_inizio_inglese, 'Y-m-d'));
                        $time_fine = strtotime(date_format($obj_data_fine_inglese, 'Y-m-d'));

                        // Verifico che il periodo specificato sia valido
                        if ( $time_fine >= $time_inizio )
                        {
                            // Procedo all'inserimento dell'offerta speciale
                            $gestoreCatalogo->inserisciOffertaSpeciale($_POST["id_prodotto"], date_format($obj_data_inizio_inglese, 'Y-m-d'), 
                                                                                    date_format($obj_data_fine_inglese, 'Y-m-d'), $crediti_convertiti, $percentuale_convertiti);
                            
                            // Ridireziono l'utente sulla pagina del prodotto
                            $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
                            $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
                            $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca";
                            header("Location: dettaglioProdotto.php?$query_string");
                        }
                        else // Periodo specificato non valido
                        {
                            $mostraPopup = true;
                            $msg = 'Periodo specificato non valido';
                            $err = true;
                        }
                    }
                    else // Composizione del messaggio d'errore
                    {
                        $mostraPopup = true;
                        $err = true;
                        $msg = '';
                        $n = 0;

                        if ( !$obj_data_inizio_inglese )
                        {
                            $msg = "Data d'inizio non valida";
                            $n++;
                        }
                        
                        if ( !$obj_data_fine_inglese )
                        {
                            if ( $n > 0 )
                                $msg .= '<br />';
                            $msg .= "Data fine non valida";

                            $n++;
                        }

                        if ( $percentuale_convertiti == 0 )
                        {
                            if ( $n > 0 )
                                $msg .= '<br />';
                            $msg .= "Percentuale di sconto non valida";

                            $n++;
                        }

                        if ( $crediti_convertiti == 0 )
                        {
                            if ( $n > 0 )
                                $msg .= '<br />';
                            $msg .= "Crediti non validi";

                            $n++;
                        }
                    }
                }
                else
                {
                    $mostraPopup = true;
                    $msg = 'Campi vuoti';
                    $err = true;
                }
            }
            else
            {
                // Tento di popolare il form in caso di offerta speciale gia' presente
                $prodotto = $gestoreCatalogo->ottieniProdotto($_POST["id_prodotto"]);

                // Devo aver trovato il prodotto altrimenti ridireziono l'utente
                if ( $prodotto->id != null )
                {
                    // Verifico se il prodotto ha o ha avuto gia'un'offerta speciale
                    if ( $prodotto->offerta_speciale != null )
                    {
                        // Popolo il form al caricamento della pagina
                        $popolazione_preliminare = true;
                        $data_inizio = date_format(date_create($prodotto->offerta_speciale->data_inizio), 'd-m-Y');
                        $data_fine = date_format(date_create($prodotto->offerta_speciale->data_fine), 'd-m-Y');
                        $crediti = $prodotto->offerta_speciale->crediti;
                        $percentuale = $prodotto->offerta_speciale->percentuale;
                    }
                }
                else
                    header("Location: homepage.php");
            }
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
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileInserisciOffertaSpeciale.css" type="text/css" />
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
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>
            <div id="sezioneForm">
                <form id="formIndietro" method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                    <fieldset >
                        <input type="hidden" value="<?php echo $_POST['id_categoria']; ?>" name="id_categoria" />
                        <input type="hidden" value="<?php echo $_POST['id_tipologia']; ?>" name="id_tipologia" />
                        <input type="hidden" value="<?php echo $_POST['contenutoRicerca']; ?>" name="contenutoRicerca" />
                        <input type="hidden" name="id_prodotto" value="<?php echo $_POST["id_prodotto"]; ?>" />
                        <input type="submit" value="Indietro &#8617;" name="btnIndietro" />
                    </fieldset>
                </form>

                <form id="formInserisciOfferta" method="post"  enctype="multipart/form-data">
                    <fieldset>  <p>Data inizio (DD-MM-YYYY): </p>  <input type="text" name="dataInizio" <?php if($err || $popolazione_preliminare) echo 'value="'. $data_inizio . '"';?>/>    </fieldset>
                    <fieldset>  <p>Data fine (DD-MM-YYYY):   </p>  <input type="text" name="dataFine" <?php if($err || $popolazione_preliminare) echo 'value="'. $data_fine . '"';?>/> </fieldset>
                    <fieldset>  <p>Percentuale sconto:       </p>  <input type="text" name="percentuale" <?php if($err || $popolazione_preliminare) echo 'value="'. $percentuale . '"';?>/> </fieldset>
                    <fieldset>  <p>Crediti erogati:          </p>  <input type="text" name="crediti" <?php if($err || $popolazione_preliminare) echo 'value="'. $crediti . '"';?>/> </fieldset>
                    <fieldset style="border-style: none; box-shadow: none;">
                        <input type="hidden" value="<?php echo $_POST['id_categoria']; ?>" name="id_categoria" />
                        <input type="hidden" value="<?php echo $_POST['id_tipologia']; ?>" name="id_tipologia" />
                        <input type="hidden" value="<?php echo $_POST['contenutoRicerca']; ?>" name="contenutoRicerca" />
                        <input type="hidden" name="id_prodotto" value="<?php echo $_POST["id_prodotto"]; ?>" />
                        <input type="button" value="Cancella" name="btnCancella" onclick="azzeraFormOffertaSpeciale();" />
                        <input type="submit" value="Inserisci" name="btnInserisci" /> 
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>