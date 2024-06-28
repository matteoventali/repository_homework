<?php
    require_once 'lib/libreria.php';
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';

    // Verifico se c'e' da gestire una richiesta di registrazione o meno
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileFaq.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Parametro di visibilita' bottone aggiunta nuova faq
            $visibilita_bottone = "none";
            
            // Bisogna controllare se l'utente è loggato oppure no
            // In base a questo avrà diversi tipi di visualizzazione
            if($sessione_attiva)
            {
                // In questo caso l'utente è loggato

                // Import della navbar
                // Visualizzo nome dell'utente e il tasto "Logout"
                $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
                $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
                echo $nav ."\n";

                // Import della sidebar
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
                echo $sidebar . "\n";

                // L'opzione di aggiungere una nuova faq deve essere fornita
                // esclusivamente ad admin e gestori
                if ( $_SESSION["ruolo"] == "A" || $_SESSION["ruolo"] == "G" )
                    $visibilita_bottone = "block";
            }
            else 
            {
                // Qui l'utente non è loggato

                // Import della navbar
                $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
                echo $nav ."\n";

                // Import della sidebar e mostro solo le opzioni del visitatore
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu('V'), $sidebar);
                echo $sidebar . "\n";
            }
        ?>

        <div id="sezioneFaq">
            <div id="parteCentrale">
                <form class="parteButton" action="inserisciFaq.php" method="post">
                    <div style="display: <?php echo $visibilita_bottone; ?>">
                        <input type="submit" value="Inserisci nuova FAQ" name="btnInserisci" />
                    </div>
                </form>
                
                <?php
                    // Contenuto di una faq vuota
                    $faq_vuota = file_get_contents("../html/frammentoFaq.html");
                    
                    // Gestori file XML
                    $gestore_domande = new GestoreDomande();
                    $gestore_risposte = new GestoreRisposte();
                    
                    // Carico le FAQ dai file XML
                    $contenuto_faq = "";
                    
                    $lista_faq = $gestore_domande->ottieniDomande("true"); // Ottengo solo le domande FAQ
                    $dim_lista = count($lista_faq);

                    for ( $i=0; $i<$dim_lista; $i++ )
                    {
                        $id_domanda = $lista_faq[$i]->id;
                        
                        $faq_piena = str_replace("%DOMANDA%", $lista_faq[$i]->contenuto, $faq_vuota);
                        $faq_piena = str_replace("%ID_DOMANDA%", $id_domanda, $faq_piena);

                        // Ottengo la risposta associata alla domanda FAQ
                        $risposta = $gestore_risposte->ottieniRisposte($id_domanda, "true");
                        $faq_piena = str_replace("%RISPOSTA%", $risposta[0]->contenuto, $faq_piena);

                        $contenuto_faq .= $faq_piena . "\n";
                    }
                    
                    // Mostro la lista delle faq
                    echo $contenuto_faq . "\n";
                ?>
            </div>
        </div>
    </body>
</html>