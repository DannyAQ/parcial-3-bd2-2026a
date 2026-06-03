
<?php

session_start();
require_once 'php/conexion.php';

// Si ya está autenticado, redirige a index
if(isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

/* =========================================
   LOGIN
========================================= */

if(isset($_POST['login'])){

    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM trabajadores 
            WHERE usuario = ? 
            AND contrasena = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $usuario, $contrasena);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){

        $trabajador = $resultado->fetch_assoc();

        $_SESSION['usuario'] = $trabajador['usuario'];
        $_SESSION['nombre'] = $trabajador['nombre'];

        header("Location: index.php");
        exit();

    }else{

        $error = "Usuario o contraseña incorrectos";
    }
}

/* =========================================
   REGISTRO
========================================= */

if(isset($_POST['registro'])){

    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $cargo = $_POST['cargo'];
    $usuario = $_POST['usuario_reg'];
    $contrasena = $_POST['contrasena_reg'];

    $verificar = "SELECT * FROM trabajadores 
                  WHERE usuario = ?";

    $stmt = $conn->prepare($verificar);

    $stmt->bind_param("s", $usuario);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){

        $error = "Ese usuario ya existe";

    }else{

        $sql = "INSERT INTO trabajadores(
                    id_cedula,
                    nombre,
                    apellido,
                    telefono,
                    cargo,
                    usuario,
                    contrasena
                )
                VALUES(?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "issssss",
            $cedula,
            $nombre,
            $apellido,
            $telefono,
            $cargo,
            $usuario,
            $contrasena
        );

        if($stmt->execute()){

            $success = "Trabajador registrado correctamente";

        }else{

            $error = "Error al registrar";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Farmacia El Danny</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Estilos específicos para login */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            width: 100%;
            max-width: 1200px;
            padding: 40px;
        }

        .login-brand {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .login-brand h1 {
            font-size: 64px;
            font-weight: 900;
            letter-spacing: 4px;
            line-height: 1.2;
            margin-bottom: 40px;
            color: white;
        }

        .login-brand p {
            color: var(--text-muted);
            font-size: 16px;
            line-height: 1.6;
            max-width: 400px;
        }

        .auth-forms {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .form-card {
            background: linear-gradient(180deg, rgba(18,18,18,0.95), rgba(10,10,10,0.98));
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            padding: 35px;
            backdrop-filter: blur(12px);
        }

        .form-card h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }

        .form-card p {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.15);
            box-shadow: 0 0 15px rgba(255,255,255,0.05);
        }

        .form-control::placeholder {
            color: var(--text-soft);
        }

        .btn-login {
            width: 100%;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 13px;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.12);
            color: #ff6b6b;
            border-color: rgba(220, 53, 69, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.12);
            color: #51cf66;
            border-color: rgba(40, 167, 69, 0.3);
        }

        #login-form {
            animation: slideIn 0.4s ease-out;
            transition: all 0.4s ease-out;
        }

        #login-form.hide {
            display: none;
            animation: slideOut 0.4s ease-out;
        }

        #registro-form {
            display: none;
            animation: slideDown 0.4s ease-out;
        }

        #registro-form.show {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-20px);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-toggle-registro {
            margin-top: 20px;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .auth-forms {
                grid-template-columns: 1fr;
            }

            .login-brand h1 {
                font-size: 48px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <!-- MARCA Y DESCRIPCIÓN -->
        <div class="login-brand">
            <h1>FARMACIA<br>EL DANNY</h1>
            <p>Sistema de gestión farmacéutica </p>
        </div>

        <!-- FORMULARIOS DE LOGIN Y REGISTRO -->
        <div class="auth-forms">

            <!-- FORMULARIO DE LOGIN -->
            <div class="form-card" id="login-form">
                <h2>Ingresar</h2>
                <p>Accede al sistema</p>

                <?php if($error != ""){ ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                    </div>
                <?php } ?>

                <?php if($success != ""){ ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> <?php echo $success; ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu usuario" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" placeholder="Ingresa tu contraseña" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary btn-login">
                        <i class="fa-solid fa-right-to-bracket"></i> Ingresar
                    </button>

                    <button type="button" class="btn btn-dark btn-toggle-registro" onclick="toggleRegistro()">
                        <i class="fa-solid fa-user-plus"></i> Registrar Empleado
                    </button>
                </form>
            </div>

            <!-- FORMULARIO DE REGISTRO -->
            <div class="form-card" id="registro-form">
                <h2>Nuevo Empleado</h2>
                <p>Registrar trabajador</p>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Cédula</label>
                        <input type="number" name="cedula" class="form-control" placeholder="Número de cédula" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo</label>
                        <input type="text" name="cargo" class="form-control" placeholder="Cargo">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="usuario_reg" class="form-control" placeholder="Crea un usuario" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena_reg" class="form-control" placeholder="Crea una contraseña segura" required>
                    </div>

                    <button type="submit" name="registro" class="btn btn-dark btn-login">
                        <i class="fa-solid fa-user-plus"></i> Registrar Empleado
                    </button>

                    <button type="button" class="btn btn-dark btn-login" onclick="toggleRegistro()" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </form>
            </div>

        </div>

    </div>

    <script>
        function toggleRegistro() {
            const loginForm = document.getElementById('login-form');
            const registroForm = document.getElementById('registro-form');
            
            loginForm.classList.toggle('hide');
            registroForm.classList.toggle('show');
            
            if (registroForm.classList.contains('show')) {
                setTimeout(() => {
                    registroForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
        }

        // Mostrar login automáticamente después de registro exitoso
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                const loginForm = document.getElementById('login-form');
                const registroForm = document.getElementById('registro-form');
                
                setTimeout(() => {
                    loginForm.classList.remove('hide');
                    registroForm.classList.remove('show');
                    
                    setTimeout(() => {
                        loginForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 400);
                }, 2000);
            }
        });
    </script>

</body>

</html>
