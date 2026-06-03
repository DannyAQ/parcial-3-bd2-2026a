# 🖨️ GUÍA - FACTURACIÓN Y DASHBOARD

## 📊 Dashboard (index.php) - Datos en Tiempo Real

El dashboard ahora muestra información real de la base de datos:

### Estadísticas Live
- **Ventas Hoy**: Suma de todas las ventas del día
- **Pedidos**: Total de compras registradas
- **Medicamentos**: Total de productos en sistema
- **Clientes**: Total de clientes registrados

### Secciones Dinámicas

**1. Medicamentos Populares**
- Top 3 productos más vendidos
- Muestra precio y número de ventas
- Actualiza automáticamente cada vez que cargas la página

**2. Stock Bajo**
- Productos con inventario < 10 unidades
- Muestra lote y cantidad disponible
- Codificado en rojo (#ff6b6b) para alertas

**3. Últimas Ventas**
- Últimas 8 ventas registradas
- Con cliente, fecha, método de pago y total
- Datos 100% reales de la BD

---

## 💾 Facturas - Generación Automática

### ¿Cómo funciona?

1. **Crear Venta**
   - Ir a módulo VENTAS
   - Hacer clic en "Nueva Venta"
   - Completar:
     - Nombre Cliente
     - Cliente (opcional, si está registrado)
     - Método de Pago

2. **Factura automática**
   - Al guardar venta, automáticamente se crea factura
   - Número: `FAC-000001` (auto-incremental)
   - Se almacena en tabla `facturas_venta`

3. **Imprimir Factura**
   - En tabla de ventas, botón "Imprimir"
   - Se abre factura en HTML
   - Se dispara print() automáticamente
   - Puedes guardar como PDF desde el navegador

### Estructura de Factura

```
┌─────────────────────────────┐
│   FARMACIA EL DANNY         │
│   NIT: 123456789-1          │
│   FACTURA #000001           │
├─────────────────────────────┤
│ CLIENTE: Juan Rodríguez     │
│ DOCUMENTO: 1234567890       │
│ MÉTODO PAGO: EFECTIVO       │
├─────────────────────────────┤
│ CÓDIGO | PRODUCTO | CANT    │
│ 00001  | Ibuprofe | 2       │
├─────────────────────────────┤
│ SUBTOTAL:        $18,000    │
│ IVA (19%):       $3,420     │
│ DESCUENTO:       -$0        │
│ ════════════════════════    │
│ TOTAL:          $21,420     │
└─────────────────────────────┘
```

### Campos de la Factura

| Campo | Descripción |
|-------|-------------|
| numero_factura | FAC-XXXXXX |
| nombre_cliente | Nombre ingresado al crear venta |
| documento_cliente | Opcional, de tabla clientes |
| telefono_cliente | Opcional, de tabla clientes |
| direccion_cliente | Opcional, de tabla clientes |
| subtotal | SUM de productos |
| iva | Subtotal * 0.19 |
| descuento | Descuentos aplicados |
| total_final | Subtotal + IVA - Descuento |
| metodo_pago | EFECTIVO/TARJETA/TRANSFERENCIA/etc |
| estado | PAGADA/PENDIENTE/ANULADA |

---

## 🖨️ Imprimir Factura

### Desde Navegador

1. En módulo VENTAS
2. Localiza la venta en la tabla
3. Haz clic en botón "Imprimir" (icono 🖨️)
4. Se abre factura en nueva ventana
5. Navegador muestra diálogo de impresión
6. **Opción**: Guardar como PDF

### Atajos de Impresión

```
Windows: Ctrl + P
Mac:     Cmd + P
```

### Opciones de Guardado

- **Imprimir Física**: Conecta impresora y presiona "Imprimir"
- **Guardar PDF**: Selecciona "Guardar como PDF" en navegador
- **Email**: Captura pantalla y envía por email

---

## 🔄 Flujo Completo

```
1. VENTAS.PHP
   ↓
2. Hacer clic "Nueva Venta"
   ↓
3. Completar formulario
   - Nombre Cliente
   - Método Pago
   ↓
4. Enviar formulario (POST)
   ↓
5. PHP crea:
   - Registro en tabla VENTAS
   - Registro en tabla FACTURAS_VENTA
   - Número factura automático
   ↓
6. Venta aparece en tabla
   ↓
7. Botón "Imprimir" disponible
   ↓
8. Click en Imprimir
   ↓
9. Abre php/generarpdf.php?id=X
   ↓
10. Genera HTML factura
    ↓
11. Navegador abre print()
    ↓
12. Usuario imprime/guarda PDF
```

---

## 📋 Ejemplos de Uso

### Ejemplo 1: Cliente registrado
```
Nombre Cliente:  Ana García
Cliente:         Ana García (id 2)
Método Pago:     TARJETA
↓
Crea factura con cliente real
```

### Ejemplo 2: Consumidor final
```
Nombre Cliente:  Consumidor
Cliente:         [Sin seleccionar]
Método Pago:     EFECTIVO
↓
Crea factura con "Consumidor" como nombre
```

### Ejemplo 3: Con productos
```
1. Crea venta (SIN productos)
2. Luego agregas productos en detalle_ventas
3. Sistema actualiza total y IVA
4. Imprimes factura con productos
```

---

## 🐛 Troubleshooting

### Problema: No aparece botón Imprimir
**Causa**: Venta no tiene factura asociada
**Solución**: Asegúrate de crear venta con método de pago seleccionado

### Problema: Factura sin datos
**Causa**: Tabla facturas_venta vacía
**Solución**: Revisa que generarpdf.php traiga datos correctamente

### Problema: No se imprime
**Causa**: PDF bloqueado o JS deshabilitado
**Solución**: 
- Usa otro navegador
- Comprueba que JavaScript esté habilitado
- Prueba con impresora virtual (PDF)

### Problema: Números incorrectos
**Causa**: IVA = 19% calculado mal
**Solución**: Verifica cálculo en formulario de ventas

---

## 🔐 Notas de Seguridad

- ✅ Facturas usan `htmlspecialchars()` para prevenir XSS
- ✅ Queries preparadas con `bind_param`
- ✅ Session validation en ventas.php
- ✅ Números de factura auto-incrementales (no reutilizables)

---

## 📞 Soporte

Para problemas:
1. Revisa CHANGELOG.md para ver cambios recientes
2. Verifica tabla facturas_venta en phpMyAdmin
3. Revisa archivo error_log de PHP
4. Abre DevTools (F12) para ver errores JavaScript

---

**Sistema de facturación operativo** ✅
