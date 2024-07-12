<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    
    // Parametri per mostrare le tessere dei clienti in maniera appropriata
    // Di default si mostrano tutti i clienti (attivi/bannati)
    $flag_attivi = false; $flag_bannati = false; $nome_cognome_username = '';

    // Variabile per mostrare il contenuto dei filtri di ricerca al caricamento
    $mostraContenuto = false;

    // Bisogna controllare che il cliente sia loggato come admin o come gestore
    // altrimenti viene ridirezionato alla homepage
    if (!($sessione_attiva && ($_SESSION["ruolo"] == "A" || $_SESSION["ruolo"] == "G")))
        header("Location: homepage.php");
    else if ( isset($_POST["btnRicerca"]) ) // Verifico se e' stata effettuata una ricerca
    {
        // Verifico se i filtri attivi bannati sono attivi
        if (isset($_POST["attivi"]) && $_POST["attivi"] == "si" )
            $flag_attivi = true;
        if (isset($_POST["bannati"]) && $_POST["bannati"] == "si" )
            $flag_bannati = true;

        // Verifico se vi e' una chiave di ricerca inserita
        $chiave = trim($_POST["contenutoRicerca"]);
        if ( isset($chiave) && strlen($chiave) > 0 )
        {
            $mostraContenuto = true;
            $nome_cognome_username = $chiave;
        }
    }
    
    // Connessione al database
    require 'lib/connection.php';

    // Ottengo la lista dei clienti passando i parametri di ricerca
    if ( $connessione )
    {
        $lista_clienti = ottieniClienti($handleDB, $flag_attivi, $flag_bannati, $nome_cognome_username);
        $handleDB->close();
    }
    else
        $lista_clienti = [];
    
    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileGestioneClienti.css" type="text/css" />
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

        <div id="sezioneClienti">
            <div id="parteControlli">
                <form id="ricercaClienti" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset><p>Attivi</p><input type="checkbox" 
                                <?php if($flag_attivi) echo 'checked="checked"'; ?> name="attivi" value="si" /> </fieldset>
                    <fieldset><p>Bannati</p><input type="checkbox" 
                                <?php if($flag_bannati) echo 'checked="checked"'; ?> name="bannati" value="si" /> </fieldset> 
                    <fieldset><p>Utente</p><input type="text" name="contenutoRicerca"
                                    value="<?php if ($mostraContenuto) echo $_POST["contenutoRicerca"]; ?>" /></fieldset>
                    <fieldset><input type="submit" name="btnRicerca" value="Cerca &#128269;" /></fieldset>
                    <fieldset><input type="reset" name="btnIndietro" onclick="azzeraRicercaClienti();" value="Reset &#8634;" /></fieldset>
                </form>
            </div>

            <div id="parteTessere">
                <?php
                    $tessera_vuota = file_get_contents("../html/frammentoTesseraUtente.html");
                    $contenuto_html = "";
                    
                    // Scansione della lista di utenti ottenuta
                    $n_clienti = count($lista_clienti);
                    for ( $i=0; $i < $n_clienti; $i++ )
                    {
                        // Costruisco la nuova tessera
                        $cliente = $lista_clienti[$i];
                        $nuova_tessera = str_replace("%NOME_COGNOME%", $cliente->nome . " " . $cliente->cognome, $tessera_vuota);
                        $nuova_tessera = str_replace("%USERNAME%", $cliente->username, $nuova_tessera);
                        $nuova_tessera = str_replace("%ID_CLIENTE%", $cliente->id_utente, $nuova_tessera);

                        if ( $cliente->stato == "A" )
                        {
                            $stato = "ATTIVO";
                            $colore_stato = "#7CFC00";
                        }
                        else if ( $cliente->stato == 'B' ) 
                        {
                            $stato = "BANNATO";
                            $colore_stato = "red";
                        } 
                        $nuova_tessera = str_replace("%COLORE_STATO%", $colore_stato, $nuova_tessera);
                        $nuova_tessera = str_replace("%STATO%", $stato, $nuova_tessera);

                        $contenuto_html .= $nuova_tessera . "\n";
                    }

                    if ( $n_clienti == 0 )
                        $contenuto_html = '<p style="width:100%; text-align:center;">Nessun cliente soddisfa i criteri di ricerca</p>';

                    echo $contenuto_html . "\n";
                ?>
            </div>
        </div>
    </body>
</html>