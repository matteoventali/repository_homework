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
    }

?>