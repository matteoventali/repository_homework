<?php
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';
    require_once 'gestoriXML/gestoreRecensioni.php';

    // Script per eliminare un intervento. Viene chiamato in maniera asincrona
    // mediante tecnologia AJAX
    // Restituisce true o false a seconda dell'esito dell'operazione
    $esito_operazione = false;

    // Check dei parametri ricevuti per eseguire l'operazione
    // I parametri vengono ricevuti mediante richiesta HTTP POST
    if ( isset($_POST["id_intervento"]) && isset($_POST["tipo_intervento"]) )
    {
        // Verifico tramite il tipo di intervento dove inserire la valutazione
        $tipo = $_POST["tipo_intervento"];
        $gestore = null;
        switch ($tipo)
        {
            case 'domanda':
                $gestore = new GestoreDomande();
                // Elimino la domanda che causa anche l'eliminazione delle risposte associate
                $esito_operazione = $gestore->rimuoviDomanda($_POST["id_intervento"]);
                break;
            
            case 'risposta':
                $gestore = new GestoreRisposte();
                // Elimino la risposta
                $esito_operazione = $gestore->rimuoviRisposta($_POST["id_intervento"]);
                break;

            case 'recensione':
                $gestore = new GestoreRecensioni();
                // Elimino la recensione
                $esito_operazione = $gestore->rimuoviRecensione($_POST["id_intervento"]);
                break;
            
            default:
                $path_xml = ''; $gestore = null; break;
        }
    }
    else
        header("Location: homepage.php"); // Chiamata allo script in modo improprio
    
    echo $esito_operazione;
?>