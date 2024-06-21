<?php
    // Verifico se c'è da gestire una richiesta di registrazione o meno
    
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileAccedi.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png">
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
            <form id="formLogin" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post"">
                <span><p>Username:</p><input type="text" name="nome"/></span>
                <span><p>Password:</p><input type="text" name="nome"/></span>
                <span>
                    <input type="reset" value="Cancella" />
                    <input type="submit" value="Login" name="btnAccedi" />
                </span>
            </form>
        </div>
    </body>
</html>