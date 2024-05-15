<?php
    session_start();

    require_once 'gestoriXMLDOM.php';

    $contenutoTabella = "";

    // Metodo per ottenere il codice HTML necessario a mostrare l'elenco delle partite nella tabella relativa
    // Riceve la lista delle partite
    function caricaPartite($listaPartite)
    {
        // Contenuto di default (tabella vuota)
        $contenutoTabella = "";

        // Per ogni partita bisogna creare una riga della tabella
        for ( $i=0; $i < count($listaPartite); $i++ )
        {
            // Estraggo la partita
            $partita = $listaPartite[$i];
            $squadraCasa = $partita->firstChild;
            $squadraOspite = $squadraCasa->nextSibling;
            $goalCasa = $squadraOspite->nextSibling;
            $goalOspite = $goalCasa->nextSibling;
            $data = $partita->lastChild;

            // Aggiungo una riga a quelle già esistenti
            $contenutoTabella = $contenutoTabella . "<tr><td>$data->textContent</td><td>$squadraCasa->textContent</td>
            <td>$squadraOspite->textContent</td><td>$goalCasa->textContent - $goalOspite->textContent</td></tr>\n";
        }

        // Restituisco il contenuto della tabella
        return $contenutoTabella;
    }

    // Se non è presente una sessione attiva distruggo quella appena creata
    // e rimando l'utente alla pagina di login
    if ( !isset($_SESSION["nome"]) )
    {
        require_once 'cancellaSessione.php';
        header("Location: accedi.php");
    }
    else // Sessione valida presente
    {
        
        // Carico in memoria l'handler DOM per la gestione delle partite (modalità validazione 1)
        $handlerPartite = new GestoreXMLDOMPartite("../xml/partite.xml", 1);

        // Ottengo la lista delle partite da mostrare nella pagina
        $partite = caricaPartite($handlerPartite->getListaPartite());

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
                <?php echo $partite; ?>
            </table>
            </div>
        </div>

        <!-- CONTENUTO FOOTER PAGINA -->
        <div class="footer">
            <p class="copyright">&copy; 2024 Matteo Ventali &amp; Stefano Rosso</p>
        </div>
    </body>
</html>