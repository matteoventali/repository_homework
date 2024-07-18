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
                    $recensione->id_utente = $figli[$i]->getAttribute("id_cliente");
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
                    $nuova_recensione->id_utente = $rec->getAttribute('id_cliente');
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

        // Metodo per inserire una valutazione nel file recensioni
        // Riceve il cliente che effettua la valutazione, l'id della recensione, il peso e il rating
        function inserisciNuovaValutazione($id_recensione, $id_cliente, $peso, $rating)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return $esito;

            // Flag per indicare se ho raggiunto la recensione
            $trovata = false;
            
            // Ottengo la lista di figli della radice, ovvero la lista delle recensioni
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                // Verifico se ho raggiunto la risposta
                if ( $figli[$i]->getAttribute('id') == $id_recensione )
                    $trovata = true;
            }

            // Se ho trovato la recensione
            if ( $trovata )
            {
                $i--;

                // Verifico che non vi sia una valutazione gia' inserita 
                // dall'utente per la recensione trovata
                if ( $this->ottieniValutazione($id_recensione, $id_cliente) == null ) // Sono sicuro che non c'e'
                {
                    // Posso procedere all'inserimento della valutazione
                    $esito = true;

                    // Creo un nuovo elemento valutazione
                    $nuova_valutazione = $this->oggettoDOM->createElement('valutazione');
                    $nuova_valutazione->setAttribute('id_utente', $id_cliente);
                    $nuova_valutazione->setAttribute('peso', $peso);
                    $nuova_valutazione->setAttribute('rating', $rating);

                    // Aggiungo la nuova valutazione alla lista di valutazioni della recensione
                    $figli[$i]->lastChild->appendChild($nuova_valutazione);

                    // Salvo le modifiche sul file xml
                    $this->salvaXML($this->pathname);
                }
            }

            return $esito;
        }

        // Metodo per inserire una nuova recensione
        // Riceve il contenuto, l'utente che effettua la recensione,
        // l'id del prodotto coinvolto
        function inserisciRecensione($cont, $id_cliente, $id_prodotto)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultima recensione
            $id_nuova_recensione = 1;
            $ultima = $this->oggettoDOM->documentElement->lastElementChild;
            if ( $ultima != null ) // Ci sono altre recensioni
            {
                $id_ultima = $ultima->getAttribute('id');
                $id_ultima = intval($id_ultima);
                $id_nuova_recensione = ++$id_ultima;
                $id_nuova_recensione = strval($id_nuova_recensione);
            }

            // Creazione della nuova recensioni
            $nuova_recensione = $this->oggettoDOM->createElement("recensione");
            $nuova_recensione->setAttribute("id", $id_nuova_recensione);
            $nuova_recensione->setAttribute("data", date("Y-m-d"));
            $nuova_recensione->setAttribute("id_cliente", $id_cliente);
            $nuova_recensione->setAttribute("id_prodotto", $id_prodotto);
            $contenuto_nuova_recensione = $this->oggettoDOM->createElement("contenuto", $cont);
            $lista_valutazioni_nuova_recensione = $this->oggettoDOM->createElement("valutazioni");

            // Inserisco il contenuto e la lista vuota di valutazioni come figli della recensioni
            $nuova_recensione->appendChild($contenuto_nuova_recensione);
            $nuova_recensione->appendChild($lista_valutazioni_nuova_recensione);

            // Aggancio della recensione
            $this->oggettoDOM->documentElement->appendChild($nuova_recensione); 

            // Salvo i cambiamenti sul file
            $this->salvaXML($this->pathname);
        }

        // Metodo per rimuovere una recensione
        function rimuoviRecensione($id_recensione)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return false;

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
                    // Elimino la recensione 
                    $this->oggettoDOM->documentElement->removeChild($figli[$i]);
                    $this->salvaXML($this->pathname);
                    $esito = true;
                }
            }

            return $esito;
        }
    }
?>