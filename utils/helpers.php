<?php
/**
 * UTILIDADES Y FUNCIONES HELPER
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

/**
 * Formatear moneda
 */
function formatearMoneda($monto) {
    return '$' . number_format($monto, 2, '.', ',');
}

/**
 * Formatear fecha para mostrar
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    if (empty($fecha)) return '';
    
    try {
        $date = new DateTime($fecha);
        return $date->format($formato);
    } catch (Exception $e) {
        return $fecha;
    }
}

/**
 * Calcular próxima fecha de pago según frecuencia
 */
function calcularProximaFecha($fechaInicio, $numeroCuota, $frecuencia) {
    try {
        $fecha = new DateTime($fechaInicio);
        
        switch (strtolower($frecuencia)) {
            case 'semanal':
                $fecha->add(new DateInterval('P' . ($numeroCuota * 7) . 'D'));
                break;
            case 'quincenal':
                $fecha->add(new DateInterval('P' . ($numeroCuota * 14) . 'D'));
                break;
            case 'mensual':
                $fecha->add(new DateInterval('P' . $numeroCuota . 'M'));
                break;
            case 'bimestral':
                $fecha->add(new DateInterval('P' . ($numeroCuota * 2) . 'M'));
                break;
            case 'trimestral':
                $fecha->add(new DateInterval('P' . ($numeroCuota * 3) . 'M'));
                break;
            default:
                $fecha->add(new DateInterval('P' . $numeroCuota . 'M'));
        }
        
        return $fecha->format('Y-m-d');
    } catch (Exception $e) {
        return date('Y-m-d');
    }
}

/**
 * Validar formato de email
 */
function validarEmail($email) {
    if (empty($email)) return true; // Email es opcional
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar formato de teléfono
 */
function validarTelefono($telefono) {
    if (empty($telefono)) return true; // Teléfono es opcional
    
    // Permitir números, espacios, guiones y paréntesis
    return preg_match('/^[\d\s\-\(\)\+]+$/', $telefono);
}

/**
 * Validar documento de identidad
 */
function validarDocumento($documento) {
    if (empty($documento)) return false;
    
    // Permitir solo números y guiones
    return preg_match('/^[\d\-]+$/', $documento) && strlen($documento) >= 6;
}

/**
 * Limpiar string para búsqueda
 */
function limpiarStringBusqueda($string) {
    return trim(strtolower($string));
}

/**
 * Generar número de préstamo único
 */
function generarNumeroPrestamo() {
    return 'PR-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
}

/**
 * Calcular días entre fechas
 */
function calcularDiasEntreFechas($fechaInicio, $fechaFin) {
    try {
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $diferencia = $inicio->diff($fin);
        return $diferencia->days;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Verificar si una fecha es válida
 */
function esFechaValida($fecha, $formato = 'Y-m-d') {
    $d = DateTime::createFromFormat($formato, $fecha);
    return $d && $d->format($formato) === $fecha;
}

/**
 * Obtener nombre del mes en español
 */
function obtenerNombreMes($numeroMes) {
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return $meses[intval($numeroMes)] ?? 'Desconocido';
}

/**
 * Calcular edad a partir de fecha de nacimiento
 */
function calcularEdad($fechaNacimiento) {
    try {
        $hoy = new DateTime();
        $nacimiento = new DateTime($fechaNacimiento);
        $edad = $hoy->diff($nacimiento);
        return $edad->y;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Convertir texto a formato título
 */
function formatearNombre($nombre) {
    return ucwords(strtolower(trim($nombre)));
}

/**
 * Generar hash para password
 */
function generarHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar password contra hash
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitizar entrada para prevenir XSS
 */
function sanitizarInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener IP del cliente
 */
function obtenerIPCliente() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    }
}

/**
 * Generar UUID simple
 */
function generarUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Convertir array a CSV
 */
function arrayToCSV($array, $filename = 'export.csv') {
    if (empty($array)) return false;
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Escribir BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Escribir headers
    fputcsv($output, array_keys($array[0]));
    
    // Escribir datos
    foreach ($array as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    return true;
}

/**
 * Redimensionar imagen
 */
function redimensionarImagen($rutaOrigen, $rutaDestino, $anchoMax = 800, $altoMax = 600) {
    if (!file_exists($rutaOrigen)) return false;
    
    $info = getimagesize($rutaOrigen);
    if (!$info) return false;
    
    $ancho = $info[0];
    $alto = $info[1];
    $tipo = $info[2];
    
    // Calcular nuevas dimensiones manteniendo proporción
    $ratio = min($anchoMax / $ancho, $altoMax / $alto);
    $nuevoAncho = intval($ancho * $ratio);
    $nuevoAlto = intval($alto * $ratio);
    
    // Crear imagen origen
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagenOrigen = imagecreatefromjpeg($rutaOrigen);
            break;
        case IMAGETYPE_PNG:
            $imagenOrigen = imagecreatefrompng($rutaOrigen);
            break;
        case IMAGETYPE_GIF:
            $imagenOrigen = imagecreatefromgif($rutaOrigen);
            break;
        default:
            return false;
    }
    
    // Crear imagen destino
    $imagenDestino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
    
    // Preservar transparencia para PNG
    if ($tipo == IMAGETYPE_PNG) {
        imagealphablending($imagenDestino, false);
        imagesavealpha($imagenDestino, true);
    }
    
    // Redimensionar
    imagecopyresampled(
        $imagenDestino, $imagenOrigen,
        0, 0, 0, 0,
        $nuevoAncho, $nuevoAlto, $ancho, $alto
    );
    
    // Guardar imagen
    $resultado = false;
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $resultado = imagejpeg($imagenDestino, $rutaDestino, 85);
            break;
        case IMAGETYPE_PNG:
            $resultado = imagepng($imagenDestino, $rutaDestino);
            break;
        case IMAGETYPE_GIF:
            $resultado = imagegif($imagenDestino, $rutaDestino);
            break;
    }
    
    // Limpiar memoria
    imagedestroy($imagenOrigen);
    imagedestroy($imagenDestino);
    
    return $resultado;
}
?>
