<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'gestoriXML/gestoreCarrelli.php';

    // Verifico che vi sia una richiesta di aggiunta al carrello
    // da parte di un cliente
    if ( isset($_POST["btnAggiungiCarrello"]) && isset($_POST["id_prodotto"]) 
            && $sessione_attiva && $_SESSION["ruolo"] == 'C' )
    {
        // Procedo all'inserimento del prodotto nel carrello
        $gestoreCarrelli = new GestoreCarrelli();
        $gestoreCarrelli->aggiungiProdottoAlCarrello($_SESSION["id_utente"], $_POST["id_prodotto"]);

        // Ridireziono l'utente alla pagina del prodotto
        $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
        $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
        $esito_operazione = 'esito_carrello=true';
        $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca&$esito_operazione";
        header("Location: dettaglioProdotto.php?$query_string");
    }
    else // Ridireziono l'utente
        header("Location: homepage.php");
?>