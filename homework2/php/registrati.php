<?php
    // Variabile paragrafo risultato operazione
    $ris = ''; $err = true;
    
    // Verifico se l'utente sia gia' loggato
    // in caso viene ridirezionato sul menu dell'applicazione
    session_start();
    if ( isset($_SESSION["nome"]) )
        header("Location: menu.php"); 
    else if ( isset($_POST["nome"]) && isset($_POST["cognome"])
                && isset($_POST["mail"]) && isset($_POST["password"]) )
    {
        if (strlen(trim($_POST["nome"])) > 0 && strlen(trim($_POST["cognome"]) > 0) 
                && strlen(trim($_POST["mail"])) > 0 && strlen(trim($_POST["password"])) > 0)
        {
            // Elimino la sessione appena creata erroneamente
            require_once 'cancellaSessione.php';

            // Se sono qui e' pervenuta una richiesta di registrazione
            // Connessione al database
            require_once 'connection.php';
            
            // Se la connessione e' andata a buon fine
            if ( $connessione )
            {
                $ris = '<p style="color:red">Errore nell\'esecuzione della query, ricontrollare i dati</p>'; 

                // Verifico che i dati siano corretti
                // Regex: /^([a-z]+)(_|[.]|-){0,1}(([a-z]|\d)+)@([a-z])*[.]([a-z])*$/
                $regex = '/^([a-z]+)(_|[.]|-){0,1}(([a-z]|\d)+)@([a-z])*[.]([a-z])*$/';
                if ( preg_match($regex, $_POST["mail"]) )
                {
                    // Prelevo i dati dal post
                    $nome = $handleDB->real_escape_string($_POST["nome"]);
                    $cognome = $handleDB->real_escape_string($_POST["cognome"]);
                    $mail = $handleDB->real_escape_string($_POST["mail"]);
                    $password = $handleDB->real_escape_string($_POST["password"]);
                    
                    // Compongo la query per l'esecuzione
                    $q = "insert into $tb_utenti(nome, cognome, mail, password) values ('$nome', '$cognome', '$mail', SHA2('$password', 256))";

                    // Esecuzione della query
                    try
                    {
                        $handleDB->query($q);
                        $ris = '<p style="color:green">Registrazione avvenuta con successo</p>';
                        $err = false;
                    }
                    catch (Exception $e)
                    {
                        $err = true;
                        if ($handleDB->errno == 1062 )
                            $ris = '<p style="color:red">e-mail presente nel database</p>';
                    }
                }

                $handleDB->close();
            }
            else
                $ris = '<p style="color:red">Errore di comunicazione con il database</p>';
        }
        else
            $ris = '<p style="color:red">Campi vuoti</p>';
    }
    else
    {
        // Elimino la sessione appena creata erroneamente
        require_once 'cancellaSessione.php';
        $err = false;
    }
    
    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileRegistrati.css" />
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
            <div class="sezioneControlli">
                <a class="home" href="menu.php">
                    <img  alt="home logo" src="../img/home.png" />
                </a>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                <div class="contenutoForm">
                    <div class="riga">
                        <p>Nome <input name="nome" type="text" <?php if($err) echo 'value="'. $_POST["nome"] . '"';?>" /></p> 
                        <p>Cognome <input name="cognome" type="text" <?php if($err) echo 'value="'. $_POST["cognome"] . '"';?>"/></p>
                    </div>

                    <div class="riga">
                        <p>e-mail <input name="mail" type="text" <?php if($err) echo 'value="'. $_POST["mail"] . '"';?>"/></p> 
                        <p>Password <input name="password" type="password" /></p>
                    </div>

                    <div class="rigaBottoni">
                        <div class="sezioneErrore">
                            <?php echo $ris ?>
                        </div>
                        <div class="sezioneBottoni">
                            <input type="reset" value="Cancella" />
                            <input type="submit" value="Registrati" />
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