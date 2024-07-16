<?php
    require_once 'gestoreXMLDOM.php';

    class Recensione
    {
        public $id;
        public $data;
        public $id_utente;
        public $contenuto;
        public $valutazioni;
    }

    class ValutazioneRecensione
    {
        public $peso;
        public $rating;
        public $id_utente;
    }

    // Gestore XML DOM per il file recensioni.xml
    class GestoreRecensioni extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file recensioni con validazione tramite schema
            parent::__construct("../xml/documenti/recensioni.xml", 1, "../xml/schema/schemaRecensioni.xsd");
        }

        // Metodo per ottenere una recensione
        // Riceve come parametro l'id della recensione
        function ottieniRecensione($id_recensione)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Inizialmente recensione e' vuota
            $recensione = "";

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista delle recensioni
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una recensione, verifico se l'id
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                // Verifico se l'id della recensione corrisponde
                // a quello passato
                $id = $figli[$i]->getAttribute("id");
                if ( $id == $id_recensione )
                {
                    // Creo una nuova recensione
                    $recensione = new Recensione();

                    // Estraggo le informazioni dalla recensione
                    $recensione->id = $figli[$i]->getAttribute("id");
                    $recensione->data = $figli[$i]->getAttribute("data");
                    $recensione->id_utente = $figli[$i]->getAttribute("id_utente");
                    $recensione->contenuto = $figli[$i]->firstChild->textContent;
                    
                    // Estraggo le valutazioni dalla recensione
                    $lista_valutazioni = [];
                    $valutazioni = $figli[$i]->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneRecensione();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');
                        $nuova_valutazione->id_utente = $valutazioni[$j]->getAttribute('id_utente');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla domanda
                    $recensione->valutazioni = $lista_valutazioni;

                    $esito = true;
                }
            }

            return $recensione;
        }

        // Metodo per ottenere le recensioni relative ad un prodotto
        // Riceve l'id del prodotto
        function ottieniRecensioni($id_prodotto)
        {
            // Verifico di poter utilizzare il file
            if ( !$this->checkValidita() )
                return null;

            // Ottengo la lista delle recensioni
            $lista_prodotti = [];
            $recensioni = $this->oggettoDOM->documentElement->childNodes;
            $n_recensioni = $this->oggettoDOM->documentElement->childElementCount;

            // Scansiono le recensioni e filtro solo quelle relative al prodotto
            for ( $i=0; $i<$n_recensioni; $i++ )
            {
                // Recensione corrente
                $rec = $recensioni[$i];
                
                // Verifico che la recensione sia associata al prodotto richiesto
                if ( $rec->getAttribute('id_prodotto') == $id_prodotto )
                {
                    // Alloco una nuova recensione
                    $nuova_recensione = new Recensione();
                    $nuova_recensione->id = $rec->getAttribute('id');
                    $nuova_recensione->data = $rec->getAttribute('data');
                    $nuova_recensione->id_utente = $rec->getAttribute('id_utente');
                    $nuova_recensione->contenuto = $rec->firstChild->textContent;

                    // Prelevo le valutazioni della nuova recensione
                    $lista_valutazioni = [];
                    $valutazioni = $rec->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneRecensione();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');
                        $nuova_valutazione->id_utente = $valutazioni[$j]->getAttribute('id_utente');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla recensione
                    $nuova_recensione->valutazioni = $lista_valutazioni;

                    // Aggiungo la risposta alla lista recensioni
                    array_push($lista_prodotti, $nuova_recensione);
                }
            }

            return $lista_prodotti;
        }

        // Metodo per ottenere la valutazione associata ad una specifica recensione
        // effettuata da un utente, se esiste. In caso negativo, restituisce null
        function ottieniValutazione($id_recensione, $id_utente)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Tento di ottenere la recensione tramite metodo opportuno gia' implementato
            $recensione = $this->ottieniRecensione($id_recensione);

            // Se la recensione esiste procedo
            $val = null;
            if ( $recensione != "" )
            {
                // Estraggo la lista delle valutazioni dalla recensione
                $valutazioni = $recensione->valutazioni;
                $n_valutazioni = count($valutazioni);
                for ( $i=0; $i<$n_valutazioni && $val == null; $i++ )
                {
                    // Verifico se l'utente coincide
                    if ( $valutazioni[$i]->id_utente == $id_utente)
                    {
                        // Alloco la nuova valutazione
                        $val = new ValutazioneRecensione();
                        $val->id_utente = $valutazioni[$i]->id_utente;
                        $val->peso = $valutazioni[$i]->peso;
                        $val->rating = $valutazioni[$i]->rating;
                    }
                }
            }

            return $val;
        }
    }

?>