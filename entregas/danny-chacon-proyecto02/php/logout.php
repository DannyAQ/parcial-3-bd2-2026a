<?php

/*
|--------------------------------------------------------------------------
| LOGOUT.PHP
|--------------------------------------------------------------------------
| Cerrar sesión del usuario
| Farmacia El Danny
|--------------------------------------------------------------------------
*/

session_start();

// Destruir sesión
session_destroy();

// Redirigir al login
header("Location: ../login.php");
exit();

?>
