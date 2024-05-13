<?php
    session_start();

    $nome = ""; $cognome = ""; $stileOpt = "";
    
    // Se non è presente una sessione attiva distruggo quella appena creata
    // e rimando l'utente alla pagina di login
    if ( !isset($_SESSION["nome"]) )
    {
        require_once 'cancellaSessione.php';
        header("Location: accedi.php");
    }
    else
    {
        $nome = $_SESSION["nome"];
        $cognome = $_SESSION["cognome"];
        
        // Verifico modalita' di invocazione dello script
        if ( isset($_POST["scelta"]) )
        {
            // Ridireziono l'utente sulla pagina corretta
            $s = $_POST["scelta"];
            if ( $s === "Logout" )
                header("Location: logout.php");
        }

        // Verifico la tipologia di utente per far apparire
        // l'opzione di inserimento nuova partita
        $t = $_SESSION["tipologia"];
        if ( $t === "U" )
            $stileOpt = 'style="display:none"';
    }    
    
    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileMenu.css" />
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
                <p><strong> <?php echo $nome . " " . $cognome; ?> </strong></p>
            
                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <p><input type="submit" name="scelta" value="Logout" /></p>
                </form>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <div id="riq1" class="sezioneCorpo uno" style="">
                <a class="icona" href="classifica.php">
                    <img  alt="icona classifica" src="../img/classifica.png" />
                </a>
                <p class="didascalia"> CLASSIFICA </p>
            </div>

            <div id="riq2" class="sezioneCorpo due">
                <a class="icona" href="resocontoPartite.php">
                    <img  alt="icona resoconto" src="../img/partite.png" />
                </a>
                <p class="didascalia"> RESOCONTO PARTITE </p>
            </div>

            <div <?php echo $stileOpt; ?> id="riq3" class="sezioneCorpo tre">
                <a class="icona" href="inserisciPartita.php">
                    <img  alt="icona nuova partita" src="../img/fischietto.png" />
                </a>
                <p class="didascalia"> NUOVA PARTITA </p>
            </div>

            <div id="riq4" class="sezioneCorpo quattro">
                <a class="icona" href="resocontoSquadre.php">
                    <img  alt="icona squadre" src="../img/squadre.png" />
                </a>
                <p class="didascalia"> SQUADRE </p>
            </div>
        </div>

        <!-- CONTENUTO FOOTER PAGINA -->
        <div class="footer">
            <p class="copyright">&copy; 2024 Matteo Ventali &amp; Stefano Rosso</p>
        </div>
    </body>
</html>
