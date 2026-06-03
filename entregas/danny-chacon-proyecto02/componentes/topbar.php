<?php
/**
 * TOPBAR COMPONENT
 * Barra superior del sistema
 */
?>

<div class="topbar">
    <div>
        <h1><?php echo isset($titulo_pagina) ? $titulo_pagina : 'Panel de Gestión'; ?></h1>
        <p style="color:#8b8b8b; margin-top:10px;">
            <?php echo isset($subtitulo_pagina) ? $subtitulo_pagina : 'Sistema de gestión farmacéutica'; ?>
        </p>
    </div>

    <div class="topbar-right">
        <button class="btn btn-dark" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
            <i class="fa-solid fa-bell"></i>
        </button>

        <span style="color: var(--text-muted); margin-left: 20px;">
            <i class="fa-solid fa-user-circle" style="margin-right: 8px;"></i>
            <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </span>
    </div>
</div>
