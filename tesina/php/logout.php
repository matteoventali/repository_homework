<?php
    session_start();
    
    if ( isset($_SESSION["nome"]) ) // Verifico se vi e' una sessione aperta
        require_once 'lib/cancellaSessione.php';
    
    header("Location: homepage.php");
?>