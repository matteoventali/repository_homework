<?php
    require_once 'installDB.php';
    require_once 'installXML.php';
    echo '<?xml version = "1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="../img/logo.png" />
        <title>UNI-TECNO</title>
    </head>

    <body>
        <h1> QUERY SQL </h1>
        <p><?php echo $queryEseguite; ?></p>
        <h1> OPERAZIONI XML </h1>
        <p> <?php echo $operazioni; ?> </p>
        <p><a href="../homepage.php">AVVIA APPLICAZIONE</a></p>
    </body>
</html>

