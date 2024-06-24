<?php
    // Script per cancellare una sessione
    session_destroy();
    setcookie(session_name(), '', time()-3600, '/');
?>