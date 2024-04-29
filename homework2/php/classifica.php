<?php
    session_start();

    $contenutoTab = "";

    // Se non è presente una sessione attiva distruggo quella appena creata
    // e rimando l'utente alla pagina di login
    if ( !isset($_SESSION["nome"]) )
    {
        require_once 'cancellaSessione.php';
        header("Location: accedi.php");
    }
    else // Sessione valida presente
    {
        // Connessione al database
        require_once 'connection.php';

        if ( $connessione )
        {
            // Query per ottenere i dati necessari alla classifica
            $q = "select * from $tb_classifica";

            // Eseguo la query
            $rs = $handleDB->query($q);

            // Popolo la tabella
            $indice = 1;
            while ( $riga = $rs->fetch_row() )
            {
                $stile = "";
                if ( $indice == 1 )
                    $stile = "style=\"background-color: rgb(248, 32, 32);\"";
                else if ( $indice == 2 || $indice == 3 )
                    $stile = "style=\"background-color: rgb(16, 156, 25);\"";
                
                $diff = $riga[1] - $riga[2];
                $contenutoTab .= "<tr><td $stile>$indice</td><td>$riga[0]</td><td>$riga[3]</td><td>$riga[1]</td><td>$riga[2]</td><td>$diff</td></tr>";
                $indice++;
            }
            
            $rs->close();
            $handleDB->close();
        }
    }

    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileClassifica.css" />
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

        <!-- CONTENUTO CORPO PAGINA 
            <td style="background-color: rgb(248, 32, 32);">1</td>
            <td style="background-color: rgb(16, 156, 25);">2</td>
            <td style="background-color: rgb(16, 156, 25);">3</td>
        -->
        <div class="corpo">
            <div class="tabella">
            <table>
                <tr>
                    <th>Posizione</th>
                    <th>Squadra</th>
                    <th>Punti</th>
                    <th>Goal fatti</th>
                    <th>Goal subiti</th>
                    <th>Diff. reti</th>
                </tr>
                <?php echo $contenutoTab; ?>
            </table>
            </div>
        </div>

        <!-- CONTENUTO FOOTER PAGINA -->
        <div class="footer">
            <p class="copyright">&copy; 2024 Matteo Ventali &amp; Stefano Rosso</p>
        </div>
    </body>
</html>
