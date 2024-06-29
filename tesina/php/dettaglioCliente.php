<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/parametriStile.php';

    // A questa pagina possono accedervi solo gli admin e i gestori
    // I gestori, pero', possono solo consultare la pagina
    // mentre gli admin possono apportare modifiche o
    // bannare/riattivare gli account dei clienti

    // Variabile che permette di mostrare i bottoni (admin) o meno (gestori)
    $visibilita_bottone = "none";
    
    if ( $_SESSION["ruolo"] != "A" && $_SESSION["ruolo"] != "G" )
        header("Location: homepage.php");

    if ( $_SESSION["ruolo"] == "A" )
        $visibilita_bottone = "display:block";
    else
        $visibilita_bottone = "visibility:hidden";
    
    // Nel get trovo l'id del cliente con cui eseguire la ricerca nel DB
    // Se l'utente non viene trovato, ridireziono alla homepage

    // Connessione al database
    require 'lib/connection.php';

    if ( $connessione )
    {
        // Prelevo i dati dal get e compongo la query
        $id_cliente = $_GET["id_cliente"];

        $cliente = ottieniUtente($id_cliente, $handleDB);

        // Se l'account non appartiene ad un cliente, ridireziono sulla homepage
        if( $cliente->id_utente == "" || $cliente->ruolo != "C")
            header("Location: homepage.php");
            
        $handleDB->close();
    }

    // Verifico se c'è da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileDettaglioCliente.css" type="text/css" />
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

            // Lo stato sara' verde se l'utente e' attivo, altrimenti rosso
            if ($cliente->stato == "A")
            {
                $stato = "ATTIVO";
                $coloreStato = "#7CFC00";
            }
            else  
            {
                $stato = "BANNATO";
                $coloreStato = "red";
            } 
        ?>

        <div id="sezioneDettagli">
                <div id="sezioneRiquadri">
                    <div class="riquadro">
                        <div class="contenutoRiquadrado">
                            <h3 style="text-decoration: underline;">Dati del cliente</h3>
                            <p>Nome: <strong><span> <?php echo $cliente->nome;?></span> </strong>
                               Cognome: <strong><span><?php echo $cliente->cognome;?></span></strong></p>
                            <p>Indirizzo: <span><strong><?php echo $cliente->indirizzo;?></strong></span></p>
                            <p>Citt&agrave;: <span><strong><?php echo $cliente->citta;?></strong></span></p>
                            <p>Cap: <strong><span><?php echo $cliente->cap;?> </span></strong></p>
                        </div>
                    </div>

                    <div class="riquadro">
                        <div class="contenutoRiquadrado">
                            <h3 style="text-decoration: underline;">Reputazione &amp; portafoglio</h3>
                            <p>Data registrazione: 
                                <strong><span>
                                    <?php echo date("d-m-Y", strtotime($cliente->data_registrazione));?>
                                </span> </strong>
                            </p>
                            <p>Reputazione: <strong> <span><?php echo $cliente->reputazione;?></span> </strong></p>
                            <p>Saldo portafoglio standard: 
                                <strong> <span> <?php echo $cliente->saldo_standard;?> </span> </strong>
                            </p>
                            <p>Saldo portafoglio bonus: 
                                <strong> <span> <?php echo $cliente->saldo_standard;?> </span> </strong>
                            </p>
                        </div>
                    </div>

                    <div class="riquadro">
                        <div class="contenutoRiquadrado">
                            <h3 style="text-decoration: underline;">Informazioni account</h3>
                            <p>Username: <strong> <span> <?php echo $cliente->username;?> </span> </strong></p>
                            <p>Mail: <strong> <span><?php echo $cliente->mail;?></span></strong></p>
                            <p>Stato: <strong> 
                                <span style="color: <?php echo $coloreStato; ?>;"><?php echo $stato;?> </span> </strong>
                            </p>
                        </div>
                    </div>

                    <div class="riquadro" style="<?php echo $visibilita_bottone; ?>">
                        <form action="" method="post">
                            <fieldset>
                            <?php 
                                if($stato == "ATTIVO")
                                    echo "<input type=\"submit\" value=\"Ban account\" name=\"btnBan\" />" ."\n";
                                else
                                    echo "<input type=\"submit\" value=\"Riattiva account\" name=\"btnRiattiva\" />" ."\n";
                            ?>
                            </fieldset>
                            <fieldset><input type="submit" value="Modifica dati" name="btnModifica" /></fieldset>
                        </form>
                    </div>
                </div>
            </div>
    </body>
</html>