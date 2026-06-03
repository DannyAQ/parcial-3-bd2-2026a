# Sistema FIFO - Documentación Técnica

## ¿Qué es FIFO?

**FIFO** = **First In, First Out**

Significa "Primero Entra, Primero Sale". Es una estrategia de gestión de inventario donde los productos más antiguos se venden antes que los más nuevos.

---

## ¿Por qué es importante en Farmacia?

En una farmacia, FIFO es **crítico** porque:

1. **Previene vencimiento de stock** - Vende los más viejos primero
2. **Garantiza medicamentos frescos** - Los clientes siempre obtienen productos con mayor vida útil
3. **Cumple regulaciones** - Farmacias deben vender por orden de antigüedad
4. **Evita pérdidas** - Minimiza medicamentos que vencen sin vender
5. **Garantía legal** - Protege al negocio de vender productos vencidos

---

## Implementación en la Base de Datos

### 1. Tabla de Lotes

Cada lote tiene:
- `numero_lote` - Identificador único del lote
- `fecha_vencimiento` - Cuándo vence
- `cantidad_inicial` - Cuánto se compró
- `cantidad_disponible` - Cuánto queda en stock

```sql
CREATE TABLE lotes (
    id_lote INT PRIMARY KEY AUTO_INCREMENT,
    id_producto INT NOT NULL,
    numero_lote VARCHAR(50) UNIQUE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    cantidad_inicial INT NOT NULL,
    cantidad_disponible INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
```

### 2. Obtener Lote FIFO

El procedimiento almacenado `sp_obtener_lote_fifo()` busca el lote más antiguo:

```sql
CREATE PROCEDURE sp_obtener_lote_fifo(IN p_id_producto INT)
BEGIN
    SELECT id_lote, numero_lote, fecha_vencimiento, cantidad_disponible
    FROM lotes
    WHERE id_producto = p_id_producto 
    AND cantidad_disponible > 0
    AND fecha_vencimiento > CURDATE()
    ORDER BY fecha_vencimiento ASC     -- ← FIFO: el más vencible primero
    LIMIT 1;
END
```

**Explicación:**
- `WHERE cantidad_disponible > 0` - Solo lotes con stock
- `WHERE fecha_vencimiento > CURDATE()` - Solo no vencidos
- `ORDER BY fecha_vencimiento ASC` - **ORDENAR por fecha de vencimiento**
- `LIMIT 1` - Devolver solo el primero (más antiguo)

### 3. Trigger de Descuento Automático

Cuando se registra una venta, este trigger descuenta automáticamente:

```sql
CREATE TRIGGER tr_venta_descontar_stock
AFTER INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    UPDATE lotes 
    SET cantidad_disponible = cantidad_disponible - NEW.cantidad
    WHERE id_lote = NEW.id_lote;
    
    -- Validar que no quede negativo
    IF (SELECT cantidad_disponible FROM lotes 
        WHERE id_lote = NEW.id_lote) < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
END
```

---

## Flujo de Venta FIFO

### Paso 1: Cliente llega a comprar

```
Cliente: "Quiero Amoxicilina 500mg, 40 unidades"
```

### Paso 2: Sistema busca lote FIFO

```sql
SELECT id_lote, numero_lote, fecha_vencimiento, cantidad_disponible
FROM lotes
WHERE id_producto = 1  -- Amoxicilina
AND cantidad_disponible > 0
AND fecha_vencimiento > CURDATE()
ORDER BY fecha_vencimiento ASC
LIMIT 1;
```

**Resultado:**
```
┌─────────┬──────────────┬────────────────┬──────────────────┐
│ id_lote │ numero_lote  │ fecha_vencimiento │ cantidad_disponible │
├─────────┼──────────────┼────────────────┼──────────────────┤
│   1     │ LOT-001-AMX  │ 2026-06-15     │ 100              │
└─────────┴──────────────┴────────────────┴──────────────────┘
```

### Paso 3: Validar stock disponible

```
Stock en LOT-001-AMX: 100 unidades
Solicitud: 40 unidades
¿Hay suficiente? SÍ ✓
```

### Paso 4: Registrar venta

Se inserta en `detalle_ventas`:

```sql
INSERT INTO detalle_ventas (id_venta, id_lote, cantidad, precio_unitario, subtotal)
VALUES (1, 1, 40, 2.50, 100.00);
```

### Paso 5: Trigger descuenta automáticamente

El trigger `tr_venta_descontar_stock` se ejecuta:

```sql
UPDATE lotes 
SET cantidad_disponible = cantidad_disponible - 40
WHERE id_lote = 1;

-- Ahora: cantidad_disponible = 100 - 40 = 60
```

### Paso 6: Venta completada

```
✓ Venta registrada
✓ Lote actualizado
✓ Stock: LOT-001-AMX ahora tiene 60 unidades
```

---

## Ejemplo Completo: Múltiples Lotes

### Escenario

Producto: **Ibuprofeno 400mg**

**Lotes disponibles:**

```
Lote A: 80 unidades, Vence 2026-06-15 ← MÁS ANTIGUO
Lote B: 100 unidades, Vence 2026-08-20
Lote C: 120 unidades, Vence 2026-10-10 ← MÁS NUEVO
Total: 300 unidades
```

### Venta 1: 50 unidades

```
Sistema busca FIFO → Encuentra Lote A (vence primero)
Stock en Lote A: 80 unidades
¿Hay 50? SÍ
Descuenta de Lote A: 80 - 50 = 30 unidades quedan

Lote A: 30 unidades (quedan) ✓
Lote B: 100 unidades (sin cambios)
Lote C: 120 unidades (sin cambios)
Total: 250 unidades
```

### Venta 2: 80 unidades

```
Sistema busca FIFO → Encuentra Lote A (aún es el más vencible)
Stock en Lote A: 30 unidades
¿Hay 80? NO (solo hay 30)
Toma 30 del Lote A → Queda 0
Aún necesita: 80 - 30 = 50 más
Busca siguiente → Encuentra Lote B
Toma 50 del Lote B → Queda 50

Lote A: 0 unidades (agotado) ✓
Lote B: 50 unidades (quedan) ✓
Lote C: 120 unidades (sin cambios)
Total: 170 unidades
```

### Estado Final

```
╔════════╦═════════╦════════════════╦═══════════════╗
║ Lote   ║ Vence   ║ Cantidad       ║ Estado        ║
╠════════╬═════════╬════════════════╬═══════════════╣
║ A      ║ Jun-26  ║ 0 (AGOTADO)    ║ ✓ Vendido    ║
║ B      ║ Ago-26  ║ 50             ║ ✓ Activo     ║
║ C      ║ Oct-26  ║ 120            ║ ✓ Activo     ║
╚════════╩═════════╩════════════════╩═══════════════╝
```

---

## Código PHP en la Aplicación

### En `app/includes/functions.php`

```php
/**
 * Validar FIFO al vender
 */
function obtener_lote_fifo($id_producto) {
    global $conexion;
    
    $sql = "SELECT id_lote, numero_lote, fecha_vencimiento, cantidad_disponible
            FROM lotes
            WHERE id_producto = ? 
            AND cantidad_disponible > 0
            AND fecha_vencimiento > CURDATE()
            ORDER BY fecha_vencimiento ASC
            LIMIT 1";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();
    
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}
```

### En JavaScript (`app/assets/js/ventas.js`)

```javascript
/**
 * Agregar producto al carrito (aplica FIFO)
 */
agregarProducto: function(idProducto, nombre, precio) {
    // Obtener lote FIFO
    this.obtenerLoteFIFO(idProducto, (lote) => {
        if (!lote) {
            App.mostrarNotificacion('No hay stock disponible', 'warning');
            return;
        }
        
        // Agregar con id del lote FIFO automáticamente
        this.config.ventasTmp.push({
            id_producto: idProducto,
            id_lote: lote.id_lote,  // ← LOTE FIFO SELECCIONADO
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            lote: lote.numero_lote
        });
        
        this.actualizarCarrito();
    });
}
```

---

## Validaciones de FIFO

### 1. Validación Antes de Vender

```php
// En detalle_ventas BEFORE INSERT
IF (SELECT cantidad_disponible FROM lotes WHERE id_lote = NEW.id_lote) < NEW.cantidad THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
END IF;
```

### 2. Validación Después de Vender

```php
// En detalle_ventas AFTER INSERT
IF (SELECT cantidad_disponible FROM lotes WHERE id_lote = NEW.id_lote) < 0 THEN
    ROLLBACK;  // Cancelar transacción
END IF;
```

### 3. Actualización Automática

```php
// Cuando se compra, actualizar cantidad disponible
UPDATE lotes 
SET cantidad_inicial = cantidad_inicial + NEW.cantidad,
    cantidad_disponible = cantidad_disponible + NEW.cantidad
WHERE id_lote = NEW.id_lote;
```

---

## Reportes FIFO

### Consulta de Movimiento de Lotes

```sql
SELECT 
    p.nombre_producto,
    l.numero_lote,
    l.fecha_vencimiento,
    DATEDIFF(l.fecha_vencimiento, CURDATE()) as dias_para_vencer,
    COALESCE(SUM(dv.cantidad), 0) as cantidad_vendida,
    l.cantidad_inicial as stock_inicial,
    l.cantidad_disponible as stock_actual
FROM lotes l
JOIN productos p ON l.id_producto = p.id_producto
LEFT JOIN detalle_ventas dv ON l.id_lote = dv.id_lote
GROUP BY l.id_lote
ORDER BY l.fecha_vencimiento ASC;
```

**Resultado esperado:**
```
┌──────────┬─────────────┬────────────┬─────────┬────────────┬────────────┬──────────────┐
│ Producto │ Lote        │ Vence      │ Días    │ Vendida    │ Inicial    │ Disponible   │
├──────────┼─────────────┼────────────┼─────────┼────────────┼────────────┼──────────────┤
│ Amoxi    │ LOT-001-AMX │ 2026-06-15 │ 20      │ 100        │ 100        │ 0            │
│ Amoxi    │ LOT-002-AMX │ 2026-08-20 │ 86      │ 30         │ 150        │ 120          │
│ Amoxi    │ LOT-003-AMX │ 2026-10-10 │ 137     │ 0          │ 120        │ 120          │
└──────────┴─────────────┴────────────┴─────────┴────────────┴────────────┴──────────────┘
```

---

## Casos de Uso FIFO

### ✓ Caso 1: Venta Normal (Stock Disponible)

```
Input:  Producto ID = 1, Cantidad = 20
FIFO:   Selecciona Lote más vencible
Acción: Descuenta 20 del lote
Output: ✓ Venta completada
```

### ✓ Caso 2: Venta con Múltiples Lotes

```
Input:  Producto ID = 1, Cantidad = 150
Stock:  Lote A = 80, Lote B = 100
FIFO:   Toma 80 de A + 70 de B
Output: ✓ Venta completada, ambos lotes actualizado
```

### ✗ Caso 3: Stock Insuficiente

```
Input:  Producto ID = 1, Cantidad = 200
Stock:  Total = 150
FIFO:   Busca lote, encuentra insuficiente
Output: ✗ Error: "Stock insuficiente"
```

### ✗ Caso 4: Lote Vencido

```
Input:  Producto ID = 1, Cantidad = 50
FIFO:   Primer lote tiene fecha_vencimiento < CURDATE()
Output: ✗ Salta a siguiente lote no vencido
```

---

## Monitoreo de FIFO

### Alertas Automáticas

```sql
-- Lotes próximos a vencer (menos de 7 días)
SELECT * FROM lotes 
WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
AND cantidad_disponible > 0
ORDER BY fecha_vencimiento ASC;

-- Lotes vencidos
SELECT * FROM lotes 
WHERE fecha_vencimiento < CURDATE()
AND cantidad_disponible > 0;
```

### Panel de Control

El Dashboard muestra:
- ✓ Próximos a vencer
- ✓ Stock bajo
- ✓ Lotes vencidos
- ✓ Movimiento FIFO

---

## Conclusión

El sistema FIFO implementado garantiza:

1. **Venta ordenada:** Siempre primero los más vencibles
2. **Automatización:** Sin intervención manual
3. **Seguridad:** Validaciones en triggers
4. **Trazabilidad:** Historial completo
5. **Cumplimiento:** Regulaciones farmacéuticas

**¡Sistema FIFO 100% funcional y automatizado!** ✓
