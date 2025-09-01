<?php
/**
 * CONFIGURACIÓN GENERAL DE LA APLICACIÓN
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

// Definir BASE_PATH si no está definido
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Configuración general
define('APP_DEBUG', true);
define('APP_TIMEZONE', 'America/Santo_Domingo');
define('APP_LOCALE', 'es_DO');

// Configuración de seguridad
define('SESSION_LIFETIME', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);
define('PASSWORD_MIN_LENGTH', 6);

// Configuración de archivos
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Configuración de préstamos
define('TASA_MORA_DEFAULT', 0.02); // 2% diario
define('PLAZO_DEFAULT', 30); // 30 días
define('FRECUENCIA_DEFAULT', 'mensual');

// Configuración de notificaciones
define('DIAS_AVISO_VENCIMIENTO', 3);
define('EMAIL_NOTIFICATIONS', false);
define('SMS_NOTIFICATIONS', false);

// Establecer zona horaria
date_default_timezone_set(APP_TIMEZONE);

// Configuración de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_secure' => false, // Cambiar a true en HTTPS
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// Función para obtener configuración
function getConfig($key, $default = null) {
    static $config = null;
    
    if ($config === null) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT clave, valor FROM configuracion");
            $config = [];
            while ($row = $stmt->fetch()) {
                $config[$row['clave']] = $row['valor'];
            }
        } catch (Exception $e) {
            $config = [];
        }
    }
    
    return isset($config[$key]) ? $config[$key] : $default;
}

// Función para establecer configuración
function setConfig($key, $value, $description = '') {
    try {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT OR REPLACE INTO configuracion (clave, valor, descripcion, fecha_actualizacion) 
             VALUES (?, ?, ?, CURRENT_TIMESTAMP)"
        );
        return $stmt->execute([$key, $value, $description]);
    } catch (Exception $e) {
        return false;
    }
}

// Función para logging
function logMessage($message, $level = 'INFO') {
    if (APP_DEBUG) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        
        $logFile = BASE_PATH . '/logs/app.log';
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Función para manejo de errores
function handleError($errno, $errstr, $errfile, $errline) {
    $message = "Error [$errno]: $errstr en $errfile línea $errline";
    logMessage($message, 'ERROR');
    
    if (APP_DEBUG) {
        echo "<div class='alert alert-danger'>$message</div>";
    }
}

// Función para manejo de excepciones
function handleException($exception) {
    $message = "Excepción: " . $exception->getMessage() . " en " . $exception->getFile() . " línea " . $exception->getLine();
    logMessage($message, 'EXCEPTION');
    
    if (APP_DEBUG) {
        echo "<div class='alert alert-danger'>$message</div>";
    } else {
        echo "<div class='alert alert-danger'>Ha ocurrido un error. Por favor, intenta de nuevo.</div>";
    }
}

// Establecer manejadores de errores
set_error_handler('handleError');
set_exception_handler('handleException');
?>
