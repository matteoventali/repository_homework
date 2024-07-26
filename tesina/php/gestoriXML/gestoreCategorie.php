<?php
    require_once 'gestoreXMLDOM.php';

    class Categoria
    {
        public $id_categoria;
        public $nome_categoria;
    }

    class Tipo
    {
        public $id_tipo;
        public $nome_tipo;
    }

    // Gestore XML DOM per il file categorieProdotti.xml
    class GestoreCategorie extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file categorieProdotti con validazione tramite dtd
            parent::__construct("../xml/documenti/categorieProdotti.xml", 0, "../xml/grammatiche/grammaticaCategorieProdotti.dtd");
        }

        // Metodo per ottenere le categorie disponibili
        // Li restituisce in un vettore
        function ottieniCategorie()
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di categorie
            $lista_categorie = [];

            // Ottengo la lista di figli della radice, ovvero la lista delle categorie
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = count($figli);

            // Per ogni figlio, ovvero una categoria, estraggo id e nome
            for ( $i=0; $i<$n_figli; $i++ )
            {
                $nuova_categoria = new Categoria();
                $nuova_categoria->id_categoria = $figli[$i]->getAttribute('id');
                $nuova_categoria->nome_categoria = $figli[$i]->firstChild->textContent;
                array_push($lista_categorie, $nuova_categoria);
            }
                
            return $lista_categorie;
        }

        // Metodo per ottenere i tipi disponibili per una certa categoria
        function ottieniTipi($id_categoria)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Lista di tipi per la categoria ricevuta come parametro
            $lista_tipi = [];

            // Ottengo la lista di figli della radice, ovvero la lista delle categorie
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = count($figli);

            // Per ogni figlio, ovvero una categoria, verifico che la categoria
            // corrisponde a quella ricevuta come parametro
            $trovata = false;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                if ( $figli[$i]->getAttribute('id') == $id_categoria )
                    $trovata = true;
            }
            
            // Se ho trovato la categoria
            if ( $trovata )
            {
                $i--;

                // Scansiono la lista dei tipi contenuta nella categoria trovata
                $tipi = $figli[$i]->firstChild->nextSibling->childNodes;
                $n_tipi = count($tipi);
                for ( $j=0; $j < $n_tipi; $j++ )
                {
                    // Creazione del nuovo tipo
                    $nuovo_tipo = new Tipo();
                    $nuovo_tipo->id_tipo = $tipi[$j]->getAttribute('id');
                    $nuovo_tipo->nome_tipo = $tipi[$j]->textContent;

                    array_push($lista_tipi, $nuovo_tipo);
                }
            }

            return $lista_tipi;
        }
    }
?>