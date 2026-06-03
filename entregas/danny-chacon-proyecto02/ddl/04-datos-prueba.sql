-- ============================================================
-- FARMACIA DE BARRIO - DATOS DE PRUEBA
-- Script 04: Inserciones de datos de ejemplo
-- ============================================================

USE farmacia_barrio;

-- ============================================================
-- INSERTAR USUARIOS (3 usuarios)
-- ============================================================

INSERT INTO usuarios (usuario, email, contraseña, rol, estado) VALUES
('admin', 'admin@farmacia.com', 'admin123', 'admin', 'activo'),
('vendedor1', 'vendedor1@farmacia.com', 'vendedor123', 'vendedor', 'activo'),
('gerente', 'gerente@farmacia.com', 'gerente123', 'gerente', 'activo');

-- ============================================================
-- INSERTAR CATEGORÍAS (5 categorías)
-- ============================================================

INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES
('Antibióticos', 'Medicamentos para infecciones bacterianas', 'activo'),
('Analgésicos', 'Medicamentos para el dolor', 'activo'),
('Antiinflamatorios', 'Medicamentos para la inflamación', 'activo'),
('Vitaminas', 'Suplementos vitamínicos y minerales', 'activo'),
('Antidiarreicos', 'Medicamentos para la diarrea', 'activo');

-- ============================================================
-- INSERTAR PROVEEDORES (5 proveedores)
-- ============================================================

INSERT INTO proveedores (nombre_proveedor, contacto, telefono, email, direccion, estado) VALUES
('Farmacéutica Global SRL', 'Carlos Mendez', '3345671234', 'carlos@farmglobal.com', 'Av. Principal 123, La Paz', 'activo'),
('Distribuidora Nacional', 'Juan Pérez', '3345672345', 'juan@distnal.com', 'Calle 2 esq. 3, Cochabamba', 'activo'),
('Medicinas Express', 'María García', '3345673456', 'maria@medexpress.com', 'Avenida Santa Cruz 456, Santa Cruz', 'activo'),
('Laboratorio Boliviano', 'Roberto Torres', '3345674567', 'roberto@labbol.com', 'Calle Junín 789, La Paz', 'activo'),
('Importadora América', 'Patricia López', '3345675678', 'patricia@impamerica.com', 'Pasaje 4 de Agosto, Oruro', 'activo');

-- ============================================================
-- INSERTAR PRODUCTOS (15 productos)
-- ============================================================

INSERT INTO productos (id_categoria, nombre_producto, codigo_sku, principio_activo, presentacion, precio_compra, precio_venta, cantidad_minima, estado) VALUES
-- Antibióticos
(1, 'Amoxicilina 500mg', 'SKU001', 'Amoxicilina', '500mg cápsula', 0.75, 2.50, 20, 'activo'),
(1, 'Ciprofloxacino 500mg', 'SKU002', 'Ciprofloxacino', '500mg comprimido', 2.00, 6.00, 15, 'activo'),
(1, 'Azitromicina 250mg', 'SKU003', 'Azitromicina', '250mg comprimido', 1.50, 5.00, 15, 'activo'),

-- Analgésicos
(2, 'Ibuprofeno 400mg', 'SKU004', 'Ibuprofeno', '400mg comprimido', 0.50, 1.80, 25, 'activo'),
(2, 'Paracetamol 500mg', 'SKU005', 'Paracetamol', '500mg comprimido', 0.35, 1.50, 30, 'activo'),
(2, 'Ketorolaco 10mg', 'SKU006', 'Ketorolaco', '10mg inyectable', 3.50, 10.00, 10, 'activo'),

-- Antiinflamatorios
(3, 'Naproxeno 500mg', 'SKU007', 'Naproxeno', '500mg comprimido', 1.20, 4.00, 20, 'activo'),
(3, 'Diclofenaco 50mg', 'SKU008', 'Diclofenaco', '50mg comprimido', 0.80, 2.80, 20, 'activo'),
(3, 'Piroxicam 20mg', 'SKU009', 'Piroxicam', '20mg cápsula', 1.50, 5.50, 12, 'activo'),

-- Vitaminas
(4, 'Vitamina C 1000mg', 'SKU010', 'Ácido Ascórbico', '1000mg comprimido', 0.40, 1.50, 30, 'activo'),
(4, 'Vitamina B12 1000mcg', 'SKU011', 'Cianocobalamina', '1000mcg ampolla', 2.50, 8.00, 12, 'activo'),
(4, 'Complejo B', 'SKU012', 'Vitaminas B1,B2,B3,B6,B12', 'cápsula', 1.00, 3.50, 15, 'activo'),

-- Antidiarreicos
(5, 'Loperamida 2mg', 'SKU013', 'Loperamida', '2mg comprimido', 0.60, 2.00, 18, 'activo'),
(5, 'Bismuto subsalicilato', 'SKU014', 'Bismuto subsalicilato', '525mg/15ml suspensión', 2.00, 6.50, 10, 'activo'),
(5, 'Lactobacillus', 'SKU015', 'Lactobacillus acidophilus', '10^9 cápsula', 1.80, 6.00, 14, 'activo');

-- ============================================================
-- INSERTAR LOTES (45 lotes: 3 por producto)
-- ============================================================

-- Lotes para Amoxicilina 500mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(1, 'LOT-001-AMX', DATE_ADD(CURDATE(), INTERVAL 90 DAY), 100, 100),
(1, 'LOT-002-AMX', DATE_ADD(CURDATE(), INTERVAL 180 DAY), 150, 150),
(1, 'LOT-003-AMX', DATE_ADD(CURDATE(), INTERVAL 270 DAY), 120, 120);

-- Lotes para Ciprofloxacino 500mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(2, 'LOT-001-CIPRO', DATE_ADD(CURDATE(), INTERVAL 120 DAY), 80, 80),
(2, 'LOT-002-CIPRO', DATE_ADD(CURDATE(), INTERVAL 200 DAY), 100, 100),
(2, 'LOT-003-CIPRO', DATE_ADD(CURDATE(), INTERVAL 300 DAY), 90, 90);

-- Lotes para Azitromicina 250mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(3, 'LOT-001-AZI', DATE_ADD(CURDATE(), INTERVAL 110 DAY), 75, 75),
(3, 'LOT-002-AZI', DATE_ADD(CURDATE(), INTERVAL 190 DAY), 110, 110),
(3, 'LOT-003-AZI', DATE_ADD(CURDATE(), INTERVAL 280 DAY), 95, 95);

-- Lotes para Ibuprofeno 400mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(4, 'LOT-001-IBU', DATE_ADD(CURDATE(), INTERVAL 150 DAY), 200, 200),
(4, 'LOT-002-IBU', DATE_ADD(CURDATE(), INTERVAL 250 DAY), 250, 250),
(4, 'LOT-003-IBU', DATE_ADD(CURDATE(), INTERVAL 350 DAY), 180, 180);

-- Lotes para Paracetamol 500mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(5, 'LOT-001-PARA', DATE_ADD(CURDATE(), INTERVAL 140 DAY), 300, 300),
(5, 'LOT-002-PARA', DATE_ADD(CURDATE(), INTERVAL 240 DAY), 350, 350),
(5, 'LOT-003-PARA', DATE_ADD(CURDATE(), INTERVAL 340 DAY), 280, 280);

-- Lotes para Ketorolaco 10mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(6, 'LOT-001-KETO', DATE_ADD(CURDATE(), INTERVAL 100 DAY), 50, 50),
(6, 'LOT-002-KETO', DATE_ADD(CURDATE(), INTERVAL 200 DAY), 60, 60),
(6, 'LOT-003-KETO', DATE_ADD(CURDATE(), INTERVAL 300 DAY), 55, 55);

-- Lotes para Naproxeno 500mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(7, 'LOT-001-NAPRO', DATE_ADD(CURDATE(), INTERVAL 130 DAY), 120, 120),
(7, 'LOT-002-NAPRO', DATE_ADD(CURDATE(), INTERVAL 220 DAY), 150, 150),
(7, 'LOT-003-NAPRO', DATE_ADD(CURDATE(), INTERVAL 310 DAY), 140, 140);

-- Lotes para Diclofenaco 50mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(8, 'LOT-001-DICLO', DATE_ADD(CURDATE(), INTERVAL 125 DAY), 140, 140),
(8, 'LOT-002-DICLO', DATE_ADD(CURDATE(), INTERVAL 215 DAY), 160, 160),
(8, 'LOT-003-DICLO', DATE_ADD(CURDATE(), INTERVAL 305 DAY), 145, 145);

-- Lotes para Piroxicam 20mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(9, 'LOT-001-PIROX', DATE_ADD(CURDATE(), INTERVAL 105 DAY), 80, 80),
(9, 'LOT-002-PIROX', DATE_ADD(CURDATE(), INTERVAL 205 DAY), 100, 100),
(9, 'LOT-003-PIROX', DATE_ADD(CURDATE(), INTERVAL 305 DAY), 95, 95);

-- Lotes para Vitamina C 1000mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(10, 'LOT-001-VITC', DATE_ADD(CURDATE(), INTERVAL 180 DAY), 300, 300),
(10, 'LOT-002-VITC', DATE_ADD(CURDATE(), INTERVAL 280 DAY), 350, 350),
(10, 'LOT-003-VITC', DATE_ADD(CURDATE(), INTERVAL 380 DAY), 320, 320);

-- Lotes para Vitamina B12
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(11, 'LOT-001-B12', DATE_ADD(CURDATE(), INTERVAL 160 DAY), 100, 100),
(11, 'LOT-002-B12', DATE_ADD(CURDATE(), INTERVAL 260 DAY), 120, 120),
(11, 'LOT-003-B12', DATE_ADD(CURDATE(), INTERVAL 360 DAY), 110, 110);

-- Lotes para Complejo B
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(12, 'LOT-001-COMB', DATE_ADD(CURDATE(), INTERVAL 150 DAY), 200, 200),
(12, 'LOT-002-COMB', DATE_ADD(CURDATE(), INTERVAL 250 DAY), 240, 240),
(12, 'LOT-003-COMB', DATE_ADD(CURDATE(), INTERVAL 350 DAY), 220, 220);

-- Lotes para Loperamida 2mg
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(13, 'LOT-001-LOPER', DATE_ADD(CURDATE(), INTERVAL 120 DAY), 150, 150),
(13, 'LOT-002-LOPER', DATE_ADD(CURDATE(), INTERVAL 220 DAY), 180, 180),
(13, 'LOT-003-LOPER', DATE_ADD(CURDATE(), INTERVAL 320 DAY), 160, 160);

-- Lotes para Bismuto subsalicilato
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(14, 'LOT-001-BISMUTO', DATE_ADD(CURDATE(), INTERVAL 135 DAY), 90, 90),
(14, 'LOT-002-BISMUTO', DATE_ADD(CURDATE(), INTERVAL 235 DAY), 110, 110),
(14, 'LOT-003-BISMUTO', DATE_ADD(CURDATE(), INTERVAL 335 DAY), 100, 100);

-- Lotes para Lactobacillus
INSERT INTO lotes (id_producto, numero_lote, fecha_vencimiento, cantidad_inicial, cantidad_disponible) VALUES
(15, 'LOT-001-LACTO', DATE_ADD(CURDATE(), INTERVAL 145 DAY), 130, 130),
(15, 'LOT-002-LACTO', DATE_ADD(CURDATE(), INTERVAL 245 DAY), 150, 150),
(15, 'LOT-003-LACTO', DATE_ADD(CURDATE(), INTERVAL 345 DAY), 140, 140);

-- ============================================================
-- INSERTAR COMPRAS (20 compras)
-- ============================================================

INSERT INTO compras (id_proveedor, numero_compra, fecha_compra, monto_total, estado) VALUES
(1, 'COMP-001', DATE_SUB(CURDATE(), INTERVAL 60 DAY), 1250.00, 'completada'),
(1, 'COMP-002', DATE_SUB(CURDATE(), INTERVAL 55 DAY), 980.50, 'completada'),
(2, 'COMP-003', DATE_SUB(CURDATE(), INTERVAL 50 DAY), 1520.00, 'completada'),
(2, 'COMP-004', DATE_SUB(CURDATE(), INTERVAL 45 DAY), 890.75, 'completada'),
(3, 'COMP-005', DATE_SUB(CURDATE(), INTERVAL 40 DAY), 1350.25, 'completada'),
(3, 'COMP-006', DATE_SUB(CURDATE(), INTERVAL 35 DAY), 760.00, 'completada'),
(4, 'COMP-007', DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1100.50, 'completada'),
(4, 'COMP-008', DATE_SUB(CURDATE(), INTERVAL 25 DAY), 945.75, 'completada'),
(5, 'COMP-009', DATE_SUB(CURDATE(), INTERVAL 20 DAY), 1280.00, 'completada'),
(5, 'COMP-010', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 875.25, 'completada'),
(1, 'COMP-011', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 1450.00, 'completada'),
(2, 'COMP-012', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 920.50, 'completada'),
(3, 'COMP-013', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 1380.75, 'completada'),
(4, 'COMP-014', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 850.00, 'completada'),
(5, 'COMP-015', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1200.25, 'completada'),
(1, 'COMP-016', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 990.00, 'completada'),
(2, 'COMP-017', CURDATE(), 1320.50, 'completada'),
(3, 'COMP-018', CURDATE(), 1100.00, 'completada'),
(4, 'COMP-019', CURDATE(), 950.75, 'completada'),
(5, 'COMP-020', CURDATE(), 1180.25, 'completada');

-- ============================================================
-- INSERTAR DETALLE COMPRAS (asociar productos a compras)
-- ============================================================

INSERT INTO detalle_compras (id_compra, id_lote, cantidad, precio_unitario, subtotal) VALUES
-- Compra 1
(1, 1, 100, 0.75, 75.00),
(1, 4, 80, 2.00, 160.00),
(1, 7, 75, 1.50, 112.50),

-- Compra 2
(2, 10, 200, 0.50, 100.00),
(2, 13, 300, 0.35, 105.00),
(2, 16, 50, 3.50, 175.00),

-- Compra 3
(3, 19, 120, 1.20, 144.00),
(3, 22, 140, 0.80, 112.00),
(3, 25, 80, 1.50, 120.00),

-- Compra 4
(4, 28, 300, 0.40, 120.00),
(4, 31, 100, 2.50, 250.00),
(4, 34, 200, 1.00, 200.00),

-- Compra 5
(5, 37, 150, 0.60, 90.00),
(5, 40, 90, 2.00, 180.00),
(5, 43, 130, 1.80, 234.00),

-- Compra 6
(6, 2, 150, 0.75, 112.50),
(6, 5, 100, 2.00, 200.00),
(6, 8, 110, 1.50, 165.00),

-- Compra 7
(7, 11, 250, 0.50, 125.00),
(7, 14, 350, 0.35, 122.50),
(7, 17, 60, 3.50, 210.00),

-- Compra 8
(8, 20, 150, 1.20, 180.00),
(8, 23, 160, 0.80, 128.00),
(8, 26, 100, 1.50, 150.00),

-- Compra 9
(9, 29, 350, 0.40, 140.00),
(9, 32, 120, 2.50, 300.00),
(9, 35, 240, 1.00, 240.00),

-- Compra 10
(10, 38, 180, 0.60, 108.00),
(10, 41, 110, 2.00, 220.00),
(10, 44, 150, 1.80, 270.00),

-- Compra 11
(11, 3, 120, 0.75, 90.00),
(11, 6, 90, 2.00, 180.00),
(11, 9, 95, 1.50, 142.50),

-- Compra 12
(12, 12, 180, 0.50, 90.00),
(12, 15, 280, 0.35, 98.00),
(12, 18, 55, 3.50, 192.50),

-- Compra 13
(13, 21, 140, 1.20, 168.00),
(13, 24, 145, 0.80, 116.00),
(13, 27, 95, 1.50, 142.50),

-- Compra 14
(14, 30, 320, 0.40, 128.00),
(14, 33, 110, 2.50, 275.00),
(14, 36, 220, 1.00, 220.00),

-- Compra 15
(15, 39, 160, 0.60, 96.00),
(15, 42, 100, 2.00, 200.00),
(15, 45, 140, 1.80, 252.00),

-- Compra 16
(16, 2, 100, 0.75, 75.00),
(16, 5, 80, 2.00, 160.00),
(16, 8, 75, 1.50, 112.50),

-- Compra 17
(17, 11, 200, 0.50, 100.00),
(17, 14, 250, 0.35, 87.50),
(17, 17, 50, 3.50, 175.00),

-- Compra 18
(18, 20, 120, 1.20, 144.00),
(18, 23, 140, 0.80, 112.00),
(18, 26, 80, 1.50, 120.00),

-- Compra 19
(19, 29, 300, 0.40, 120.00),
(19, 32, 100, 2.50, 250.00),
(19, 35, 200, 1.00, 200.00),

-- Compra 20
(20, 38, 150, 0.60, 90.00),
(20, 41, 90, 2.00, 180.00),
(20, 44, 130, 1.80, 234.00);

-- ============================================================
-- INSERTAR VENTAS (20 ventas)
-- ============================================================

INSERT INTO ventas (numero_venta, fecha_venta, monto_total, cliente_nombre, cliente_ci, metodo_pago, estado) VALUES
('VEN-001', DATE_SUB(CURDATE(), INTERVAL 30 DAY), 125.50, 'Juan Mamani', '5123456', 'efectivo', 'completada'),
('VEN-002', DATE_SUB(CURDATE(), INTERVAL 28 DAY), 85.75, 'María García', '4234567', 'tarjeta', 'completada'),
('VEN-003', DATE_SUB(CURDATE(), INTERVAL 25 DAY), 210.00, 'Carlos López', '6345678', 'efectivo', 'completada'),
('VEN-004', DATE_SUB(CURDATE(), INTERVAL 22 DAY), 95.25, 'Rosa Martínez', '7456789', 'efectivo', 'completada'),
('VEN-005', DATE_SUB(CURDATE(), INTERVAL 20 DAY), 165.50, 'Pedro Flores', '8567890', 'tarjeta', 'completada'),
('VEN-006', DATE_SUB(CURDATE(), INTERVAL 18 DAY), 78.00, 'Lucia Mendoza', '9678901', 'efectivo', 'completada'),
('VEN-007', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 195.75, 'Andrés Rodríguez', '10789012', 'transferencia', 'completada'),
('VEN-008', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 145.00, 'Isabel Pérez', '11890123', 'efectivo', 'completada'),
('VEN-009', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 220.50, 'Manuel Torres', '12901234', 'tarjeta', 'completada'),
('VEN-010', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 89.75, 'Sofía Sánchez', '13012345', 'efectivo', 'completada'),
('VEN-011', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 175.25, 'Ricardo Vargas', '14123456', 'efectivo', 'completada'),
('VEN-012', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 132.00, 'Carmen Fernández', '15234567', 'tarjeta', 'completada'),
('VEN-013', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 198.50, 'Javier Quispe', '16345678', 'efectivo', 'completada'),
('VEN-014', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 156.75, 'Elena Rojas', '17456789', 'transferencia', 'completada'),
('VEN-015', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 112.00, 'Miguel Chávez', '18567890', 'efectivo', 'completada'),
('VEN-016', CURDATE(), 189.50, 'Patricia Morales', '19678901', 'tarjeta', 'completada'),
('VEN-017', CURDATE(), 145.75, 'Fernando Gutierrez', '20789012', 'efectivo', 'completada'),
('VEN-018', CURDATE(), 201.00, 'Beatriz Castillo', '21890123', 'efectivo', 'completada'),
('VEN-019', CURDATE(), 167.25, 'Gilberto Alarcón', '22901234', 'tarjeta', 'completada'),
('VEN-020', CURDATE(), 98.50, 'Doris Camacho', '23012345', 'efectivo', 'completada');

-- ============================================================
-- INSERTAR DETALLE VENTAS (con FIFO automático)
-- ============================================================

INSERT INTO detalle_ventas (id_venta, id_lote, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 2, 2.50, 5.00),
(1, 4, 5, 6.00, 30.00),
(1, 10, 15, 1.50, 22.50),
(1, 13, 18, 1.50, 27.00),
(1, 28, 5, 1.50, 7.50),

(2, 1, 3, 2.50, 7.50),
(2, 7, 8, 4.00, 32.00),
(2, 10, 10, 1.50, 15.00),
(2, 28, 8, 1.50, 12.00),

(3, 2, 4, 2.50, 10.00),
(3, 5, 10, 6.00, 60.00),
(3, 11, 20, 1.50, 30.00),
(3, 14, 25, 1.50, 37.50),
(3, 31, 5, 8.00, 40.00),

(4, 2, 2, 2.50, 5.00),
(4, 8, 6, 5.00, 30.00),
(4, 13, 12, 1.50, 18.00),
(4, 29, 8, 1.50, 12.00),
(4, 34, 6, 3.50, 21.00),

(5, 3, 5, 2.50, 12.50),
(5, 6, 8, 6.00, 48.00),
(5, 12, 15, 1.50, 22.50),
(5, 30, 10, 1.50, 15.00),
(5, 35, 8, 3.50, 28.00),

(6, 3, 1, 2.50, 2.50),
(6, 9, 4, 5.00, 20.00),
(6, 15, 8, 1.50, 12.00),
(6, 37, 6, 2.00, 12.00),

(7, 4, 6, 6.00, 36.00),
(7, 16, 10, 10.00, 100.00),
(7, 22, 12, 2.80, 33.60),
(7, 25, 3, 5.50, 16.50),

(8, 5, 3, 6.00, 18.00),
(8, 17, 12, 10.00, 120.00),
(8, 23, 5, 2.80, 14.00),

(9, 6, 2, 10.00, 20.00),
(9, 11, 25, 1.50, 37.50),
(9, 18, 15, 3.50, 52.50),
(9, 38, 8, 6.00, 48.00),
(9, 40, 10, 6.50, 65.00),

(10, 7, 5, 4.00, 20.00),
(10, 19, 8, 5.50, 44.00),
(10, 28, 5, 1.50, 7.50),
(10, 39, 3, 6.00, 18.00),

(11, 8, 4, 2.80, 11.20),
(11, 20, 10, 5.50, 55.00),
(11, 29, 12, 1.50, 18.00),
(11, 41, 8, 6.50, 52.00),

(12, 9, 3, 5.50, 16.50),
(12, 21, 7, 5.50, 38.50),
(12, 30, 10, 1.50, 15.00),
(12, 42, 10, 6.00, 60.00),

(13, 12, 15, 1.50, 22.50),
(13, 24, 8, 2.80, 22.40),
(13, 31, 4, 8.00, 32.00),
(13, 43, 12, 6.00, 72.00),

(14, 14, 20, 1.50, 30.00),
(14, 32, 5, 8.00, 40.00),
(14, 40, 6, 6.50, 39.00),
(14, 45, 8, 6.00, 48.00),

(15, 13, 10, 1.50, 15.00),
(15, 26, 6, 5.50, 33.00),
(15, 36, 8, 3.50, 28.00),
(15, 44, 5, 6.00, 30.00),

(16, 1, 4, 2.50, 10.00),
(16, 27, 5, 5.50, 27.50),
(16, 37, 10, 2.00, 20.00),
(16, 41, 5, 6.50, 32.50),

(17, 3, 3, 2.50, 7.50),
(17, 25, 4, 5.50, 22.00),
(17, 38, 12, 6.00, 72.00),
(17, 43, 6, 6.00, 36.00),

(18, 2, 5, 2.50, 12.50),
(18, 11, 18, 1.50, 27.00),
(18, 33, 6, 8.00, 48.00),
(18, 39, 8, 6.00, 48.00),
(18, 45, 10, 6.00, 60.00),

(19, 6, 3, 10.00, 30.00),
(19, 28, 10, 1.50, 15.00),
(19, 40, 8, 6.50, 52.00),
(19, 42, 8, 6.00, 48.00),

(20, 4, 2, 6.00, 12.00),
(20, 30, 6, 1.50, 9.00),
(20, 34, 12, 3.50, 42.00),
(20, 44, 8, 6.00, 48.00),
(20, 45, 3, 6.00, 18.00);

-- ============================================================
-- CONFIRMACIÓN
-- ============================================================
SELECT 'Datos de prueba insertados exitosamente' AS Mensaje;

-- Ver resumen de datos
SELECT 'Resumen de Datos Insertados' AS Reporte;
SELECT (SELECT COUNT(*) FROM usuarios) AS Total_Usuarios;
SELECT (SELECT COUNT(*) FROM categorias) AS Total_Categorias;
SELECT (SELECT COUNT(*) FROM productos) AS Total_Productos;
SELECT (SELECT COUNT(*) FROM lotes) AS Total_Lotes;
SELECT (SELECT COUNT(*) FROM proveedores) AS Total_Proveedores;
SELECT (SELECT COUNT(*) FROM compras) AS Total_Compras;
SELECT (SELECT COUNT(*) FROM ventas) AS Total_Ventas;
