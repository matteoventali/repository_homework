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
    $popolazione_preliminare = false;

    // Verifico che vi sia una sessione attiva per un gestore e che esista un prodotto
    if ( $sessione_attiva && $_SESSION["ruolo"] == 'G' && isset($_POST["id_prodotto"]) )
    {
        // Conversione categoria e tipologia
        $prodotto = $gestoreCatalogo->ottieniProdotto($_POST["id_prodotto"]);
        $categoria = $categorie[intval($prodotto->id_categoria) - 1]->nome_categoria;
        $tipologia = $gestoreCategorie->ottieniTipi($prodotto->id_categoria)[intval($prodotto->id_tipo) - 1]->nome_tipo;

        // Verifico se sia stato premuto il tasto indietro
        if ( isset($_POST["btnIndietro"]) )
        {
            // Ridireziono l'utente sulla pagina del prodotto
            $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
            $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
            $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca";
            header("Location: dettaglioProdotto.php?$query_string");
        }
        else
        {
            // Verifico se c'è una richiesta di modifica prodotto
            // Nel caso in cui non sia stata caricata una nuova immagine si procede
            // comunque alla modifica
            if ( isset($_POST["btnModifica"]) && isset($_POST["nome"]) && isset($_POST["prezzoListino"])
                   && isset($_POST["specifiche"]) && isset($_POST["descrizione"]) )
            {
                // Prelevo i dati dal post
                $nome = trim($_POST["nome"]);
                $prezzoListino = trim($_POST["prezzoListino"]);
                $specifiche = trim($_POST["specifiche"]); 
                $descrizione = trim($_POST["descrizione"]);

                // Verifico che tutti i dati siano presenti
                if ( strlen($nome) > 0 && strlen($prezzoListino) > 0 && strlen($specifiche) > 0 && strlen($descrizione) > 0 )
                {
                    // Verifico che il prezzo listino sia un intero
                    $prezzoListino = intval($prezzoListino);
                    
                    if ( $prezzoListino > 0 )
                    {
                        // Verifico se e' presente anche una nuova immagine in caso la sostituisco
                        $immagine = $_FILES["immagine"];
                        if ( $immagine["name"] != "" )
                            copy($immagine["tmp_name"], $prodotto->percorso_immagine);
                        
                        // Procedo alla modifica dei dati del prodotto
                        $gestoreCatalogo->modificaProdotto($_POST["id_prodotto"], $nome, $prezzoListino, $specifiche, $descrizione);

                        // Ridireziono l'utente sulla pagina del prodotto
                        $id_categoria = $_POST['id_categoria']; $id_tipologia = $_POST['id_tipologia'];
                        $contenuto_ricerca = $_POST['contenutoRicerca']; $id_prodotto = $_POST["id_prodotto"];
                        $query_string = "id_prodotto=$id_prodotto&id_categoria=$id_categoria&id_tipologia=$id_tipologia&contenutoRicerca=$contenuto_ricerca";
                        header("Location: dettaglioProdotto.php?$query_string");
                    }
                    else
                    {
                        $mostraPopup = true;
                        $err = true;
                        $msg = 'Prezzo di listino non valido'; 
                    }
                }
                else
                {
                    // Campi vuoti
                    $mostraPopup = true;
                    $err = true;
                    $msg = 'Campi vuoti';
                }
            }
            else
            {
                // Devo aver trovato il prodotto altrimenti ridireziono l'utente
                if ( $prodotto->id != null )
                {
                    $popolazione_preliminare = true;
                    $nome = $prodotto->nome;
                    $prezzoListino = $prodotto->prezzo_listino;
                    $specifiche = $prodotto->specifiche;
                    $descrizione = $prodotto->descrizione;
                    $percorso = $prodotto->percorso_immagine;
                }
                else
                    header("Location: homepage.php");
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

            <form id="formInserimentoProdotto" method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>" enctype="multipart/form-data">
                <fieldset>  <p>Nome:                </p>  <input type="text" name="nome" <?php if($err || $popolazione_preliminare ) echo 'value="'. $nome . '"';?>/>    </fieldset>
                <fieldset>  <p>Prezzo di listino:   </p>  <input type="text" name="prezzoListino" <?php if($err || $popolazione_preliminare ) echo 'value="'. $prezzoListino . '"';?>/> </fieldset>
                <fieldset>  <p>Categoria:           </p>  <input type="text" disabled="disabled" name="categoria" <?php if($err || $popolazione_preliminare) echo 'value="'. $categoria . '"';?>/> </fieldset>
                <fieldset>  <p>Tipologia:           </p>  <input type="text" disabled="disabled" name="tipologia" <?php if($err || $popolazione_preliminare) echo 'value="'. $tipologia . '"';?>/> </fieldset>
                <fieldset>  <p>Specifiche:          </p>  <textarea id="prova" rows="6" cols="45" name="specifiche"><?php if($err || $popolazione_preliminare) echo $specifiche;?></textarea> </fieldset>
                <fieldset>  <p>Descrizione:         </p>  <textarea rows="6" cols="45" name="descrizione"><?php if($err || $popolazione_preliminare) echo $descrizione;?></textarea> </fieldset>
                <fieldset>  <p>Immagine:            </p>  <input type="file" name="immagine" accept="image/png, image/jpeg"/></fieldset>
                <fieldset style="border-style: none; box-shadow: none;">
                    <input type="hidden" value="<?php echo $_POST['id_categoria']; ?>" name="id_categoria" />
                    <input type="hidden" value="<?php echo $_POST['id_tipologia']; ?>" name="id_tipologia" />
                    <input type="hidden" value="<?php echo $_POST['contenutoRicerca']; ?>" name="contenutoRicerca" />
                    <input type="hidden" name="id_prodotto" value="<?php echo $_POST["id_prodotto"]; ?>" />
                    <input type="submit" value="Indietro &#8617;" name="btnIndietro" />
                    <input type="button" value="Cancella" name="btnCancella" onclick="azzeraFormInserimentoProdotto();" />
                    <input type="submit" value="Modifica" name="btnModifica" /> 
                </fieldset>
            </form>
        </div>
    </body>
</html>