<?php
session_start();
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['crear_producto'])){

    $nombre = trim($_POST['nombre']);
    $principio_activo = trim($_POST['principio_activo']);
    $presentacion = trim($_POST['presentacion']);
    $descripcion = trim($_POST['descripcion']);
    $precio_venta = (float)$_POST['precio_venta'];
    $stock_minimo = (int)$_POST['stock_minimo'];
    $tipo_venta = $_POST['tipo_venta'];

    $categoria_nueva = trim($_POST['categoria_nueva'] ?? '');
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);

    // Crear categoría nueva si fue digitada
    if(!empty($categoria_nueva)){

        $sql_cat = "SELECT id_categoria FROM categorias WHERE nombre = ?";
        $stmt_cat = $conn->prepare($sql_cat);
        $stmt_cat->bind_param("s", $categoria_nueva);
        $stmt_cat->execute();

        $res_cat = $stmt_cat->get_result();

        if($fila = $res_cat->fetch_assoc()){
            $id_categoria = $fila['id_categoria'];
        } else {

            $sql_insert_cat = "INSERT INTO categorias(nombre) VALUES(?)";
            $stmt_insert_cat = $conn->prepare($sql_insert_cat);
            $stmt_insert_cat->bind_param("s", $categoria_nueva);

            if($stmt_insert_cat->execute()){
                $id_categoria = $conn->insert_id;
            }

            $stmt_insert_cat->close();
        }

        $stmt_cat->close();
    }

    if($id_categoria > 0){

        $sql = "INSERT INTO productos
                (
                    nombre,
                    principio_activo,
                    presentacion,
                    descripcion,
                    precio_venta,
                    stock_minimo,
                    id_categoria,
                    tipo_venta
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssdiis",
            $nombre,
            $principio_activo,
            $presentacion,
            $descripcion,
            $precio_venta,
            $stock_minimo,
            $id_categoria,
            $tipo_venta
        );

        if($stmt->execute()){
            $mensaje_exito = "Medicamento creado correctamente";
        } else {
            $mensaje_error = "Error al crear el medicamento";
        }

        $stmt->close();

    } else {
        $mensaje_error = "Debe seleccionar o crear una categoría";
    }
}

$titulo_pagina = "Medicamentos";
$subtitulo_pagina = "Listado y visualización de medicamentos";

$buscar = $_GET['buscar'] ?? '';
$id_categoria = $_GET['id_categoria'] ?? '';

$sql = "SELECT
            p.*,
            c.nombre AS categoria_nombre,
            COALESCE(SUM(l.cantidad_disponible),0) AS cantidad_disponible
        FROM productos p
        LEFT JOIN categorias c
            ON p.id_categoria = c.id_categoria
        LEFT JOIN lotes l
            ON p.id_producto = l.id_producto
            AND l.estado <> 'ELIMINADO'
        WHERE 1=1";

$params = [];
$tipos = "";

if (!empty($buscar)) {
    $sql .= " AND (p.nombre LIKE ? OR p.principio_activo LIKE ?)";
    $buscar_param = "%{$buscar}%";
    $params[] = $buscar_param;
    $params[] = $buscar_param;
    $tipos .= "ss";
}

if (!empty($id_categoria)) {
    $sql .= " AND p.id_categoria = ?";
    $params[] = (int)$id_categoria;
    $tipos .= "i";
}
$sql .= " GROUP BY p.id_producto";
$sql .= " ORDER BY p.nombre ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();

$resultado = $stmt->get_result();

$productos = [];

while ($row = $resultado->fetch_assoc()) {
    $productos[] = $row;
}

$resultado->free();
$stmt->close();

$categorias = [];

$sql_cat = "SELECT * FROM categorias ORDER BY nombre ASC";

if ($resultado = $conn->query($sql_cat)) {
    while ($row = $resultado->fetch_assoc()) {
        $categorias[] = $row;
    }
    $resultado->free();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos - Farmacia El Danny</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: linear-gradient(180deg, rgba(18,18,18,0.95), rgba(10,10,10,0.98));
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            padding: 35px;
            max-width: 600px;
            width: 90%;
            backdrop-filter: blur(12px);
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 15px;
        }
        .modal-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
            transition: var(--transition);
        }
        .modal-close:hover {
            color: white;
        }
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }
        .search-box input {
            flex: 1;
            padding: 12px 16px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }
        .search-box input:focus {
            outline: none;
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.15);
        }
        .search-box button {
            padding: 12px 24px;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .btn-small {
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.02);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-small:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.2);
        }
        .btn-small.danger {
            color: #ff6b6b;
            border-color: rgba(255, 107, 107, 0.2);
        }
        .btn-small.danger:hover {
            background: rgba(255, 107, 107, 0.1);
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <?php require_once 'componentes/sidebar.php'; ?>

    <div class="main">
        <?php require_once 'componentes/topbar.php'; ?>

        <?php if(isset($mensaje_exito)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <div class="card-title">Total Medicamentos</div>
                <div class="card-value"><?php echo count($productos); ?></div>
                <div class="card-small">Productos en el sistema</div>
            </div>

            <div class="card">
                <div class="card-title">Categorías</div>
                <div class="card-value"><?php echo count($categorias); ?></div>
                <div class="card-small">Categorías disponibles</div>
            </div>
        </div>

        <div class="toolbar">
            <form method="GET" style="display: flex; flex: 1; gap: 10px; min-width: 300px;">
                <input type="text" name="buscar" placeholder="Buscar medicamento..." value="<?php echo htmlspecialchars($buscar); ?>" style="flex: 1;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
            </form>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fa-solid fa-plus"></i> Nuevo Medicamento
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Principio Activo</th>
                        <th>Presentación</th>
                        <th>Precio</th>
                        <th>Stock Mín.</th>
                        <th>Cantidad</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($productos) > 0): ?>
                        <?php foreach($productos as $med): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($med['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($med['principio_activo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($med['presentacion'] ?? '-'); ?></td>
                                <td>$<?php echo number_format($med['precio_venta'], 2); ?></td>
                                <td><?php echo $med['stock_minimo']; ?></td>
                                <td><?php echo htmlspecialchars($med['cantidad_disponible'] ?? '-'); ?></td>    
                                <td><?php echo htmlspecialchars($med['categoria_nombre'] ?? '-'); ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-small" onclick="alert('Editar próximamente')">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <a href="?eliminar=<?php echo $med['id_producto']; ?>" class="btn-small danger" onclick="return confirm('¿Eliminar este medicamento?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                No hay medicamentos registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalProducto">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nuevo Medicamento</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre del Medicamento</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Ibuprofeno" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Principio Activo</label>
                    <input type="text" name="principio_activo" class="form-control" placeholder="Ej: Ibuprofen 400mg">
                </div>

                <div class="form-group">
                    <label class="form-label">Presentación</label>
                    <input type="text" name="presentacion" class="form-control" placeholder="Ej: Tabletas x 20">
                </div>

                <div class="form-group">
                    <label class="form-label">Categoría</label>
                    <select name="id_categoria" class="form-control" >
                        <option value="">Seleccionar categoría</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">O crear nueva categoría</label>

                    <input
                        type="text"
                        name="categoria_nueva"
                        class="form-control"
                        placeholder="Ej: Antibióticos, Analgésicos..."
                    >
                </div>
                <div class="form-group">
                <label class="form-label">Tipo de Venta</label>

                <select name="tipo_venta" class="form-control" required>
                    <option value="VENTA_LIBRE">Venta Libre</option>
                    <option value="FORMULA_MEDICA">Fórmula Médica</option>
                </select>
            </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Precio Venta</label>
                        <input type="number" name="precio_venta" class="form-control" placeholder="0.00" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" class="form-control" value="5" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción del medicamento"></textarea>
                </div>

                <button type="submit" name="crear_producto" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    <i class="fa-solid fa-plus"></i> Crear Medicamento
                </button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(){
            document.getElementById('modalProducto').classList.add('active');
        }

        function cerrarModal(){
            document.getElementById('modalProducto').classList.remove('active');
        }

        window.onclick = function(event){
            let modal = document.getElementById('modalProducto');
            if(event.target === modal){
                modal.classList.remove('active');
            }
        }
    </script>

</body>

</html>
