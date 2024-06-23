<?php
    require_once 'parametriStile.php';

    $margine_popup = $margine_popup_mostra;
    $background_popup = $colore_background_popup_verde;
    $display_popup = $opzione_display_popup_nascondi;
    
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
                // Import del popup per comunicare errore o meno
                // I settings della finestra sono ottenuti preliminarmente a seconda della richiesta pervenuta
                $popup = file_get_contents("../html/popupErrore.html");
                $popup = str_replace("%OPZIONE_DISPLAY_POPUP%", $display_popup, $popup);
                $popup = str_replace("%MARGINE_DESTRO_POPUP%", $margine_popup, $popup);
                $popup = str_replace("%COLORE_SFONDO_POPUP%", $background_popup, $popup);
                echo $popup . "\n";
            ?>

            <form id="formLogin" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                <fieldset><p>Username:</p><input type="text" name="nome"/></fieldset>
                <fieldset><p>Password:</p><input type="password" name="nome"/></fieldset>
                <fieldset>
                    <input type="reset" value="Cancella" />
                    <input type="submit" value="Login" name="btnAccedi" />
                </fieldset>                
            </form>
        </div>
    </body>
</html>