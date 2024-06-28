<?php
    require 'gestoriXML/gestoreRichiesteRicariche.php';

    $g = new GestoreRichiesteRicariche();

    var_dump($g->ottieniRichiestiRicaricheDaGestire());
?>