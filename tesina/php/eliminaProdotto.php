<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // Verifico che la richiesta sia effettuata 
    // da un gestore
    if ( $_SESSION["ruolo"] == 'G' )
    {
        // Verifico che vi sia un prodotto da eliminare
        if ( isset($_POST["id_prodotto"]) )
        {
            // Alloco il gestore catalogo e procedo alla sua eliminazione
            $gestoreCatalogo = new GestoreCatalogoProdotti();
            $gestoreCatalogo->rimuoviProdotto($_POST["id_prodotto"]);
        }
        
        // Redireziono l'utente alla homepage del catalogo    
        header("Location: homepageCatalogo.php");
    }
?>