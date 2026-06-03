<?php

/**
 * CONFIGURACIÓN GLOBAL
 * Centraliza todos los valores configurables
 */

// BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'farmacia_danny');

// SITE
define('SITE_NAME', 'Farmacia El Danny');
define('SITE_URL', 'http://localhost/danny-chacon-proyecto02');
define('TIMEZONE', 'America/Bogota');

// SEGURIDAD
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_NAME', 'farmacia_danny_session');

// VALORES DE NEGOCIO
define('DIAS_ALERTA_VENCIMIENTO', 30);
define('STOCK_MINIMO_GLOBAL', 5);
define('STOCK_BAJO_VISUAL', 10);
define('IVA_PORCENTAJE', 19);

// FORMATOS
define('FORMATO_FECHA', 'd/m/Y');
define('FORMATO_FECHA_HORA', 'd/m/Y H:i');
define('FORMATO_MONEDA', 'COP');

?>
