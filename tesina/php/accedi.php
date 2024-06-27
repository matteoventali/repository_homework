<?php
    require_once 'lib/libreria.php';
    
    // Variabile per verificare se il popup vada mostrato
    $mostraPopup = true;
    
    // Variabili utili all'identificazione dell'errore
    $msg = ''; $err = true;

    // Verifico se vi e' una sessione aperta per account attivo
    require_once 'lib/verificaSessioneAttiva.php';
    
    if ( $sessione_attiva )
        header("Location: areaRiservata.php");
    else if ( isset($_POST["username"]) && isset($_POST["password"]) ) // Richiesta di login pervenuta
    {
        $msg = 'Errore di comunicazione con il database';
        
        // Elimino la sessione appena creata erroneamente
        require_once 'lib/cancellaSessione.php';

        // Connessione al database
        require_once 'lib/connection.php';

        if ( $connessione )
        {
            // Prelevo i dati dal post e compongo la query
            // Il controllo che i campi siano vuoti non e' necessario in quanto la query
            // sicuramente fallira'
            $username = $handleDB->real_escape_string($_POST["username"]);
            $password = $handleDB->real_escape_string($_POST["password"]);
            $q = "select * from $tb_utenti where username='$username' and password=SHA2('$password', 256)";

            // Esecuzione della query
            try
            {
                $rs = $handleDB->query($q);

                if ( $riga = $rs->fetch_row() ) // Corrispondenza trovata
                {
                    // Il login e' eseguito se l'account e' attivo
                    if ( $riga[8] != 'B' )
                    {
                        session_start();
                        $_SESSION["id_utente"]          = $riga[0];
                        $_SESSION["nome"]               = $riga[1];
                        $_SESSION["cognome"]            = $riga[2];
                        $_SESSION["indirizzo"]          = $riga[3];
                        $_SESSION["citta"]              = $riga[4];
                        $_SESSION["cap"]                = $riga[5];
                        $_SESSION["reputazione"]        = $riga[6];
                        $_SESSION["data_registrazione"] = $riga[7];
                        $_SESSION["username"]           = $riga[9];
                        $_SESSION["mail"]               = $riga[10];
                        $_SESSION["ruolo"]              = $riga[12];
                        $_SESSION["saldo_standard"]     = $riga[13];
                        header("Location: areaRiservata.php");
                        $err = false;
                        $mostraPopup = false;
                    }
                    else // Account bannato
                        $msg = 'Account disattivato. Contattare l\'admin';    
                }
                else
                    $msg = 'Credenziali errate, riprovare';
                
                $rs->close();
            }
            catch (Exception $e){}

            $handleDB->close();
        }
    }
    else if ( $sessione_esistente ) // Elimino la sessione esistente per l'account disattivato
    {
        require_once 'lib/cancellaSessione.php';
        $msg = 'Account disattivato. Contattare l\'admin';    
    }
    else
    {
        // Elimino la sessione appena creata erroneamente
        require_once 'lib/cancellaSessione.php';
        $err = false;
        $mostraPopup = false;
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
        <link rel="stylesheet" href="../css/stileAccedi.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Import della navbar
            // Nascondo il bottone accedi
            // Mostro il bottone registrati
            $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
            $nav = str_replace("%OPZIONE_DISPLAY_REGISTRATI%", "block", $nav);
            $nav = str_replace("%OPZIONE_DISPLAY_ACCEDI%", "none", $nav);
            echo $nav ."\n";

            // Import della sidebar e mostro solo le opzioni del visitatore
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", "", $sidebar);
            echo $sidebar . "\n";
        ?>

        <!-- FORM DI LOGIN !-->
        <div id="sezioneLogin">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <form id="formLogin" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                <fieldset><p>Username:</p><input type="text" name="username"/></fieldset>
                <fieldset><p>Password:</p><input type="password" name="password"/></fieldset>
                <fieldset style="border-style: none; box-shadow: none;">
                    <input type="reset" value="Cancella" />
                    <input type="submit" value="Login" name="btnAccedi" />
                </fieldset>                
            </form>
        </div>
    </body>
</html>