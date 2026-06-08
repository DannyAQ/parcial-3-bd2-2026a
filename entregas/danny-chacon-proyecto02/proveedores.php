<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$titulo_pagina = "Proveedores";
$subtitulo_pagina = "Gestión de proveedores";

if(isset($_POST['crear_proveedor'])){
    $nombre_empresa = $_POST['nombre_empresa'] ?? '';
    $nit = $_POST['nit'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $contacto = $_POST['contacto'] ?? '';

    if($nombre_empresa){
        $sql = "INSERT INTO proveedores (nombre_empresa, nit, telefono, correo, direccion, contacto) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nombre_empresa, $nit, $telefono, $correo, $direccion, $contacto);
        if($stmt->execute()){
            $mensaje_exito = "Proveedor registrado exitosamente";
        }
    }
if(isset($_GET['desactivar'])){
    $id = $_GET['desactivar'];

    $sql = "UPDATE proveedores SET activo = 0 WHERE id_proveedor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        $mensaje_exito = "Proveedor desactivado";
    }
}

if(isset($_GET['activar'])){
    $id = $_GET['activar'];

    $sql = "UPDATE proveedores SET activo = 1 WHERE id_proveedor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        $mensaje_exito = "Proveedor activado";
    }
}

$buscar = $_GET['buscar'] ?? '';
$sql = "SELECT * FROM proveedores WHERE nombre_empresa LIKE ? OR nit LIKE ? OR correo LIKE ? ORDER BY nombre_empresa ASC";
$stmt = $conn->prepare($sql);
$buscar_param = "%{$buscar}%";
$stmt->bind_param("sss", $buscar_param, $buscar_param, $buscar_param);
$stmt->execute();
$resultado = $stmt->get_result();
$proveedores = [];
while($row = $resultado->fetch_assoc()){
    $proveedores[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - Farmacia El Danny</title>
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
                <div class="card-title">Total Proveedores</div>
                <div class="card-value"><?php echo count($proveedores); ?></div>
                <div class="card-small">Proveedores activos</div>
            </div>
        </div>

        <div class="toolbar">
            <form method="GET" style="display: flex; flex: 1; gap: 10px; min-width: 300px;">
                <input type="text" name="buscar" placeholder="Buscar proveedor..." value="<?php echo htmlspecialchars($buscar); ?>" style="flex: 1; padding: 12px 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: white; font-family: 'Inter', sans-serif;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            </form>
            <button class="btn btn-primary" onclick="abrirModal()"><i class="fa-solid fa-plus"></i> Nuevo Proveedor</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>NIT</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($proveedores) > 0): ?>
                        <?php foreach($proveedores as $prov): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prov['nombre_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($prov['nit'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($prov['telefono'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($prov['correo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($prov['contacto'] ?? '-'); ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-small"><i class="fa-solid fa-edit"></i></button>
                                        <a href="?eliminar=<?php echo $prov['id_proveedor']; ?>" class="btn-small danger" onclick="return confirm('¿Eliminar?')"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">No hay proveedores registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 25px;">
            <a href="pedidos.php" class="btn btn-dark" style="border: 1px solid rgba(255,255,255,0.1);">
                <i class="fa-solid fa-arrow-left"></i> Volver a Pedidos
            </a>
        </div>
    </div>

    <div class="modal" id="modalProveedor">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nuevo Proveedor</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre Empresa</label>
                    <input type="text" name="nombre_empresa" class="form-control" placeholder="Nombre de la empresa" required>
                </div>

                <div class="form-group">
                    <label class="form-label">NIT</label>
                    <input type="text" name="nit" class="form-control" placeholder="NIT">
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
                </div>

                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" placeholder="correo@empresa.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Contacto</label>
                    <input type="text" name="contacto" class="form-control" placeholder="Nombre del contacto">
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3" placeholder="Dirección completa"></textarea>
                </div>

                <button type="submit" name="crear_proveedor" class="btn btn-primary" style="width: 100%; margin-top: 20px;"><i class="fa-solid fa-plus"></i> Registrar Proveedor</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(){document.getElementById('modalProveedor').classList.add('active');}
        function cerrarModal(){document.getElementById('modalProveedor').classList.remove('active');}
        window.onclick = function(event){let modal = document.getElementById('modalProveedor'); if(event.target === modal){modal.classList.remove('active');}}
    </script>

</body>

</html>
