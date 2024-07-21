<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCarrelli.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // A questa pagina possono accedervi solo i clienti
    // Nel caso in cui l'utente non fosse cliente o fosse bannato,
    // viene ridirezionato
    // L'utente viene riderizionato se non e' presente un acquisto da finalizzare
    if ( !$sessione_attiva || $_SESSION["ruolo"] != "C" || !(isset($_POST["id_prodotto"])) )
        header("Location: homepage.php");

    // Gestori
    $gestoreCatalogo = new GestoreCatalogoProdotti();

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
                            // Popolazione lista di riepilogo
                            // e calcolo del totale provissorio
                            $prodotti = $_POST["id_prodotto"];
                            $n_prodotti = 0;
                            if ( $prodotti != null )
                                $n_prodotti = count($prodotti);

                            $totale_provvisorio = 0;

                            for ( $i=0; $i < $n_prodotti; $i++ )
                            {
                                $nome = $gestoreCatalogo->ottieniProdotto($prodotti[$i])->nome;
                                echo "<li>$nome</li>\n";
                                $totale_provvisorio += $_POST["prezzo_di_acquisto"][$i];
                            }
                        ?>
                    </ul>
                </div>

                
                <form id="sezioneAcquisto" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>"> 
                        <h2> Sub-totale: <?php echo $totale_provvisorio; ?> </h2>
                        <fieldset>
                            <p>Crediti bonus aggiuntivi (max 9999): </p>
                            <input type="text" name="creditiBonus" /> 
                        </fieldset>
                        <h2> Totale: </h2>
                        <fieldset>
                            <input type="submit" value="Acquista" name="btnAcquista" />
                        </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>