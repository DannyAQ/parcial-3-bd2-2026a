<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Clientes";
$subtitulo_pagina = "Gestión de datos de clientes";

if(isset($_POST['crear_cliente'])){
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    if($nombre){
        $sql = "INSERT INTO clientes (nombre, apellido, telefono, correo, direccion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellido, $telefono, $correo, $direccion);
        if($stmt->execute()){
            $mensaje_exito = "Cliente registrado exitosamente";
        }
    }
}

if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM clientes WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $mensaje_exito = "Cliente eliminado";
    }
}

$buscar = $_GET['buscar'] ?? '';

// Cargar historial de ventas si se visualiza un cliente
$cliente_detalle = NULL;
$ventas_cliente = [];
if(isset($_GET['ver'])){
    $id_cli = (int)$_GET['ver'];
    $sql_det = "SELECT * FROM clientes WHERE id_cliente = ?";
    $stmt_det = $conn->prepare($sql_det);
    $stmt_det->bind_param("i", $id_cli);
    $stmt_det->execute();
    $cliente_detalle = $stmt_det->get_result()->fetch_assoc();
    
    if($cliente_detalle){
        $sql_vent = "
            SELECT v.id_venta, v.fecha_venta, v.metodo_pago, v.total_venta, 
                   f.id_factura, f.numero_factura
            FROM ventas v
            LEFT JOIN facturas_venta f ON v.id_venta = f.id_venta
            WHERE v.id_cliente = ?
            ORDER BY v.fecha_venta DESC
        ";
        $stmt_vent = $conn->prepare($sql_vent);
        $stmt_vent->bind_param("i", $id_cli);
        $stmt_vent->execute();
        $resultado_ventas = $stmt_vent->get_result();

            while($row = $resultado_ventas->fetch_assoc()){
                $ventas_cliente[] = $row;
            }
            
        }
    }
// }
$sql = "SELECT * FROM clientes WHERE nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ? OR correo LIKE ? ORDER BY nombre ASC";
$stmt = $conn->prepare($sql);
$buscar_param = "%{$buscar}%";
$stmt->bind_param("ssss", $buscar_param, $buscar_param, $buscar_param, $buscar_param);
$stmt->execute();
$resultado = $stmt->get_result();
$clientes = [];
while($row = $resultado->fetch_assoc()){
    $clientes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Farmacia El Danny</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;}
        .modal.active {display: flex;}
        .modal-content {background: linear-gradient(180deg, rgba(18,18,18,0.95), rgba(10,10,10,0.98)); border: 1px solid rgba(255,255,255,0.05); border-radius: 14px; padding: 35px; max-width: 600px; width: 90%; backdrop-filter: blur(12px); max-height: 90vh; overflow-y: auto;}
        .modal-header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px;}
        .modal-header h2 {font-size: 24px; font-weight: 700; color: white;}
        .modal-close {background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer; transition: var(--transition);}
        .modal-close:hover {color: white;}
        .toolbar {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;}
        .btn-small {padding: 8px 12px; font-size: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); color: white; border-radius: 8px; cursor: pointer; transition: var(--transition);}
        .btn-small:hover {background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2);}
        .btn-small.danger {color: #ff6b6b; border-color: rgba(255, 107, 107, 0.2);}
        .btn-small.danger:hover {background: rgba(255, 107, 107, 0.1);}
        .actions {display: flex; gap: 8px;}
    </style>
</head>

<body>

    <?php require_once 'componentes/sidebar.php'; ?>

    <div class="main">
        <?php require_once 'componentes/topbar.php'; ?>

        <?php if(isset($mensaje_exito)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <div class="card-title">Total Clientes</div>
                <div class="card-value"><?php echo count($clientes); ?></div>
                <div class="card-small">Clientes registrados</div>
            </div>
        </div>

        <div class="toolbar">
            <form method="GET" style="display: flex; flex: 1; gap: 10px; min-width: 300px;">
                <input type="text" name="buscar" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($buscar); ?>" style="flex: 1; padding: 12px 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: white; font-family: 'Inter', sans-serif;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            </form>
            <button class="btn btn-primary" onclick="abrirModal()"><i class="fa-solid fa-plus"></i> Nuevo Cliente</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($clientes) > 0): ?>
                        <?php foreach($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['apellido'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['correo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['direccion'] ?? '-'); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="?ver=<?php echo $cliente['id_cliente']; ?>" class="btn-small" title="Ver historial de ventas"><i class="fa-solid fa-history"></i> Historial</a>
                                        <button class="btn-small"><i class="fa-solid fa-edit"></i></button>
                                        <a href="?eliminar=<?php echo $cliente['id_cliente']; ?>" class="btn-small danger" onclick="return confirm('¿Eliminar?')"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay clientes registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($cliente_detalle): ?>
        <div style="margin-top: 30px; padding: 25px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: white; margin: 0;">
                    <i class="fa-solid fa-user"></i> 
                    <?php echo htmlspecialchars($cliente_detalle['nombre']); ?>
                    <?php if($cliente_detalle['apellido']): ?>
                        <?php echo htmlspecialchars($cliente_detalle['apellido']); ?>
                    <?php endif; ?>
                </h2>
                <a href="clientes.php" class="btn btn-dark" style="border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                <div style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <small style="color: var(--text-muted);">Teléfono</small>
                    <div style="color: white; font-weight: 600; margin-top: 5px;">
                        <?php echo htmlspecialchars($cliente_detalle['telefono'] ?? '-'); ?>
                    </div>
                </div>
                <div style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <small style="color: var(--text-muted);">Correo</small>
                    <div style="color: white; font-weight: 600; margin-top: 5px;">
                        <?php echo htmlspecialchars($cliente_detalle['correo'] ?? '-'); ?>
                    </div>
                </div>
                <div style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <small style="color: var(--text-muted);">Total Compras</small>
                    <div style="color: #51cf66; font-weight: 600; margin-top: 5px;">
                        <?php echo count($ventas_cliente); ?>
                    </div>
                </div>
            </div>

            <h3 style="color: white; margin-bottom: 15px;">
                <i class="fa-solid fa-receipt"></i> Historial de Ventas/Facturas
            </h3>

            <?php if(count($ventas_cliente) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Número Factura</th>
                            <th>Fecha</th>
                            <th>Método Pago</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ventas_cliente as $venta): ?>
                        <tr>
                            <td>
                                <?php if($venta['numero_factura']): ?>
                                    <?php echo htmlspecialchars($venta['numero_factura']); ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">Sin factura</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></td>
                            <td><?php echo htmlspecialchars($venta['metodo_pago']); ?></td>
                            <td>$<?php echo number_format($venta['total_venta'], 2); ?></td>
                            <td>
                                <?php if($venta['id_factura']): ?>
                                <button class="btn-small" onclick="imprimirFactura(<?php echo $venta['id_factura']; ?>)">
                                    <i class="fa-solid fa-print"></i> Imprimir
                                </button>
                                <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 20px;">Este cliente no tiene ventas registradas.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        

    <div class="modal" id="modalCliente">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nuevo Cliente</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre del cliente" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" placeholder="Apellido">
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono de contacto">
                </div>

                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3" placeholder="Dirección de entrega"></textarea>
                </div>

                <button type="submit" name="crear_cliente" class="btn btn-primary" style="width: 100%; margin-top: 20px;"><i class="fa-solid fa-plus"></i> Registrar Cliente</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(){document.getElementById('modalCliente').classList.add('active');}
        function cerrarModal(){document.getElementById('modalCliente').classList.remove('active');}
        window.onclick = function(event){let modal = document.getElementById('modalCliente'); if(event.target === modal){modal.classList.remove('active');}}
        
        function imprimirFactura(id){
            let ventana = window.open('php/generarpdf.php?id=' + id, '_blank');
            setTimeout(() => {
                ventana.print();
            }, 500);
        }
    </script>

</body>

</html>
