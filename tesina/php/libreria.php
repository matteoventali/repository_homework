<?php
    /* Funzioni di libreria da utilizzare al bisogno */

    // Ottieni l'url per uno sfondo tra quelli disponibili
    function ottieniURLSfondo()
    {
        $nome_file = ["smartphone.jpg", "console.jpg", "elettrodomestici.jpg", "laptop.png", "televisore.jpg"];
        $scelta_casuale = rand(0,4);
    
        $url = '../img/background/' . $nome_file[$scelta_casuale];
        return $url;
    }
?>