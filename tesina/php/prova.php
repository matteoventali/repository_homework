<?php
    require 'gestoriXML/gestoreDomande.php';
    require 'gestoriXML/gestoreRisposte.php';

    $g = new GestoreDomande();
    $g1 = new GestoreRisposte();

    $id_d = $g->inserisciDomanda('Nuova Domanda?', "1", "true");
    $g1->inserisciRisposta('Si', "1", "true", $id_d);
?>