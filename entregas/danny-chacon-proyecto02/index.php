<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Panel Principal";
$subtitulo_pagina = "Interfaz de gestión farmacéutica";
$fecha_hoy = date('Y-m-d');

// VENTAS DEL DÍA
$sql_venta_hoy = "SELECT COALESCE(SUM(total_venta), 0) as total FROM ventas WHERE DATE(fecha_venta) = ?";
$stmt = $conn->prepare($sql_venta_hoy);
$stmt->bind_param("s", $fecha_hoy);
$stmt->execute();
$venta_hoy = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// ESTADÍSTICAS
$total_productos = $conn->query("SELECT COUNT(*) as total FROM productos")->fetch_assoc()['total'];
$total_clientes = $conn->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total'];
$total_pedidos = $conn->query("SELECT COUNT(*) as total FROM compras")->fetch_assoc()['total'];

// BAJO STOCK
$sql_bajo = "SELECT l.*, p.nombre FROM lotes l JOIN productos p ON l.id_producto = p.id_producto WHERE l.cantidad_disponible < 10 ORDER BY l.cantidad_disponible ASC LIMIT 6";
$bajo_stock = [];
if($resultado = $conn->query($sql_bajo)){
    while($row = $resultado->fetch_assoc()) $bajo_stock[] = $row;
}

// ÚLTIMAS VENTAS
$sql_ventas = "SELECT v.*, c.nombre as cliente_nombre FROM ventas v LEFT JOIN clientes c ON v.id_cliente = c.id_cliente ORDER BY v.fecha_venta DESC LIMIT 8";
$ult_ventas = [];
if($resultado = $conn->query($sql_ventas)){
    while($row = $resultado->fetch_assoc()) $ult_ventas[] = $row;
}

// POPULARES
$sql_pop = "SELECT p.id_producto, p.nombre, p.precio_venta, COUNT(dv.id_detalle_venta) as vendidos FROM productos p LEFT JOIN detalle_ventas dv ON p.id_producto = dv.id_producto GROUP BY p.id_producto ORDER BY vendidos DESC LIMIT 6";
$populares = [];
if($resultado = $conn->query($sql_pop)){
    while($row = $resultado->fetch_assoc()) $populares[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Farmacia El Danny</title>
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
                <div class="card-small"><i class="fa-solid fa-arrow-trend-up"></i> Venta del día</div>
            </div>
            <div class="card">
                <div class="card-title">Pedidos</div>
                <div class="card-value"><?php echo $total_pedidos; ?></div>
                <div class="card-small">Compras registradas</div>
            </div>
            <div class="card">
                <div class="card-title">Medicamentos</div>
                <div class="card-value"><?php echo $total_productos; ?></div>
                <div class="card-small">En inventario</div>
            </div>
            <div class="card">
                <div class="card-title">Clientes</div>
                <div class="card-value"><?php echo $total_clientes; ?></div>
                <div class="card-small">Registrados</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-top: 25px;">
            <div>
                <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-fire" style="color: #6e4a4a;"></i> Medicamentos mas vendidos
                </h3>
                <div class="products-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <?php if(count($populares) > 0): ?>
                        <?php foreach(array_slice($populares, 0, 3) as $prod): ?>
                            <div class="product-card">
                                <div class="product-content" style="padding: 15px;">
                                    <h3 class="product-title" style="font-size: 14px;"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                                    <div class="product-price" style="margin: 10px 0;">$<?php echo number_format($prod['precio_venta'], 0); ?></div>
                                    <small style="color: var(--text-muted);"><i class="fa-solid fa-shopping-cart"></i> <?php echo $prod['vendidos']; ?> vendidos</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="grid-column: 1/-1; color: var(--text-muted);">Sin datos</p>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ffc107;"></i> Stock Bajo
                </h3>
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 12px; max-height: 400px; overflow-y: auto;">
                    <?php if(count($bajo_stock) > 0): ?>
                        <?php foreach($bajo_stock as $item): ?>
                            <div style="padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between;">
                                <div>
                                    <div style="color: white; font-weight: 600; font-size: 13px;"><?php echo htmlspecialchars($item['nombre']); ?></div>
                                    <small style="color: var(--text-muted);">Lote: <?php echo htmlspecialchars($item['codigo_lote']); ?></small>
                                </div>
                                <div style="color: #ff6b6b; font-weight: 700;"><?php echo $item['cantidad_disponible']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted); padding: 20px; text-align: center;">Todos los productos están bien</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h3 style="margin-bottom: 15px; color: white; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clock-rotate-left"></i> Últimas Ventas
            </h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($ult_ventas) > 0): ?>
                            <?php foreach($ult_ventas as $venta): ?>
                                <tr>
                                    <td>#<?php echo $venta['id_venta']; ?></td>
                                    <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Consumidor'); ?></td>
                                    <td><?php echo date('d/m H:i', strtotime($venta['fecha_venta'])); ?></td>
                                    <td><?php echo htmlspecialchars($venta['metodo_pago']); ?></td>
                                    <td>$<?php echo number_format($venta['total_venta'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay ventas registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>