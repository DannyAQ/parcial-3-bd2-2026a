-- ============================================================
-- FARMACIA DE BARRIO - CREACIÓN DE TABLAS
-- Script 02: Tablas principales
-- ============================================================

USE farmacia_barrio;

-- ============================================================
-- TABLA: usuarios
-- Descripción: Usuarios del sistema
-- ============================================================
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'vendedor', 'gerente') DEFAULT 'vendedor',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_usuario (usuario),
    INDEX idx_email (email),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: categorias
-- Descripción: Categorías de medicamentos
-- ============================================================
CREATE TABLE categorias (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nombre (nombre_categoria),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: productos
-- Descripción: Catálogo de medicamentos
-- ============================================================
CREATE TABLE productos (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria INT NOT NULL,
    nombre_producto VARCHAR(150) UNIQUE NOT NULL,
    codigo_sku VARCHAR(50) UNIQUE NOT NULL,
    principio_activo VARCHAR(150) NOT NULL,
    presentacion VARCHAR(50),
    precio_compra DECIMAL(10, 2) NOT NULL,
    precio_venta DECIMAL(10, 2) NOT NULL,
    cantidad_minima INT DEFAULT 10,
    estado ENUM('activo', 'descontinuado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_categoria (id_categoria),
    INDEX idx_nombre (nombre_producto),
    INDEX idx_codigo_sku (codigo_sku),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: proveedores
-- Descripción: Proveedores de medicamentos
-- ============================================================
CREATE TABLE proveedores (
    id_proveedor INT PRIMARY KEY AUTO_INCREMENT,
    nombre_proveedor VARCHAR(150) UNIQUE NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nombre (nombre_proveedor),
    INDEX idx_telefono (telefono),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: lotes
-- Descripción: Lotes de medicamentos con control de vencimiento
-- Nota: Esta es la tabla clave para el control FIFO
-- ============================================================
CREATE TABLE lotes (
    id_lote INT PRIMARY KEY AUTO_INCREMENT,
    id_producto INT NOT NULL,
    numero_lote VARCHAR(50) UNIQUE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    cantidad_inicial INT NOT NULL,
    cantidad_disponible INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_producto (id_producto),
    INDEX idx_vencimiento (fecha_vencimiento),
    INDEX idx_numero_lote (numero_lote),
    INDEX idx_disponible (cantidad_disponible)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: compras
-- Descripción: Registro de compras a proveedores
-- ============================================================
CREATE TABLE compras (
    id_compra INT PRIMARY KEY AUTO_INCREMENT,
    id_proveedor INT NOT NULL,
    numero_compra VARCHAR(50) UNIQUE NOT NULL,
    fecha_compra DATE NOT NULL,
    monto_total DECIMAL(12, 2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada',
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_proveedor (id_proveedor),
    INDEX idx_fecha (fecha_compra),
    INDEX idx_numero (numero_compra)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: detalle_compras
-- Descripción: Detalle de productos en cada compra
-- ============================================================
CREATE TABLE detalle_compras (
    id_detalle_compra INT PRIMARY KEY AUTO_INCREMENT,
    id_compra INT NOT NULL,
    id_lote INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_compra (id_compra),
    INDEX idx_lote (id_lote)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: ventas
-- Descripción: Registro de ventas al cliente
-- ============================================================
CREATE TABLE ventas (
    id_venta INT PRIMARY KEY AUTO_INCREMENT,
    numero_venta VARCHAR(50) UNIQUE NOT NULL,
    fecha_venta DATE NOT NULL,
    monto_total DECIMAL(12, 2) NOT NULL,
    cliente_nombre VARCHAR(150),
    cliente_ci VARCHAR(20),
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
    estado ENUM('completada', 'cancelada', 'devuelto') DEFAULT 'completada',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_fecha (fecha_venta),
    INDEX idx_cliente (cliente_ci),
    INDEX idx_numero (numero_venta),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: detalle_ventas
-- Descripción: Detalle de productos vendidos
-- Nota: TRIGGER de FIFO se ejecuta en INSERT
-- ============================================================
CREATE TABLE detalle_ventas (
    id_detalle_venta INT PRIMARY KEY AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_lote INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_venta (id_venta),
    INDEX idx_lote (id_lote)
) ENGINE=InnoDB;

-- ============================================================
-- CONFIRMACIÓN
-- ============================================================
SELECT 'Todas las tablas han sido creadas exitosamente' AS Mensaje;
SHOW TABLES;
