<?php
    require_once 'gestoreXMLDOM.php';

    // Gestore XML DOM per il file portafogliBonus.xml
    class GestorePortafogliBonus extends GestoreXMLDOM
    {
        // Costruttore che chiama quello della classe padre
        function __construct()
        {
            // Apertura del file portafogli bonus con validazione tramite schema
            parent::__construct("../xml/documenti/portafogliBonus.xml", 1, "../xml/schema/schemaPortafogliBonus.xsd");
        }

        // Metodo per creare un nuovo portafoglio bonus vuoto nel file
        // riceve l'id del cliente da associare al portafoglio bonus
        function aggiungiNuovoPortafoglioBonus($id_cliente)
        {
            // Esito dell'operazione
            $esito = false;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return $esito;

            // Qui sono sicuro di poter utilizzare il file
            // Creazione del nuovo portafoglio bonus
            $nuovo_portafoglio = $this->oggettoDOM->createElement('portafoglio');
            $nuovo_portafoglio->setAttribute('totale', '0');
            $nuovo_portafoglio->setAttribute('id_cliente', $id_cliente);

            // Inizializzo il portafoglio con un saldo a 0 di crediti bonus
            // per l'anno corrente
            $anno_corrente = date("Y");
            $saldo_anno_corrente = $this->oggettoDOM->createElement('saldo', '0');
            $saldo_anno_corrente->setAttribute('anno', $anno_corrente);

            // Il saldo e' figlio di portafoglio bonus
            $nuovo_portafoglio->appendChild($saldo_anno_corrente);

            // Aggiungo il portafoglio
            $this->oggettoDOM->documentElement->appendChild($nuovo_portafoglio);
            
            // Salvataggio delle modifiche sul file
            $this->salvaXML($this->pathname);
            $esito = true;

            return $esito;
        }

        // Metodo per ricevere il saldo del portafoglio bonus di un utente
        function ottieniSaldoPortafoglioBonus($id_cliente)
        {
            // Saldo del portafoglio
            $saldo = 0;
            
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return $saldo;

            // Ottengo la lista di figli della radice, ovvero la lista dei portafogli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Scorro il file finche' non raggiungo il portafoglio da aggiornare
            $trovata = false;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                if ( $figli[$i]->getAttribute('id_cliente') == $id_cliente )
                {
                    $trovata = true;

                    // Ogni figlio del portafoglio e' un saldo relativo ad un certo anno
                    $saldi = $figli[$i]->childNodes; $n_saldi = count($saldi);
                    for ( $j=0; $j < $n_saldi; $j++ )
                    {
                        // Aggiungo i crediti al saldo
                        $saldo += $saldi[$j]->textContent;
                    }
                }
            }

            return $saldo;
        }

        // Metodo per calcolare il massimo numero di crediti utilizzabili 
        // in un determinato acquisto, di cui viene passato il sub-totale senza considerare le offerte speciali.
        // Lo sconto massimo e' del 20% del sub-totale (vedi documento)
        function ottieniCreditiMassimi($id_cliente, $sub_totale)
        {
            // Verifico che il file sia utilizzabile
            if ( !$this->checkValidita() )
                return 0;

            $crediti_max = 0;
            $sub_totale = intval($sub_totale);

            // Ottengo la lista di figli della radice, ovvero la lista dei portafogli
            $figli = $this->oggettoDOM->documentElement->childNodes;
            $n_figli = $this->oggettoDOM->documentElement->childElementCount;

            // Scorro il file finche' non raggiungo il portafoglio da analizzare
            $trovata = false;
            for ( $i=0; $i<$n_figli && !$trovata; $i++ )
            {
                if ( $figli[$i]->getAttribute('id_cliente') == $id_cliente )
                    $trovata = true;
            }

            if ( $trovata )
            {
                $i--;

                // Portafoglio da analizzare
                $saldi = $figli[$i]->childNodes;
                $n_saldi = count($saldi);

                $crediti_disponibili = $this->ottieniSaldoPortafoglioBonus($id_cliente);
                $sconto_corrente = 0;
                $crediti_utilizzabili = 0;
                $sconto_massimo_raggiunto = false;

                $anno_corrente = intval(date('Y'));

                // Finche' non ho applicato uno sconto del 20%
                while ( !$sconto_massimo_raggiunto && $crediti_disponibili > 0 )
                {
                    for ( $j=0; !$sconto_massimo_raggiunto && $j < $n_saldi; $j++ )
                    {
                        // Calcolo dei crediti massimi disponibili nel saldo corrente
                        $crediti_saldo_corrente = intval($saldi[$j]->textContent);

                        // Calcolo il peso dei crediti del saldo corrente
                        $anno_saldo = intval($saldi[$j]->getAttribute('anno'));
                        $peso = 0;
                        switch ( $anno_corrente - $anno_saldo )
                        {
                            case 0:
                                // Anno corrente
                                $peso = 0.8; break;
                            
                            case 1:
                                // Anno precedente
                                $peso = 0.9; break;
                            
                            case 2:
                                // Due anni precedenti
                                $peso = 1; break;
                            
                            default:
                                $peso = 2; break;
                        }

                        // Analizzo ogni credito del saldo corrente
                        while ( $crediti_saldo_corrente > 0 && !$sconto_massimo_raggiunto )
                        {
                            $sconto_corrente = $sconto_corrente + 1/$peso;
                            
                            // Verifico di non aver superato il 20%
                            if ( $sconto_corrente > 0.2 * $sub_totale )
                                $sconto_massimo_raggiunto = true;
                            else
                                $crediti_utilizzabili++;

                            // Decremento i crediti disponibili dal saldo corrente
                            $crediti_saldo_corrente--;
                            $crediti_disponibili--;
                        }
                    }
                }

                $crediti_max = $crediti_utilizzabili;
            }

            return $crediti_max;
        }
    }
?>