<?php
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';
    require_once 'gestoriXML/gestoreRecensioni.php';

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
        switch ($tipo)
        {
            case 'domanda':
                $gestore = new GestoreDomande();
                break;
            
            case 'risposta':
                $gestore = new GestoreRisposte();
                break;

            case 'recensione':
                $gestore = new GestoreRecensioni();
                break;
            
            default:
                $gestore = null; break;
        }

        // Se ho un gestore
        if ( $gestore != null )
            // Chiamata al metodo di inserimento valutazione
            $esito_operazione = $gestore->inserisciNuovaValutazione($_POST["id_intervento_xml"], $_POST["id_utente"], 
                                                                                $_POST["reputazione_utente"], $_POST["stella_premuta"]);
    }
    else
        header("Location: homepage.php"); // Chiamata allo script in modo improprio
    
    echo $esito_operazione;
?>