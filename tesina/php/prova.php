<?php
    require 'lib/libreriaDB.php';
    require 'lib/connection.php';

    $lista = ottieniClienti($handleDB, false, false, 're');
    var_dump($lista);
?>