<?php
    // Verifico modalita' di invocazione dello script
    if ( isset($_POST["scelta"]) )
    {
        // Ridireziono l'utente sulla pagina corretta
        $s = $_POST["scelta"];
        if ( $s === "Accedi" )
            header("Location: accedi.php");
        else if ( $s === "Registrati" )
            header("Location: registrati.php");
    }
    
    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileIndex.css" />
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
                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <input type="submit" name="scelta" value="Accedi" />
                    <input type="submit" name="scelta" value="Registrati" />
                </form>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <div id="riq1" class="sezioneCorpo uno" style="margin-left: 10%;">
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

            <div id="riq3" class="sezioneCorpo tre">
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
