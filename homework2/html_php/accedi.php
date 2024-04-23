<?php
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
            <div class="sezioneControlli">
                <a class="home" href="index.php">
                    <img  alt="home logo" src="../img/home.png" />
                </a>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <form method="get" action="">
                <div class="contenutoForm">
                    <div class="riga">
                        <p>e-mail <input name="mail" type="text" /></p> 
                        <p>Password <input name="password" type="password" /></p>
                    </div>

                    <div class="rigaBottoni">
                        <input type="reset" value="Cancella" />
                        <input type="submit" value="Registrati" />
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