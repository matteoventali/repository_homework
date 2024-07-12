<?php
    require_once 'gestoreXMLDOM.php';

    class Acquisto
    {
        public $id;
        public $id_cliente;
        public $data;
        public $crediti_bonus_ricevuti;
        public $crediti_bonus_utilizzati;
        public $indirizzo_consegna;
        public $prodotti;
    }

    class Prodotto
    {
        public $id_prodotto;
        public $prezzo;
    }

    class GestoreAcquisti extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file acquisti con validazione tramite schema
            parent::__construct("../xml/documenti/acquisti.xml", 1, "../xml/schema/schemaAcquisti.xsd");
        }

        // Metodo per ottenere un acquisto
        // Riceve come parametro l'id dell'acquisto
        function ottieniAcquisto($id_acquisto)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Inizialmente acquisto e' vuoto
            $acquisto = "";

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista degli acquisti
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un acquisto, verifico se l'id
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                // Verifico se l'id dell'acquisto corrisponde
                // a quello passato
                $id = $figli[$i]->getAttribute("id");
                if ( $id == $id_acquisto )
                {
                    // Creo una nuova domanda
                    $acquisto = new Acquisto();

                    // Estraggo le informazioni dall'acquisto
                    $acquisto->id = $figli[$i]->getAttribute("id");
                    $acquisto->data = $figli[$i]->getAttribute("data");
                    $acquisto->id_cliente = $figli[$i]->getAttribute("id_cliente");
                    $acquisto->crediti_bonus_ricevuti = $figli[$i]->getAttribute("crediti_bonus_ricevuti");
                    $acquisto->crediti_bonus_utilizzati = $figli[$i]->getAttribute("crediti_bonus_utilizzati");
                    $acquisto->indirizzo_consegna = $figli[$i]->lastChild->textContent;
                    
                    // Estraggo i prodotti dell'acquisto
                    $lista_prodotti = [];
                    $prodotti = $figli[$i]->firstChild->childNodes;
                    $n_prodotti = $prodotti->length;
                    for ( $j=0; $j<$n_prodotti; $j++ )
                    {
                        $nuovo_prodotto = new Prodotto();
                        $nuovo_prodotto->id_prodotto = $prodotti[$j]->getAttribute('id_prodotto');
                        $nuovo_prodotto->prezzo = $prodotti[$j]->getAttribute('prezzo');

                        // Aggiungo la valutazione alla lista dei prodotti
                        array_push($lista_prodotti, $nuovo_prodotto);
                    }

                    // Aggiungo la lista dei prodotti all'acquisto
                    $acquisto->prodotti = $lista_prodotti;

                    $esito = true;
                }
            }

            return $acquisto;
        }

        // Metodo per ottenere gli acquisti
        // Riceve come parametro l'id del cliente
        function ottieniAcquistiCliente($id_cliente)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di acquisti
            $lista_acquisti = [];

            // Ottengo la lista di figli della radice, ovvero la lista degli acquisti
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un acquisto, verifico se si riferisce al cliente
            // in oggetto
            // In tal caso, estraggo indirizzo di consegna e valutazioni
            for ( $i=0; $i<$n_figli; $i++ )
            {
                // Verifico se l'acquisto e' del cliente passato
                if ( $id_cliente == $figli[$i]->getAttribute("id_cliente") )
                {
                    // Creo un nuovo acquisto
                    $nuovo_acquisto = new Acquisto();

                    // Estraggo le informazioni
                    $nuovo_acquisto->id = $figli[$i]->getAttribute("id");
                    $nuovo_acquisto->data = $figli[$i]->getAttribute("data");
                    $nuovo_acquisto->id_cliente = $figli[$i]->getAttribute("id_cliente");
                    $nuovo_acquisto->crediti_bonus_ricevuti = $figli[$i]->getAttribute("crediti_bonus_ricevuti");
                    $nuovo_acquisto->crediti_bonus_utilizzati = $figli[$i]->getAttribute("crediti_bonus_utilizzati");
                    $nuovo_acquisto->indirizzo_consegna = $figli[$i]->lastChild->textContent;
                    
                    // Estraggo i prodotti
                    $lista_prodotti = [];
                    $prodotti = $figli[$i]->firstChild->childNodes;
                    $n_prodotti = $prodotti->length;
                    for ( $j=0; $j<$n_prodotti; $j++ )
                    {
                        $nuovo_prodotto = new Prodotto();
                        $nuovo_prodotto->id_prodotto = $prodotti[$j]->getAttribute('id_prodotto');
                        $nuovo_prodotto->prezzo = $prodotti[$j]->getAttribute('prezzo');

                        // Aggiungo il prodotto alla lista dei prodotti
                        array_push($lista_prodotti, $nuovo_prodotto);
                    }

                    // Aggiungo la lista deli prodotti all'acquisto
                    $nuovo_acquisto->prodotti = $lista_prodotti;

                    // Aggiungo l'acquisto alla lista di acquisti
                    array_push($lista_acquisti, $nuovo_acquisto);
                }
            }

            return $lista_acquisti;
        }

        // Metodo per ottenere le statistiche relative ad un cliente
        // in base ai suoi acquisti. Le statistiche sono:
        // crediti n spesi dal cliente;
        // crediti m spesi nell'anno solare
        function ottieniStatistische($id_cliente)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            $anno_corrente = date('Y');

            // Ottengo la lista degli acquisti associata al cliente
            $acquisti = $this->ottieniAcquistiCliente($id_cliente);
            $n_acquisti = count($acquisti);

            // Calcolo delle statistiche
            $n = $m = 0;
            for ( $i=0; $i < $n_acquisti; $i++ )
            {
                // Acquisto corrente
                $acquisto = $acquisti[$i];
                $totale = 0; // Totale riferito all'acquisto corrente
                
                // Estraggo i prodotti dall'acquisto
                $lista_prodotti = $acquisto->prodotti;
                $n_prodotti = count($lista_prodotti);
                for ( $j=0; $j<$n_prodotti; $j++ )
                {
                    $somma = $lista_prodotti[$j]->prezzo;
                    $totale = $totale + $somma;
                }

                // Incremento n e eventualmente anche m
                $n += $totale;
                if ( date('Y', strtotime($acquisto->data)) == $anno_corrente )
                    $m += $totale;
            }

            // I valori vengono ritornati in una lista
            $risultati = [];
            array_push($risultati, $n);
            array_push($risultati, $m);

            return $risultati;
        }
    }

    // Funzione che permette di calcolare il totale dell'acquisto
    // Riceve come parametro l'id dell'acquisto
    function calcolaTotaleAcquisto($id_acquisto)
    {
        $totale = 0;

        $acq = new GestoreAcquisti();

        $acquisto = new Acquisto();

        // Ricerco l'acquisto interessato
        $acquisto = $acq->ottieniAcquisto($id_acquisto);

        // Estraggo i prodotti dall'acquisto
        $lista_prodotti = $acquisto->prodotti;
        $n_prodotti = count($lista_prodotti);
        for ( $i=0; $i<$n_prodotti; $i++ )
        {
            $somma = $lista_prodotti[$i]->prezzo;
            $totale = $totale + $somma;
        }

        // Restituisco il totale
        return $totale;
    }
?>