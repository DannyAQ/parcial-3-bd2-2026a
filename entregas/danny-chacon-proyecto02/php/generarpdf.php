<?php
require_once 'conexion.php';
require_once 'utilidades.php';

if(!isset($_GET['id'])){
    die('Error: No se especificó factura');
}

$id_factura = (int)$_GET['id'];
$sql = "SELECT f.*, v.metodo_pago FROM facturas_venta f LEFT JOIN ventas v ON f.id_venta = v.id_venta WHERE f.id_factura = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_factura);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();

if(!$factura){
    die('Error: Factura no encontrada');
}

$sql_items = "SELECT * FROM detalle_factura_venta WHERE id_factura = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $id_factura);
$stmt->execute();
$items = [];
while($row = $stmt->get_result()->fetch_assoc()){
    $items[] = $row;
}

$hoy = date('d/m/Y H:i:s');
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
        <p>FACTURA #" . str_pad($factura['id_factura'], 5, '0', STR_PAD_LEFT) . "</p>
    </div>

    <div class='factura-info'>
        <div>
            <strong>CLIENTE:</strong><br>
            " . htmlspecialchars($factura['nombre_cliente']) . "<br>
            <strong>DOCUMENTO:</strong> " . htmlspecialchars($factura['documento_cliente'] ?? '-') . "<br>
            <strong>TELÉFONO:</strong> " . htmlspecialchars($factura['telefono_cliente'] ?? '-') . "<br>
            <strong>DIRECCIÓN:</strong> " . htmlspecialchars($factura['direccion_cliente'] ?? '-') . "
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
                <th>CÓDIGO</th>
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
                <td>" . str_pad($item['id_lote'], 5, '0', STR_PAD_LEFT) . "</td>
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
    </div>
</body>
</html>";

echo $html;
?>