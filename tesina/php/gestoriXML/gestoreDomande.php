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
        public $id_utente;
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

        // Metodo per ottenere una domanda
        // Riceve come parametro l'id della domanda
        function ottieniDomanda($id_domanda)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Inizialmente domanda e' vuota
            $domanda = "";

            // Variabile per ottimizzare il ciclo
            $esito = false;

            // Ottengo la lista di figli della radice, ovvero la lista delle domande
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Per ogni figlio, ovvero una domanda, verifico se l'id
            // corrisponde a quello passato come parametro
            for ( $i=0; $i<$n_figli && !$esito; $i++ )
            {
                // Verifico se l'id della domanda corrisponde
                // a quello passato
                $id = $figli[$i]->getAttribute("id");
                if ( $id == $id_domanda )
                {
                    // Creo una nuova domanda
                    $domanda = new Domanda();

                    // Estraggo le informazioni dalla domanda
                    $domanda->id = $figli[$i]->getAttribute("id");
                    $domanda->data = $figli[$i]->getAttribute("data");
                    $domanda->id_utente = $figli[$i]->getAttribute("id_utente");
                    $domanda->contenuto = $figli[$i]->firstChild->textContent;
                    
                    // Estraggo le valutazioni dalla domanda
                    $lista_valutazioni = [];
                    $valutazioni = $figli[$i]->lastChild->childNodes;
                    $n_valutazioni = $valutazioni->length;
                    for ( $j=0; $j<$n_valutazioni; $j++ )
                    {
                        $nuova_valutazione = new ValutazioneDomanda();
                        $nuova_valutazione->peso = $valutazioni[$j]->getAttribute('peso');
                        $nuova_valutazione->rating = $valutazioni[$j]->getAttribute('rating');
                        $nuova_valutazione->id_utente = $valutazioni[$j]->getAttribute('id_utente');

                        // Aggiungo la valutazione alla lista delle valutazioni
                        array_push($lista_valutazioni, $nuova_valutazione);
                    }

                    // Aggiungo la lista delle valutazioni alla domanda
                    $domanda->valutazioni = $lista_valutazioni;

                    $esito = true;
                }
            }

            return $domanda;
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

        // Metodo per inserire una domanda
        // Riceve il contenuto, l'utente che effettua la domanda
        // e se la domanda fa parte delle faq o meno.
        // Ritorna l'id della domanda appena inserita o altrimenti null in caso di errore
        function inserisciDomanda($cont, $id_utente, $faq)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Ottengo l'id dell'ultimo figlio della radice, ovvero dell'ultima domanda
            $id_nuova_domanda = 1;
            $ultima = $this->oggettoDOM->documentElement->lastElementChild;
            if ( $ultima != null ) // Ci sono altre domande
            {
                $id_ultima = $ultima->getAttribute('id');
                $id_ultima = intval($id_ultima);
                $id_nuova_domanda = ++$id_ultima;
                $id_nuova_domanda = strval($id_nuova_domanda);
            }

            // Creazione della nuova domanda
            $nuova_domanda = $this->oggettoDOM->createElement("domanda");
            $nuova_domanda->setAttribute("id", $id_nuova_domanda);
            $nuova_domanda->setAttribute("faq", $faq);
            $nuova_domanda->setAttribute("data", date("Y-m-d"));
            $nuova_domanda->setAttribute("id_utente", $id_utente);
            $contenuto_nuova_domanda = $this->oggettoDOM->createElement("contenuto", $cont);
            $lista_valutazioni_nuova_domanda = $this->oggettoDOM->createElement("valutazioni");

            // Inserisco il contenuto e la lista vuota di valutazioni come figli della domanda
            $nuova_domanda->appendChild($contenuto_nuova_domanda);
            $nuova_domanda->appendChild($lista_valutazioni_nuova_domanda);

            // Aggancio della domanda
            $this->oggettoDOM->documentElement->appendChild($nuova_domanda); 

            // Salvo i cambiamenti sul file
            $this->salvaXML($this->pathname);

            return $id_nuova_domanda;
        }

        // Metodo per ottenere la valutazione associata ad una specifica domanda
        // effettuata da un utente, se esiste. In caso negativo, restituisce null
        function ottieniValutazione($id_domanda, $id_utente)
        {
            // Verifico se posso usare il file
            if ( !$this->checkValidita() )
                return null;

            // Tento di ottenere la domanda tramite metodo opportuno gia' implementato
            $domanda = $this->ottieniDomanda($id_domanda);

            // Se la domanda esiste procedo
            $val = null;
            if ( $domanda != "" )
            {
                // Estraggo la lista delle valutazioni dalla domanda
                $valutazioni = $domanda->valutazioni;
                $n_valutazioni = count($valutazioni);
                for ( $i=0; $i<$n_valutazioni && $val == null; $i++ )
                {
                    // Verifico se l'utente coincide
                    if ( $valutazioni[$i]->id_utente == $id_utente)
                    {
                        // Alloco la nuova valutazione
                        $val = new ValutazioneDomanda();
                        $val->id_utente = $valutazioni[$i]->id_utente;
                        $val->peso = $valutazioni[$i]->peso;
                        $val->rating = $valutazioni[$i]->rating;
                    }
                }
            }
            return $val;
        }

        // Metodo per inserire una valutazione nel file domande
        // Riceve l'utente che effettua la valutazione e la domanda di riferimento
        function inserisciNuovaValutazione($id_domanda, $id_utente)
        {

        }    
    }
?>