<?php
    $stellina_vuota = " &#9734";
    $stellina_piena = " &#9733";

    class Utente
    {
        public $id_utente            = "";
        public $nome                 = "";
        public $cognome              = "";
        public $indirizzo            = "";
        public $citta                = "";
        public $cap                  = "";
        public $reputazione          = "";
        public $data_registrazione   = "";
        public $stato                = "";
        public $username             = "";
        public $mail                 = "";
        public $ruolo                = "";
        public $saldo_standard       = "";
    }
    
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
                            '</li><li><a href="carrello.php">Carrello</a></li>' .
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
?>