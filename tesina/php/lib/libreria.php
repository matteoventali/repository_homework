<?php
    $stellina_vuota = " &#9734;";
    $stellina_piena = " &#9733;";

    // Funzioni di libreria da utilizzare al bisogno

    // Funzione per url sfondo tra quelli disponibili
    function ottieniURLSfondo()
    {
        $nome_file = ["smartphone.jpg", "console.jpg", "elettrodomestici.jpg", "laptop.png", "televisore.jpg"];
        $scelta_casuale = rand(0,4);
    
        $url = '../img/background/' . $nome_file[$scelta_casuale];
        return $url;
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
                $ris = '<li><a href="gestioneClienti.php">Gestione clienti</a>' .
                            '</li><li><a href="gestioneRicariche.php">Gestione ricariche</a></li>';
                break;
            
            case 'G':
                $ris = '<li><a href="gestioneClienti.php">Visualizza clienti</a>';
                break;
            
            case 'C':
                $ris = '<li><a href="areaPersonale.php">Area personale</a>' .
                            '</li><li><a href="carrello.php">Carrello</a>' .
                            '</li><li><a href="richiestaRicarica.php">Richiesta ricarica</a></li>';
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
?>