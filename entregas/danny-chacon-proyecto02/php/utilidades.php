<?php

/**
 * UTILIDADES GLOBALES - FARMACIA EL DANNY
 * Funciones reutilizables para todos los módulos
 */

/**
 * Obtiene el ID del trabajador actual desde la BD
 * @param $conn mysqli connection
 * @param $usuario string username from session
 * @return int ID del trabajador
 */
function obtener_id_trabajador($conn, $usuario) {
    $sql = "SELECT id_cedula FROM trabajadores WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_assoc();
    return $row['id_cedula'] ?? 0;
}

/**
 * Formatea un número como moneda COP
 * @param $valor float
 * @return string valor formateado
 */
function formato_moneda($valor) {
    return '$' . number_format($valor, 2, ',', '.');
}

/**
 * Formatea una fecha para mostrar
 * @param $fecha string fecha en formato Y-m-d o Y-m-d H:i:s
 * @param $incluir_hora bool incluir hora
 * @return string fecha formateada
 */
function formato_fecha($fecha, $incluir_hora = true) {
    if(!$fecha) return 'N/A';
    
    if($incluir_hora) {
        return date('d/m/Y H:i', strtotime($fecha));
    } else {
        return date('d/m/Y', strtotime($fecha));
    }
}

/**
 * Valida si un email es válido
 * @param $email string
 * @return bool
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Limpia string para output seguro
 * @param $string string
 * @return string limpio
 */
function limpio($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Calcula si una fecha está vencida
 * @param $fecha string fecha vencimiento
 * @return bool true si está vencida
 */
function esta_vencida($fecha) {
    return strtotime($fecha) < strtotime(date('Y-m-d'));
}

/**
 * Calcula si una fecha está próxima a vencer (30 días)
 * @param $fecha string fecha vencimiento
 * @return bool true si vence en 30 días
 */
function proxima_a_vencer($fecha) {
    $hoy = strtotime(date('Y-m-d'));
    $fecha_obj = strtotime($fecha);
    $dias = ($fecha_obj - $hoy) / 86400;
    return $dias >= 0 && $dias <= 30;
}

?>
