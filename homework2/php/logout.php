<?php
    session_start();

    if ( isset($_SESSION["nome"]) ) // Verifico se vi e' una sessione aperta
        require_once 'cancellaSessione.php';
    
    header("Location: accedi.php");
?>