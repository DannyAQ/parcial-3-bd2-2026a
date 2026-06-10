-- ================================================================
-- MIGRACIÓN OPCIONAL: Agregar soporte para borrado lógico en productos
-- Ejecutar SOLO si la tabla productos NO tiene el campo 'estado'
-- ================================================================

-- Verificar si el campo existe y agregarlo si no existe
ALTER TABLE productos ADD COLUMN estado ENUM('activo', 'descontinuado') DEFAULT 'activo';

-- Opcional: Crear índice para mejorar performance en búsquedas
ALTER TABLE productos ADD INDEX idx_estado (estado);

-- Los cambios en medicamentos.php requieren:
-- 1. Campo 'estado' en tabla productos
-- 2. Uso en queries: WHERE p.estado <> 'descontinuado'
-- 3. Eliminación lógica: UPDATE productos SET estado = 'descontinuado' WHERE id_producto = ?
