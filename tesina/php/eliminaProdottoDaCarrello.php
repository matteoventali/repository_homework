<?php 
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCarrelli.php';
    
    // Verifico che vi sia una richiesta di eliminazione
    if ( $sessione_attiva && $_SESSION["ruolo"] == 'C' && isset($_POST["id_prodotto"]))
    {
        $gestoreCarrelli = new GestoreCarrelli();
        $gestoreCarrelli->rimuoviProdottoDaCarrello($_SESSION["id_utente"], $_POST["id_prodotto"]);
    }
?>  