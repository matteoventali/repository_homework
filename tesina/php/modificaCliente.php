<?php
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';
    require_once 'lib/verificaSessioneAttiva.php';

    // Variabili utili all'identificazione dell'errore
    $mostraPopup = false; $msg = ''; $err = false;
    
    // Flag per tenere traccia dei controlli sulla lunghezza dei campi
    $flag_nome      = true;
    $flag_cognome   = true;
    $flag_indirizzo = true;
    $flag_citta     = true;
    $flag_cap       = true;
    $flag_reputazione = true;

    // Verifica della sessione
    if ( $sessione_attiva && ($_SESSION["ruolo"] == 'A' || $_SESSION["ruolo"] == 'C') )
    {
        //In caso di sessione per admin prelevo l'id del cliente dal POST. 
        // In caso di cliente prelevo l'id dal SESSION

        // Prelevo l'id dell'utente
        $html_reputazione = '';
        if ( $_SESSION["ruolo"] == 'A' ) 
            $id_cliente = $_POST["id_cliente"];
        else
            $id_cliente = $_SESSION["id_utente"];

        // Prelevo le informazioni dell'utente oggetto dalla modifica
        // Effettuo la connessione al database
        require 'lib/connection.php';
        if ( $connessione )
        {
            $cliente = ottieniUtente($id_cliente, $handleDB);
            $handleDB->close();

            // Un admin puo' modificare anche la reputazione del cliente
            if ( $_SESSION["ruolo"] == 'A' )
                $html_reputazione = "<fieldset>  <p>Reputazione: </p>  <input type=\"text\" value=\"$cliente->reputazione\" name=\"reputazione\" />  </fieldset>";
        }
        else // Ridireziono l'utente in caso di errore
            header("Location: homepage.php");

        // Verifico che vi sia una richiesta di modifica
        if ( isset($_POST['btnSalvaModifica']) ) 
        {
            // A seguito della richiesta devo mostrare il popup
            $mostraPopup = true; $err = true;

            // Mi connetto al database
            require 'lib/connection.php';
            if ( $connessione )
            {
                // Regex per controllo sui dati cap e reputazione
                $regex_cap = '/^\d\d\d\d\d$/';
                $regex_reputazione = '/^[1-9][0-9]?$|^100$/';
                
                // Prelevo i dati dal post per la richiesta di modifica
                $nome = trim($handleDB->real_escape_string($_POST["nome"]));
                $cognome = trim($handleDB->real_escape_string($_POST["cognome"]));
                $indirizzo = trim($handleDB->real_escape_string($_POST["indirizzo"]));
                $citta = trim($handleDB->real_escape_string($_POST["citta"]));
                $cap = trim($handleDB->real_escape_string($_POST["cap"]));
                $reputazione = '';

                // Calcolo le lunghezze dei campi per tenere traccia dei campi vuoti
                $flag_nome = strlen($nome) > 0; 
                $flag_cognome = strlen($cognome) > 0;
                $flag_indirizzo = strlen($indirizzo) > 0;
                $flag_citta = strlen($citta) > 0;
                $flag_cap = strlen($cap) > 0;

                // Se sono admin prelevo dal post anche la reputazione
                $reputazione_valida = true;
                if ( $_SESSION["ruolo"] == "A" )
                {
                    $reputazione = trim($handleDB->real_escape_string($_POST["reputazione"]));
                    $flag_reputazione = strlen($reputazione) > 0;
                    $reputazione_valida = preg_match($regex_reputazione, $reputazione);
                    
                    // Se la reputazione e'valida inzializzo il contenuto a quel valore
                    if ( $flag_reputazione && $reputazione_valida )
                        $html_reputazione = "<fieldset>  <p>Reputazione: </p>  <input type=\"text\" value=\"$reputazione\" name=\"reputazione\" />  </fieldset>";
                }

                // Controllo sul cap
                $cap_valido = preg_match($regex_cap, $cap);
                
                // Verifico che ci siano tutti i dati
                if ( $flag_nome && $flag_cognome && $flag_indirizzo && $flag_citta  && $flag_cap && $flag_reputazione )
                {
                    if ( $cap_valido && $reputazione_valida )
                    {
                        // Effettuo la modifica
                        $err = !modificaCliente($handleDB, $id_cliente, $nome, $cognome, $citta, $cap, $indirizzo, $reputazione, $_SESSION["ruolo"]);

                        // Check errori
                        if (!$err)
                        {
                            $msg = 'Modifica avvenuta con successo';

                            // Rieseguo la query per mostrare i dati dell'utente piu aggiornati, a seguito
                            // della modifica
                            $cliente = ottieniUtente($id_cliente, $handleDB);
                        }
                        else 
                            $msg = 'Modifica fallita';
                    }
                    else 
                    {
                        // Verifico quali campi abbiano generato l'errore
                        if ( !$cap_valido && !$reputazione_valida )
                            $msg = 'Cap e reputazione non validi';
                        else if ( !$cap_valido )
                            $msg = 'Cap non valido';
                        else
                            $msg = 'Reputazione non valida';
                    }
                }
                else
                    $msg = 'Campi vuoti';

                $handleDB->close();
            }
        }
    }
    else // Ridireziono l'utente in caso di sessione non presente
        header("Location: homepage.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileModificaCliente.css" type="text/css" />
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

        <div id="sezioneModifica">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <form id="formModifica" method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                <fieldset>  <p>Nome:        </p>  <input type="text" value="<?php if($err && $flag_nome) echo $_POST["nome"]; else echo $cliente->nome; ?>" name="nome" />         </fieldset>
                <fieldset>  <p>Cognome:     </p>  <input type="text" value="<?php if($err && $flag_cognome) echo $_POST["cognome"]; else echo $cliente->cognome; ?>" name="cognome" />      </fieldset>
                <fieldset>  <p>Indirizzo:   </p>  <input type="text" value="<?php if($err && $flag_indirizzo) echo $_POST["indirizzo"]; else echo $cliente->indirizzo; ?>" name="indirizzo" />    </fieldset>
                <fieldset>  <p>Citt&agrave;:</p>  <input type="text" value="<?php if($err && $flag_citta) echo $_POST["citta"]; else echo $cliente->citta; ?>" name="citta" />        </fieldset>
                <fieldset>  <p>CAP:         </p>  <input type="text" value="<?php if($err && $flag_cap && $cap_valido) echo $_POST["cap"]; else echo $cliente->cap; ?>" name="cap" />          </fieldset>
                <?php echo $html_reputazione; ?>

                <input type="hidden" value="<?php echo $id_cliente; ?>" name="id_cliente" />
                
                <fieldset style="border-style: none; box-shadow: none;"> 
                    <input type="button" onclick="tornaIndietroDallaModificaCliente('<?php echo $_SESSION['ruolo']; ?>');" value="Indietro &#8617;" name="btnIndietro" />
                    <input type="reset" value="Cancella" name="btnCancella" />
                    <input type="submit" value="Modifica" name="btnSalvaModifica" />
                </fieldset>
            </form>
        </div>
    </body>
</html>