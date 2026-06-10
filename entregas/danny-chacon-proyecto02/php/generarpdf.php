<?php
require_once 'conexion.php';
require_once 'utilidades.php';

if(!isset($_GET['id'])){
    die('Error: No se especificó factura');
}

$id_factura = (int)$_GET['id'];

// Cargar factura y relacionar con venta y cliente
$sql = "
    SELECT f.*, v.metodo_pago, v.id_cliente, v.id_venta
    FROM facturas_venta f 
    LEFT JOIN ventas v ON f.id_venta = v.id_venta 
    WHERE f.id_factura = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_factura);
$stmt->execute();
$result = $stmt->get_result();
$factura = $result->fetch_assoc();
$stmt->close();

if(!$factura){
    die('Error: Factura no encontrada');
}

// Cargar datos completos del cliente desde tabla clientes
$cliente_datos = [];
if($factura['id_cliente']){
    $sql_cli = "SELECT nombre, apellido, telefono, correo, direccion FROM clientes WHERE id_cliente = ?";
    $stmt_cli = $conn->prepare($sql_cli);
    $stmt_cli->bind_param("i", $factura['id_cliente']);
    $stmt_cli->execute();
    $result_cli = $stmt_cli->get_result();
    $cliente_datos = $result_cli->fetch_assoc();
    $stmt_cli->close();
}

// Cargar items de la venta desde detalle_ventas con JOINs
$sql_items = "
    SELECT 
        dv.id_producto,
        dv.id_lote,
        dv.cantidad,
        dv.precio_unitario,
        dv.subtotal,
        p.nombre as nombre_producto,
        p.presentacion,
        l.codigo_lote
    FROM detalle_ventas dv
    INNER JOIN productos p ON dv.id_producto = p.id_producto
    INNER JOIN lotes l ON dv.id_lote = l.id_lote
    WHERE dv.id_venta = ?
";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $factura['id_venta']);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$items = [];
while($row = $result_items->fetch_assoc()){
    $items[] = $row;
}
$stmt_items->close();

$hoy = date('d/m/Y H:i:s');

// Preparar datos del cliente: usar datos de tabla clientes si existen, sino usar datos de factura
$nombre_cliente_display = ($cliente_datos && !empty($cliente_datos['nombre'])) 
    ? $cliente_datos['nombre'] . ' ' . ($cliente_datos['apellido'] ?? '')
    : $factura['nombre_cliente'];
$telefono_cliente_display = ($cliente_datos && !empty($cliente_datos['telefono'])) 
    ? $cliente_datos['telefono'] 
    : ($factura['telefono_cliente'] ?? '-');
$direccion_cliente_display = ($cliente_datos && !empty($cliente_datos['direccion'])) 
    ? $cliente_datos['direccion'] 
    : ($factura['direccion_cliente'] ?? '-');

$html = "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background: white; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; }
        .factura-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; font-size: 12px; }
        .factura-info div { border: 1px solid #ccc; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f0f0f0; border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 8px; }
        .total-row { background: #f0f0f0; font-weight: bold; }
        .footer { text-align: center; font-size: 11px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>FARMACIA EL DANNY</h1>
        <p>NIT: 123456789-1</p>
        <p>FACTURA #" . htmlspecialchars($factura['numero_factura']) . "</p>
    </div>

    <div class='factura-info'>
        <div>
            <strong>CLIENTE:</strong><br>
            " . htmlspecialchars($nombre_cliente_display) . "<br>
            <strong>TELÉFONO:</strong> " . htmlspecialchars($telefono_cliente_display) . "<br>
            <strong>DIRECCIÓN:</strong> " . htmlspecialchars($direccion_cliente_display) . "
        </div>
        <div>
            <strong>FECHA EMISIÓN:</strong> " . date('d/m/Y H:i', strtotime($factura['fecha_emision'])) . "<br>
            <strong>FECHA DOCUMENTO:</strong> " . $hoy . "<br>
            <strong>MÉTODO PAGO:</strong> " . htmlspecialchars($factura['metodo_pago'] ?? '-') . "<br>
            <strong>ESTADO:</strong> " . htmlspecialchars($factura['estado']) . "
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>CÓDIGO LOTE</th>
                <th>PRODUCTO</th>
                <th style='text-align: right;'>CANT</th>
                <th style='text-align: right;'>V/UNIT</th>
                <th style='text-align: right;'>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>";

foreach($items as $item){
    $html .= "
            <tr>
                <td>" . htmlspecialchars($item['codigo_lote'] ?? '-') . "</td>
                <td>" . htmlspecialchars($item['nombre_producto']) . " - " . htmlspecialchars($item['presentacion'] ?? '') . "</td>
                <td style='text-align: right;'>" . $item['cantidad'] . "</td>
                <td style='text-align: right;'>\$" . number_format($item['precio_unitario'], 2, '.', ',') . "</td>
                <td style='text-align: right;'>\$" . number_format($item['subtotal'], 2, '.', ',') . "</td>
            </tr>";
}

$html .= "
        </tbody>
    </table>

    <div style='text-align: right; width: 300px; margin-left: auto;'>
        <table style='width: 100%; border: none;'>
            <tr>
                <td style='border: none; text-align: right;'><strong>SUBTOTAL:</strong></td>
                <td style='border: none; text-align: right;'>\$" . number_format($factura['subtotal'], 2, '.', ',') . "</td>
            </tr>
            <tr>
                <td style='border: none; text-align: right;'><strong>IVA (19%):</strong></td>
                <td style='border: none; text-align: right;'>\$" . number_format($factura['iva'], 2, '.', ',') . "</td>
            </tr>
            <tr>
                <td style='border: none; text-align: right;'><strong>DESCUENTO:</strong></td>
                <td style='border: none; text-align: right;'>\$" . number_format($factura['descuento'], 2, '.', ',') . "</td>
            </tr>
            <tr class='total-row'>
                <td style='border: none; text-align: right;'><strong>TOTAL:</strong></td>
                <td style='border: none; text-align: right; font-size: 16px;'>\$" . number_format($factura['total_final'], 2, '.', ',') . "</td>
            </tr>
        </table>
    </div>

    <div class='footer'>
        <p>Factura generada automáticamente - Farmacia El Danny</p>
        <p>Gracias por su compra</p>
        <p> Todos los derechos reservados &copy; " . date('Y') . " </p>
        <p>Dannyak_ofc</p>
    </div>
</body>
</html>";

echo $html;
?>