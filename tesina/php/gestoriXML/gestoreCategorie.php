<?php
    require_once 'gestoreXMLDOM.php';

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
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una categoria, estraggo importo e crediti
            for ( $i=0; $i<$n_figli; $i++ )
                array_push($lista_categorie, $figli[$i]->firstChild->textContent);
            
            return $lista_categorie;
        }
    }
?>