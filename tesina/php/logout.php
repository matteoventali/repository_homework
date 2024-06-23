<?php
    session_start();

    echo session_name();

    if ( isset($_SESSION["nome"]) ) // Verifico se vi e' una sessione aperta
        require_once 'cancellaSessione.php';
    
    header("Location: homepage.php");
?>