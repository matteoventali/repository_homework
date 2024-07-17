<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // Verifico che la richiesta sia effettuata 
    // da un gestore
    if ( $_SESSION["ruolo"] == 'G' )
    {
        // Verifico che vi sia un prodotto da gestire
        if ( isset($_POST["id_prodotto"]) && (isset($_POST["btnMostra"]) || isset($_POST["btnNascondi"])) )
        {
            // Alloco il gestore catalogo e procedo alla sua gestione
            $gestoreCatalogo = new GestoreCatalogoProdotti();

            // Discrimino se nascondere o mostrare il prodotto
            if ( isset($_POST["btnMostra"]))    
                $gestoreCatalogo->mostraProdotto($_POST["id_prodotto"]);
            else if ( isset($_POST["btnNascondi"]) )
                $gestoreCatalogo->nascondiProdotto($_POST["id_prodotto"]);
        }

        // Ridireziono il gestore alla pagina del prodotto
        // Redireziono l'utente alla pagina del prodotto
        $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
        $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
        $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca";
        header("Location: dettaglioProdotto.php?$query_string");
    }
    else
        header("Location: homepage.php");
?>