<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreCategorie.php';

    // Gestore per popolare categorie e tipi
    $gestoreCategorie = new GestoreCategorie();

    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileHomepageCatalogo.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Bisogna controllare se l'utente è loggato oppure no
            // In base a questo avrà diversi tipi di visualizzazione
            if($sessione_attiva)
            {
                // In questo caso l'utente è loggato

                // Import della navbar
                // Visualizzo nome dell'utente e il tasto "Logout"
                $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
                $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
                echo $nav ."\n";

                // Import della sidebar
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
                echo $sidebar . "\n";

                // L'opzione di aggiungere una nuova faq deve essere fornita
                // esclusivamente ad admin e gestori
                if ( $_SESSION["ruolo"] == "A" || $_SESSION["ruolo"] == "G" )
                    $visibilita_bottone = "block";
            }
            else 
            {
                // Qui l'utente non è loggato

                // Import della navbar
                $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
                echo $nav ."\n";

                // Import della sidebar e mostro solo le opzioni del visitatore
                $sidebar = file_get_contents("../html/strutturaSidebar.html");
                $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu('V'), $sidebar);
                echo $sidebar . "\n";
            }
            
        ?>

        <div id="sezioneCentrale">
            <div id="sezioneRicerca">
                <form id="ricercaProdotti" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset><p>Categoria</p>
                        <select name="id_categoria" onchange="ottieniTipologie(this)">
                            <option value='0' selected="selected">Seleziona categoria</option>
                            <?php
                                // Stampa delle categorie disponibili
                                $categorie = $gestoreCategorie->ottieniCategorie();
                                $n_categorie = count($categorie);

                                // Popolo la tendina
                                for ( $i=0; $i<$n_categorie; $i++ )
                                {
                                    $nome_cat = $categorie[$i]->nome_categoria;
                                    $id_cat = $categorie[$i]->id_categoria;
                                    echo "<option value=\"$id_cat\">$nome_cat</option>" . "\n";
                                }
                            ?>
                        </select>
                    </fieldset>
                    <fieldset><p>Tipologia</p>
                        <select name="id_tipologia" id="tendinaTipologia">
                            <option  selected="selected">Seleziona tipologia</option>
                        </select> 
                    </fieldset>
                    <fieldset><p>Ricerca</p><input type="text" name="contenutoRicerca" /></fieldset>
                    <fieldset><input type="submit" name="btnRicerca" value="Cerca &#128269;" /></fieldset>
                    <fieldset><input type="reset" name="btnIndietro" onclick="azzeraRicercaProdotti();" value="Reset &#8634;" /></fieldset>
                </form>
            </div>
            <div id="sezioneCategorie">
                <?php
                    $categorie_html = '';

                    // Frammento categoria vuoto
                    $frammento_vuoto = file_get_contents('../html/frammentoCategoria.html');

                    // Per ogni categoria creo un div
                    for ( $i=0; $i<$n_categorie; $i++ )
                    {
                        $frammento_pieno = str_replace("%NOME_CATEGORIA%", $categorie[$i]->nome_categoria, $frammento_vuoto);
                        $frammento_pieno = str_replace("%ID_CATEGORIA%", $categorie[$i]->id_categoria, $frammento_pieno);
                        $frammento_pieno = str_replace("%PATH_ICONA%", ottieniPathIcona($categorie[$i]->id_categoria), $frammento_pieno);
                        $categorie_html .= $frammento_pieno . "\n";
                    }

                    echo $categorie_html . "\n";
                ?>
            </div>
        </div>
    </body>
</html>