<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreRichiesteRicariche.php';

    // Gestore richieste di ricarica
    $gestore_richieste = new GestoreRichiesteRicariche();

    // Bisogna controllare che il cliente sia loggato come admin
    // altrimenti viene ridirezionato alla homepage
    if ( !$sessione_attiva || $_SESSION["ruolo"] != "A" )
        header("Location: homepage.php");
    else if ( isset($_POST["btnAccetta"]) || isset($_POST["btnRifiuta"]) ) // Verifico che vi sia una gestione per una richiesta 
    {
        // Set del flag a seconda del bottone cliccato
        $flag = false;
        if ( isset($_POST["btnAccetta"]) )
            $flag = true;

        $gestore_richieste->gestisciRichiestaRicarica($_POST["idRichiestaXML"], $_SESSION['id_utente'], $flag);
    }

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileGestioneRicariche.css" type="text/css" />
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

        <div id="sezioneRichieste">
            <div id="parteCentrale">
                <?php
                    $richieste_html = "<p style='font-size:150%;'>Nessuna richiesta da gestire presente</p>";
                    
                    // Frammento di richiesta vuota
                    $frammento_richiesta_vuoto = file_get_contents('../html/frammentoRichiestaRicarica.html');
                    $lista_richieste = $gestore_richieste->ottieniRichiestiRicaricheDaGestire();
                    $n_richieste = count($lista_richieste);

                    // Connessione al database per estrarre le informazioni dell'utente
                    require 'lib/connection.php';
                    if ( $connessione && $n_richieste > 0 )
                    {
                        $richieste_html = "";
                        
                        for ( $i=0; $i<$n_richieste; $i++ )
                        {
                            // Richiesta corrente
                            $richiesta = $lista_richieste[$i];

                            // Prelevo le informazioni dell'utente
                            $utente = ottieniUtente($richiesta->id_cliente, $handleDB);

                            // Nuovo frammento
                            $frammento = str_replace("%CREDITI%", $richiesta->crediti_richiesti . " crediti", $frammento_richiesta_vuoto);
                            $frammento = str_replace("%CLIENTE%", $utente->nome . " " . $utente->cognome, $frammento);
                            $frammento = str_replace("%DATA_RICHIESTA%", date("d-m-Y", strtotime($richiesta->data)), $frammento);
                            $frammento = str_replace("%ID_RICHIESTA_XML%", $richiesta->id_richiesta, $frammento);
                            $frammento = str_replace("%AZIONE_FORM%", $_SERVER['PHP_SELF'], $frammento);

                            $richieste_html .= $frammento . "\n";
                        }

                        $handleDB->close();
                    }
                    
                    echo $richieste_html . "\n";
                ?>
            </div>
        </div>
    </body>
</html>