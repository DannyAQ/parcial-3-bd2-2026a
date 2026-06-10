#  FARMACIA EL DANNY - Sistema de Gestión

Sistema web para gestión integral de farmacia con módulos de productos, clientes, ventas, inventario y reportes.

---

##  INSTALACIÓN RÁPIDA

### Requisitos Previos
- **XAMPP** instalado y ejecutándose (Apache + MySQL)
- **Navegador web** moderno (Chrome, Firefox, Edge)
- **phpMyAdmin** disponible en `http://localhost/phpmyadmin`

### Pasos de Instalación

#### 1. Crear la Base de Datos
1. Abre `http://localhost/phpmyadmin`
2. En la sección "Importar", selecciona el archivo `basededatos/database.sql`
3. Haz clic en "Ejecutar"

#### 2. Configurar Conexión
- El archivo `config/config.php` está pre-configurado
- Asegúrate que los datos coincidan con tu instalación de XAMPP

#### 3. Ejecutar la Aplicación
- Copia la carpeta del proyecto a `C:\xampp\htdocs\`
- Abre en el navegador: `http://localhost/[nombre-carpeta]/`

---

##  Credenciales de Prueba

| Campo | Valor |
|-------|-------|
| **Usuario** | 1097782213 |
| **Contraseña** | (consultar en config/config.php) |

---

##  Estructura del Proyecto

```
.
├── login.php              # Página de autenticación
├── index.php              # Dashboard principal
├── medicamentos.php       # Gestión de medicamentos (CRUD)
├── clientes.php          # Gestión de clientes
├── proveedores.php       # Gestión de proveedores
├── pedidos.php           # Registro de compras
├── ventas.php            # Registro de ventas
├── inventario.php        # Gestión de lotes
├── reportes.php          # Reportes y estadísticas
│
├── basededatos/
│   └── database.sql      # Schema e datos iniciales
├── config/
│   └── config.php        # Parámetros de conexión a BD
├── php/
│   ├── conexion.php      # Conexión a MySQL
│   ├── auth.php          # Autenticación
│   ├── logout.php        # Cierre de sesión
│   ├── generarpdf.php    # Generación de facturas PDF
│   └── utilidades.php    # Funciones auxiliares
├── componentes/
│   ├── sidebar.php       # Menú lateral
│   └── topbar.php        # Barra superior
├── css/
│   ├── global.css        # Estilos principales
│   └── alerts.css        # Estilos de alertas
└── js/
    ├── app.js            # Funciones generales
    ├── ventas.js         # Lógica de ventas
    ├── inventario.js     # Lógica de inventario
    └── [otros].js        # Scripts específicos
```

---

## Módulos Disponibles

- ✅ **Login** - Autenticación de usuarios
- ✅ **Dashboard** - Panel principal con estadísticas
- ✅ **Medicamentos** - CRUD completo con categorías
- ✅ **Clientes** - Registro, edición y historial de compras
- ✅ **Proveedores** - Gestión de proveedores
- ✅ **Pedidos** - Registro y seguimiento de compras
- ✅ **Ventas** - Registro de ventas con facturas
- ✅ **Inventario** - Gestión de lotes con FIFO
- ✅ **Reportes** - Estadísticas de ventas y compras
- ✅ **Facturas PDF** - Generación de facturas en PDF

---

##  Base de Datos

El archivo `basededatos/database.sql` contiene:
- Tablas normalizadas en 3NF
- Datos de prueba iniciales
- Índices para optimización
- Relaciones FK entre tablas

**Tablas principales:**
- `trabajadores` - Usuarios del sistema
- `categorias` - Categorías de medicamentos
- `productos` - Medicamentos disponibles
- `clientes` - Datos de clientes
- `proveedores` - Información de proveedores
- `lotes` - Inventario por lote
- `compras` - Registro de compras a proveedores
- `ventas` - Registro de ventas a clientes
- `facturas_venta` - Facturas de ventas
- `detalle_ventas` - Líneas de venta

---

**Versión**: 2.0 | **Última actualización**: Junio 2026


