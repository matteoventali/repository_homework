<?php
    require 'gestoriXML/gestorePortafogliBonus.php';

    $g = new GestorePortafogliBonus();
    var_dump($g->ottieniCreditiMassimi('3', '100'));
?>