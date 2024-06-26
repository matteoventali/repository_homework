<?php
    require_once 'lib/libreria.php';
    require_once 'gestoriXML/gestoreCarrelli.php';
    require_once 'gestoriXML/gestorePortafogliBonus.php';
    
    // Variabile per verificare se il popup vada mostrato
    $mostraPopup = true;

    // Variabili utili all'identificazione dell'errore
    $msg = ''; $err = true;

    // Verifico se vi e' una sessione aperta per account attivo
    require_once 'lib/verificaSessioneAttiva.php';
    
    if ( $sessione_attiva )
        header("Location: area_riservata.php");
    else if( isset($_POST["nome"]) && isset($_POST["cognome"]) && isset($_POST["citta"]) 
            && isset($_POST["indirizzo"]) && isset($_POST["mail"]) && isset($_POST["username"])
             && isset($_POST["password"])) // Vi e' una richiesta di registrazioone
    {   
        // Verifico che i campi siano stati riempiti
        if ( strlen(trim($_POST["nome"])) > 0 && strlen(trim($_POST["cognome"])) > 0 &&
            strlen(trim($_POST["indirizzo"])) > 0 && strlen(trim($_POST["citta"])) > 0 &&
            strlen(trim($_POST["mail"])) > 0 && strlen(trim($_POST["username"])) > 0 &&
            strlen(trim($_POST["password"])) > 0)
        {
            // Arrivato qui, ho la certezza che i campi non sono vuoti
            // Elimino la sessione appena creata erroneamente
            require_once 'lib/cancellaSessione.php';

            // Effettuo la connessione al database
            require_once 'lib/connection.php';

            // Verifico che la connessione sia andata a buon fine
            if ( $connessione )
            {
                $msg = 'Errore nell\'esecuzione della query, ricontrollare i dati'; 

                // Verifico che i dati siano corretti

                // Regex per il controllo della mail e del cap
                $regex_mail = '/^([a-z]+)(_|[.]|-){0,1}(([a-z]|\d)+)@([a-z])*[.]([a-z])*$/';
                $regex_cap = '/^\d\d\d\d\d$/';
                $email_valida = preg_match($regex_mail, $_POST["mail"]);
                $cap_valido = preg_match($regex_cap, $_POST["cap"]);

                if ( $email_valida && $cap_valido )
                {
                    // Prelevo i dati dal post
                    $nome = $handleDB->real_escape_string($_POST["nome"]);
                    $cognome = $handleDB->real_escape_string($_POST["cognome"]);
                    $indirizzo = $handleDB->real_escape_string($_POST["indirizzo"]);
                    $citta = $handleDB->real_escape_string($_POST["citta"]);
                    $cap = $handleDB->real_escape_string($_POST["cap"]);
                    $mail = $handleDB->real_escape_string($_POST["mail"]);
                    $username = $handleDB->real_escape_string($_POST["username"]);
                    $password = $handleDB->real_escape_string($_POST["password"]);

                    // Compongo la query per l'esecuzione
                    $q = "insert into $tb_utenti(nome, cognome, indirizzo, citta, cap, data_registrazione, 
                    username, mail, password, saldo_standard) values ('$nome', '$cognome', '$indirizzo', '$citta', 
                    '$cap', DATE(NOW()), '$username', '$mail', SHA2('$password', 256), 0)";

                    // Esecuzione della query
                    try
                    {
                        $handleDB->query($q);
                        $msg = 'Registrazione avvenuta con successo';
                        $err = false;

                        // Creazione del carrello per il nuovo utente nel file xml
                        $gestore_carrelli = new GestoreCarrelli();
                        $gestore_carrelli->aggiungiNuovoCarrello($handleDB->insert_id);
                        
                        // Creazione del portafoglio bonus per il nuovo utente
                        $gestore_portafogli_bonus = new GestorePortafogliBonus();
                        $gestore_portafogli_bonus->aggiungiNuovoPortafoglioBonus($handleDB->insert_id);
                    }
                    catch (Exception $e)
                    {
                        $err = true;
                        if ($handleDB->errno == 1062 )
                            $msg = 'E-mail o username gi&agrave; presente nel database';
                    }
                }
                else // Gestione dell'errore sui campi
                {   
                    $msg = '';
                    
                    if ( !$email_valida )
                        $msg = 'E-mail non valida';
                    
                    if ( !$cap_valido )
                    {
                        if ( $msg == '' )
                            $msg = 'CAP non valido';
                        else
                            $msg .= '<br /> CAP non valido';
                    }
                }
                    
                // Chiusura della connessione al database
                $handleDB->close();
            }
            else 
                $msg = 'Errore di comunicazione con il database';
        }
        else {
            $msg = 'Campi vuoti';   // Se qualche campo risulta vuoto, lo segnalo
        }
    }
    else if ( $sessione_esistente ) // Elimino la sessione esistente per l'account disattivato
    {
        require_once 'lib/cancellaSessione.php';
        $msg = 'Account disattivato. Contattare l\'admin';    
    }
    else
    {
        // Elimino la sessione appena creata erroneamente
        require_once 'lib/cancellaSessione.php';
        $err = false;
        $mostraPopup = false;
    }

    echo '<?xml version = "1.0" encoding="UTF-8" ?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="../css/stileLayout.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileCatalogo.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileSidebar.css" type="text/css" />
        <link rel="stylesheet" href="../css/stileRegistrati.css" type="text/css" />
        <link rel="stylesheet" href="../css/stilePopup.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <script type="text/javascript" src="../js/utility.js"></script>
        <title>UNI-TECNO</title>
    </head>

    <body>
        <?php
            // Import della navbar
            // Nascondo il bottone registrati
            // Mostro il bottone accedi
            $nav = file_get_contents("../html/strutturaNavbarVisitatori.html");
            $nav = str_replace("%OPZIONE_DISPLAY_REGISTRATI%", "none", $nav);
            $nav = str_replace("%OPZIONE_DISPLAY_ACCEDI%", "block", $nav);
            echo $nav ."\n";

            // Import della sidebar e mostro solo le opzioni del visitatore
            $sidebar = file_get_contents("../html/strutturaSidebar.html");
            $sidebar = str_replace("%OPERAZIONI_UTENTE%", "", $sidebar);
            echo $sidebar . "\n";
        ?>

        <!-- FORM DI REGISTRAZIONE -->
        <div id="sezioneRegistrazione">
            <?php 
                // Stampo il popup se necessario
                echo creaPopup($mostraPopup, $msg, $err) . "\n";
            ?>

            <form id="formRegistrazione" method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                <fieldset>  <p>Nome:        </p>  <input type="text" name="nome" <?php if($err) echo 'value="'. $_POST["nome"] . '"';?>/>    </fieldset>
                <fieldset>  <p>Cognome:     </p>  <input type="text" name="cognome" <?php if($err) echo 'value="'. $_POST["cognome"] . '"';?>/> </fieldset>
                <fieldset>  <p>Indirizzo:   </p>  <input type="text" name="indirizzo" <?php if($err) echo 'value="'. $_POST["indirizzo"] . '"';?>/>    </fieldset>
                <fieldset>  <p>Citt&agrave;:</p>  <input type="text" name="citta" <?php if($err) echo 'value="'. $_POST["citta"] . '"';?>/>    </fieldset>
                <fieldset>  <p>CAP:         </p>  <input type="text" name="cap" <?php if($err) echo 'value="'. $_POST["cap"] . '"';?>/>    </fieldset>
                <fieldset>  <p>Username:    </p>  <input type="text" name="username" <?php if($err) echo 'value="'. $_POST["username"] . '"';?>/>    </fieldset>
                <fieldset>  <p>Mail:        </p>  <input type="text" name="mail" <?php if($err) echo 'value="'. $_POST["mail"] . '"';?>/>    </fieldset>
                <fieldset>  <p>Password:    </p>  <input type="password" name="password"/>    </fieldset>
                
                <fieldset style="border-style: none; box-shadow: none;"> <input type="button" value="Cancella" name="btnCancella" onclick="azzeraFormRegistrazione();" />
                <input type="submit" value="Invia" name="btnRegistrati" /> </fieldset>
            </form>
        </div>
    </body>
</html>