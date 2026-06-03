<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Inventario";
$subtitulo_pagina = "Gestión de lotes y alertas de stock";

$lotes = [];
$sql = "SELECT l.*, p.nombre as producto_nombre
        FROM lotes l
        LEFT JOIN productos p ON l.id_producto = p.id_producto
        WHERE l.estado <> 'ELIMINADO'
        ORDER BY l.fecha_vencimiento ASC";
$resultado = $conn->query($sql);
if($resultado){
    while($row = $resultado->fetch_assoc()){
        $lotes[] = $row;
    }
}

$vencidos = 0;
$por_vencer = 0;
$stock_bajo = 0;

$fecha_hoy = date('Y-m-d');
$fecha_alerta = date('Y-m-d', strtotime('+30 days'));

foreach($lotes as $lote){
    if($lote['fecha_vencimiento'] < $fecha_hoy){
        $vencidos++;
    } elseif($lote['fecha_vencimiento'] <= $fecha_alerta){
        $por_vencer++;
    }
    
    if($lote['cantidad_disponible'] < 10){
        $stock_bajo++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Farmacia El Danny</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .badge-alert {display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;}
        .badge-vencido {background: rgba(255, 107, 107, 0.2); color: #ff6b6b;}
        .badge-alerta {background: rgba(255, 193, 7, 0.2); color: #ffc107;}
        .badge-ok {background: rgba(40, 167, 69, 0.2); color: #51cf66;}
    </style>
</head>

<body>

    <?php require_once 'componentes/sidebar.php'; ?>

    <div class="main">
        <?php require_once 'componentes/topbar.php'; ?>

        <div class="cards">
            <div class="card">
                <div class="card-title">Total Lotes</div>
                <div class="card-value"><?php echo count($lotes); ?></div>
                <div class="card-small">Lotes en inventario</div>
            </div>

            <div class="card">
                <div class="card-title">Productos Vencidos</div>
                <div class="card-value" style="color: #ff6b6b;"><?php echo $vencidos; ?></div>
                <div class="card-small">Requieren eliminación</div>
            </div>

            <div class="card">
                <div class="card-title">Por Vencer (30 días)</div>
                <div class="card-value" style="color: #ffc107;"><?php echo $por_vencer; ?></div>
                <div class="card-small">Próximo vencimiento</div>
            </div>

            <div class="card">
                <div class="card-title">Stock Bajo</div>
                <div class="card-value" style="color: #ff9999;"><?php echo $stock_bajo; ?></div>
                <div class="card-small">Menos de 10 unidades</div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Producto</th>
                        <th>Ingreso</th>
                        <th>Vencimiento</th>
                        <th>Cantidad</th>
                        <th>Precio Compra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($lotes) > 0): ?>
                        <?php foreach($lotes as $lote): ?>
                            <?php
                                $estado = 'ok';
                                if($lote['fecha_vencimiento'] < $fecha_hoy){
                                    $estado = 'vencido';
                                } elseif($lote['fecha_vencimiento'] <= $fecha_alerta){
                                    $estado = 'alerta';
                                }
                            ?>
                            <tr>
    <td><?php echo htmlspecialchars($lote['codigo_lote']); ?></td>
    <td><?php echo htmlspecialchars($lote['producto_nombre'] ?? '-'); ?></td>
    <td><?php echo date('d/m/Y', strtotime($lote['fecha_ingreso'])); ?></td>
    <td><?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?></td>
    <td><?php echo $lote['cantidad_disponible']; ?></td>
    <td>$<?php echo number_format($lote['precio_compra'], 2); ?></td>

    <td>
        <?php if($estado === 'vencido'): ?>
            <span class="badge-alert badge-vencido">
                <i class="fa-solid fa-circle-exclamation"></i> VENCIDO
            </span>
        <?php elseif($estado === 'alerta'): ?>
            <span class="badge-alert badge-alerta">
                <i class="fa-solid fa-triangle-exclamation"></i> POR VENCER
            </span>
        <?php else: ?>
            <span class="badge-alert badge-ok">
                <i class="fa-solid fa-circle-check"></i> OK
            </span>
        <?php endif; ?>
    </td>

    <td>
        <?php if($estado === 'vencido'): ?>
            <a href="php/eliminar_lote.php?id=<?php echo $lote['id_lote']; ?>"
               onclick="return confirm('¿Eliminar este lote vencido?');"
               style="
               background:#dc3545;
               color:white;
               padding:8px 12px;
               border-radius:6px;
               text-decoration:none;">
               <i class="fa-solid fa-trash"></i> Eliminar
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay lotes registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
