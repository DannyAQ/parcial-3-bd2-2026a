# MER - Farmacia de Barrio
## Modelo Entidad-Relación Normalizado a 3FN

---

## 1. ANÁLISIS DE ENTIDADES

### ENTIDAD: usuarios
**Descripción:** Usuarios del sistema con acceso al aplicativo.
- PK: id_usuario (INT, AUTO_INCREMENT)
- usuario (VARCHAR, UNIQUE)
- email (VARCHAR, UNIQUE)
- contraseña (VARCHAR)
- rol (ENUM: admin, vendedor, gerente)
- estado (ENUM: activo, inactivo)
- fecha_creacion (TIMESTAMP)

**1FN:** ✅ Sin grupos repetitivos
**2FN:** ✅ Todos los atributos dependen completamente de PK
**3FN:** ✅ Sin dependencias transitivas

---

### ENTIDAD: categorias
**Descripción:** Categorías de medicamentos.
- PK: id_categoria (INT, AUTO_INCREMENT)
- nombre_categoria (VARCHAR, UNIQUE)
- descripcion (TEXT)
- estado (ENUM: activo, inactivo)
- fecha_creacion (TIMESTAMP)

**Normalización:** 3FN ✅

---

### ENTIDAD: productos
**Descripción:** Catálogo de medicamentos.
- PK: id_producto (INT, AUTO_INCREMENT)
- FK: id_categoria (INT) → categorias
- nombre_producto (VARCHAR, UNIQUE)
- codigo_sku (VARCHAR, UNIQUE)
- principio_activo (VARCHAR)
- presentacion (VARCHAR) [100mg, 250mg, etc.]
- precio_compra (DECIMAL)
- precio_venta (DECIMAL)
- cantidad_minima (INT) [para alertas]
- estado (ENUM: activo, descontinuado)
- fecha_creacion (TIMESTAMP)

**Relación:** Muchos productos → Una categoría
**Normalización:** 3FN ✅
**Índices:** nombre_producto, codigo_sku, id_categoria

---

### ENTIDAD: proveedores
**Descripción:** Proveedores de medicamentos.
- PK: id_proveedor (INT, AUTO_INCREMENT)
- nombre_proveedor (VARCHAR, UNIQUE)
- contacto (VARCHAR)
- telefono (VARCHAR)
- email (VARCHAR)
- direccion (TEXT)
- estado (ENUM: activo, inactivo)
- fecha_creacion (TIMESTAMP)

**Normalización:** 3FN ✅

---

### ENTIDAD: lotes
**Descripción:** Lotes de medicamentos con control de vencimiento.
- PK: id_lote (INT, AUTO_INCREMENT)
- FK: id_producto (INT) → productos
- numero_lote (VARCHAR, UNIQUE)
- fecha_vencimiento (DATE)
- cantidad_inicial (INT)
- cantidad_disponible (INT)
- fecha_creacion (TIMESTAMP)

**Relación:** Muchos lotes → Un producto
**Normalización:** 3FN ✅
**Índices:** id_producto, fecha_vencimiento, numero_lote
**Nota:** cantidad_disponible se actualiza con cada venta (FIFO)

---

### ENTIDAD: compras
**Descripción:** Registro de compras a proveedores.
- PK: id_compra (INT, AUTO_INCREMENT)
- FK: id_proveedor (INT) → proveedores
- numero_compra (VARCHAR, UNIQUE)
- fecha_compra (DATE)
- monto_total (DECIMAL)
- estado (ENUM: pendiente, completada, cancelada)
- observaciones (TEXT)
- fecha_creacion (TIMESTAMP)

**Relación:** Muchas compras → Un proveedor
**Normalización:** 3FN ✅
**Índices:** id_proveedor, fecha_compra

---

### ENTIDAD: detalle_compras
**Descripción:** Detalle de productos en cada compra.
- PK: id_detalle_compra (INT, AUTO_INCREMENT)
- FK: id_compra (INT) → compras
- FK: id_lote (INT) → lotes
- cantidad (INT)
- precio_unitario (DECIMAL)
- subtotal (DECIMAL)

**Relación:** Muchos detalles → Una compra
**Relación:** Muchos detalles → Un lote
**Normalización:** 3FN ✅
**Nota:** El lote se crea en el detalle de compra

---

### ENTIDAD: ventas
**Descripción:** Registro de ventas al cliente.
- PK: id_venta (INT, AUTO_INCREMENT)
- numero_venta (VARCHAR, UNIQUE)
- fecha_venta (DATE)
- monto_total (DECIMAL)
- cliente_nombre (VARCHAR)
- cliente_ci (VARCHAR)
- metodo_pago (ENUM: efectivo, tarjeta, transferencia)
- estado (ENUM: completada, cancelada, devuelto)
- fecha_creacion (TIMESTAMP)

**Normalización:** 3FN ✅
**Índices:** fecha_venta, cliente_ci

---

### ENTIDAD: detalle_ventas
**Descripción:** Detalle de productos vendidos.
- PK: id_detalle_venta (INT, AUTO_INCREMENT)
- FK: id_venta (INT) → ventas
- FK: id_lote (INT) → lotes
- cantidad (INT)
- precio_unitario (DECIMAL)
- subtotal (DECIMAL)

**Relación:** Muchos detalles → Una venta
**Relación:** Muchos detalles → Un lote
**Normalización:** 3FN ✅
**Nota:** TRIGGER: Al insertar, descontar de cantidad_disponible del lote (FIFO)

---

## 2. DIAGRAMA DE RELACIONES

```
┌─────────────────┐
│    usuarios     │
└─────────────────┘

┌─────────────────┐          ┌─────────────────┐
│  categorias     │◄─────────┤   productos     │
└─────────────────┘          └─────────────────┘
                                      ▲
                                      │
                          ┌───────────┴──────────────┐
                          │                          │
                    ┌─────────────┐          ┌──────────────┐
                    │    lotes    │          │ proveedores  │
                    └─────────────┘          └──────────────┘
                          ▲                          ▲
                          │                          │
        ┌─────────────────┴──────────────┐   ┌───────┴──────────────┐
        │                                │   │                      │
   ┌──────────────┐             ┌──────────────────┐        ┌─────────────┐
   │   ventas     │             │     compras      │        │   ventas    │
   └──────────────┘             └──────────────────┘        └─────────────┘
        │                                │
   ┌────┴──────────────┐        ┌────────┴─────────────┐
   │ detalle_ventas    │        │ detalle_compras      │
   └───────────────────┘        └──────────────────────┘
```

---

## 3. INTEGRIDAD REFERENCIAL

### Restricciones Implementadas:

**productos.id_categoria → categorias.id_categoria**
- ON DELETE RESTRICT
- ON UPDATE CASCADE

**lotes.id_producto → productos.id_producto**
- ON DELETE RESTRICT
- ON UPDATE CASCADE

**compras.id_proveedor → proveedores.id_proveedor**
- ON DELETE RESTRICT
- ON UPDATE CASCADE

**detalle_compras.id_compra → compras.id_compra**
- ON DELETE CASCADE
- ON UPDATE CASCADE

**detalle_compras.id_lote → lotes.id_lote**
- ON DELETE RESTRICT
- ON UPDATE CASCADE

**detalle_ventas.id_venta → ventas.id_venta**
- ON DELETE CASCADE
- ON UPDATE CASCADE

**detalle_ventas.id_lote → lotes.id_lote**
- ON DELETE RESTRICT
- ON UPDATE CASCADE

---

## 4. ÍNDICES PARA OPTIMIZACIÓN

```sql
-- Búsquedas frecuentes
CREATE INDEX idx_productos_categoria ON productos(id_categoria);
CREATE INDEX idx_productos_nombre ON productos(nombre_producto);
CREATE INDEX idx_productos_codigo ON productos(codigo_sku);

-- FIFO y Vencimientos
CREATE INDEX idx_lotes_producto ON lotes(id_producto);
CREATE INDEX idx_lotes_vencimiento ON lotes(fecha_vencimiento);

-- Reportes
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_cliente ON ventas(cliente_ci);
CREATE INDEX idx_compras_fecha ON compras(fecha_compra);
CREATE INDEX idx_compras_proveedor ON compras(id_proveedor);
```

---

## 5. TRIGGER FIFO AUTOMÁTICO

**Nombre:** tr_venta_descontar_stock

**Evento:** AFTER INSERT en detalle_ventas

**Lógica:**
1. Obtener id_lote del detalle_venta insertado
2. Obtener cantidad a descontar
3. Actualizar cantidad_disponible del lote
4. Si cantidad_disponible < 0, ROLLBACK (validación)

```sql
DELIMITER $$
CREATE TRIGGER tr_venta_descontar_stock
AFTER INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    UPDATE lotes 
    SET cantidad_disponible = cantidad_disponible - NEW.cantidad
    WHERE id_lote = NEW.id_lote;
    
    IF (SELECT cantidad_disponible FROM lotes WHERE id_lote = NEW.id_lote) < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
END $$
DELIMITER ;
```

---

## 6. TRIGGER ALERTAS DE VENCIMIENTO

**Nombre:** tr_producto_alert_vencimiento

**Evento:** DAILY (procedimiento almacenado)

**Lógica:**
- Detectar lotes que vencen en 7 días
- Detectar lotes ya vencidos
- Registrar en tabla de alertas

---

## 7. TRIGGER STOCK BAJO

**Nombre:** tr_producto_alert_stock

**Evento:** AFTER UPDATE en lotes (cantidad_disponible)

**Lógica:**
- Si cantidad_disponible < cantidad_minima
- Registrar alerta
- Notificar en dashboard

---

## 8. NORMALIZACIÓN VERIFICADA

### Primera Forma Normal (1FN)
✅ No hay grupos repetitivos
✅ Todos los atributos contienen valores atómicos
✅ No hay arrays o colecciones en campos

### Segunda Forma Normal (2FN)
✅ Está en 1FN
✅ Todos los atributos no-clave dependen completamente de la clave primaria
✅ Sin dependencias parciales

### Tercera Forma Normal (3FN)
✅ Está en 2FN
✅ Sin dependencias transitivas
✅ Todos los atributos dependen únicamente de la PK
✅ Separación correcta de categorías, proveedores, usuarios

---

## 9. TABLA DE DATOS

| Tabla | Registros |
|-------|-----------|
| usuarios | 3 |
| categorias | 5 |
| productos | 15 |
| lotes | 45 (3 por producto) |
| proveedores | 5 |
| compras | 20 |
| detalle_compras | ~60 |
| ventas | 20 |
| detalle_ventas | ~40 |

---

**Fecha:** 26 de mayo de 2026
**Diseño:** Arquitectura 3FN Normalizada
**Estado:** ✅ Listo para implementar
