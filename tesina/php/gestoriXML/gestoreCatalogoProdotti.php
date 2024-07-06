<?php
    require_once 'gestoreXMLDOM.php';

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
    }

?>