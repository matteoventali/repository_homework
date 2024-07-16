<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreRecensioni.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';

    // Inizializzazione variabili per gestione popup
    $mostraPopup = false; $err = false; $msg = "";
    $prodotto = null;

    // Verifico che vi sia una sessione attiva per un cliente
    if (!($sessione_attiva && $_SESSION["ruolo"] == 'C'))
        header("Location: homepage.php");
    else if ( isset($_POST["id_prodotto"]) ) 
    {
        // Verifico se vi sia da evadere una richiesta o meno
        if ( isset($_POST["btnAggiungi"]) && isset($_POST["recensione"]) )
        {
            // Verifico la consistenza dei dati
            $recensione = trim($_POST["recensione"]);
            if ( strlen($recensione) > 0 )
            {
                // Alloco il gestore recensioni e procedo ad inserire la nuova recensione
                $gestoreRecensioni = new GestoreRecensioni();
                $gestoreRecensioni->inserisciRecensione($recensione, $_SESSION["id_utente"], $_POST["id_prodotto"]);

                // Redireziono l'utente alla pagina del prodotto
                header("Location: dettaglioProdotto.php?id_prodotto=" . $_POST["id_prodotto"]);
            }
            else
            {
                // Errore, recensione non presente
                $mostraPopup = true;
                $err = true;
                $msg = 'Campi vuoti';
            }
        }
        else
        {
            // Prelevo le informazioni del prodotto per comporre il titolo
            // del form
            $gestoreCatalogo = new GestoreCatalogoProdotti();
            $prodotto = $gestoreCatalogo->ottieniProdotto($_POST["id_prodotto"]);
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
        <link rel="stylesheet" href="../css/stileInserisciRecensione.css" type="text/css" />
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
                    <p>Nuova recensione per: <strong><?php if($prodotto != null) echo $prodotto->nome; ?></strong></p>
                    <textarea rows="6" cols="45" name="recensione"><?php if($err) echo $recensione;?></textarea>
                    <input type="hidden" name="id_prodotto" value="<?php echo $_POST["id_prodotto"]; ?>" />
                </fieldset>
                    
                <div class="parteButton">
                    <input type="submit" value="Aggiungi recensione" name="btnAggiungi" />
                </div>
            </form>
        </div>
    </body>
</html>