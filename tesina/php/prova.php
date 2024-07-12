<?php
    require 'gestoriXML/gestoreCatalogoProdotti.php';

    $g = new GestoreCatalogoProdotti();
    var_dump($g->ricercaProdotti('1', '', ''));
?>