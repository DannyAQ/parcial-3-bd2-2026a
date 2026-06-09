<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Ventas";
$subtitulo_pagina = "Registro de ventas y facturación";

if(isset($_POST['crear_venta'])){
    $id_cliente = $_POST['id_cliente'] ?? NULL;
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $nombre_cliente = $_POST['nombre_cliente'] ?? 'Consumidor';
    
    $id_trabajador = 0;
    $sql_trab = "SELECT id_cedula FROM trabajadores WHERE usuario = ?";
    $stmt_trab = $conn->prepare($sql_trab);
    $stmt_trab->bind_param("s", $_SESSION['usuario']);
    $stmt_trab->execute();
    $resultado_trab = $stmt_trab->get_result();
    if($row_trab = $resultado_trab->fetch_assoc()){
        $id_trabajador = $row_trab['id_cedula'];
    }

    if($metodo_pago && $id_trabajador){
        // Contar productos con cantidad > 0
        $tiene_productos = false;
        foreach($_POST as $key => $value){
            if(strpos($key, 'cantidad_') === 0 && intval($value) > 0){
                $tiene_productos = true;
                break;
            }
        }
        
        if(!$tiene_productos){
            $mensaje_error = "Debe agregar al menos un medicamento con cantidad mayor a 0.";
        } else {
            $total_venta = 0;
            $iva = 0;
            $subtotal = 0;
            
            $sql = "INSERT INTO ventas (id_cliente, id_trabajador, metodo_pago, total_venta) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisd", $id_cliente, $id_trabajador, $metodo_pago, $total_venta);
            if($stmt->execute()){
                $id_venta = $conn->insert_id;
                $total_venta = 0;

                foreach($_POST as $key => $value){
                    if(strpos($key, 'cantidad_') === 0){
                    $id_producto = (int) str_replace('cantidad_', '', $key);
                    $cantidad = intval($value);

                    if($cantidad <= 0){
                        continue;
                    }

                    $sql_producto = "
            SELECT
                p.precio_venta,
                l.id_lote,
                l.cantidad_disponible
            FROM productos p
            INNER JOIN lotes l
            ON p.id_producto = l.id_producto
            WHERE
                p.id_producto = ?
                AND l.estado='ACTIVO'
                AND l.cantidad_disponible > 0
            ORDER BY
                l.fecha_ingreso ASC,
                l.id_lote ASC
            LIMIT 1
            ";

        $stmt_prod =
        $conn->prepare($sql_producto);

        $stmt_prod->bind_param(
            "i",
            $id_producto
        );

        $stmt_prod->execute();

        $prod =
        $stmt_prod->get_result()
                  ->fetch_assoc();

        if(!$prod){
            continue;
        }
        if($cantidad > $prod['cantidad_disponible']){

    die(
        "Error: El producto "
        . $id_producto .
        " no tiene suficiente stock."
    );
}
        $precio =
        $prod['precio_venta'];

        $subtotal_linea =
        $precio * $cantidad;

        $subtotal +=
        $subtotal_linea;

        $total_venta +=
        $subtotal_linea;

        $sql_detalle = "
        INSERT INTO detalle_ventas
        (
            id_venta,
            id_producto,
            id_lote,
            cantidad,
            precio_unitario,
            subtotal
        )
        VALUES
        (?,?,?,?,?,?)
        ";

        $stmt_det =
        $conn->prepare($sql_detalle);

        $stmt_det->bind_param(
            "iiiidd",
            $id_venta,
            $id_producto,
            $prod['id_lote'],
            $cantidad,
            $precio,
            $subtotal_linea
        );

        $stmt_det->execute();

        $nuevo_stock =
        $prod['cantidad_disponible']
        - $cantidad;

        $sql_stock = "
        UPDATE lotes
        SET cantidad_disponible=?
        WHERE id_lote=?
        ";

        $stmt_stock =
        $conn->prepare($sql_stock);

        $stmt_stock->bind_param(
            "ii",
            $nuevo_stock,
            $prod['id_lote']
        );

        $stmt_stock->execute();
                }
            }

            $iva = $subtotal * 0.19;
            $total_venta = $subtotal + $iva;

            $sql_total = "
            UPDATE ventas
            SET total_venta=?
            WHERE id_venta=?
            ";

            $stmt_total =
            $conn->prepare($sql_total);

            $stmt_total->bind_param(
                "di",
                $total_venta,
                $id_venta
            );

            $stmt_total->execute();
            
            // Crear factura automáticamente
            $numero_factura = 'FAC-' . str_pad($id_venta, 6, '0', STR_PAD_LEFT);
            $sql_factura = "INSERT INTO facturas_venta (numero_factura, id_venta, nombre_cliente, subtotal, iva, total_final) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_factura = $conn->prepare($sql_factura);
            $stmt_factura->bind_param("sisddd", $numero_factura, $id_venta, $nombre_cliente, $subtotal, $iva, $total_venta);
            $stmt_factura->execute();
            
            $mensaje_exito = "Venta registrada. Factura: " . $numero_factura;
            }
        }
    }
}

$clientes = [];
$sql_cli = "SELECT * FROM clientes ORDER BY nombre ASC";
$resultado_cli = $conn->query($sql_cli);
if($resultado_cli){
    while($row = $resultado_cli->fetch_assoc()){
        $clientes[] = $row;
    }
}

$ventas = [];
$sql_ven = "SELECT v.*, c.nombre as cliente_nombre, f.numero_factura, f.id_factura FROM ventas v LEFT JOIN clientes c ON v.id_cliente = c.id_cliente LEFT JOIN facturas_venta f ON v.id_venta = f.id_venta ORDER BY v.fecha_venta DESC LIMIT 50";
$resultado_ven = $conn->query($sql_ven);
if($resultado_ven){
    while($row = $resultado_ven->fetch_assoc()){
        $ventas[] = $row;
    }
}
$productos = [];

$sql_prod = "
SELECT
    p.id_producto,
    p.nombre,
    p.precio_venta,
    l.id_lote,
    l.cantidad_disponible
FROM productos p
INNER JOIN lotes l
ON p.id_producto = l.id_producto
WHERE l.estado='ACTIVO'
AND l.cantidad_disponible > 0
ORDER BY p.nombre
";

$resultado_prod = $conn->query($sql_prod);

while($row = $resultado_prod->fetch_assoc()){
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Farmacia El Danny</title>
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
    </style>
</head>

<body>

    <?php require_once 'componentes/sidebar.php'; ?>

    <div class="main">
        <?php require_once 'componentes/topbar.php'; ?>

        <?php if(isset($mensaje_exito)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if(isset($mensaje_error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> <?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <div class="card-title">Total Ventas</div>
                <div class="card-value"><?php echo count($ventas); ?></div>
                <div class="card-small">Ventas registradas</div>
            </div>

            <div class="card">
                <div class="card-title">Clientes Activos</div>
                <div class="card-value"><?php echo count($clientes); ?></div>
                <div class="card-small">Clientes en el sistema</div>
            </div>
        </div>

        <button class="btn btn-primary" onclick="abrirModal()" style="margin-bottom: 25px;"><i class="fa-solid fa-plus"></i> Nueva Venta</button>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($ventas) > 0): ?>
                        <?php foreach($ventas as $venta): ?>
                            <tr>
                                <td>#<?php echo $venta['id_venta']; ?></td>
                                <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Consumidor'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></td>
                                <td><?php echo htmlspecialchars($venta['metodo_pago']); ?></td>
                                <td>$<?php echo number_format($venta['total_venta'], 2); ?></td>
                                <td>
                                    <?php if($venta['id_factura']): ?>
                                        <button class="btn-small" onclick="imprimirFactura(<?php echo $venta['id_factura']; ?>)"><i class="fa-solid fa-print"></i> Imprimir</button>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 12px;">Sin factura</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay ventas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalVenta">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nueva Venta</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>

            <form method="POST">
<hr style="margin:20px 0">
<h3>Medicamentos</h3>

    <table style="width:100%; margin-top:15px;">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach($productos as $p): ?>

<tr>

    <td>

        <label>

            <input
                type="checkbox"
                class="producto-check"
                data-precio="<?php echo $p['precio_venta']; ?>"
                data-stock="<?php echo $p['cantidad_disponible']; ?>"
                value="<?php echo $p['id_producto']; ?>"
                name="productos[]">

            <?php echo $p['nombre']; ?>

            <br>

            <small>
                Precio:
                $<?php echo number_format($p['precio_venta']); ?>
            </small>

            <br>

            <small>
                Stock:
                <?php echo $p['cantidad_disponible']; ?>
            </small>

        </label>

    </td>

    <td>

        <input
            type="number"
            min="1"
            
            class="form-control cantidad"
            data-stock="<?php echo $p['cantidad_disponible']; ?>"
            data-precio="<?php echo $p['precio_venta']; ?>"
            name="cantidad_<?php echo $p['id_producto']; ?>">

    </td>

</tr>

<?php endforeach; ?>

        </tbody>

    </table>
                <div style="margin-top:20px">

    <h3>Total Venta</h3>

    <div
        id="totalVenta"
        style="
        font-size:28px;
        font-weight:bold;
        color:#00ff88;
        margin-top:10px;">
        $0
    </div>

</div>

<div
    id="alertaStock"
    style="
    display:none;
    margin-top:15px;
    padding:10px;
    border-radius:10px;
    background:#3a0000;
    color:#ff6b6b;">
</div>
<hr style="margin:20px 0">
    <div class="form-group">
        <label class="form-label">Nombre Cliente</label>
        <input type="text"
               name="nombre_cliente"
               class="form-control"
               required>
    </div>

    <div class="form-group">
        <label class="form-label">Documento</label>
        <input type="text"
               name="documento_cliente"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label">Teléfono</label>
        <input type="text"
               name="telefono_cliente"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label">Dirección</label>
        <input type="text"
               name="direccion_cliente"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label">Método Pago</label>

        <select name="metodo_pago"
                class="form-control"
                required>

            <option value="">Seleccione</option>
            <option value="EFECTIVO">Efectivo</option>
            <option value="TARJETA">Tarjeta</option>
            <option value="TRANSFERENCIA">Transferencia</option>
            <option value="NEQUI">Nequi</option>
            <option value="DAVIPLATA">Daviplata</option>

        </select>
    </div>

    

    
<hr style="margin:20px 0">
    <button
        type="submit"
        name="crear_venta"
        class="btn btn-primary"
        style="width:100%;margin-top:20px;">

        Registrar Venta

    </button>

</form>
        </div>
    </div>

    <script>

function abrirModal(){
    document.getElementById('modalVenta')
    .classList.add('active');
}

function cerrarModal(){
    document.getElementById('modalVenta')
    .classList.remove('active');
}

function imprimirFactura(id){

    let ventana =
    window.open(
        'php/generarpdf.php?id=' + id,
        '_blank'
    );

    setTimeout(() => {
        ventana.print();
    }, 500);
}

window.onclick = function(event){

    let modal =
    document.getElementById('modalVenta');

    if(event.target === modal){

        modal.classList.remove('active');
    }
};

function calcularTotal(){

    let total = 0;
    let error = false;
    let cantidadValida = 0;

    document
    .querySelectorAll(".cantidad")
    .forEach(input => {

        let cantidad =
        parseInt(input.value) || 0;

        let stock =
        parseInt(
            input.dataset.stock
        );

        let precio =
        parseFloat(
            input.dataset.precio
        );

        if(cantidad > 0 && cantidad <= stock){

            input.style.border = "";

            total +=
            cantidad * precio;

            cantidadValida++;

        }
        else if(cantidad > 0){

            error = true;

            input.style.border =
            "2px solid red";

        }
        else{

            input.style.border = "";
        }
    });

    document
    .getElementById("totalVenta")
    .innerHTML =
    "$" + total.toLocaleString();

    let boton =
    document.querySelector(
        "button[name='crear_venta']"
    );

    let alerta =
    document.getElementById(
        "alertaStock"
    );

    if(error){

        alerta.style.display =
        "block";

        alerta.innerHTML =
        "Verifique las cantidades seleccionadas.";

        boton.disabled = true;
    }
    else if(cantidadValida === 0){

        alerta.style.display =
        "block";

        alerta.innerHTML =
        "Debe seleccionar al menos 1 unidad de al menos un medicamento.";

        boton.disabled = true;
    }
    else{

        alerta.style.display =
        "none";

        boton.disabled = false;
    }
}

document
.querySelectorAll(".cantidad")
.forEach(input => {

    input.addEventListener(
        "input",
        calcularTotal
    );
});

document
.querySelectorAll(".producto-check")
.forEach(check => {

    check.addEventListener(
        "change",
        calcularTotal
    );
});

calcularTotal();

</script>
</body>

</html>
