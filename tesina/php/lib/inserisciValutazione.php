<?php
    require_once '../gestoriXML/gestoreDomande.php';
    require_once '../gestoriXML/gestoreRisposte.php';
    require_once '../gestoriXML/gestoreRecensioni.php';

    // Script per inserimento valutazione. Viene chiamato in maniera asincrona
    // mediante tecnologia AJAX
    // Restituisce true o false a seconda dell'esito dell'operazione
    $esito_operazione = false;

    // Check dei parametri ricevuti per eseguire l'operazione
    // I parametri vengono ricevuti mediante richiesta HTTP POST
    if ( isset($_POST["id_utente"]) && isset($_POST["reputazione_utente"]) 
            && isset($_POST["id_intervento_xml"]) && isset($_POST["tipo_intervento"]) 
            && isset($_POST["stella_premuta"]) )
    {
        // Verifico tramite il tipo di intervento dove inserire la valutazione
        $tipo = $_POST["tipo_intervento"];
        $gestore = null;
        $path_xml = '';
        switch ($tipo)
        {
            case 'domanda':
                $path_xml = '../../xml/documenti/domande.xml'; 
                $gestore = new GestoreDomande();
                break;
            
            case 'risposta':
                $path_xml = '../../xml/documenti/risposte.xml'; 
                $gestore = new GestoreRisposte();
                break;

            case 'recensione':
                $path_xml = '../../xml/documenti/domande.xml'; 
                $gestore = new GestoreRecensioni();
                break;
            
            default:
                $path_xml = ''; $gestore = null; break;
        }

        // Se ho un gestore
        if ( $gestore != null )
        {
            // Chiamata al metodo di inserimento valutazione
        } 
    }
    
    echo $esito_operazione;
?>