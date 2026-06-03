## 🔧 Instrucciones para crear la Base de Datos y Hacer Pruebas

### ✅ Lo que se ha corregido:

1. **Variables de conexión unificadas**: Cambiado de `$conexion` a `$conn` en login.php
2. **Validación de sesión agregada** en todos los archivos .php principales
3. **Login redirecta automáticamente** si ya está autenticado
4. **Logout creado** en `php/logout.php`
5. **Todos los módulos** (medicamentos, inventario, ventas, clientes, proveedores, reportes, pedidos) tienen estructura básica con validación

---

### 📋 Pasos para crear la BD y probar:

#### **1️⃣ Crear la Base de Datos**

Abre **phpMyAdmin** (http://localhost/phpmyadmin) y ejecuta este SQL:

```sql
-- Crear la base de datos
CREATE DATABASE farmacia_danny 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE farmacia_danny;

-- Tabla de trabajadores (empleados)
CREATE TABLE trabajadores (
    id_cedula BIGINT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    cargo VARCHAR(50),
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- Tabla de productos (medicamentos)
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

-- Tabla de proveedores
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    nit VARCHAR(50) UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(200),
    contacto VARCHAR(100)
);
```

#### **2️⃣ Insertar datos de prueba**

```sql
-- Insertar un usuario de prueba
INSERT INTO trabajadores 
(id_cedula, nombre, apellido, telefono, cargo, usuario, contrasena)
VALUES 
(12345678, 'Danny', 'López', '3001234567', 'Administrador', 'admin', '1234');

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion)
VALUES 
('Analgésicos', 'Medicamentos para el dolor'),
('Antibióticos', 'Medicamentos antibacterianos');
```

#### **3️⃣ Pruebas de acceso**

1. Abre: **http://localhost/danny-chacon-proyecto02/login.php**
2. Intenta sin autenticación (debe redirigir al login)
3. Ingresa con:
   - **Usuario**: `admin`
   - **Contraseña**: `1234`
4. Deberías entrar a `index.php`
5. Prueba navegar por los menús
6. Usa "Cerrar Sesión" para logout

---

### 🐛 Errores que se corrigieron:

| Problema | Solución |
|----------|----------|
| `$conexion` indefinido | Cambiado a `$conn` |
| Sin validación de sesión | Agregado check de sesión en todos los .php |
| Acceso sin login | Redirige a login.php |
| Sin archivo logout | Creado `php/logout.php` |

---

### 📁 Archivos modificados:

✅ login.php - Corregida variable + validación de sesión
✅ index.php - Agregada validación de sesión
✅ medicamentos.php - Estructura completa + validación
✅ inventario.php - Estructura completa + validación
✅ ventas.php - Estructura completa + validación
✅ clientes.php - Estructura completa + validación
✅ proveedores.php - Estructura completa + validación
✅ reportes.php - Estructura completa + validación
✅ pedidos.php - Estructura completa + validación
✅ php/logout.php - **CREADO NUEVO**

---

### 🚀 Próximos pasos:

1. Crear la BD (paso 1)
2. Insertar datos de prueba (paso 2)
3. Probar login/logout (paso 3)
4. Agregar funcionalidades CRUD en cada módulo

¡Listo para empezar a probar! 🎉
