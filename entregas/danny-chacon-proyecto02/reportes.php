<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Reportes";
$subtitulo_pagina = "Análisis y reportes del negocio";

$fecha_hoy = date('Y-m-d');
$fecha_inicio_mes = date('Y-m-01');
$fecha_inicio_año = date('Y-01-01');

// VENTAS DEL DÍA
$sql_venta_hoy = "SELECT COALESCE(SUM(total_venta), 0) as total FROM ventas WHERE DATE(fecha_venta) = ?";
$stmt = $conn->prepare($sql_venta_hoy);
$stmt->bind_param("s", $fecha_hoy);
$stmt->execute();
$resultado = $stmt->get_result();
$venta_hoy = $resultado->fetch_assoc()['total'] ?? 0;

// COMPRAS DEL MES
$sql_compra_mes = "SELECT COALESCE(SUM(total_compra), 0) as total FROM compras WHERE DATE(fecha_compra) >= ? AND DATE(fecha_compra) <= ?";
$fecha_fin_mes = date('Y-m-t');
$stmt = $conn->prepare($sql_compra_mes);
$stmt->bind_param("ss", $fecha_inicio_mes, $fecha_fin_mes);
$stmt->execute();
$resultado = $stmt->get_result();
$compra_mes = $resultado->fetch_assoc()['total'] ?? 0;

// PRODUCTOS VENCIDOS
$sql_vencidos = "SELECT COUNT(*) as cantidad FROM lotes WHERE fecha_vencimiento < ?";
$stmt = $conn->prepare($sql_vencidos);
$stmt->bind_param("s", $fecha_hoy);
$stmt->execute();
$resultado = $stmt->get_result();
$productos_vencidos = $resultado->fetch_assoc()['cantidad'] ?? 0;

// STOCK BAJO
$sql_stock_bajo = "SELECT COUNT(DISTINCT l.id_producto) as cantidad FROM lotes l WHERE l.cantidad_disponible < 10";
$resultado = $conn->query($sql_stock_bajo);
$stock_bajo = $resultado->fetch_assoc()['cantidad'] ?? 0;

// ÚLTIMAS VENTAS
$ventas = [];
$sql_ventas = "SELECT v.*, c.nombre as cliente_nombre FROM ventas v LEFT JOIN clientes c ON v.id_cliente = c.id_cliente ORDER BY v.fecha_venta DESC LIMIT 10";
$resultado = $conn->query($sql_ventas);
if($resultado){
    while($row = $resultado->fetch_assoc()){
        $ventas[] = $row;
    }
}

// ÚLTIMAS COMPRAS
$compras = [];
$sql_compras = "SELECT c.*, p.nombre_empresa FROM compras c LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor ORDER BY c.fecha_compra DESC LIMIT 10";
$resultado = $conn->query($sql_compras);
if($resultado){
    while($row = $resultado->fetch_assoc()){
        $compras[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Farmacia El Danny</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <?php require_once 'componentes/sidebar.php'; ?>

    <div class="main">
        <?php require_once 'componentes/topbar.php'; ?>

        <div class="cards">
            <div class="card">
                <div class="card-title">Ventas Hoy</div>
                <div class="card-value">$<?php echo number_format($venta_hoy, 0); ?></div>
                <div class="card-small">Venta del día</div>
            </div>

            <div class="card">
                <div class="card-title">Compras Mes</div>
                <div class="card-value">$<?php echo number_format($compra_mes, 0); ?></div>
                <div class="card-small">Inversión mensual</div>
            </div>

            <div class="card">
                <div class="card-title">Productos Vencidos</div>
                <div class="card-value" style="color: #ff6b6b;"><?php echo $productos_vencidos; ?></div>
                <div class="card-small">Requieren eliminación</div>
            </div>

            <div class="card">
                <div class="card-title">Stock Bajo</div>
                <div class="card-value" style="color: #ffc107;"><?php echo $stock_bajo; ?></div>
                <div class="card-small">Productos < 10 unidades</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 25px;">
            
            <div class="table-container">
                <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-down" style="color: #51cf66;"></i> Últimas Ventas
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($ventas) > 0): ?>
                            <?php foreach($ventas as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Consumidor'); ?></td>
                                    <td><?php echo date('d/m H:i', strtotime($venta['fecha_venta'])); ?></td>
                                    <td>$<?php echo number_format($venta['total_venta'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 20px; color: var(--text-muted);">No hay ventas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-up" style="color: #ff6b6b;"></i> Últimas Compras
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($compras) > 0): ?>
                            <?php foreach($compras as $compra): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($compra['nombre_empresa'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m H:i', strtotime($compra['fecha_compra'])); ?></td>
                                    <td>$<?php echo number_format($compra['total_compra'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 20px; color: var(--text-muted);">No hay compras</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

</html>
