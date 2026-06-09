# 📊 TABLA COMPARATIVA - CUMPLIMIENTO DE REQUISITOS

## Evaluación por Requisito Funcional

| Requisito | Estado | % | Descripción |
|-----------|:------:|:--:|-----------|
| **RF1: Catálogo de Productos** | ⚠️ PARCIAL | 75% | CRUD Create/Read completo. Falta Update/Delete |
| **RF2: Lotes con Vencimiento** | ✅ CUMPLE | 100% | Campos correctos: fecha_ingreso, fecha_vencimiento, cantidad_inicial, cantidad_disponible |
| **RF3: Ventas FIFO** | 🔴 CRÍTICO | 40% | **NO IMPLEMENTA FIFO**: LIMIT 1 sin ORDER BY fecha_ingreso. Vende aleatoriamente, no por antigüedad |
| **RF3: Descuento de Stock** | ✅ CUMPLE | 100% | Actualiza cantidad_disponible correctamente tras venta |
| **RF3: Detalle de Ventas** | ✅ CUMPLE | 100% | Registra id_lote, cantidad, precio_unitario, subtotal |
| **RF4: Compras a Proveedores** | ✅ CUMPLE | 100% | Crea compra + detalle + lote con transacción |
| **RF4: Actualización de Inventario** | ✅ CUMPLE | 100% | Ingresa cantidad_inicial y cantidad_disponible correctamente |
| **RF5: Alertas Stock Bajo** | ⚠️ PARCIAL | 50% | Funciona pero no es configurable (hardcodeado < 10) |
| **RF5: Alertas Vencimiento** | ⚠️ PARCIAL | 50% | Funciona pero no es configurable (hardcodeado 30 días) |
| **RF5: Config. Alertas en BD** | 🔴 NO | 0% | Tabla existe pero NUNCA se consulta desde PHP |
| **RF6: Reporte Rango Fechas** | 🔴 NO | 0% | No existe filtro por fecha inicial/final |
| **RF6: Reporte por Producto** | 🔴 NO | 0% | No existe filtro por producto |
| **RF6: Reporte Totales** | ✅ CUMPLE | 100% | Calcula ventas hoy, compras mes, vencidos, stock bajo |
| **RF7: Búsqueda por Nombre** | ✅ CUMPLE | 100% | Implementado con LIKE |
| **RF7: Búsqueda por Categoría** | ✅ CUMPLE | 100% | Dropdown funcional |
| **RF7: Búsqueda por P. Activo** | ✅ CUMPLE | 100% | Implementado con LIKE |

---

## 🎯 RESUMEN GENERAL

```
Total de requisitos evaluados: 16
✅ Completamente Cumple:  9 (56%)
⚠️  Parcialmente Cumple:  4 (25%)
🔴 No Cumple / Crítico:  3 (19%)

PORCENTAJE DE CUMPLIMIENTO GENERAL: 55-60%
```

---

## 🔴 CAMBIOS NECESARIOS PARA 100% CUMPLIMIENTO
### (Ordenados por importancia/impacto)

### NIVEL 1: CRÍTICO - Afecta funcionalidad core

#### 1. **IMPLEMENTAR FIFO REAL EN VENTAS** 🔴🔴🔴
- **Criticidad:** MÁXIMA
- **Ubicación:** `ventas.php` línea 60-72
- **Problema:** 
  ```php
  // ACTUAL (INCORRECTO):
  SELECT ... FROM lotes WHERE id_producto=? AND estado='ACTIVO' LIMIT 1
  
  // DEBERÍA SER:
  SELECT ... FROM lotes WHERE id_producto=? AND estado='ACTIVO' 
  AND cantidad_disponible > 0
  ORDER BY fecha_ingreso ASC, id_lote ASC LIMIT 1
  ```
- **Impacto:** Sistema vende medicamentos de forma incorrecta
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** MEDIA

---

#### 2. **ENCRIPTAR CONTRASEÑAS EN BASE DE DATOS** 🔴🔴
- **Criticidad:** CRÍTICA (SEGURIDAD)
- **Ubicación:** `login.php` línea 34, todo el sistema
- **Problema:** Contraseñas en texto plano
  ```php
  // ACTUAL (INSEGURO):
  WHERE usuario = ? AND contrasena = ?
  
  // DEBERÍA SER:
  WHERE usuario = ?
  // Luego: password_verify($input, $hash_bd)
  ```
- **Impacto:** Riesgo grave de seguridad si BD es comprometida
- **Tiempo estimado:** 3-4 horas (re-encriptar existentes)
- **Complejidad:** MEDIA-ALTA

---

#### 3. **FIX: MANEJO DE ERRORES EN TRANSACCIONES** 🔴
- **Criticidad:** ALTA
- **Ubicación:** `ventas.php` línea 84, `pedidos.php` línea 26-91
- **Problema:** 
  ```php
  // ACTUAL (MAL):
  die("Error: El producto " . $id_producto . " no tiene suficiente stock.");
  
  // DEBERÍA SER:
  throw new Exception("Stock insuficiente");
  // Con rollback automático
  ```
- **Impacto:** Errores exponen información técnica, DB queda inconsistente
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** MEDIA

---

### NIVEL 2: IMPORTANTE - Funcionalidades faltantes de requisitos

#### 4. **ACTIVAR Y USAR TABLA CONFIGURACION_ALERTAS** ⚠️⚠️
- **Criticidad:** ALTA
- **Ubicación:** `inventario.php` línea 22-29
- **Problema:** 
  ```php
  // ACTUAL (HARDCODEADO):
  if($lote['cantidad_disponible'] < 10) ...
  $fecha_alerta = date('Y-m-d', strtotime('+30 days'));
  
  // DEBERÍA SER:
  $config = $conn->query("SELECT * FROM configuracion_alertas LIMIT 1")->fetch_assoc();
  if($lote['cantidad_disponible'] < $config['stock_bajo_global']) ...
  ```
- **Impacto:** Requisito RF5 no completamente cumplido
- **Tiempo estimado:** 1-2 horas
- **Complejidad:** BAJA

---

#### 5. **COMPLETAR CRUD DE MEDICAMENTOS (Update/Delete)** ⚠️⚠️
- **Criticidad:** ALTA
- **Ubicación:** `medicamentos.php`
- **Problema:** Botones Edit existen pero no funcionan, no hay Delete
- **Impacto:** RF1 solo 75% cumplido
- **Tiempo estimado:** 3-4 horas
- **Complejidad:** MEDIA

---

#### 6. **AGREGAR FILTROS A REPORTES** ⚠️⚠️
- **Criticidad:** ALTA
- **Ubicación:** `reportes.php`
- **Problema:** No hay filtros por fecha, producto, cliente
- **Funcionalidad faltante:**
  - Rango de fechas (inicio - fin)
  - Filtro por producto específico
  - Filtro por cliente
- **Impacto:** RF6 solo 33% cumplido
- **Tiempo estimado:** 4-5 horas
- **Complejidad:** MEDIA

---

#### 7. **IMPLEMENTAR PANEL DE CONFIGURACIÓN DE ALERTAS** ⚠️
- **Criticidad:** MEDIA
- **Ubicación:** Nueva página `config.php`
- **Funcionalidad:** 
  - UI para cambiar `dias_alerta_vencimiento`
  - UI para cambiar `stock_bajo_global`
  - Guardar en BD
- **Impacto:** Requisito RF5 parcialmente cumplido
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** BAJA-MEDIA

---

### NIVEL 3: RECOMENDADO - Mejoras de seguridad y calidad

#### 8. **AGREGAR VALIDACIONES DE DATOS DE ENTRADA** 🟡
- **Criticidad:** MEDIA
- **Ubicación:** Todos los formularios
- **Ejemplo:**
  ```php
  if(!is_numeric($_POST['precio']) || $_POST['precio'] <= 0) ...
  if(!validar_email($_POST['correo'])) ...
  if(strtotime($_POST['fecha']) === false) ...
  ```
- **Impacto:** Prevenir datos inválidos en BD
- **Tiempo estimado:** 3-4 horas
- **Complejidad:** BAJA

---

#### 9. **AGREGAR TOKENS CSRF A FORMULARIOS** 🟡
- **Criticidad:** MEDIA (bajo riesgo en panel admin)
- **Ubicación:** Todos los formularios
- **Implementación:**
  ```php
  // Generar: $_SESSION['csrf'] = bin2hex(random_bytes(32))
  // Validar: if($_POST['csrf'] !== $_SESSION['csrf']) die();
  ```
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** BAJA

---

#### 10. **AGREGAR LOGGING DE ERRORES** 🟡
- **Criticidad:** MEDIA
- **Ubicación:** `php/conexion.php` y handlers de error
- **Funcionalidad:**
  - Log a archivo: `/logs/error.log`
  - No mostrar errores al usuario
  - Mostrar página de error genérica
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** BAJA

---

### NIVEL 4: ENHANCEMENT - Mejoras de UX/features

#### 11. **AGREGAR GRÁFICOS Y VISUALIZACIONES** 🟢
- **Criticidad:** BAJA (feature)
- **Ubicación:** `reportes.php`, `index.php`
- **Librería:** Chart.js
- **Gráficos sugeridos:**
  - Ventas por mes
  - Productos más vendidos
  - Inventario por categoría
- **Tiempo estimado:** 4-5 horas
- **Complejidad:** MEDIA

---

#### 12. **EXPORTAR REPORTES A PDF/EXCEL** 🟢
- **Criticidad:** BAJA (feature)
- **Ubicación:** `reportes.php`
- **Librería:** TCPDF o mPDF (PDF), PHPExcel (Excel)
- **Tiempo estimado:** 3-4 horas
- **Complejidad:** MEDIA

---

#### 13. **AGREGAR EDICIÓN DE CLIENTES Y PROVEEDORES** 🟢
- **Criticidad:** BAJA (funcionalidad incompleta pero secundaria)
- **Ubicación:** `clientes.php`, `proveedores.php`
- **Tiempo estimado:** 2-3 horas
- **Complejidad:** BAJA

---

#### 14. **CREAR ÍNDICE EN LOTES(FECHA_INGRESO)** 🟢
- **Criticidad:** BAJA (performance)
- **Ubicación:** `database_completo.sql`
- **Impacto:** Optimización de FIFO query
- **SQL:**
  ```sql
  CREATE INDEX idx_lote_fecha_ingreso ON lotes(fecha_ingreso);
  CREATE INDEX idx_lote_cantidad ON lotes(cantidad_disponible);
  ```
- **Tiempo estimado:** 15 minutos
- **Complejidad:** TRIVIAL

---

#### 15. **LIMPIAR CÓDIGO Y DOCUMENTACIÓN** 🟢
- **Criticidad:** BAJA (mantenibilidad)
- **Ubicación:** Todo el proyecto
- **Acciones:**
  - Eliminar `php/auth.php` (vacío)
  - Agregar PHPDoc comments
  - Reducir código duplicado
  - Crear README técnico
- **Tiempo estimado:** 3-4 horas
- **Complejidad:** BAJA

---

## 📈 IMPACTO POR CAMBIO

```
Cambio                          Impacto en %    Criticidad    Tiempo
────────────────────────────────────────────────────────────────────
1. FIFO Real                    +20%            🔴 CRÍTICO     3h
2. Encriptar contraseñas        +5%             🔴 CRÍTICO     4h  
3. Manejo de errores            +3%             🔴 CRÍTICO     3h
4. Activar config_alertas       +10%            🟡 ALTO        2h
5. CRUD Medicamentos complete   +15%            🟡 ALTO        4h
6. Filtros en reportes          +15%            🟡 ALTO        5h
7. Panel de configuración       +5%             🟡 MEDIO       3h
8-15. Mejoras menores           +12%            🟢 BAJO        24h
────────────────────────────────────────────────────────────────────
TOTAL PARA 100%                 +85%                            48h
```

---

## 🎯 PLAN RECOMENDADO

### **FASE 1: Críticas (8 horas)** - Hacer ahora
1. Implementar FIFO real
2. Encriptar contraseñas  
3. Fijar manejo de errores

### **FASE 2: Importantes (12 horas)** - Próxima semana
4. Activar tabla de configuración
5. Completar CRUD medicamentos
6. Agregar filtros a reportes

### **FASE 3: Recomendado (10 horas)** - Después
7-10. Validaciones, CSRF, logging, configuración

### **FASE 4: Enhancements (15 horas)** - Opcional
11-15. Gráficos, exportes, documentación

**Tiempo total para 100%:** ~48 horas

---

## ✅ CHECKLIST DE VALIDACIÓN

Después de implementar los cambios:

- [ ] FIFO real testado con múltiples lotes
- [ ] Contraseñas encriptadas en BD
- [ ] Todos los errores loguados y manejados
- [ ] Configuración de alertas editable
- [ ] CRUD medicamentos 100% funcional
- [ ] Reportes con filtros funcionan
- [ ] Validaciones de datos activas
- [ ] Tokens CSRF en formularios
- [ ] Sin errores PHP en error_log
- [ ] Índices creados
- [ ] Documentación actualizada

---

**Generado:** 2026-06-09  
**Evaluador:** Ingeniero de Software - PHP/MySQL  
**Proyecto:** Farmacia El Danny v1.0
