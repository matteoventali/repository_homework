<?php
    require_once 'gestoriXML/gestoreCategorie.php';

    // Verifico che abbia ricevuto dal POST l'id della categoria
    // da cui estrarre le tipologie
    if ( isset($_POST["id_categoria"]))
    {
        // Tramite il gestore estraggo le tipologie
        $gestore_categorie = new GestoreCategorie();
        $tipologie = $gestore_categorie->ottieniTipi($_POST["id_categoria"]);

        // Invio al client le tipologie in formato JSON
        echo json_encode($tipologie);
    }
    else echo json_encode('');
?>