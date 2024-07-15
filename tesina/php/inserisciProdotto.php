<?php
    require_once 'lib/verificaSessioneAttiva.php';
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreCategorie.php';
    require_once 'gestoriXML/gestoreCatalogoProdotti.php';
    
    // Gestore categorie
    $gestoreCategorie = new GestoreCategorie();
    $categorie = $gestoreCategorie->ottieniCategorie();

    // Gestore catalogo
    $gestoreCatalogo = new GestoreCatalogoProdotti();

    // Variabili utili all'identificazione dell'errore
    $mostraPopup = false; $msg = ''; $err = false;

    // Verifico che vi sia una sessione attiva per un gestore
    if ( $sessione_attiva && $_SESSION["ruolo"] == 'G')
    {
        // Verifico se sia stato premuto il tasto indietro
        if ( isset($_POST["btnIndietro"]) )
            // Ridireziono l'utente sulla homepage del catalogo
            header("Location: homepageCatalogo.php");
        else
        {
            // Verifico se è pervenuta una richiesta di inserimento prodotto
            if ( isset($_POST["btnInserisci"]) && isset($_POST["nome"]) && isset($_POST["prezzoListino"])
                   && isset($_POST["specifiche"]) && isset($_POST["descrizione"])
                   && isset($_FILES["immagine"]) && isset($_POST["id_tipologia"]) && isset($_POST["id_categoria"]))
            {
                // Effettuo il controllo sui campi
                $nome = trim($_POST["nome"]);
                $prezzo = trim($_POST["prezzoListino"]);
                $specifiche = trim($_POST["specifiche"]);
                $descrizione = trim($_POST["descrizione"]);
                $categoria = $_POST["id_categoria"];
                $tipologia = $_POST["id_tipologia"];
                $img = $_FILES["immagine"];

                // Verifico che i campi siano compilati
                if ( strlen($nome) > 0  && strlen($prezzo) > 0 && strlen($specifiche) > 0
                        && $categoria != '0' && $tipologia != '0' && $img["size"] > 0 )
                {
                    // Verifico che il prezzo sia un numero intero (unita' di misura crediti)
                    if ( $prezzo = intval($prezzo) )
                    {
                        // Procedo a salvare la foto nella directory per le immagini catalogo
                        $path = '../img/immagini_prodotti/' . $img["name"];
                        copy($img["tmp_name"], $path);

                        // Procedo quindi ad inserire il prodotto tramite apposito metodo
                        // del gestore catalogo
                        if ( !$gestoreCatalogo->inserisciProdotto($nome, $categoria, $tipologia, $prezzo, $path, $specifiche, $descrizione))
                            $msg = 'Inserimento fallito';
                        else
                            header("Location: homepageCatalogo.php");
                    }
                    else
                    {
                        $msg = 'Prezzo non valido';
                        $mostraPopup = true;
                        $err = true;
                    }
                        
                }
                else
                {
                    $msg = 'Campi vuoti';
                    $mostraPopup = true;
                    $err = true;
                }
            }
        }
    }
    else
        header("Location: homepage.php");

    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileInserisciProdotto.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Import della navbar
            // Visualizzo nome dell'utente e il tasto "Logout"
            $nav = file_get_contents("../html/strutturaNavbarUtenti.html");
            $nav = str_replace("%NOME_UTENTE%", $_SESSION["nome"] . " " . $_SESSION["cognome"], $nav);
            echo $nav ."\n";

            // Import della sidebar e mostro solo le opzioni dell'admin
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", ottieniOpzioniMenu($_SESSION["ruolo"]), $sidebar);
            echo $sidebar . "\n";
        ?>

        <div id="sezioneCentrale">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <form id="formRegistrazione" method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>" enctype="multipart/form-data">
                <fieldset>  <p>Nome:                </p>  <input type="text" name="nome" <?php if($err) echo 'value="'. $nome . '"';?>/>    </fieldset>
                <fieldset>  <p>Prezzo di listino:   </p>  <input type="text" name="prezzoListino" <?php if($err) echo 'value="'. $prezzo . '"';?>/> </fieldset>
                <fieldset>  <p>Categoria</p>
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
                        <option value='0' selected="selected">Seleziona tipologia</option>
                    </select> 
                </fieldset>

                <fieldset>  <p>Specifiche:          </p>  <textarea id="prova" rows="6" cols="45" name="specifiche"><?php if($err) echo $specifiche;?></textarea> </fieldset>
                <fieldset>  <p>Descrizione:         </p>  <textarea rows="6" cols="45" name="descrizione"><?php if($err) echo $descrizione;?></textarea> </fieldset>
                <fieldset>  <p>Immagine:            </p>  <input type="file" name="immagine" accept="image/png, image/jpeg"/></fieldset>
                <fieldset style="border-style: none; box-shadow: none;">
                    <input type="submit" value="Indietro &#8617;" name="btnIndietro" />
                    <input type="button" value="Cancella" name="btnCancella" onclick="azzeraFormRegistrazione();" />
                    <input type="submit" value="Inserisci" name="btnInserisci" /> 
                </fieldset>
            </form>
        </div>
    </body>
</html>