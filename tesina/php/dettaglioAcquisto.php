<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreriaDB.php';
    require_once 'gestoriXML/gestoreAcquisti.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';

    // Recupero l'id dell'acquisto per poterlo individuare nel file xml
    $id_acquisto = null;

    // Bisogna controllare che l'utente sia attivo e che sia un cliente
    if ( $sessione_attiva && $_SESSION["ruolo"] == 'C'  )
    {
        // Gestore per acquisti
        $gestore_acquisti = new GestoreAcquisti();
        $id_acquisto = $_POST["id_acquisto"];
        
        // Popolo la struttura dell'acquisto
        $acquisto = $gestore_acquisti->ottieniAcquisto($id_acquisto);

        // Controllo che l'acquisto appartenga al cliente
        // altrimenti ridireziono alla homepage
        if( $acquisto->id_cliente != $_SESSION["id_utente"])
            header("Location: homepage.php");

        // Estraggo la lista dei prodotti dall'acquisto
        $lista_prodotti = $acquisto->prodotti;
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
        <link rel="stylesheet" href="../css/stileDettaglioAcquisto.css" type="text/css" />
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
            <div class="acquisto">
                <p> 
                    <span> <span>Data acquisto: </span><?php echo date('d-m-Y', strtotime($acquisto->data));?></span> 
                    <span> <span>Crediti bonus ricevuti: </span><?php echo $acquisto->crediti_bonus_ricevuti;?></span> 
                    <span> <span>Totale acquisto: </span><?php echo calcolaTotaleAcquisto($id_acquisto)?></span> 
                </p>
            </div>

            <div id="sezioneProdotti">
                <?php
                    // Allocazione gestore catalogo prodotti
                    $gestore_catalogo = new GestoreCatalogoProdotti();
                    
                    // Salvo il numero di prodotti presenti nell'acquisto
                    $n_prodotti = count($lista_prodotti);
                    $lista_html = "<ul>\n";
                    for ($i=0; $i < $n_prodotti; $i++)
                    {
                        // Ottengo il prodotto
                        $prodotto = $gestore_catalogo->ottieniProdotto($lista_prodotti[$i]->id_prodotto);
                        
                        // Per ogni prodotto creo un elemento della lista
                        $prezzo_acq = $lista_prodotti[$i]->prezzo;
                        $nuovo = "<li><p><span><span>Nome prodotto: </span><span>$prodotto->nome</span></span>\n<span><span>Prezzo di listino: </span><span>$prodotto->prezzo_listino". 
                                            "</span></span>\n<span><span>Prezzo di acquisto: </span><span>$prezzo_acq</span></span></p></li>\n";
                        $lista_html .= $nuovo;
                    }
                    $lista_html .= '</ul>' . "\n";
                    echo $lista_html . "\n";
                ?>
                <!-- IMPLEMENTARE IL GESTORE DEI PRODOTTI E RISOLVERE IL PROBLEMA ID ACQUISTO POST -->
            </div>
        </div>
    </body>
</html>