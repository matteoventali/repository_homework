<?php
    session_start();

    require_once 'gestoriXMLDOM.php';

    // Dichiarazione variabili globali
    $vet_vittorie;
    $vet_pareggi;
    $vet_fatti;
    $vet_subiti;
    $vet_punti;
    $vet_diff;
    $squadre;

    // L'idea è quella di generare due array associativi che memorizzano vittorie e pareggi delle 8 squadre
    function caricaClassifica($listaPartite) 
    {
        // Inizialmente la tabella è vuota
        $contenutoTabella = "";

        // Utilizziamo i 4 array associativi globali
        global $vet_vittorie, $vet_pareggi, $vet_fatti, $vet_subiti, $vet_punti, $squadre, $vet_diff;

        // Inizializzazione degli array
        $vet_vittorie = $vet_pareggi = $vet_fatti = $vet_subiti = $vet_punti = $vet_diff = array("Real Madrid" => 0, "Milan" => 0, "Inter" => 0, "Juventus" => 0, "Bayern Monaco" => 0 ,"Liverpool" => 0, "Barcellona" => 0, "Manchester United" => 0);
        
        // Chiamiamo la funzione che popola i 4 array associativi
        calcolaRisultati($listaPartite);

        // Ora nei 4 array abbiamo vittorie, pareggi, goal fatti e goal subiti delle squadre

        // Salviamo in un altro vettore il totale dei punti delle varie squadre
        for( $i=0; $i < count($squadre); $i++) 
        {
            $punti = 0;

            $vittorie = current($vet_vittorie);
            $pareggi = current($vet_pareggi);

            $punti = 3 * $vittorie + $pareggi;

            // Memorizziamo i punti nel relativo array
            $vet_punti[$squadre[$i]->textContent] = $punti;

            // Andiamo avanti nel vettore
            next($vet_vittorie);
            next($vet_pareggi);
        }

        // Ordiniamo il vettore dei punti
        arsort($vet_punti);

        // Dobbiamo quindi popolare la tabella
        $indice = 1;
        while ( $indice - 1 < count($squadre))
        {
            $stile = "";
            if ( $indice == 1 )
                $stile = "style=\"background-color: rgb(248, 32, 32);\"";
            else if ( $indice == 2 || $indice == 3 )
                $stile = "style=\"background-color: rgb(16, 156, 25);\"";
            
            // Estraiamo la squadra e i relativi punti
            $squadra = key($vet_punti);
            $punti = current($vet_punti);
            
            // Facciamo il match di tale squadra con gli altri array
            for( $j=0; $j < count($squadre); $j++)
            {
                if( $squadra == $squadre[$j]->textContent ){
                    $fatti = $vet_fatti[$squadre[$j]->textContent];
                    $subiti = $vet_subiti[$squadre[$j]->textContent];
                    $diff = $vet_diff[$squadre[$j]->textContent];
                }
            }

            $contenutoTabella .= "<tr><td $stile>$indice</td><td>$squadra</td><td>$punti</td><td>$fatti</td><td>$subiti</td><td>$diff</td></tr>";

            // Andiamo avanti nel vettore
            next($vet_punti);

            $indice++;
        }

        return $contenutoTabella;
    }

    function calcolaRisultati($listaPartite)
    {
        // Utilizziamo i 4 array associativi globali
        global $vet_vittorie, $vet_pareggi, $vet_fatti, $vet_subiti, $vet_diff;

        // Carico in memoria l'handler DOM per il file XML squadre
        // Validazione con DTD (modalita' 0)
        $handlerSquadre = new GestoreXMLDOMSquadre("../xml/squadre.xml", 0);
        
        // Ottengo la lista delle squadre
        global $squadre;
        $squadre = $handlerSquadre->getListaSquadre();
        
        // Per ogni partita bisogna popolare il contenuto degli array
        for ( $i=0; $i < count($listaPartite); $i++ )
        {
            // Estraggo la partita
            $partita = $listaPartite[$i];
            $squadraCasa = $partita->firstChild;
            $squadraOspite = $squadraCasa->nextSibling;
            $goalCasa = $squadraOspite->nextSibling;
            $goalOspite = $goalCasa->nextSibling;
            $data = $partita->lastChild;

            // Memorizzo i goal di casa
            $casa=0;
            $casa=$casa+$goalCasa->textContent;

            // Memorizzo goal ospite
            $ospite=0;
            $ospite=$ospite+$goalOspite->textContent;

            for ( $j=0; $j < count($squadre); $j++) 
            {
                // Goal fatti e subiti dalla squadra di casa
                if( key($vet_fatti) == $squadraCasa->textContent) 
                {
                    $vet_fatti[$squadre[$j]->textContent] = $casa + $vet_fatti[$squadre[$j]->textContent];
                    $vet_subiti[$squadre[$j]->textContent] = $ospite + $vet_subiti[$squadre[$j]->textContent];
                }   // L'else successivo è per i goal fatti e subiti dalla squadra ospite
                if ( key($vet_fatti) == $squadraOspite->textContent)
                {
                    $vet_fatti[$squadre[$j]->textContent] = $ospite + $vet_fatti[$squadre[$j]->textContent];
                    $vet_subiti[$squadre[$j]->textContent] = $casa + $vet_subiti[$squadre[$j]->textContent];
                }

                // Inserisco le vittorie
                if ( $casa > $ospite && key($vet_vittorie) == $squadraCasa->textContent )
                    $vet_vittorie[$squadre[$j]->textContent]++;
                else if ( $casa < $ospite && key($vet_vittorie) == $squadraOspite->textContent )
                    $vet_vittorie[$squadre[$j]->textContent]++;
                else if ( $casa == $ospite && (key($vet_vittorie) == $squadraOspite->textContent || key($vet_vittorie) == $squadraCasa->textContent) )
                    $vet_pareggi[$squadre[$j]->textContent]++;
                // Andiamo all'elemento successivo
                next($vet_fatti);
                next($vet_subiti);
                next($vet_vittorie);
                next($vet_pareggi);
            }

            // Torno all'inizio degli array
            reset($vet_fatti);
            reset($vet_subiti);
            reset($vet_vittorie);
            reset($vet_pareggi);

        }

        // Calcolo la differenza reti nel relativo array
        for( $i=0; $i < count($squadre); $i++)
        {
            $vet_diff[$squadre[$i]->textContent] = $vet_fatti[$squadre[$i]->textContent] - $vet_subiti[$squadre[$i]->textContent];
        }

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

        // Ottengo la lista delle partite su cui calcolare la classifica
        $partite = caricaClassifica($handlerPartite->getListaPartite());

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
