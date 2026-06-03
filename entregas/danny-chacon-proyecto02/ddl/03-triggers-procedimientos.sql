-- ============================================================
-- FARMACIA DE BARRIO - TRIGGERS Y RELACIONES
-- Script 03: Triggers para lógica de negocio
-- ============================================================

USE farmacia_barrio;

-- ============================================================
-- TRIGGER 1: Descontar stock FIFO al vender
-- Evento: AFTER INSERT en detalle_ventas
-- Descripción: Automáticamente actualiza cantidad_disponible
--              del lote más antiguo al registrar una venta
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_venta_descontar_stock
AFTER INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    DECLARE stock_actual INT;
    
    -- Descontar la cantidad del lote
    UPDATE lotes 
    SET cantidad_disponible = cantidad_disponible - NEW.cantidad
    WHERE id_lote = NEW.id_lote;
    
    -- Validar que no haya quedado en negativo
    SELECT cantidad_disponible INTO stock_actual 
    FROM lotes 
    WHERE id_lote = NEW.id_lote;
    
    IF stock_actual < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Error: Stock insuficiente en el lote';
    END IF;
END $$

DELIMITER ;

-- ============================================================
-- TRIGGER 2: Validar cantidad en detalle_ventas
-- Evento: BEFORE INSERT en detalle_ventas
-- Descripción: Valida que haya stock suficiente antes de vender
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_validar_stock_venta
BEFORE INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    DECLARE stock_disponible INT;
    
    SELECT cantidad_disponible INTO stock_disponible
    FROM lotes
    WHERE id_lote = NEW.id_lote;
    
    IF IFNULL(stock_disponible, 0) < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente para esta venta';
    END IF;
END $$

DELIMITER ;

-- ============================================================
-- TRIGGER 3: Validar cantidad en detalle_compras
-- Evento: BEFORE INSERT en detalle_compras
-- Descripción: Valida que las cantidades sean positivas
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_validar_cantidad_compra
BEFORE INSERT ON detalle_compras
FOR EACH ROW
BEGIN
    IF NEW.cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor a 0';
    END IF;
    
    IF NEW.precio_unitario <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio unitario debe ser mayor a 0';
    END IF;
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END $$

DELIMITER ;

-- ============================================================
-- TRIGGER 4: Actualizar cantidad_inicial en lote
-- Evento: AFTER INSERT en detalle_compras
-- Descripción: Actualiza cantidad_inicial del lote basándose
--              en la cantidad comprada
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_actualizar_lote_compra
AFTER INSERT ON detalle_compras
FOR EACH ROW
BEGIN
    UPDATE lotes
    SET cantidad_inicial = cantidad_inicial + NEW.cantidad,
        cantidad_disponible = cantidad_disponible + NEW.cantidad
    WHERE id_lote = NEW.id_lote;
END $$

DELIMITER ;

-- ============================================================
-- TRIGGER 5: Calcular monto_total en detalle_ventas
-- Evento: BEFORE INSERT en detalle_ventas
-- Descripción: Calcula automáticamente el subtotal
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_calcular_subtotal_venta
BEFORE INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    IF NEW.cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor a 0';
    END IF;
    
    IF NEW.precio_unitario <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio unitario debe ser mayor a 0';
    END IF;
    
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END $$

DELIMITER ;

-- ============================================================
-- TRIGGER 6: Validar producto en lotes
-- Evento: BEFORE INSERT en lotes
-- Descripción: Verifica que el producto exista
-- ============================================================

DELIMITER $$

CREATE TRIGGER tr_validar_producto_lote
BEFORE INSERT ON lotes
FOR EACH ROW
BEGIN
    DECLARE producto_existe INT;
    
    SELECT COUNT(*) INTO producto_existe
    FROM productos
    WHERE id_producto = NEW.id_producto;
    
    IF producto_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto especificado no existe';
    END IF;
    
    IF NEW.cantidad_inicial < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad inicial debe ser mayor o igual a 0';
    END IF;
    
    SET NEW.cantidad_disponible = NEW.cantidad_inicial;
END $$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO ALMACENADO: Obtener lotes para FIFO
-- Descripción: Retorna el lote más antiguo de un producto
--              para aplicar la política FIFO
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_obtener_lote_fifo(
    IN p_id_producto INT
)
BEGIN
    SELECT 
        id_lote,
        numero_lote,
        fecha_vencimiento,
        cantidad_disponible,
        DATEDIFF(fecha_vencimiento, CURDATE()) as dias_para_vencer
    FROM lotes
    WHERE id_producto = p_id_producto 
    AND cantidad_disponible > 0
    AND fecha_vencimiento > CURDATE()
    ORDER BY fecha_vencimiento ASC
    LIMIT 1;
END $$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO ALMACENADO: Alertas de stock bajo
-- Descripción: Retorna productos con stock bajo
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_alertas_stock_bajo()
BEGIN
    SELECT 
        p.id_producto,
        p.nombre_producto,
        p.codigo_sku,
        p.cantidad_minima,
        COALESCE(SUM(l.cantidad_disponible), 0) as stock_actual,
        CONCAT(ROUND((COALESCE(SUM(l.cantidad_disponible), 0) / p.cantidad_minima * 100), 2), '%') as porcentaje_stock
    FROM productos p
    LEFT JOIN lotes l ON p.id_producto = l.id_producto
    GROUP BY p.id_producto, p.nombre_producto, p.codigo_sku, p.cantidad_minima
    HAVING stock_actual < p.cantidad_minima
    ORDER BY stock_actual ASC;
END $$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO ALMACENADO: Alertas de vencimiento
-- Descripción: Retorna lotes que vencen en los próximos 7 días
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_alertas_vencimiento()
BEGIN
    SELECT 
        l.id_lote,
        p.nombre_producto,
        l.numero_lote,
        l.fecha_vencimiento,
        l.cantidad_disponible,
        DATEDIFF(l.fecha_vencimiento, CURDATE()) as dias_para_vencer,
        CASE 
            WHEN DATEDIFF(l.fecha_vencimiento, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(l.fecha_vencimiento, CURDATE()) <= 3 THEN 'CRÍTICO'
            WHEN DATEDIFF(l.fecha_vencimiento, CURDATE()) <= 7 THEN 'ALERTA'
            ELSE 'OK'
        END as estado_vencimiento
    FROM lotes l
    JOIN productos p ON l.id_producto = p.id_producto
    WHERE l.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY l.fecha_vencimiento ASC;
END $$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO ALMACENADO: Reporte de ventas por fecha
-- Descripción: Retorna ventas en un rango de fechas
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_reporte_ventas_fecha(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT 
        v.id_venta,
        v.numero_venta,
        v.fecha_venta,
        v.cliente_nombre,
        v.cliente_ci,
        v.monto_total,
        v.metodo_pago,
        v.estado,
        COUNT(dv.id_detalle_venta) as cantidad_items
    FROM ventas v
    LEFT JOIN detalle_ventas dv ON v.id_venta = dv.id_venta
    WHERE v.fecha_venta BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY v.id_venta, v.numero_venta, v.fecha_venta, v.cliente_nombre, 
             v.cliente_ci, v.monto_total, v.metodo_pago, v.estado
    ORDER BY v.fecha_venta DESC;
END $$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO ALMACENADO: Reporte de inventario actual
-- Descripción: Retorna stock actual de todos los productos
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_reporte_inventario()
BEGIN
    SELECT 
        p.id_producto,
        p.nombre_producto,
        p.codigo_sku,
        c.nombre_categoria,
        p.precio_venta,
        COALESCE(SUM(l.cantidad_disponible), 0) as stock_total,
        COUNT(DISTINCT l.id_lote) as cantidad_lotes,
        MIN(l.fecha_vencimiento) as proximo_vencimiento,
        CASE 
            WHEN COALESCE(SUM(l.cantidad_disponible), 0) < p.cantidad_minima THEN 'BAJO'
            WHEN COALESCE(SUM(l.cantidad_disponible), 0) > p.cantidad_minima * 3 THEN 'ALTO'
            ELSE 'NORMAL'
        END as estado_stock
    FROM productos p
    JOIN categorias c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON p.id_producto = l.id_producto
    WHERE p.estado = 'activo'
    GROUP BY p.id_producto, p.nombre_producto, p.codigo_sku, 
             c.nombre_categoria, p.precio_venta, p.cantidad_minima
    ORDER BY stock_total ASC;
END $$

DELIMITER ;

-- ============================================================
-- CONFIRMACIÓN
-- ============================================================
SELECT 'Todos los triggers y procedimientos han sido creados' AS Mensaje;
