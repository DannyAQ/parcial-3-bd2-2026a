<?php
session_start();
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Pedidos / Ingreso de Medicamentos";
$subtitulo_pagina = "Centro principal de entrada de medicamentos";

$id_trabajador = 0;

$sql_trab = "SELECT id_cedula FROM trabajadores WHERE usuario = ?";
$stmt = $conn->prepare($sql_trab);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();

$resultado = $stmt->get_result();

if ($row = $resultado->fetch_assoc()) {
    $id_trabajador = $row['id_cedula'];
}

$stmt->close();

if (isset($_POST['registrar_pedido'])) {

    $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
    $id_producto = (int)($_POST['id_producto'] ?? 0);

    $codigo_lote = trim($_POST['codigo_lote'] ?? '');
    $fecha_ingreso = $_POST['fecha_ingreso'] ?? date('Y-m-d');
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';

    $cantidad_inicial = (int)($_POST['cantidad_inicial'] ?? 0);
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);

    if (
        $id_proveedor > 0 &&
        $id_producto > 0 &&
        $cantidad_inicial > 0 &&
        $precio_compra > 0 &&
        !empty($codigo_lote)
    ) {

        $conn->begin_transaction();

        try {

            $cantidad_disponible = $cantidad_inicial;

            $sql_lote = "
                INSERT INTO lotes
                (
                    codigo_lote,
                    id_producto,
                    fecha_ingreso,
                    fecha_vencimiento,
                    cantidad_inicial,
                    cantidad_disponible,
                    precio_compra
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt_lote = $conn->prepare($sql_lote);

            $stmt_lote->bind_param(
                "sissiid",
                $codigo_lote,
                $id_producto,
                $fecha_ingreso,
                $fecha_vencimiento,
                $cantidad_inicial,
                $cantidad_disponible,
                $precio_compra
            );

            $stmt_lote->execute();

            $total_compra = $cantidad_inicial * $precio_compra;

            $sql_compra = "
                INSERT INTO compras
                (
                    id_proveedor,
                    id_trabajador,
                    total_compra
                )
                VALUES (?, ?, ?)
            ";

            $stmt_compra = $conn->prepare($sql_compra);

            $stmt_compra->bind_param(
                "iid",
                $id_proveedor,
                $id_trabajador,
                $total_compra
            );

            $stmt_compra->execute();

            $id_compra = $conn->insert_id;

            $subtotal = $total_compra;

            $sql_detalle = "
                INSERT INTO detalle_compras
                (
                    id_compra,
                    id_producto,
                    codigo_lote,
                    fecha_vencimiento,
                    cantidad,
                    precio_unitario,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt_detalle = $conn->prepare($sql_detalle);

            $stmt_detalle->bind_param(
                "iisiidd",
                $id_compra,
                $id_producto,
                $codigo_lote,
                $fecha_vencimiento,
                $cantidad_inicial,
                $precio_compra,
                $subtotal
            );

            $stmt_detalle->execute();

            $conn->commit();

            $mensaje_exito = "Pedido registrado correctamente. Compra #{$id_compra}";

        } catch (Exception $e) {

            $conn->rollback();

            $mensaje_error = "Error al registrar el pedido: " . $e->getMessage();
        }

    } else {

        $mensaje_error = "Completa todos los campos requeridos";
    }
}

/*
|--------------------------------------------------------------------------
| PROVEEDORES
|--------------------------------------------------------------------------
*/

$proveedores = [];

$sql_prov = "
    SELECT *
    FROM proveedores
    ORDER BY nombre_empresa ASC
";

if ($resultado = $conn->query($sql_prov)) {

    while ($row = $resultado->fetch_assoc()) {
        $proveedores[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| MEDICAMENTOS EXISTENTES
|--------------------------------------------------------------------------
*/

$medicamentos = [];

$sql_med = "
    SELECT
        p.id_producto,
        p.nombre,
        c.nombre AS categoria
    FROM productos p
    LEFT JOIN categorias c
        ON p.id_categoria = c.id_categoria
    ORDER BY p.nombre ASC
";

if ($resultado = $conn->query($sql_med)) {

    while ($row = $resultado->fetch_assoc()) {
        $medicamentos[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| COMPRAS RECIENTES
|--------------------------------------------------------------------------
*/

$compras = [];

$sql_comp = "
    SELECT
        c.*,
        p.nombre_empresa
    FROM compras c
    LEFT JOIN proveedores p
        ON c.id_proveedor = p.id_proveedor
    ORDER BY c.fecha_compra DESC
    LIMIT 50
";

if ($resultado = $conn->query($sql_comp)) {

    while ($row = $resultado->fetch_assoc()) {
        $compras[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Farmacia El Danny</title>
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
        .btn-small {padding: 8px 12px; font-size: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); color: white; border-radius: 8px; cursor: pointer; transition: var(--transition);}
        .btn-small:hover {background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2);}
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
                <div class="card-title">Total Medicamentos</div>
                <div class="card-value"><?php $count = $conn->query("SELECT COUNT(*) as c FROM productos")->fetch_assoc()['c']; echo $count; ?></div>
                <div class="card-small">Productos en sistema</div>
            </div>

            <div class="card">
                <div class="card-title">Total Lotes</div>
                <div class="card-value"><?php $count = $conn->query("SELECT COUNT(*) as c FROM lotes")->fetch_assoc()['c']; echo $count; ?></div>
                <div class="card-small">Lotes registrados</div>
            </div>

            <div class="card">
                <div class="card-title">Compras Realizadas</div>
                <div class="card-value"><?php echo count($compras); ?></div>
                <div class="card-small">Total de compras</div>
            </div>

            <div class="card">
                <div class="card-title">Proveedores</div>
                <div class="card-value"><?php echo count($proveedores); ?></div>
                <div class="card-small">Activos</div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap;">
            <button class="btn btn-primary" onclick="abrirModal()"><i class="fa-solid fa-plus"></i> Nuevo Pedido</button>
            <a href="proveedores.php" class="btn btn-dark" style="border: 1px solid rgba(255,255,255,0.1); text-decoration: none;">
                <i class="fa-solid fa-person-hiking"></i> Gestionar Proveedores
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Compra</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($compras) > 0): ?>
                        <?php foreach($compras as $compra): ?>
                            <tr>
                                <td>#<?php echo $compra['id_compra']; ?></td>
                                <td><?php echo htmlspecialchars($compra['nombre_empresa'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha_compra'])); ?></td>
                                <td>$<?php echo number_format($compra['total_compra'], 2); ?></td>
                                <td>
                                    <span style="background: rgba(40, 167, 69, 0.2); color: #51cf66; padding: 4px 12px; border-radius: 20px; font-size: 12px;">Pendiente</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay compras registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalCompra">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nuevo Pedido De Medicamentos </h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>

            <form method="POST" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group">
                    <label class="form-label">Proveedor *</label>
                    <select name="id_proveedor" class="form-control" required>
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id_proveedor']; ?>">
                                <?php echo htmlspecialchars($prov['nombre_empresa']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                <h4 style="color: white; margin: 15px 0;">Producto</h4>

              <div class="form-group">
    <label class="form-label">Medicamento *</label>

    <select name="id_producto" class="form-control" required>
        <option value="">Seleccione un medicamento</option>

        <?php foreach($medicamentos as $med): ?>
            <option value="<?= $med['id_producto']; ?>">
                <?= htmlspecialchars($med['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                

                <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                <h4 style="color: white; margin: 15px 0;">Lote</h4>

                <div class="form-group">
                    <label class="form-label">Código Lote *</label>
                    <input type="text" name="codigo_lote" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Vencimiento *</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cantidad Inicial *</label>
                    <input type="number" name="cantidad_inicial" class="form-control" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Precio Compra (unitario) *</label>
                    <input type="number" name="precio_compra" class="form-control" step="0.01" required>
                </div>

                <button type="submit" name="registrar_pedido" class="btn btn-primary" style="width: 100%; margin-top: 20px;"><i class="fa-solid fa-save"></i> Registrar Pedido</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(){document.getElementById('modalCompra').classList.add('active');}
        function cerrarModal(){document.getElementById('modalCompra').classList.remove('active');}
        window.onclick = function(event){let modal = document.getElementById('modalCompra'); if(event.target === modal){modal.classList.remove('active');}}
    </script>

</body>

</html>
