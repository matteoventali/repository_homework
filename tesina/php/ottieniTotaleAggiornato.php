<?php
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    require_once 'gestoriXML/gestoreCarrelli.php';
    require_once 'gestoriXML/gestorePortafogliBonus.php';
    require_once 'lib/libreria.php';
    require_once 'lib/libreriaDB.php';

    // Verifico che vi sia una richiesta di aggiornamento al totale
    if ( isset($_POST["id_cliente"]) && isset($_POST["crediti_utilizzati"]) )
    {
        if ( is_numeric($_POST["crediti_utilizzati"]) )
            $crediti = intval($_POST["crediti_utilizzati"]);
        else
            $crediti = -1;

        require 'lib/connection.php';

        if ( $crediti > -1 && $connessione )
        {
            // Gestori
            $gestoreCatalogo = new GestoreCatalogoProdotti();
            $gestoreCarrelli = new GestoreCarrelli();
            $gestorePortafogliBonus = new GestorePortafogliBonus();

            // Prelevo i dati dell'utente
            $utente = ottieniUtente($_POST['id_cliente'], $handleDB);
            $handleDB->close();

            // Verifico di aver trovato l'utente
            if ( $utente->id_utente == $_POST['id_cliente'] )
            {
                // Calcolo lo sconto fisso per il cliente loggato
                $sconto_fisso = calcolaScontoFisso($utente->id_utente, $utente->reputazione, $utente->data_registrazione);
                
                // Calcolo del totale associato al carrello
                $prodotti = $gestoreCarrelli->ottieniProdottiCarrello($_POST["id_cliente"]);
                $n_prodotti = 0;
                if ( $prodotti != null )
                    $n_prodotti = count($prodotti);

                $totale = 0; $totale_senza_offerte = 0;

                for ( $i=0; $i < $n_prodotti; $i++ )
                {
                    // Ottengo il prodotto i-esimo del carrello
                    $prodotto = $gestoreCatalogo->ottieniProdotto($prodotti[$i]);
                    
                    // Incremento il totale
                    if ( $prodotto->offerta_speciale != null && strtotime($prodotto->offerta_speciale->data_fine) + 86400 >= time() )
                        $prezzo = applicaSconto($prodotto->prezzo_listino, $prodotto->offerta_speciale->percentuale);
                    else
                    {
                        $prezzo = applicaSconto($prodotto->prezzo_listino, $sconto_fisso);
                        $totale_senza_offerte += $prezzo;
                    }
                        
                    $totale += $prezzo;
                }

                // Decremento i crediti dal totale se essi non superano il massimo
                $crediti_massimi = $gestorePortafogliBonus->ottieniCreditiMassimi($_POST["id_cliente"], $totale_senza_offerte);
                if ( $crediti <= $crediti_massimi )
                    echo $totale - $crediti;
                else
                    echo false;
            }
        }
        else
            echo false;
    }
?>