<?php
    require 'gestoriXML/gestoreCategorie.php';

    $g = new GestoreCategorie();
    var_dump($g->ottieniCategorie());
    var_dump($g->ottieniTipi("3"));
?>