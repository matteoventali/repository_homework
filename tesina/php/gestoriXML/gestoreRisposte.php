<?php
    require_once 'gestoreXMLDOM.php';

    class Risposta
    {
        public $id;
        public $data;
        public $id_utente;
        public $contenuto;
        public $valutazioni;
    }

    class ValutazioneRisposta
    {
        public $peso;
        public $rating;
    }

    // Gestore XML DOM per il file risposte.xml
    class GestoreRisposte extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file risposte con validazione tramite schema
            parent::__construct("../xml/documenti/risposte.xml", 1, "../xml/schema/schemaRisposte.xsd");
        }

        // Metodo per ottenere le risposte relative ad una domanda
        // Riceve l'id della domanda e il filtro per la risposta faq (true) o meno
        // In caso il filtro sia true viene selezionata solo la risposta da mostrare nelle faq
        function ottieniRisposte($id_domanda, $filtro)
        {
            // Verifico di poter utilizzare il file
            if ( !$this->checkValidita() )
                return null;

            // Ottengo la lista dei risposte
            $lista_risposte = [];
            $risposte = $this->oggettoDOM->documentElement->childNodes;
            $n_risposte = $this->oggettoDOM->documentElement->childElementCount;

            // Scansiono le risposte e filtro solo quelle relative alla domanda
            for ( $i=0; $i<$n_risposte; $i++ )
            {
                // Risposta corrente
                $risp = $risposte[$i];
                $f = $risp->getAttribute('faq');

                // Verifico che la risposta sia associata alla domanda richiesta
                // Se il filtro e' a true, devo selezionare solo quella da far apparire
                // nelle faq, altrimenti tutte quelle associate alla domanda.
                if ( $risp->getAttribute('id_domanda') == $id_domanda &&
                        (($filtro == "true" && $f == $filtro ) || ($filtro == "false")) )
                {
                    // Alloco una nuova risposta
                    $nuova_risposta = new Risposta();
                    $nuova_risposta->id = $risp->getAttribute('id');
                    $nuova_risposta->data = $risp->getAttribute('data');
                    $nuova_risposta->id_utente = $risp->getAttribute('id_utente');
                    $nuova_risposta->contenuto = $risp->firstChild->textContent;

                    // Prelevo le valutazioni della nuova risposta
                    $lista_valutazioni = [];
                    $valutazioni = $risp->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneRisposta();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla risposta
                    $nuova_risposta->valutazioni = $lista_valutazioni;

                    // Aggiungo la risposta alla lista risposte
                    array_push($lista_risposte, $nuova_risposta);
                }
            }

            return $lista_risposte;
        }
    }
?>