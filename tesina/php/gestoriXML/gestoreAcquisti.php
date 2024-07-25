<?php
    require_once 'gestoreXMLDOM.php';
    require_once 'gestoreCarrelli.php';
    require_once 'gestoreCatalogoProdotti.php';
    require_once 'gestorePortafogliBonus.php';
    
    class Acquisto
    {
        public $id;
        public $id_cliente;
        public $data;
        public $crediti_bonus_ricevuti;
        public $crediti_bonus_utilizzati;
        public $indirizzo_consegna;
        public $totale_effettivo;
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
                    $acquisto->totale_effettivo = $figli[$i]->getAttribute("totale_effettivo");
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
                    $nuovo_acquisto->totale_effettivo = $figli[$i]->getAttribute("totale_effettivo");
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
                
                // Incremento n e eventualmente anche m
                $n += $acquisto->totale_effettivo;
                if ( date('Y', strtotime($acquisto->data)) == $anno_corrente )
                    $m += $acquisto->totale_effettivo;
            }

            // I valori vengono ritornati in una lista
            $risultati = [];
            array_push($risultati, $n);
            array_push($risultati, $m);

            return $risultati;
        }

        // Metodo per inserire un nuovo acquisto effettuato da un cliente
        // riceve anche l'ammontare di crediti bonus che il cliente vuole sfruttare
        // per beneficiare dello sconto variabile
        function inserisciAcquisto($id_cliente, $crediti_bonus, $indirizzo_consegna)
        {
            require 'lib/libreriaDB.php';
            require 'lib/configurazione.php';
            require 'lib/connection.php';
            
            // Esito dell'operazione
            // 1 -> successo
            // 2 -> crediti bonus passati superiore ai massimi consentiti
            // 3 -> crediti del portafoglio standard insufficienti
            $esito = 1;
            
            // Verifico se posso usare il file e sono connesso al database
            if ( !$this->checkValidita() || !$connessione )
                return false;

            // Gestori utili
            $gestoreCarrelli = new GestoreCarrelli();
            $gestoreCatalogo = new GestoreCatalogoProdotti();
            $gestorePortafogliBonus = new GestorePortafogliBonus();

            // Prelevo i dati dell'utente
            $utente = ottieniUtente($id_cliente, $handleDB);
            
            // Calcolo lo sconto fisso per il cliente che effettua l'acquisto
            $sconto_fisso = calcolaScontoFisso($utente->id_utente, $utente->reputazione, $utente->data_registrazione);

            // Calcolo il totale del carrello e il totale dei prodotti non in offerta speciale
            $prodotti = $gestoreCarrelli->ottieniProdottiCarrello($id_cliente);
            $n_prodotti = 0;
            if ( $prodotti != null )
                $n_prodotti = count($prodotti);

            $totale = 0; $totale_senza_offerte = 0;

            for ( $i=0; $i < $n_prodotti; $i++ )
            {
                // Ottengo il prodotto i-esimo del carrello
                $prodotto = $gestoreCatalogo->ottieniProdotto($prodotti[$i]);
                
                // Incremento il totale
                if ( $prodotto->offerta_speciale != null && strtotime($prodotto->offerta_speciale->data_fine) + 86400 >= time() )
                    $prezzo = applicaSconto($prodotto->prezzo_listino, $prodotto->offerta_speciale->percentuale);
                else
                {
                    $prezzo = applicaSconto($prodotto->prezzo_listino, $sconto_fisso);
                    $totale_senza_offerte += $prezzo;
                }
                    
                $totale += $prezzo;
            }
            
            // Verifico che i crediti bonus non superino il massimo applicabile
            $crediti_max = $gestorePortafogliBonus->ottieniCreditiMassimi($id_cliente, $totale_senza_offerte);
            if ( $crediti_bonus <= $crediti_max )
            {
                // Verifico che il totale dei crediti - crediti bonus siano disponibili
                // nel portafoglio standard del cliente
                $totale_effettivo = $totale - $crediti_bonus;
                
                if ( $totale_effettivo <= intval($utente->saldo_standard) )
                {
                    // Finalizzo l'acquisto
                    // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultimo acquisto
                    $id_nuovo_acquisto = 1;
                    $ultimo = $this->oggettoDOM->documentElement->lastElementChild;
                    if ( $ultimo != null ) // Ci sono altre domande
                    {
                        $id_ultimo = $ultimo->getAttribute('id');
                        $id_ultimo = intval($id_ultimo);
                        $id_nuovo_acquisto = ++$id_ultimo;
                        $id_nuovo_acquisto = strval($id_nuovo_acquisto);
                    }

                    // Creazione del nuovo acquisto
                    //$nuovo_aqcuisto = $this->oggettoDOM->createElement('acquisto');
                }
                else
                    $esito = 3;
            }
            else
                $esito = 2;

            return $esito; 
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