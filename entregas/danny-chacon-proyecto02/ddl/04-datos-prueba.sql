INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Antibiotico', NULL),
(3, 'Analgesicos', NULL),
(4, 'Salud Sexual', NULL);
INSERT INTO `productos` (`id_producto`, `nombre`, `principio_activo`, `presentacion`, `descripcion`, `precio_venta`, `stock_minimo`, `id_categoria`, `tipo_venta`, `estado`) VALUES
(1, 'X RAYDOL', 'Acet -Nax- Caf', 'Tableta x 10', '', 15000.00, 5, 1, 'VENTA_LIBRE', 'ACTIVO'),
(2, 'IBUFLASH', 'Ibuprofeno', 'Tableta x 20', '', 15000.00, 4, 3, 'VENTA_LIBRE', 'ACTIVO'),
(3, 'MED PRUEBA', 'Ibuprofeno', 'Tableta x 20', '', 15000.00, 5, 3, 'VENTA_LIBRE', 'descontinuado'),
(4, 'CONDONES TODAY', 'LATEX', 'X 3 UND', 'Condones latex medicados', 12000.00, 5, 4, 'VENTA_LIBRE', 'ACTIVO');

INSERT INTO `clientes` (`id_cliente`, `nombre`, `apellido`, `documento`, `telefono`, `correo`, `direccion`) VALUES
(1, 'Dany Aquerles', 'Chacon Zambrano', NULL, '3232533296', 'cdany6156@gmail.com', 'RURAL Vereda Vizcaina Casa 489 los acacios');

INSERT INTO `proveedores` (`id_proveedor`, `nombre_empresa`, `nit`, `telefono`, `correo`, `direccion`, `contacto`, `activo`) VALUES
(1, 'FARMA+', '1097782', '3232323', 'farma@prueba.com', 'GUAPOTA SANTANDER', 'Mau', 1),
(2, 'PROVEEDOR2', '1111111111111', '000000000000', 'proveedor@prueba.com', '', 'Mau', 0);

INSERT INTO `lotes` (`id_lote`, `codigo_lote`, `id_producto`, `fecha_ingreso`, `fecha_vencimiento`, `cantidad_inicial`, `cantidad_disponible`, `precio_compra`, `estado`) VALUES
(1, '151515', 1, '2026-05-28', '2026-05-14', 15000, 15000, 1200.00, 'ELIMINADO'),
(2, '121215', 2, '2026-06-02', '2023-05-26', 250, 250, 1000.00, 'ELIMINADO'),
(3, '526265', 2, '2026-06-02', '2026-06-04', 250, 246, 1200.00, 'ELIMINADO'),
(4, '161515', 1, '2026-06-02', '2026-06-15', 16, 0, 1555.00, 'ACTIVO'),
(5, '1010', 2, '2026-06-09', '2028-02-05', 150, 147, 2000.00, 'ACTIVO'),
(6, '1015', 2, '2026-06-09', '2029-02-05', 150, 150, 2000.00, 'ACTIVO'),
(7, 'CT0001', 4, '2026-06-09', '2029-06-15', 200, 200, 12000.00, 'ACTIVO'),
(8, 'XD0002', 1, '2026-06-09', '2028-07-14', 400, 400, 2000.00, 'ACTIVO');

INSERT INTO `compras` (`id_compra`, `fecha_compra`, `id_proveedor`, `id_trabajador`, `total_compra`) VALUES
(2, '2026-05-28 00:36:32', 1, 1097782213, 18000000.00),
(3, '2026-06-02 16:45:18', 1, 1097782213, 250000.00),
(4, '2026-06-02 16:46:31', 1, 1097782213, 300000.00),
(5, '2026-06-02 16:52:29', 1, 1097782213, 24880.00),
(6, '2026-06-09 19:25:06', 1, 1097782213, 300000.00),
(7, '2026-06-09 19:31:01', 1, 1097782213, 300000.00),
(8, '2026-06-09 23:29:10', 1, 1097782213, 2400000.00),
(9, '2026-06-09 23:39:37', 1, 1097782213, 800000.00);

INSERT INTO `detalle_compras` (`id_detalle_compra`, `id_compra`, `id_producto`, `codigo_lote`, `fecha_vencimiento`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 2, 1, '151515', '0000-00-00', 15000, 1200.00, 18000000.00),
(2, 3, 2, '121215', '0000-00-00', 250, 1000.00, 250000.00),
(3, 4, 2, '526265', '0000-00-00', 250, 1200.00, 300000.00),
(4, 5, 1, '161515', '0000-00-00', 16, 1555.00, 24880.00),
(5, 6, 2, '1010', '0000-00-00', 150, 2000.00, 300000.00),
(6, 7, 2, '1015', '0000-00-00', 150, 2000.00, 300000.00),
(7, 8, 4, 'CT0001', '0000-00-00', 200, 12000.00, 2400000.00),
(8, 9, 1, 'XD0002', '0000-00-00', 400, 2000.00, 800000.00);

INSERT INTO `ventas` (`id_venta`, `fecha_venta`, `id_cliente`, `id_trabajador`, `metodo_pago`, `total_venta`) VALUES
(1, '2026-06-02 20:19:33', NULL, 1097782213, 'EFECTIVO', 0.00),
(2, '2026-06-02 20:20:14', NULL, 1097782213, 'TARJETA', 0.00),
(3, '2026-06-02 20:24:38', NULL, 1097782213, 'EFECTIVO', 0.00),
(4, '2026-06-02 20:25:14', NULL, 1097782213, 'EFECTIVO', 17850.00),
(5, '2026-06-02 20:25:38', NULL, 1097782213, 'EFECTIVO', 17850.00),
(6, '2026-06-02 20:27:11', NULL, 1097782213, 'NEQUI', 178500.00),
(7, '2026-06-09 21:57:12', NULL, 1097782213, 'EFECTIVO', 180000.00),
(8, '2026-06-09 22:17:02', 1, 1097782213, 'EFECTIVO', 15000.00);

INSERT INTO `detalle_ventas` (`id_detalle_venta`, `id_venta`, `id_producto`, `id_lote`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 4, 2, 3, 1, 15000.00, 15000.00),
(2, 5, 2, 3, 1, 15000.00, 15000.00),
(3, 6, 1, 4, 1, 150000.00, 150000.00),
(4, 7, 2, 5, 2, 15000.00, 30000.00),
(5, 7, 1, 4, 1, 150000.00, 150000.00),
(6, 8, 2, 5, 1, 15000.00, 15000.00);

INSERT INTO `facturas_venta` (`id_factura`, `numero_factura`, `id_venta`, `fecha_emision`, `nombre_cliente`, `documento_cliente`, `telefono_cliente`, `direccion_cliente`, `subtotal`, `iva`, `descuento`, `total_final`, `estado`, `archivo_pdf`) VALUES
(1, 'FAC-000001', 1, '2026-06-02 20:19:33', 'Dany Chacon', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0.00, 'PAGADA', NULL),
(2, 'FAC-000002', 2, '2026-06-02 20:20:14', 'Dany Chacon', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0.00, 'PAGADA', NULL),
(3, 'FAC-000004', 4, '2026-06-02 20:25:14', 'Dany Chacon', NULL, NULL, NULL, 15000.00, 2850.00, 0.00, 17850.00, 'PAGADA', NULL),
(4, 'FAC-000005', 5, '2026-06-02 20:25:38', 'Dany Chacon', NULL, NULL, NULL, 15000.00, 2850.00, 0.00, 17850.00, 'PAGADA', NULL),
(5, 'FAC-000006', 6, '2026-06-02 20:27:11', 'Dany Chacon', NULL, NULL, NULL, 150000.00, 28500.00, 0.00, 178500.00, 'PAGADA', NULL),
(6, 'FAC-000007', 7, '2026-06-09 21:57:12', 'Dany Chacon', NULL, NULL, NULL, 180000.00, 34200.00, 0.00, 180000.00, 'PAGADA', NULL),
(7, 'FAC-000008', 8, '2026-06-09 22:17:02', 'Dany Aquerles', NULL, NULL, NULL, 15000.00, 2850.00, 0.00, 15000.00, 'PAGADA', NULL);


DELETE FROM detalle_factura_venta;
DELETE FROM facturas_venta;
DELETE FROM detalle_ventas;
DELETE FROM ventas;

ALTER TABLE detalle_factura_venta AUTO_INCREMENT = 1;
ALTER TABLE facturas_venta AUTO_INCREMENT = 1;
ALTER TABLE detalle_ventas AUTO_INCREMENT = 1;
ALTER TABLE ventas AUTO_INCREMENT = 1;