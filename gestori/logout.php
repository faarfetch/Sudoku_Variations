<?php
if (!isset($_SESSION)) {
    session_start();
}
//elimina il cookie e manda lutente alla pagina di login
session_destroy();
header("location: ../login.php?message=logout fatto");
exit();
