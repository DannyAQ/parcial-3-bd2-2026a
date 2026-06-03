<?php
session_start();
require_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id'])){

    $id_lote = intval($_GET['id']);

    $sql = "UPDATE lotes
            SET estado = 'ELIMINADO'
            WHERE id_lote = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_lote);

    if($stmt->execute()){
        header("Location: ../inventario.php");
        exit();
    }else{
        echo "Error al eliminar lote";
    }

}else{
    header("Location: ../inventario.php");
    exit();
}
?>