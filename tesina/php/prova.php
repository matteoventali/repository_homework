<?php
    require 'gestoriXML/gestoreRisposte.php';

    $g = new GestoreRisposte();

    var_dump($g->ottieniRisposte("1","false"));
?>