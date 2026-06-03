<?php
/**
 * SIDEBAR COMPONENT
 * Componente reutilizable del menú lateral
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="logo">
        FARMACIA<br>EL DANNY
    </div>
    
    <nav class="sidebar-menu">
        <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
            <span>Inicio</span>
        </a>

        <a href="medicamentos.php" class="<?php echo ($current_page === 'medicamentos.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-capsules"></i>
            <span>Medicamentos</span>
        </a>

        <a href="inventario.php" class="<?php echo ($current_page === 'inventario.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-boxes"></i>
            <span>Inventario</span>
        </a>

        <a href="clientes.php" class="<?php echo ($current_page === 'clientes.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Clientes</span>
        </a>

        <a href="pedidos.php" class="<?php echo ($current_page === 'pedidos.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Pedidos</span>
        </a>

       
        <a href="ventas.php" class="<?php echo ($current_page === 'ventas.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Ventas</span>
        </a>

        <a href="reportes.php" class="<?php echo ($current_page === 'reportes.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Reportes</span>
        </a>

        <a href="php/logout.php" style="color: #ff6b6b; margin-top: auto;">
            <i class="fa-solid fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </a>
    </nav>
</aside>
