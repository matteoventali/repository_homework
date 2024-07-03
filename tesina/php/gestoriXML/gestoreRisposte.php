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
        public $id_utente;
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

        // Metodo per ottenere una risposta
        // Riceve come parametro l'id della risposta
        function ottieniRisposta($id_risposta)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Inizialmente risposta e' vuota
            $risposta = "";

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista delle risposte
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una risposta, verifico se l'id
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                // Verifico se l'id della domanda corrisponde
                // a quello passato
                $id = $figli[$i]->getAttribute("id");
                if ( $id == $id_risposta )
                {
                    // Creo una nuova risposta
                    $risposta = new Risposta();

                    // Estraggo le informazioni dalla domanda
                    $risposta->id = $figli[$i]->getAttribute("id");
                    $risposta->data = $figli[$i]->getAttribute("data");
                    $risposta->id_utente = $figli[$i]->getAttribute("id_utente");
                    $risposta->contenuto = $figli[$i]->firstChild->textContent;
                    
                    // Estraggo le valutazioni dalla risposta
                    $lista_valutazioni = [];
                    $valutazioni = $figli[$i]->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneRisposta();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');
                        $nuova_valutazione->id_utente = $valutazioni[$j]->getAttribute('id_utente');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla domanda
                    $risposta->valutazioni = $lista_valutazioni;

                    $esito = true;
                }
            }

            return $risposta;
        }
                
        // Metodo per inserire una risposta
        // Riceve il contenuto, l'utente che effettua la risposta,
        // l'id della domanda e se la risposta e' quella da mostrare nelle faq
        function inserisciRisposta($cont, $id_utente, $faq, $id_domanda)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultima risposta
            $id_nuova_risposta = 1;
            $ultima = $this->oggettoDOM->documentElement->lastElementChild;
            if ( $ultima != null ) // Ci sono altre domande
            {
                $id_ultima = $ultima->getAttribute('id');
                $id_ultima = intval($id_ultima);
                $id_nuova_risposta = ++$id_ultima;
                $id_nuova_risposta = strval($id_nuova_risposta);
            }

            // Creazione della nuova risposta
            $nuova_risposta = $this->oggettoDOM->createElement("risposta");
            $nuova_risposta->setAttribute("id", $id_nuova_risposta);
            $nuova_risposta->setAttribute("faq", $faq);
            $nuova_risposta->setAttribute("data", date("Y-m-d"));
            $nuova_risposta->setAttribute("id_utente", $id_utente);
            $nuova_risposta->setAttribute("id_domanda", $id_domanda);
            $contenuto_nuova_risposta = $this->oggettoDOM->createElement("contenuto", $cont);
            $lista_valutazioni_nuova_risposta = $this->oggettoDOM->createElement("valutazioni");

            // Inserisco il contenuto e la lista vuota di valutazioni come figli della risposta
            $nuova_risposta->appendChild($contenuto_nuova_risposta);
            $nuova_risposta->appendChild($lista_valutazioni_nuova_risposta);

            // Aggancio della risposta
            $this->oggettoDOM->documentElement->appendChild($nuova_risposta); 

            // Salvo i cambiamenti sul file
            $this->salvaXML($this->pathname);
        }

        // Funzione che permette di capire se esiste una risposta appartenente alle faq 
        // per la domanda passata (appartenente anch'essa alle faq)
        function verificaPresenzaRispostaFaq($id_domanda)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Variabile per il risultato da restituire, inizialmente a false
            $esito = false;

            // Utilizzo la funzione sopra scritta per ottenere la lista di risposte
            // con attributo faq = "true"
            // Tra quelle devo vedere se ne esiste una con id_domanda uguale a quello passato
            $risposte = $this->ottieniRisposte($id_domanda, "true");
            $dim_lista = count($risposte);
            
            // Tramite la dimensione della lista riesco a capire se la risposta
            // alla faq esiste
            if($dim_lista > 0)
            {
                $esito = true;
            }

            return $esito;
        }

        // Metodo per ottenere la valutazione associata ad una specifica risposta
        // effettuata da un utente, se esiste. In caso negativo, restituisce null
        function ottieniValutazione($id_risposta, $id_utente)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Tento di ottenere la risposta tramite metodo opportuno gia' implementato
            $risposta = $this->ottieniRisposta($id_risposta);

            // Se la risposta esiste procedo
            $val = null;
            if ( $risposta != "" )
            {
                // Estraggo la lista delle valutazioni dalla risposta
                $valutazioni = $risposta->valutazioni;
                $n_valutazioni = count($valutazioni);
                for ( $i=0; $i<$n_valutazioni && $val == null; $i++ )
                {
                    // Verifico se l'utente coincide
                    if ( $valutazioni[$i]->id_utente == $id_utente)
                    {
                        // Alloco la nuova valutazione
                        $val = new ValutazioneRisposta();
                        $val->id_utente = $valutazioni[$i]->id_utente;
                        $val->peso = $valutazioni[$i]->peso;
                        $val->rating = $valutazioni[$i]->rating;
                    }
                }
            }
            return $val;
        }

        // Metodo per inserire una valutazione nel file rispsote
        // Riceve l'utente che effettua la valutazione, la risposta di riferimento, il peso e il rating
        function inserisciNuovaValutazione($id_risposta, $id_utente, $peso, $rating)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return $esito;

            // Flag per indicare se ho raggiunto la risposta
            $trovata = false;
            
            // Ottengo la lista di figli della radice, ovvero la lista delle risposte
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                // Verifico se ho raggiunto la risposta
                if ( $figli[$i]->getAttribute('id') == $id_risposta )
                    $trovata = true;
            }

            // Se ho trovato la risposta
            if ( $trovata )
            {
                $i--;

                // Verifico che non vi sia una valutazione gia' inserita 
                // dall'utente per la domanda trovata
                if ( $this->ottieniValutazione($id_risposta, $id_utente) == null ) // Sono sicuro che non c'e'
                {
                    // Posso procedere all'inserimento della valutazione
                    $esito = true;

                    // Creo un nuovo elemento valutazione
                    $nuova_valutazione = $this->oggettoDOM->createElement('valutazione');
                    $nuova_valutazione->setAttribute('id_utente', $id_utente);
                    $nuova_valutazione->setAttribute('peso', $peso);
                    $nuova_valutazione->setAttribute('rating', $rating);

                    // Aggiungo la nuova valutazione alla lista di valutazioni della domanda
                    $figli[$i]->lastChild->appendChild($nuova_valutazione);

                    // Salvo le modifiche sul file xml
                    $this->salvaXML($this->pathname);
                }
            }

            return $esito;
        }
    }
?>