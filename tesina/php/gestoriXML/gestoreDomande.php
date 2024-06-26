<?php
    require_once 'gestoreXMLDOM.php';

    class Domanda
    {
        public $id;
        public $data;
        public $id_utente;
        public $contenuto;
        public $valutazioni;
    }

    class ValutazioneDomanda
    {
        public $peso;
        public $rating;
    }

    // Gestore XML DOM per il file domande.xml
    class GestoreDomande extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file domande con validazione tramite schema
            parent::__construct("../xml/documenti/domande.xml", 1, "../xml/schema/schemaDomande.xsd");
        }

        // Metodo per ottenere le domande
        // Riceve come parametro il filtro sulle faq (true) o meno (false)
        // Il parametro deve essere di tipo stringa
        function ottieniDomande($filtro)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di domande
            $lista_domande = [];

            // Ottengo la lista di figli della radice, ovvero la lista delle domande
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una domanda, estraggo contenuto e valutazioni
            for ( $i=0; $i<$n_figli; $i++ )
            {
                // Verifico se il filtro faq corrisponde a quello del parametro
                // altrimenti salto la domanda
                $f = $figli[$i]->getAttribute("faq");
                if ( ($filtro == "true" && ($filtro == $f)) || $filtro == "false" )
                {
                    // Creo una nuova domanda
                    $nuova_domanda = new Domanda();

                    // Estraggo le informazioni dalla domanda
                    $nuova_domanda->id = $figli[$i]->getAttribute("id");
                    $nuova_domanda->data = $figli[$i]->getAttribute("data");
                    $nuova_domanda->id_utente = $figli[$i]->getAttribute("id_utente");
                    $nuova_domanda->contenuto = $figli[$i]->firstChild->textContent;
                    
                    // Estraggo le valutazioni dalla domanda
                    $lista_valutazioni = [];
                    $valutazioni = $figli[$i]->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneDomanda();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla domanda
                    $nuova_domanda->valutazioni = $lista_valutazioni;

                    // Aggiungo la domanda alla lista delle domande
                    array_push($lista_domande, $nuova_domanda);
                }
            }

            return $lista_domande;
        }
    } 
?>