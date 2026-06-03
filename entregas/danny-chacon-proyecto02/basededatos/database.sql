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
    contacto VARCHAR(100)
);



CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
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
