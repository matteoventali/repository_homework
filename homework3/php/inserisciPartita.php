<?php
    session_start();

    // Variabile paragrafo risultato operazione
    $ris = ''; $err = true;

    // Metodo per ottenere il codice HTML necessario a mostrare
    // l'elenco delle squadre nelle tendine.
    // Riceve l'oggetto DOM che rappresenta le squadre.
    function caricaSquadre($squadre)
    {
        // Contenuto di default
        $contenutoTendine = "<option value=\"0\">Scegli squadra</option>\n";
        
        $radice = $squadre->documentElement;
        $listaSquadre = $radice->childNodes;

        // Per ogni squadra nella lista creo un'opzione della tendina
        for ( $i=0; $i < count($listaSquadre); $i++ )
        {
            $squadra = $listaSquadre[$i];
            $id = $squadra->getAttribute("id");
            $contenutoTendine = $contenutoTendine . "<option value=\"$id\">$squadra->textContent</option>\n";
        }
    
        return $contenutoTendine;
    }

    function controllaInput()
    {
        // Risultato del controllo
        $ris = false;
        $goal_casa  = $_POST["goal_casa"];
        $goal_ospite = $_POST["goal_ospite"];
        $squadra_casa = $_POST["squadra_casa"];
        $squadra_ospite = $_POST["squadra_ospite"];
        $data = $_POST["data_partita"];

        settype($squadra_casa, "int");
        settype($squadra_ospite, "int");

        // Regex di controllo
        $regGoal = '/^\d{1,2}$/';

        // Controllo sui dati
        if ( preg_match($regGoal, $goal_casa) && preg_match($regGoal, $goal_ospite)
                && $data != "" && $squadra_casa != $squadra_ospite
                && $squadra_casa > 0 && $squadra_ospite > 0)
        {
            // Verifico coerenza campi data
            $info_data = explode("-", $data);
            if ( checkdate($info_data[1], $info_data[2], $info_data[0]))
                $ris = true;
        }

        return $ris;
    }
    
    // Se non è presente una sessione attiva distruggo quella appena creata
    // e rimando l'utente alla pagina di login
    if ( !isset($_SESSION["nome"]) )
    {
        require_once 'cancellaSessione.php';
        header("Location: accedi.php");
    }
    else if ( $_SESSION["tipologia"] === "A" ) // Sessione presente per un admin
    {
        // Carico in memoria l'oggetto DOM per il file XML squadre
        require_once 'loadXMLSquadre.php';

        // Carico in memoria l'oggetto DOM per il file XML partite
        /* ....... */
        
        if ( $loadSquadre )
        {
            // Ottengo la lista delle squadre da mostrare nella pagina
            $squadre = caricaSquadre($squadre);

            // Verifico richiesta inserimento partita
            if ( isset($_POST["data_partita"]) && isset($_POST["squadra_casa"]) && isset($_POST["goal_casa"])
                && isset($_POST["squadra_ospite"]) && isset($_POST["goal_ospite"]) )
                
            {
                // Verifico che il form sia compilato
                if (strlen(trim($_POST["data_partita"])) > 0 && strlen(trim($_POST["squadra_casa"])) > 0
                            && strlen(trim($_POST["goal_casa"])) > 0 && strlen(trim($_POST["goal_ospite"])) > 0
                            && strlen(trim($_POST["squadra_ospite"])) > 0)
                {
                    $ris = '<p style="color:red">Errore nell\'esecuzione della query, ricontrollare i dati</p>'; 
                    
                    // Effettuo il controllo sui dati pervenuti
                    if ( controllaInput() )
                    {   
                        $goal_casa = $_POST["goal_casa"];
                        $goal_ospite = $_POST["goal_ospite"];
                        $squadra_casa = $_POST["squadra_casa"];
                        $squadra_ospite = $_POST["squadra_ospite"];
                        $data = $_POST["data_partita"];

                        // Aggiorno il file XML
                        
                        //$ris = '<p style="color:green">Inserimento avvenuto con successo</p>';
                        //$ris = '<p style="color:red">La partita &egrave; gi&agrave; registrata</p>';
                    }
                    else
                        $ris = '<p style="color:red">Informazioni inserite non valide</p>';     
                }
                else
                    $ris = '<p style="color:red">Campi vuoti</p>'; 
            }
            else
                $err = false;
        }
    }
    else
        header("Location: menu.php");
    
    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link type="text/css" rel="stylesheet" href="../css/stileLayout.css" />
        <link type="text/css" rel="stylesheet" href="../css/stileNuovaPartita.css" />
        <link rel="icon" type="image/png" href="../img/favicon.png" />
    </head>

    <body>
        <!-- CONTENUTO HEADER PAGINA -->
        <div class="header">
            <div class="sezioneLogo">
                <img class="logo"  alt="champions logo" src="../img/champions.png" />
            </div>
            <div class="sezioneTitolo">
                <p>CHAMPIONS LEAGUE</p>
            </div>
            <div class="sezioneControlli">
                <a class="home" href="menu.php">
                    <img  alt="home logo" src="../img/home.png" />
                </a>
            </div>
        </div>

        <!-- CONTENUTO CORPO PAGINA -->
        <div class="corpo">
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class="contenutoForm">
                    <div class="riga">
                        <p style="width:100%;">Data partita (gg/mm/aaaa) <input name="data_partita" type="text" <?php if($err) echo 'value="'. $_POST["data_partita"] . '"';?>/></p> <br />
                    </div>

                    <div class="riga">
                        <p>
                            Squadra casa
                            <select name="squadra_casa">
                                <?php if ( isset($squadre) ) echo $squadre; ?>
                            </select>
                        </p>
                        <p>Goal casa <input name="goal_casa" style="width: 15%;" type="text" <?php if($err) echo 'value="'. $_POST["goal_casa"] . '"';?>/></p> <br />
                    </div>

                    <div class="riga">
                        <p>
                            Squadra ospite
                            <select name="squadra_ospite">
                                <?php if ( isset($squadre) ) echo $squadre; ?>
                            </select>
                        </p>
                        <p>Goal ospite <input name="goal_ospite" style="width: 15%;" type="text" <?php if($err) echo 'value="'. $_POST["goal_ospite"] . '"';?>/></p> <br />
                    </div>
                    
                    <div class="rigaBottoni">
                        <div class="sezioneErrore">
                            <?php echo $ris ?>
                        </div>
                        <div class="sezioneBottoni">
                            <input type="reset" value="Cancella" />
                            <input type="submit" value="Inserisci" />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- CONTENUTO FOOTER PAGINA -->
        <div class="footer">
            <p class="copyright">&copy; 2024 Matteo Ventali &amp; Stefano Rosso</p>
        </div>
    </body>
</html>