<?php
    require_once 'installDB.php';
    require_once 'installXML.php';
    echo '<?xml version = "1.0" encoding="ISO-8859-1"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> CHAMPIONS LEAGUE </title>
        <link rel="icon" type="image/png" href="../img/favicon.png" />
    </head>

    <body>
        <h1> QUERY SQL </h1>
        <p><?php echo $queryEseguite; ?></p>
        <h1> CONTENUTO XML </h1>
        <p><pre><?php  echo htmlspecialchars($contenutoXML); ?></pre></p>
        <a href="accedi.php"> AVVIA APPLICAZIONE</a>
    </body>
</html>

