<?php
    require_once 'gestoreXMLDOM.php';

    // Gestore XML DOM per il file carrello.xml
    class GestoreCarrelli extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file carrelli con validazione tramite schema
            parent::__construct("../xml/documenti/carrelli.xml", 1, "../xml/schema/schemaCarrelli.xsd");
        }

        // Metodo per creare un nuovo carrello vuoto nel file
        // riceve l'id del cliente da associare al carrello
        function aggiungiNuovoCarrello($id_cliente)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return $esito;

            // Qui sono sicuro di poter utilizzare il file
            // Creazione del nuovo carrello
            $nuovo_carrello = $this->oggettoDOM->createElement('carrello');
            $nuovo_carrello->setAttribute('id_cliente', $id_cliente);

            // Aggiunta del carrello al file
            $this->oggettoDOM->documentElement->appendChild($nuovo_carrello);

            // Salvataggio delle modifiche sul file
            $this->salvaXML($this->pathname);
            $esito = true;

            return $esito;
        }

        // Metodo per aggiungere un nuovo prodotto al carrello
        // di un utente di cui viene passato l'id
        function aggiungiProdottoAlCarrello($id_cliente, $id_prodotto)
        {
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return false;

            // Variabile per ottimizzare il ciclo
            $trovato = false;

            // Ottengo la lista di figli della radice, ovvero la lista dei carrelli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            for ( $i=0; $i < $n_figli && !$trovato; $i++ )
            {
                // Verifico corrispondenza sull'id del cliente
                if ( $figli[$i]->getAttribute('id_cliente') == $id_cliente )
                {
                    // Aggiungo il prodotto al carrello
                    $nuovo_prodotto = $this->oggettoDOM->createElement('prodotto');
                    $nuovo_prodotto->setAttribute('id_prodotto', $id_prodotto);
                    $figli[$i]->appendChild($nuovo_prodotto);

                    // Salvo i cambiamenti sul file
                    $this->salvaXML($this->pathname);
                    $trovato = true;
                }
            }
        }

        // Metodo per ottenere gli id dei prodotti in un carrello
        function ottieniProdottiCarrello($id_cliente)
        {
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return null;

            // Variabile per ottimizzare il ciclo
            $trovato = false;

            // Lista di prodotti
            $lista_prodotti = [];

            // Ottengo la lista di figli della radice, ovvero la lista dei carrelli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            for ( $i=0; $i < $n_figli && !$trovato; $i++ )
            {
                // Verifico corrispondenza sull'id del cliente
                if ( $figli[$i]->getAttribute('id_cliente') == $id_cliente )
                {
                    // Ottengo la lista dei prodotti dal file xml
                    $lista = $figli[$i]->childNodes;
                    $n_prod = 0;
                    if ( $lista != null )
                        $n_prod = count($lista);
                    
                    // Per ogni prodotto aggiungo l'id alla lista da resituire
                    for ( $j=0; $j < $n_prod; $j++ )
                        array_push($lista_prodotti, $lista[$j]->getAttribute('id_prodotto'));
                }
            }

            return $lista_prodotti;
        }

        // Metodo per rimuovere un prodotto da un determinato carrello
        // Rimuove UN PRODOTTO ALLA VOLTA
        function rimuoviProdottoDaCarrello($id_cliente, $id_prodotto)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return false;

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista dei carrelli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero un carrello, verifico se l'id del cliente
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                if ( $figli[$i]->getAttribute('id_cliente') == $id_cliente )
                {
                    // Trovo la prima corrispondenza del prodotto e la rimuovo
                    $lista = $figli[$i]->childNodes;
                    $n_prod = 0; $trovato = false;
                    if ( $lista != null )
                        $n_prod = count($lista);

                    for ( $j=0; $j < $n_prod && !$trovato; $j++ )
                    {
                        if ( $lista[$j]->getAttribute('id_prodotto') == $id_prodotto )
                        {
                            $trovato = true;
                            $figli[$i]->removeChild($lista[$j]);

                            // Salvo i cambiamenti
                            $this->salvaXML($this->pathname);
                        }
                    }
                }
            }

            return $esito;
        }
    } 
?>