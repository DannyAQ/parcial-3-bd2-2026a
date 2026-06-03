# Farmacia de Barrio - Sistema de Gestión

## 📋 Descripción General

Sistema completo de gestión para una farmacia de barrio desarrollado con tecnologías web modernas. Diseñado para administrar inventario, ventas, compras y control de lotes con vencimiento.

---

## 🎯 Objetivos Principales

✅ Gestión integral de medicamentos y productos farmacéuticos
✅ Control FIFO automático de inventario
✅ Alertas de stock bajo y vencimiento
✅ Sistema de reportes y estadísticas
✅ Interfaz moderna y profesional
✅ Seguridad y control de acceso

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lenguaje de programación puro (sin frameworks)
- **MySQL 5.7+** - Base de datos relacional
- **XAMPP** - Entorno de desarrollo local

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos avanzados con variables CSS y Flexbox/Grid
- **JavaScript Vanilla** - Lógica del cliente sin dependencias externas

### Base de Datos
- Normalización hasta **3FN**
- Triggers automáticos para FIFO
- Procedimientos almacenados para reportes y alertas

---

## 📁 Estructura del Proyecto

```
entregas/danny-chacon-proyecto02/
│
├── README.md                    (Este archivo)
│
├── mer/                         (Modelo Entidad-Relación)
│   ├── diagrama-mer.png
│   ├── diagrama-mer.pdf
│   └── notas-normalizacion.md
│
├── ddl/                         (Scripts SQL)
│   ├── 01-crear-bd.sql
│   ├── 02-tablas.sql
│   ├── 03-triggers-procedimientos.sql
│   └── 04-datos-prueba.sql
│
├── docs/                        (Documentación)
│   ├── reglas-fifo.md
│   ├── casos-de-uso.md
│   └── capturas/
│
└── app/                         (Aplicación web)
    ├── index.php
    ├── login.php
    ├── logout.php
    │
    ├── assets/
    │   ├── css/
    │   │   ├── global.css       (Estilos globales)
    │   │   ├── dashboard.css    (Dashboard y componentes)
    │   │   └── forms.css        (Formularios)
    │   ├── js/
    │   │   ├── app.js           (Funciones principales)
    │   │   ├── alerts.js        (Gestión de alertas)
    │   │   └── ventas.js        (Módulo de ventas)
    │   ├── img/
    │   └── icons/
    │
    ├── config/
    │   ├── conexion.php         (Conexión a BD)
    │   └── config.php           (Configuración general)
    │
    ├── includes/
    │   ├── header.php
    │   ├── navbar.php
    │   ├── sidebar.php
    │   ├── footer.php
    │   ├── auth.php             (Autenticación)
    │   └── functions.php        (Funciones auxiliares)
    │
    ├── dashboard/
    │   └── dashboard.php        (Panel principal)
    │
    ├── productos/
    │   ├── listar.php
    │   ├── crear.php
    │   ├── editar.php
    │   ├── eliminar.php
    │   └── buscar.php
    │
    ├── categorias/
    │   ├── listar.php
    │   ├── crear.php
    │   └── editar.php
    │
    ├── lotes/
    │   ├── listar.php
    │   ├── crear.php
    │   ├── vencimientos.php
    │   └── fifo.php
    │
    ├── proveedores/
    │   ├── listar.php
    │   ├── crear.php
    │   └── editar.php
    │
    ├── compras/
    │   ├── listar.php
    │   ├── crear.php
    │   ├── detalle.php
    │   └── guardar.php
    │
    ├── ventas/
    │   ├── listar.php
    │   ├── crear.php
    │   ├── ticket.php
    │   ├── detalle.php
    │   └── guardar.php
    │
    ├── reportes/
    │   ├── ventas.php
    │   ├── inventario.php
    │   └── vencimientos.php
    │
    └── alerts/
        ├── stock-bajo.php
        └── proximos-vencer.php
```

---

## 🚀 Instalación y Configuración

### Requisitos Previos
- XAMPP instalado (PHP 7.4+, MySQL 5.7+)
- PHP con extensión MySQLi habilitada
- Navegador web moderno (Chrome, Firefox, Edge, Safari)

### Paso 1: Descargar y Ubicar los Archivos

```bash
# Copiar la carpeta app/ a:
C:\xampp\htdocs\farmacia
```

### Paso 2: Crear la Base de Datos

```bash
# Abrir PhpMyAdmin
http://localhost/phpmyadmin

# Ejecutar los scripts SQL en orden:
1. ddl/01-crear-bd.sql
2. ddl/02-tablas.sql
3. ddl/03-triggers-procedimientos.sql
4. ddl/04-datos-prueba.sql
```

### Paso 3: Configurar la Conexión

Editar `app/config/conexion.php` si es necesario:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Cambiar si tu MySQL tiene contraseña
define('DB_NAME', 'farmacia_barrio');
```

### Paso 4: Acceder a la Aplicación

```
http://localhost/farmacia/app/login.php
```

---

## 👤 Usuarios de Prueba

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | admin123 | Administrador |
| vendedor1 | vendedor123 | Vendedor |
| gerente | gerente123 | Gerente |

---

## 📊 Funcionalidades Principales

### 1. **Dashboard Ejecutivo**
- Ventas del día
- Monto recaudado
- Productos en catálogo
- Lotes disponibles
- Últimas ventas
- Alertas de stock
- Próximos a vencer

### 2. **Gestión de Productos**
- CRUD completo de medicamentos
- Clasificación por categoría
- Control de código SKU
- Principio activo
- Precios de compra y venta
- Estado del producto

### 3. **Control de Lotes**
- Registro de lotes por producto
- Número de lote único
- Fecha de vencimiento
- Cantidad inicial y disponible
- Control FIFO automático
- Historial de movimientos

### 4. **Módulo de Compras**
- Registro de compras a proveedores
- Detalles de cada compra
- Actualización automática de lotes
- Validación de cantidad y precio
- Historial de proveedores

### 5. **Módulo de Ventas**
- Interface intuitiva de punto de venta
- Búsqueda rápida de productos
- Carrito de compra dinámico
- Cálculo automático de totales
- Registro de cliente
- Métodos de pago múltiples
- Generación de ticket de venta
- Control FIFO automático

### 6. **Sistema de Alertas**
- **Stock Bajo:** Productos bajo cantidad mínima
- **Próximos a Vencer:** Lotes con vencimiento próximo
- **Vencidos:** Lotes expirados
- Dashboard de alertas en tiempo real

### 7. **Reportes**
- **Ventas por Período:** Análisis de ingresos
- **Inventario Actual:** Stock por producto
- **Vencimientos:** Timeline de vencimientos
- Exportación a PDF y impresión

### 8. **Seguridad**
- Login con sesiones PHP
- Validación de acceso por rol
- Autenticación en cada página
- Timeout de sesión configurabel
- Tokens CSRF (implementación disponible)

---

## 🗄️ Base de Datos - Detalles

### Tablas Principales

#### usuarios
```sql
id_usuario, usuario, email, contraseña, rol, estado, fecha_creacion
```

#### productos
```sql
id_producto, id_categoria, nombre_producto, codigo_sku, principio_activo, 
presentacion, precio_compra, precio_venta, cantidad_minima, estado
```

#### lotes
```sql
id_lote, id_producto, numero_lote, fecha_vencimiento, 
cantidad_inicial, cantidad_disponible
```

#### ventas
```sql
id_venta, numero_venta, fecha_venta, monto_total, cliente_nombre, 
cliente_ci, metodo_pago, estado
```

#### detalle_ventas
```sql
id_detalle_venta, id_venta, id_lote, cantidad, precio_unitario, subtotal
```

### Triggers Automáticos

1. **tr_venta_descontar_stock** - Descuenta automáticamente del lote al vender
2. **tr_validar_stock_venta** - Valida stock antes de vender
3. **tr_validar_cantidad_compra** - Valida datos de compra
4. **tr_actualizar_lote_compra** - Actualiza lote al comprar
5. **tr_calcular_subtotal_venta** - Calcula subtotales automáticamente

### Procedimientos Almacenados

- `sp_obtener_lote_fifo()` - Obtiene lote más antiguo para FIFO
- `sp_alertas_stock_bajo()` - Retorna productos con stock bajo
- `sp_alertas_vencimiento()` - Retorna lotes próximos a vencer
- `sp_reporte_ventas_fecha()` - Reporte de ventas por período
- `sp_reporte_inventario()` - Estado actual del inventario

---

## 🔄 Sistema FIFO (First In First Out)

### Cómo Funciona

El sistema implementa control FIFO automático para garantizar que se vendan primero los productos más antiguos:

1. **Al Crear un Lote:**
   - Se establece la fecha de vencimiento
   - Se registra cantidad inicial = cantidad disponible

2. **Al Vender:**
   - Sistema busca el lote más antiguo (ORDER BY fecha_vencimiento ASC)
   - Descuenta cantidad del lote encontrado
   - Actualiza cantidad_disponible
   - Si lote se agota, busca el siguiente

3. **Validación:**
   - No permite vender si stock es insuficiente
   - Trigger previene cantidad negativa
   - Registra movimiento en detalle_ventas

### Ejemplo Práctico

```
Producto: Amoxicilina 500mg

Lote 1: 100 unidades, Vence 2026-06-15
Lote 2: 150 unidades, Vence 2026-08-20
Lote 3: 120 unidades, Vence 2026-10-10

Venta de 120 unidades:
- Sistema selecciona Lote 1 (más antiguo)
- Descuenta 100 unidades del Lote 1 (queda 0)
- Descuenta 20 unidades del Lote 2 (queda 130)
```

---

## 🎨 Diseño de Interfaz

### Características del Diseño

- **Dark Mode Premium:** Colores oscuros inspirados en Starlink
- **Minimalista:** Interfaz limpia sin elementos innecesarios
- **Responsivo:** Adapta a diferentes tamaños de pantalla
- **Moderno:** Animaciones suaves y transiciones elegantes
- **Accesible:** Contraste adecuado y navegación intuitiva

### Paleta de Colores

| Elemento | Color | Hex |
|----------|-------|-----|
| Primario | Verde Neón | #00FF96 |
| Fondo | Negro Oscuro | #0A0E27 |
| Bordes | Gris Oscuro | #FFFFFF1A |
| Éxito | Verde | #00FF96 |
| Error | Rojo | #FF4757 |
| Advertencia | Naranja | #FFA502 |

---

## 📈 Estadísticas Iniciales

El sistema incluye datos de prueba para demostración:

- **15 Productos** activos
- **5 Categorías** de medicamentos
- **5 Proveedores** registrados
- **45 Lotes** (3 por producto)
- **20 Compras** registradas
- **20 Ventas** de prueba
- **3 Usuarios** de ejemplo

---

## 🔒 Seguridad

### Medidas Implementadas

1. **Sesiones PHP**
   - Timeout configurabel (30 minutos)
   - Validación en cada página
   - Cierre de sesión al logout

2. **Validación de Datos**
   - Limpieza de entrada (htmlspecialchars)
   - Prepared Statements para evitar SQL Injection
   - Validación lado servidor

3. **Control de Acceso**
   - Roles de usuario (admin, vendedor, gerente)
   - Restricción por funcionalidad
   - Verificación de permisos

4. **Logs y Auditoría**
   - Registro de login/logout
   - Historial de cambios
   - Trazabilidad de transacciones

---

## 🐛 Solución de Problemas

### La aplicación no carga

**Error:** Conexión a BD rechazada

**Solución:**
```bash
1. Verificar XAMPP esté iniciado
2. Revisar MySQL esté corriendo
3. Confirmar configuración en config/conexion.php
4. Ejecutar scripts DDL nuevamente
```

### Error: "Base de datos no existe"

**Solución:**
```bash
1. Abrir PhpMyAdmin: http://localhost/phpmyadmin
2. Ejecutar ddl/01-crear-bd.sql
3. Ejecutar ddl/02-tablas.sql
4. Ejecutar ddl/03-triggers-procedimientos.sql
5. Ejecutar ddl/04-datos-prueba.sql
```

### No puedo iniciar sesión

**Verificar:**
1. Usuario y contraseña correctos (ver tabla arriba)
2. Usuario tenga estado 'activo'
3. No haya error de configuración de BD
4. Limpiar cookies del navegador

### FIFO no funciona

**Causar posibles:**
1. Triggers no creados → Ejecutar 03-triggers-procedimientos.sql
2. Datos inconsistentes → Actualizar lotes con cantidad válida
3. Error de cálculo → Revisar detalle_ventas

---

## 📞 Soporte y Contacto

**Desarrollado por:** Danny Chacón
**Fecha:** 26 de mayo de 2026
**Versión:** 1.0.0
**Estado:** Listo para sustentación

---

## 📄 Licencia

Este proyecto es de uso educativo únicamente.

---

## 🎓 Notas para la Sustentación

### Puntos Clave a Presentar

1. **Base de Datos:**
   - Mostrar MER y explicar normalización 3FN
   - Demostrar triggers automáticos
   - Explicar integridad referencial

2. **Sistema FIFO:**
   - Demostrar creación de venta
   - Mostrar descuento automático de lotes
   - Validar orden de vencimiento

3. **Alertas:**
   - Mostrar alertas de stock bajo
   - Explicar alertas de vencimiento
   - Demostrar actualización en tiempo real

4. **Reportes:**
   - Generar reporte de ventas
   - Mostrar reporte de inventario
   - Explicar análisis de datos

5. **Interfaz:**
   - Destacar diseño moderno
   - Demostrar responsividad
   - Explicar experiencia de usuario

---

**¡Éxito en tu sustentación!** 🎓
