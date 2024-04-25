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
            // Query per ottenere il resoconto delle partite
            $q = "select casa.nome, ospite.nome, DATE_FORMAT(p.data, '%d/%m/%Y'), p.goal_casa, p.goal_ospite 
                    from $tb_squadre casa, $tb_squadre ospite, $tb_partite p where p.squadra_casa = casa.id and p.squadra_ospite = ospite.id";

            // Eseguo la query
            $rs = $handleDB->query($q);

            // Popolo la tabella
            while ( $riga=$rs->fetch_row() )
                $contenutoTab .= "<tr>\n<td>$riga[2]</td>\n<td>$riga[0]</td>\n<td>$riga[1]</td>\n<td>$riga[3] - $riga[4]</td>\n</tr>";
            
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
        <link type="text/css" rel="stylesheet" href="../css/stilePartite.css" />
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
            <div class="tabella">
            <table>
                <tr>
                    <th>Data</th>
                    <th>Casa</th>
                    <th>Ospite</th>
                    <th>Risultato</th>
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