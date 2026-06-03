<?php

$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "farmacia_danny";

$conn = new mysqli(
    $host,
    $usuario,
    $password,
    $basedatos
);

if($conn->connect_error){

    die(
        "Error de conexión: " .
        $conn->connect_error
    );
}

$conn->set_charset("utf8");

date_default_timezone_set('America/Bogota');

?>