# ✨ FARMACIA EL DANNY - QUICK START

## 🚀 INICIAR EL SISTEMA

### Paso 1: Crear la Base de Datos

1. Abre **phpMyAdmin**: http://localhost/phpmyadmin
2. Haz clic en **Importar** (Import)
3. Selecciona el archivo: `basededatos/database_completo.sql`
4. Haz clic en **Continuar** (Execute)

✅ La BD `farmacia_danny` se creará con todos los datos de prueba

### Paso 2: Acceder al Sistema

1. Abre en tu navegador: http://localhost/danny-chacon-proyecto02/login.php
2. Usa las credenciales:
   - **Usuario**: `admin`
   - **Contraseña**: `1234`
3. Haz clic en **Ingresar**

### Paso 3: Explorar los Módulos

Una vez dentro, puedes acceder a:

| Módulo | Descripción |
|--------|-------------|
| 🏠 Inicio | Dashboard principal |
| 💊 Medicamentos | CRUD de productos con búsqueda |
| 👥 Clientes | Gestión de clientes |
| 🏭 Proveedores | Gestión de proveedores |
| 📦 Pedidos | Registro de compras |
| 🛒 Ventas | Registro de ventas |
| 📊 Inventario | Lotes con alertas de vencimiento |
| 📈 Reportes | Estadísticas en tiempo real |

---

## ✅ VERIFICAR QUE TODO FUNCIONE

### Teste Medicamentos
1. Haz clic en **Medicamentos**
2. Haz clic en **+ Nuevo Medicamento**
3. Completa el formulario con datos de prueba
4. Haz clic en **Guardar**
5. Verifica que aparezca en la tabla

### Teste Búsqueda
1. En Medicamentos, escribe una palabra en la búsqueda
2. Verifica que filtre los resultados

### Teste Eliminar
1. Haz clic en el botón de **Eliminar** (🗑️)
2. Verifica que se elimine el registro

### Repite con Clientes, Proveedores, etc.

---

## 📁 ARCHIVOS IMPORTANTES

```
configuración
├── php/conexion.php - Credenciales BD
└── config/config.php - Valores globales

código
├── medicamentos.php - Modelo medicamentos
├── clientes.php - Modelo clientes
├── proveedores.php - Modelo proveedores
├── pedidos.php - Compras
├── ventas.php - Ventas
├── inventario.php - Lotes
└── reportes.php - Estadísticas

componentes
├── componentes/sidebar.php - Navegación
└── componentes/topbar.php - Encabezado

base de datos
├── basededatos/database.sql - Original
└── basededatos/database_completo.sql - Completo con datos
```

---

## 🔧 SOLUCIONAR PROBLEMAS

### Error: "Error de conexión"
1. Verifica que XAMPP esté corriendo
2. Abre phpMyAdmin y verifica que puedas conectarte
3. Revisa php/conexion.php - usuario/contraseña

### Error: "Base de datos no existe"
1. Ejecuta el SQL `database_completo.sql` en phpMyAdmin
2. Verifica que la BD se cree correctamente

### Error: "Tabla no existe"
1. Revisa que el SQL se ejecutó completamente
2. Vuelve a importar el archivo SQL

### Página en blanco
1. Abre las DevTools (F12)
2. Revisa la consola para errores
3. Busca en el archivo error_log de PHP

---

## 📝 DETALLES TÉCNICOS

### Autenticación
- Usuario y contraseña se validan contra tabla `trabajadores`
- Las sesiones se mantienen con `$_SESSION['usuario']`
- Logout destruye la sesión

### Tablas Principales
- **trabajadores** - Personal (usuario/contraseña)
- **productos** - Medicamentos (catálogo)
- **clientes** - Datos de clientes
- **proveedores** - Datos de proveedores
- **lotes** - Control de inventario
- **compras** - Órdenes de compra
- **ventas** - Registro de ventas

### Seguridad
- Todas las consultas usan `prepared statements`
- Prevención de SQL injection
- Validación de sesión en cada página
- Caracteres escapados en output

### Estilos
- Dark luxury theme
- Glassmorphism effects
- Responsive design
- Animaciones suaves

---

## 💡 TIPS ÚTILES

1. **Para cambiar credenciales de BD**: Edita `php/conexion.php`
2. **Para cambiar valores globales**: Edita `config/config.php`
3. **Para agregar usuario nuevo**: Inserta en tabla `trabajadores`
4. **Para agregar proveedores**: Usa el módulo Proveedores en el sistema

---

## 🎨 Tema y Diseño

El sistema está totalmente personalizado con:
- Colores oscuros premium (#030303)
- Iconos FontAwesome
- Efectos glassmorphism
- Transiciones suaves (0.3s)
- Diseño responsive

---

**¡El sistema está listo para usar!** 🚀

Para preguntas o reportar bugs, revisa IMPLEMENTACION.md
