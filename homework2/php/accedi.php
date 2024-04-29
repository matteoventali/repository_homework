<?php
    session_start();

    $ris = '';

    if ( isset($_SESSION["nome"]) ) // Verifico se vi e' una sessione aperta
        header("Location: menu.php");
    else if ( isset($_POST["mail"]) && isset($_POST["password"]) ) // Richiesta di login pervenuta
    {
        $ris = '<p style="color:red">Errore di comunicazione con il database</p>';
        
        // Elimino la sessione appena creata erroneamente
        require_once 'cancellaSessione.php';

        // Se sono qui e' pervenuta una richiesta di login
        // Connessione al database
        require_once 'connection.php';

        if ( $connessione )
        {
            // Prelevo i dati dal post e compongo la query
            // Il controllo che i campi siano vuoti non e' necessario in quanto la query
            // sicuramente fallira'
            $mail = $handleDB->real_escape_string($_POST["mail"]);
            $password = $handleDB->real_escape_string($_POST["password"]);
            $q = "select nome, cognome, tipologia from $tb_utenti where mail='$mail' and password=SHA2('$password', 256)";

            // Esecuzione della query
            try
            {
                $rs = $handleDB->query($q);
                $err = false;

                if ( $riga = $rs->fetch_row() ) // Corrispondenza trovata
                {
                    $err = false;
                    session_start();
                    $_SESSION["nome"] = $riga[0];
                    $_SESSION["cognome"] = $riga[1];
                    $_SESSION["tipologia"] = $riga[2];

                    // Vado sul menu
                    header("Location: menu.php");
                }
                else
                {
                    $err = true;
                    $ris = '<p style="color:red">Credenziali errate, riprovare</p>';
                }

                $rs->close();
                    
            }
            catch (Exception $e)
            {
                $err = true;
            }

            $handleDB->close();
        }
    }
    else
    {
        // Elimino la sessione appena creata erroneamente
        require_once 'cancellaSessione.php';
    }

    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileAccedi.css" />
        <link rel="icon" type="image/png" href="../img/favicon.png" />
    </head>

    <body>
        <!-- CONTENUTO HEADER PAGINA -->
        <div class="header">
            <div class="sezioneLogo">
                <img class="logo"  alt="champions logo" src="../img/champions.png" />
            </div>
            <div class="sezioneTitolo">
                <p>CHAMPIONS LEAGUE</p>
            </div>
            <div style="display:none;" class="sezioneControlli">
                <a class="home" href="menu.php">
                    <img  alt="home logo" src="../img/home.png" />
                </a>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
                <div class="contenutoForm">
                    <div class="riga">
                        <p>e-mail <input name="mail" type="text" /></p> 
                        <p>Password <input name="password" type="password" /></p>
                    </div>

                    <div class="riga" id="sezioneReg">
                        <p> Non sei registrato? <a href="registrati.php"> Clicca qui </a> </p>   
                    </div>

                    <div class="rigaBottoni">
                        <div class="sezioneErrore">
                            <?php echo $ris ?>
                        </div>
                        <div class="sezioneBottoni">
                            <input type="reset" value="Cancella" />
                            <input type="submit" value="Accedi" />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- CONTENUTO FOOTER PAGINA -->
        <div class="footer">
            <p class="copyright">&copy; 2024 Matteo Ventali &amp; Stefano Rosso</p>
        </div>
    </body>
</html>