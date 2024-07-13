<?php
    $stellina_vuota = " &#9734;";
    $stellina_piena = " &#9733;";

    require_once 'gestoriXML/gestoreAcquisti.php';

    // Funzioni di libreria da utilizzare al bisogno

    // Funzione per url sfondo tra quelli disponibili
    function ottieniURLSfondo()
    {
        $nome_file = ["smartphone.jpg", "console.jpg", "elettrodomestici.jpg", "laptop.png", "televisore.jpg"];
        $scelta_casuale = rand(0,4);
    
        $url = '../img/background/' . $nome_file[$scelta_casuale];
        return $url;
    }

    // Funzione per ottenere il path della icona associata ad una categoria
    function ottieniPathIcona($id_categoria)
    {
        $nome_file = '';
        
        // Switch sulla categoria per determinale il nome del file
        switch ($id_categoria)
        {
            case "1":
                $nome_file='laptop.png'; break;
            case "2":
                $nome_file='smartphone.png'; break;
            case "3":
                $nome_file='tv.png'; break;
            case "4":
                $nome_file='lavatrice.png'; break;
            case "5":
                $nome_file='console.png'; break;

            default:
                $nome_file = ''; break;
        }

        return '../img/icone/' . $nome_file;
    }

    // Funzione per ottenere elenco operazioni da includere nella sidebar
    // Riceve:
    // - V -> visitatore
    // - C -> cliente
    // - A -> admin
    // - G -> gestore
    function ottieniOpzioniMenu($ruolo)
    {
        $ris = "";
        
        switch($ruolo)
        {
            case 'V':
                $ris = ""; break;
            
            case 'A':
                $ris = '<li class="liSidebar"><a href="gestioneClienti.php">Gestione clienti</a>' .
                            '</li><li class="liSidebar"><a href="gestioneRicariche.php">Gestione ricariche</a></li>';
                break;
            
            case 'G':
                $ris = '<li class="liSidebar"><a href="gestioneClienti.php">Visualizza clienti</a></li>';
                break;
            
            case 'C':
                $ris = '<li class="liSidebar"><a href="areaPersonale.php">Area personale</a>' .
                            '</li><li class="liSidebar"><a href="carrello.php">Carrello</a>' .
                            '</li><li class="liSidebar"><a href="richiestaRicarica.php">Richiesta ricarica</a></li>';
                break;
        }

        return $ris;
    }

    // Funzione che predispone il popup
    // Se mostra = false gli altri due parametri vengono ignorati
    function creaPopup($mostra, $contenuto, $errore)
    {
        require_once 'parametriStile.php';

        $popup = "";

        if ( $mostra )
        {
            // Import del popup per comunicare errore o meno
            // I settings della finestra sono ottenuti preliminarmente a seconda della richiesta pervenuta
            $popup = file_get_contents("../html/popupErrore.html");
            
            $popup = str_replace("%CONTENUTO_FINESTRA_POPUP%", $contenuto, $popup);
            $popup = str_replace("%OPZIONE_DISPLAY_POPUP%", $opzione_display_popup_mostra, $popup);
            $popup = str_replace("%MARGINE_DESTRO_POPUP%", $margine_popup_mostra, $popup);
            
            // Ci sono errori
            if ($errore)
                $popup = str_replace("%COLORE_SFONDO_POPUP%", $colore_background_popup_rosso, $popup);
            else // Tutto ok
                $popup = str_replace("%COLORE_SFONDO_POPUP%", $colore_background_popup_verde, $popup);
        }
        return $popup;
    }

    // Funzione per ottenere la media dei rating
    // riceve una lista di valutazioni associata ad un intervento
    function calcolaMediaRating($lista_valutazioni)
    {
        // Scansione della lista per calcolo media aritmetica
        $media = 0;
        $lung = count($lista_valutazioni);
        for ( $i=0; $i<$lung; $i++ )
        {
            // Estraggo il rating dalla valutazione i-esima
            $rating = intval($lista_valutazioni[$i]->rating);
            $media += $rating;
        }

        if ( $lung > 0 )
            $media = $media / $lung;

        return $media;
    }

    // Funzione per inizializzare un frammento di stelline
    // a seconda della media passata. Riceve un float
    // e lo arrotonda, il colore delle stelline, se le stelline
    // devono essere statiche o dinamiche e l'id del container padre
    function initStelline($media, $colore, $modalita, $padre)
    {
        global $stellina_piena; global $stellina_vuota;
        
        // Prelevo un frammento stelline vuoto
        $frammento = file_get_contents('../html/frammentoStelline.html');

        // Funzioni js da inserire a seconda se stelline statiche o dinamiche
        $funzione_over = ''; $funzione_out = ''; $funzione_onclick = ''; $funzione_onclick_vuota = '';
        if ( $modalita )
        {
            $funzione_over = "coloraStellina('stella[$padre]', this)";
            $funzione_out = "decoloraStelline('stella[$padre]', '$colore')";
            $funzione_onclick_vuota = "inserisciValutazione('$padre', %INDICE_STELLA_i%)";
        }
        
        // Arrotondo la media
        $num_stelline = round($media);

        // Variabile d'appoggio per tipo stella
        $stella = "";

        // Coloro le stelline in maniera adeguata
        for ( $i=1; $i<=5; $i++ )
        {
            // Genero il contenuto da sostituire nel frammento
            $da_sostituire = "%STELLA_" . $i ."%";
            
            // Verifico se la stella i-esima sia da riempire o meno
            if ( $i <= $num_stelline )
                $stella = $stellina_piena;
            else
                $stella = $stellina_vuota;    
            $frammento = str_replace($da_sostituire, $stella, $frammento);

            // Costruzione funzione onclick in modo da passargli l'id del container
            // da cui pare la richiesta e l'indice della stella che e' stata cliccata
            if ( $modalita )
            {
                $app = str_replace("%INDICE_STELLA_i%", "%INDICE_STELLA_" . $i ."%", $funzione_onclick_vuota);
                $funzione_onclick = str_replace("%INDICE_STELLA_" . $i ."%", $i, $app);
            }
                
            // I parametri della funzione di onclick dipendono dall'indice della stellina
            $frammento = str_replace("%FUNZIONE_ONCLICK_" . $i . "%", $funzione_onclick, $frammento);
        }

        // Set del colore e funzioni per gestire gli eventi lato client
        $frammento = str_replace("%COLORE%", $colore, $frammento);
        $frammento = str_replace("%FUNZIONE_OVER%", $funzione_over, $frammento);
        $frammento = str_replace("%FUNZIONE_OUT%", $funzione_out, $frammento);
        $frammento = str_replace("%ID_PADRE%", $padre, $frammento);
        
        return $frammento;
    }

    // Funzione per ritornare l'importo dopo aver applicato lo sconto
    function applicaSconto($importo, $sconto)
    {
        // Conversione dell'importo e dello sconto a tipo numerico
        $importo = floatval($importo);
        $sconto = floatval($sconto);

        // Nuovo importo
        $ris = round((100 - $sconto)/100 * $importo);
        
        return $ris;
    }

    // Funzione per calcolare lo sconto fisso. Riceve l'id del cliente
    // la reputazione e la data di registrazione (dati dal database).
    // Provvede a reperire gli altri parametri di calcolo ovvero:
    // ammontare dei crediti spesi dal cliente;
    // ammontare dei crediti spesi dal cliente quest'anno
    function calcolaScontoFisso($id_cliente, $reputazione, $data_registrazione)
    {
        // Allocazione gestore acquisti per reperire le informazioni suddette
        $gestore_acquisti = new GestoreAcquisti();

        // Ottengo le statistiche suddette associate al cliente
        $statistiche = $gestore_acquisti->ottieniStatistische($id_cliente);

        // Calcolo del periodo decorso in anni dall'iscrizione dell'utente
        $anno_corrente = date('Y');
        $anno_reg = date('Y', strtotime($data_registrazione));
        $periodo = $anno_corrente - $anno_reg;

        // Applico la formula del documento per il calcolo dello sconto fisso
        $sconto_fisso = 0.5 * $periodo + 0.01 * $statistiche[0] + 0.03 * $statistiche[1] + 0.1 * intval($reputazione) / 100;
        $sconto_fisso = round($sconto_fisso);

        // Lo sconto fisso non puo' superare il 20% 
        if ( $sconto_fisso > 20 )
            $sconto_fisso = 20;

        return $sconto_fisso;
    }

    // Funzione per ordinare il vettore dei prodotti in base al prezzo decrescente
    function ordinaProdottiPrezzoDecrescente($prodotti)
    {
        // Applico l'algoritmo bubblesort di ordinamento
        $scambi = true; $k = 0;
        $dim = count($prodotti);

        while ( $scambi )
        {
            // Set degli scambi a false
            $scambi = false;

            // Scansione del vettore e ordinamento per prezzo
            // Alla fine di ogni iterazione, in ultima posizione
            // ci sara' l'elemento corretto
            for ( $i=0; $i < $dim - $k - 1; $i++ )
            {
                // Verifico se effettuare lo scambio
                if ( $prodotti[$i]->prezzo_listino < $prodotti[$i+1]->prezzo_listino )
                {
                    $app = $prodotti[$i];
                    $prodotti[$i] = $prodotti[$i+1];
                    $prodotti[$i+1] = $app;
                    $scambi = true;
                }
            }
        }

        return $prodotti;
    }

    // Funzione per ordinare il vettore dei prodotti in base al prezzo crescente
    function ordinaProdottiPrezzoCrescente($prodotti)
    {
        // Applico l'algoritmo bubblesort di ordinamento
        $scambi = true; $k = 0;
        $dim = count($prodotti);

        while ( $scambi )
        {
            // Set degli scambi a false
            $scambi = false;

            // Scansione del vettore e ordinamento per prezzo
            // Alla fine di ogni iterazione, in ultima posizione
            // ci sara' l'elemento corretto
            for ( $i=0; $i < $dim - $k - 1; $i++ )
            {
                // Verifico se effettuare lo scambio
                if ( $prodotti[$i]->prezzo_listino > $prodotti[$i+1]->prezzo_listino )
                {
                    $app = $prodotti[$i];
                    $prodotti[$i] = $prodotti[$i+1];
                    $prodotti[$i+1] = $app;
                    $scambi = true;
                }
            }
        }

        return $prodotti;
    }
?>