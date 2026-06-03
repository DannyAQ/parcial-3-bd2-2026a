-- ============================================================
-- FARMACIA DE BARRIO - CREACIÓN DE BASE DE DATOS
-- Script 01: Crear la base de datos y usuario
-- ============================================================

-- Verificar si la BD ya existe
DROP DATABASE IF EXISTS farmacia_barrio;

-- Crear la base de datos
CREATE DATABASE farmacia_barrio 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE farmacia_barrio;

-- ============================================================
-- Usuario de acceso a la BD (Opcional, si se requiere usuario específico)
-- ============================================================
-- CREATE USER 'farmacia_user'@'localhost' IDENTIFIED BY 'Farmacia2026!';
-- GRANT ALL PRIVILEGES ON farmacia_barrio.* TO 'farmacia_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================================
-- Confirmación
-- ============================================================
SELECT 'Base de datos farmacia_barrio creada exitosamente' AS Mensaje;
