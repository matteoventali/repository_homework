<?php
    require_once 'gestoreXMLDOM.php';
    require_once 'gestoreRecensioni.php';

    class ProdottoCatalogo
    {
        public $id;
        public $id_tipo;
        public $id_categoria;
        public $nome;
        public $prezzo_listino;
        public $percorso_immagine;
        public $specifiche;
        public $descrizione;
        public $offerta_speciale;
    }

    class OffertaSpeciale
    {
        public $data_inizio;
        public $data_fine;
        public $percentuale;
        public $crediti;
    }

    // Gestore XML DOM per il file catalogoProdotti.xml
    class GestoreCatalogoProdotti extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file catalogoProdotti con validazione tramite schema
            parent::__construct("../xml/documenti/catalogoProdotti.xml", 1, "../xml/schema/schemaCatalogoProdotti.xsd");
        }

        // Metodo per ottenere un prodotto dal catalogo
        function ottieniProdotto($id_prodotto)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Struttura prodotto
            $prodotto = new ProdottoCatalogo();

            // Ottengo la lista di figli della radice, ovvero la lista dei prodotti
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un prodotto, verifico corrispondenza con id ricevuto
            $trovato = false;
            for ( $i=0; $i<$n_figli && !$trovato; $i++ )
            {
                $id = $figli[$i]->getAttribute('id');
               
                if ( $id_prodotto == $id )
                {
                    $trovato = true;

                    // Fill della struttura
                    $prodotto->id = $id_prodotto;
                    $prodotto->id_tipo = $figli[$i]->getAttribute('id_tipo');
                    $prodotto->id_categoria = $figli[$i]->getAttribute('id_categoria');
                    $prodotto->nome = $figli[$i]->firstChild->textContent;
                    $prodotto->prezzo_listino = $figli[$i]->firstChild->nextSibling->textContent;
                    $prodotto->percorso_immagine = $figli[$i]->firstChild->nextSibling->nextSibling->textContent;
                    $prodotto->specifiche = $figli[$i]->firstChild->nextSibling->nextSibling->nextSibling->textContent;
                    $prodotto->descrizione = $figli[$i]->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->textContent;

                    // Se esiste un'offerta speciale per quel prodotto
                    $offerta_speciale_xml = $figli[$i]->getElementsByTagName('offerta_speciale');
                    if ( count($offerta_speciale_xml) > 0 )
                    {
                        // Fill dell'offerta speciale
                        $offerta = new OffertaSpeciale();
                        $offerta->data_inizio = $offerta_speciale_xml[0]->firstChild->textContent;
                        $offerta->data_fine = $offerta_speciale_xml[0]->lastChild->textContent;
                        $offerta->percentuale = $offerta_speciale_xml[0]->getAttribute('percentuale');
                        $offerta->crediti = $offerta_speciale_xml[0]->getAttribute('crediti');
                        $prodotto->offerta_speciale = $offerta;
                    }
                    else
                        $prodotto->offerta_speciale = null;    
                } 
            }
            
            return $prodotto;
        }

        // Metodo per ottenere i prodotti in base ai parametri di ricerca
        // categoria, tipo, contenuto testo
        function ricercaProdotti($id_categoria, $id_tipologia, $contenuto_testo)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di prodotti che soddisfano i criteri di ricerca
            $lista_prodotti = [];

            // Ottengo la lista di figli della radice, ovvero la lista dei prodotti
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un prodotto, verifico corrispondenza con id ricevuto
            $trovato = false;
            for ( $i=0; $i<$n_figli && !$trovato; $i++ )
            {
                // Prelevo le informazioni del prodotto
                $prodotto = new ProdottoCatalogo();
                
                // Fill della struttura prodotto
                $prodotto->id = $figli[$i]->getAttribute('id');
                $prodotto->id_tipo = $figli[$i]->getAttribute('id_tipo');
                $prodotto->id_categoria = $figli[$i]->getAttribute('id_categoria');
                $prodotto->nome = $figli[$i]->firstChild->textContent;
                $prodotto->prezzo_listino = $figli[$i]->firstChild->nextSibling->textContent;
                $prodotto->percorso_immagine = $figli[$i]->firstChild->nextSibling->nextSibling->textContent;
                $prodotto->specifiche = $figli[$i]->firstChild->nextSibling->nextSibling->nextSibling->textContent;
                $prodotto->descrizione = $figli[$i]->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->textContent;

                // Se esiste un'offerta speciale per quel prodotto
                $offerta_speciale_xml = $figli[$i]->getElementsByTagName('offerta_speciale');
                if ( count($offerta_speciale_xml) > 0 )
                {
                    // Fill dell'offerta speciale
                    $offerta = new OffertaSpeciale();
                    $offerta->data_inizio = $offerta_speciale_xml[0]->firstChild->textContent;
                    $offerta->data_fine = $offerta_speciale_xml[0]->lastChild->textContent;
                    $offerta->percentuale = $offerta_speciale_xml[0]->getAttribute('percentuale');
                    $offerta->crediti = $offerta_speciale_xml[0]->getAttribute('crediti');
                    $prodotto->offerta_speciale = $offerta;
                }
                else
                    $prodotto->offerta_speciale = null;

                // Filtro per categoria e eventuale tipologia
                if ( 
                    ( $id_categoria == '' ) ||
                    ( $id_categoria != '' && $id_tipologia == '' && $prodotto->id_categoria == $id_categoria) ||
                    ( $id_tipologia != '' && $prodotto->id_categoria == $id_categoria && $prodotto->id_tipo == $id_tipologia)
                )
                {
                    // Filtro per eventuale testo nel nome del prodotto
                    $flag = str_contains(strtolower($prodotto->nome), strtolower($contenuto_testo));
                    if ( $contenuto_testo == '' || ( $contenuto_testo != '' && $flag) )
                    {
                        // Aggiungo il prodotto corrente alla lista prodotti
                        array_push($lista_prodotti, $prodotto);
                    }
                }
            }

            return $lista_prodotti;
        }

        // Metodo per inserire un nuovo prodotto
        function inserisciProdotto($nome, $id_categoria, $id_tipologia, $prezzo_listino, $path_immagine, $specifiche, $descrizione)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return false;

            // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultimo prodotto
            $id_nuovo_prodotto = 1;
            $ultimo = $this->oggettoDOM->documentElement->lastElementChild;
            if ( $ultimo != null ) // Ci sono altre domande
            {
                $id_ultimo = $ultimo->getAttribute('id');
                $id_ultimo = intval($id_ultimo);
                $id_nuovo_prodotto = ++$id_ultimo;
                $id_nuovo_prodotto = strval($id_nuovo_prodotto);
            }

            // Creazione della nuova domanda
            $nuovo_prodotto = $this->oggettoDOM->createElement("prodotto");
            $nuovo_prodotto->setAttribute('id', $id_nuovo_prodotto);
            $nuovo_prodotto->setAttribute('id_categoria', $id_categoria);
            $nuovo_prodotto->setAttribute('id_tipo', $id_tipologia);

            $tag_nome = $this->oggettoDOM->createElement("nome", $nome);
            $tag_prezzoListino = $this->oggettoDOM->createElement("prezzo_listino", $prezzo_listino);
            $tag_path = $this->oggettoDOM->createElement("percorso_immagine", $path_immagine);
            $tag_spec = $this->oggettoDOM->createElement("specifiche", $specifiche);
            $tag_desc = $this->oggettoDOM->createElement("descrizione", $descrizione);

            $nuovo_prodotto->appendChild($tag_nome);
            $nuovo_prodotto->appendChild($tag_prezzoListino);
            $nuovo_prodotto->appendChild($tag_path);
            $nuovo_prodotto->appendChild($tag_spec);
            $nuovo_prodotto->appendChild($tag_desc);
            
            // Aggancio del prodotto
            $this->oggettoDOM->documentElement->appendChild($nuovo_prodotto); 

            // Salvo i cambiamenti sul file
            $this->salvaXML($this->pathname);

            return true;
        }

        // Metodo per rimuovere un prodotto
        function rimuoviProdotto($id_prodotto)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return false;

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista dei prodotti
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un prodotto, verifico se l'id
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                // Verifico se l'id del prodotto corrisponde
                // a quello passato
                $id = $figli[$i]->getAttribute("id");
                if ( $id == $id_prodotto )
                {
                    // Elimino le recensioni associate al prodotto
                    $gestoreRecensioni = new GestoreRecensioni();
                    $gestoreRecensioni->rimuoviRecensioni($id_prodotto);

                    // Elimino l'immagine associata al prodotto
                    $percorso_immagine = $figli[$i]->firstChild->nextSibling->nextSibling->textContent;
                    unlink($percorso_immagine);
                    
                    // Elimino il prodotto
                    $this->oggettoDOM->documentElement->removeChild($figli[$i]);
                    $this->salvaXML($this->pathname);
                    $esito = true;
                }
            }

            return $esito;
        }
    }
?>