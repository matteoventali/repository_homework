<?php
    // Script invocato tramite richiesta con tecnologia AJAX
    // per cambiare lo stato associato ad un cliente
    $esito = false;

    // Verifico di aver ricevuto i dati nel modo corretto
    if ( isset($_POST["id_utente"]) && isset($_POST["operazione"]))
    {
        // Mi connetto al database
        require_once 'libreriaDB.php';
        require_once 'connection.php';

        $id_utente = $_POST["id_utente"];
        $operazione = $_POST["operazione"];
        $nuovo_stato = "";

        if ( $connessione )
        {
            // Effettuo il parsing dell'operazione
            switch($operazione)
            {
                case "1":
                    $nuovo_stato = 'B'; break;
                case "2":
                    $nuovo_stato = 'A'; break;
                default:
                    $nuovo_stato = ''; break;
            }

            // Effettuo la chiamata alla funzione apposita
            $esito = cambiaStato($handleDB, $id_utente, $nuovo_stato);
            $handleDB->close();
        }
    }
    else
        header("Location: homepage.php");
    
    echo $esito;
?>