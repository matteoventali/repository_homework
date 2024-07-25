<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/parametriStile.php';
    require_once 'gestoriXML/gestorePortafogliBonus.php';
    require_once 'gestoriXML/gestoreAcquisti.php';

    // A questa pagina possono accedervi solo i clienti
    // Nel caso in cui l'utente non fosse cliente o fosse bannato,
    // viene ridirezionato

    if ( !$sessione_attiva || $_SESSION["ruolo"] != "C" )
        header("Location: homepage.php");

    // Memorizzo l'id del cliente tramite la variabile SESSION
    $id_cliente = $_SESSION["id_utente"];

    // Ricerco il cliente nel DB
    // Qualora non esistesse, ridireziono l'utente alla homepage

    require 'lib/connection.php';

    // Connessione al DB
    if ( $connessione )
    {
        $cliente = ottieniUtente($id_cliente, $handleDB);
        $handleDB->close();
    }

    if( $cliente->id_utente == "" )
        header("Location: homepage.php");

    // Prelevo dal file xml il saldo del portafoglio bonus
    $gestore_portafogli_bonus = new GestorePortafogliBonus();
    $saldo_bonus = $gestore_portafogli_bonus->ottieniSaldoPortafoglioBonus($cliente->id_utente);

    // Verifico se c'è da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileAreaPersonale.css" type="text/css" />
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

        <div id="sezioneDati">
            <div class="riquadroInformazioni">
                <div class="colonna">
                    <p>Nome: <strong><span> <?php echo $cliente->nome;?></span> </strong></p>
                    <p>Citt&agrave;: <span><strong><?php echo $cliente->citta;?></strong></span></p>
                    <p>Reputazione: <strong> <span><?php echo $cliente->reputazione;?></span> </strong></p>
                    <p>Username: <strong> <span> <?php echo $cliente->username;?> </span> </strong></p>
                </div>

                <div class="colonna">
                    <p>Cognome: <strong><span><?php echo $cliente->cognome;?></span></strong></p>
                    <p>Cap: <strong><span><?php echo $cliente->cap;?> </span></strong></p>
                    <p> Saldo portafoglio standard: 
                    <strong> <span> <?php echo $cliente->saldo_standard;?> </span> </strong> </p>
                    <p>Mail: <strong> <span><?php echo $cliente->mail;?></span></strong></p>
                </div>
                <div class="colonna">
                    <p>Indirizzo: <span><strong><?php echo $cliente->indirizzo;?></strong></span></p>
                    <p>Data registrazione: 
                        <strong><span>
                            <?php echo date("d-m-Y", strtotime($cliente->data_registrazione));?>
                        </span> </strong>
                    </p>
                    <p>Saldo portafoglio bonus: 
                        <strong> <span> <?php echo $saldo_bonus;?> </span> </strong>
                    </p>
                </div>
                <div class="parteButton">
                    <form action="modificaCliente.php" method="post">
                        <fieldset><input type="submit" value="Modifica dati" name="btnModifica" /></fieldset>
                    </form>
                </div>
            </div>

            <div class="riquadroAcquisti" >
                <h4 style="text-decoration: underlined;">Acquisti effettuati</h4>
                <?php
                    // Contenuto di un acquisto vuoto
                    $acquisto_vuoto = file_get_contents("../html/frammentoAcquisto.html");
                        
                    // Gestori file XML
                    $gestore_acquisti = new GestoreAcquisti();
                        
                    // Carico gli acquisti dai file XML
                    $dettaglio_acquisti = "";

                    $lista_acquisti = $gestore_acquisti->ottieniAcquistiCliente($id_cliente);
                    $dim_lista = count($lista_acquisti);

                    if($dim_lista == 0)
                        echo "<p style=\"font-size: 100%;\"> Nessun acquisto effettuato </p>";

                    for ( $i=0; $i<$dim_lista; $i++ )
                    {
                        $id_acquisto = $lista_acquisti[$dim_lista - 1 - $i]->id;

                        $acquisto_pieno = str_replace("%DATA%", date("d-m-Y", strtotime($lista_acquisti[$dim_lista - 1 - $i]->data)), $acquisto_vuoto);
                        $acquisto_pieno = str_replace("%CREDITI_BONUS_RICEVUTI%", $lista_acquisti[$dim_lista - 1 - $i]->crediti_bonus_ricevuti, $acquisto_pieno);
                        $acquisto_pieno = str_replace("%CREDITI_BONUS_UTILIZZATI%", $lista_acquisti[$dim_lista - 1 - $i]->crediti_bonus_utilizzati, $acquisto_pieno);
                        $acquisto_pieno = str_replace("%CREDITI_SPESI%", $lista_acquisti[$dim_lista - 1 - $i]->totale_effettivo, $acquisto_pieno);
                        $acquisto_pieno = str_replace("%ID_ACQUISTO%", $id_acquisto, $acquisto_pieno);

                        // Popoliamo ora la parte relativa ai prodotti
                        $dettaglio_acquisti .= $acquisto_pieno . "\n";
                    }
                        
                        // Mostro la lista degli acquisti
                        echo $dettaglio_acquisti . "\n";
                    ?>

            </div>
            
        </div>
    </body>
</html>