<?php
    require_once 'gestoriXML/gestoreDomande.php';
    require_once 'gestoriXML/gestoreRisposte.php';
    require_once 'gestoriXML/gestoreRecensioni.php';
    require_once 'lib/libreriaDB.php';

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
        $intervento = null;
        switch ($tipo)
        {
            case 'domanda':
                $gestore = new GestoreDomande();
                $intervento = $gestore->ottieniDomanda($_POST["id_intervento_xml"]);
                break;
            
            case 'risposta':
                $gestore = new GestoreRisposte();
                $intervento = $gestore->ottieniRisposta($_POST["id_intervento_xml"]);
                break;

            case 'recensione':
                $gestore = new GestoreRecensioni();
                break;
            
            default:
                $gestore = null; break;
        }

        // Se ho un gestore
        if ( $gestore != null )
        {
            // Chiamata al metodo di inserimento valutazione
            $esito_operazione = $gestore->inserisciNuovaValutazione($_POST["id_intervento_xml"], $_POST["id_utente"], 
                                                                                $_POST["reputazione_utente"], $_POST["stella_premuta"]);
            
            // Aggiorno la reputazione dell'utente proprietario dell'intervento
            require 'lib/connection.php';
            if ( $connessione )
            {
                aggiornaReputazione($handleDB, $intervento->id_utente, $_POST["stella_premuta"], $_POST["reputazione_utente"]);
                $handleDB->close();
            }
        }
    }
    else
        header("Location: homepage.php"); // Chiamata allo script in modo improprio
    
    echo $esito_operazione;
?>