CREATE DATABASE farmacia_danny;
USE farmacia_danny;


CREATE TABLE trabajadores (
    id_cedula BIGINT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    cargo VARCHAR(50),
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);



CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    principio_activo VARCHAR(150),
    presentacion VARCHAR(100),
    descripcion TEXT,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_minimo INT DEFAULT 5,
    id_categoria INT NOT NULL,
    tipo_venta ENUM('VENTA_LIBRE','FORMULA_MEDICA') NOT NULL DEFAULT 'VENTA_LIBRE',
    estado ENUM('ACTIVO','descontinuado') DEFAULT 'ACTIVO',

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
);



CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    nit VARCHAR(50) UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(200),
    contacto VARCHAR(100),
    activo TINYINT(1) NOT NULL DEFAULT 1
);



CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    documento VARCHAR(50),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(200)
);



CREATE TABLE lotes (
    id_lote INT AUTO_INCREMENT PRIMARY KEY,
    codigo_lote VARCHAR(50) UNIQUE NOT NULL,
    id_producto INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    cantidad_inicial INT NOT NULL,
    cantidad_disponible INT NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    estado ENUM('ACTIVO','VENCIDO','ELIMINADO') NOT NULL DEFAULT 'ACTIVO',

    CONSTRAINT fk_lote_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
);



CREATE TABLE compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_proveedor INT NOT NULL,
    id_trabajador BIGINT NOT NULL,
    total_compra DECIMAL(12,2) DEFAULT 0,

    CONSTRAINT fk_compra_proveedor
        FOREIGN KEY (id_proveedor)
        REFERENCES proveedores(id_proveedor),

    CONSTRAINT fk_compra_trabajador
        FOREIGN KEY (id_trabajador)
        REFERENCES trabajadores(id_cedula)
);


CREATE TABLE detalle_compras (
    id_detalle_compra INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    codigo_lote VARCHAR(50) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_detalle_compra
        FOREIGN KEY (id_compra)
        REFERENCES compras(id_compra),

    CONSTRAINT fk_detalle_producto_compra
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
);



CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,

    id_cliente INT NULL,
    id_trabajador BIGINT NOT NULL,

    metodo_pago ENUM(
        'EFECTIVO',
        'TARJETA',
        'TRANSFERENCIA',
        'NEQUI',
        'DAVIPLATA'
    ) NOT NULL,

    total_venta DECIMAL(12,2) DEFAULT 0,

    CONSTRAINT fk_venta_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    CONSTRAINT fk_venta_trabajador
        FOREIGN KEY (id_trabajador)
        REFERENCES trabajadores(id_cedula)
);



CREATE TABLE detalle_ventas (
    id_detalle_venta INT AUTO_INCREMENT PRIMARY KEY,

    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    id_lote INT NOT NULL,

    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_detalle_venta
        FOREIGN KEY (id_venta)
        REFERENCES ventas(id_venta),

    CONSTRAINT fk_detalle_producto_venta
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto),

    CONSTRAINT fk_detalle_lote
        FOREIGN KEY (id_lote)
        REFERENCES lotes(id_lote)
);


CREATE TABLE facturas_venta (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,

    numero_factura VARCHAR(50) UNIQUE NOT NULL,

    id_venta INT NOT NULL,

    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,

    nombre_cliente VARCHAR(150) NOT NULL,
    documento_cliente VARCHAR(50),
    telefono_cliente VARCHAR(20),
    direccion_cliente VARCHAR(200),

    subtotal DECIMAL(12,2) NOT NULL,
    iva DECIMAL(12,2) DEFAULT 0,
    descuento DECIMAL(12,2) DEFAULT 0,
    total_final DECIMAL(12,2) NOT NULL,

    estado ENUM(
        'PAGADA',
        'PENDIENTE',
        'ANULADA'
    ) DEFAULT 'PAGADA',

    archivo_pdf VARCHAR(255),

    CONSTRAINT fk_factura_venta
        FOREIGN KEY (id_venta)
        REFERENCES ventas(id_venta)
);


CREATE TABLE detalle_factura_venta (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,

    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    id_lote INT NOT NULL,

    nombre_producto VARCHAR(150) NOT NULL,
    presentacion VARCHAR(100),

    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_detalle_factura
        FOREIGN KEY (id_factura)
        REFERENCES facturas_venta(id_factura),

    CONSTRAINT fk_detalle_factura_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto),

    CONSTRAINT fk_detalle_factura_lote
        FOREIGN KEY (id_lote)
        REFERENCES lotes(id_lote)
);


CREATE TABLE configuracion_alertas (
    id_configuracion INT AUTO_INCREMENT PRIMARY KEY,
    dias_alerta_vencimiento INT DEFAULT 30,
    stock_bajo_global INT DEFAULT 5
);



CREATE INDEX idx_producto_nombre
ON productos(nombre);

CREATE INDEX idx_producto_principio
ON productos(principio_activo);

CREATE INDEX idx_categoria_nombre
ON categorias(nombre);

CREATE INDEX idx_lote_vencimiento
ON lotes(fecha_vencimiento);

CREATE INDEX idx_venta_fecha
ON ventas(fecha_venta);

CREATE INDEX idx_compra_fecha
ON compras(fecha_compra);

CREATE INDEX idx_factura_numero
ON facturas_venta(numero_factura);

CREATE INDEX idx_factura_fecha
ON facturas_venta(fecha_emision);

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
