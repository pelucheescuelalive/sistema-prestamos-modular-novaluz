<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

// Definir BASE_PATH si no está definido
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Configuración de la base de datos SQLite
define('DB_PATH', BASE_PATH . '/prestamos.db');
define('DB_TYPE', 'sqlite');

/**
 * Clase para manejar la conexión a la base de datos
 */
class DatabaseConnection {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        try {
            $this->connection = new PDO('sqlite:' . DB_PATH);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Habilitar claves foráneas
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
            // Crear tablas si no existen
            $this->createTables();
            
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Crear tablas del sistema
     */
    private function createTables() {
        $sql = [
            // Tabla de clientes
            "CREATE TABLE IF NOT EXISTS clientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                documento TEXT UNIQUE,
                telefono TEXT,
                email TEXT,
                direccion TEXT,
                genero TEXT DEFAULT 'no_especificado',
                calificacion REAL DEFAULT 5.0,
                foto_perfil TEXT,
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Tabla de préstamos
            "CREATE TABLE IF NOT EXISTS prestamos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cliente_id INTEGER NOT NULL,
                monto REAL NOT NULL,
                tasa REAL NOT NULL,
                plazo INTEGER,
                numero_cuotas INTEGER,
                tipo_prestamo TEXT NOT NULL DEFAULT 'solo_interes',
                frecuencia TEXT NOT NULL DEFAULT 'mensual',
                fecha_inicio DATE NOT NULL,
                fecha_vencimiento DATE,
                estado TEXT DEFAULT 'activo',
                monto_cuota REAL,
                total_interes REAL,
                total_pagar REAL,
                saldo_pendiente REAL,
                cuotas_pagadas INTEGER DEFAULT 0,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id)
            )",
            
            // Tabla de pagos
            "CREATE TABLE IF NOT EXISTS pagos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                prestamo_id INTEGER NOT NULL,
                monto REAL NOT NULL,
                fecha_pago DATE NOT NULL,
                tipo_pago TEXT DEFAULT 'cuota',
                metodo_pago TEXT DEFAULT 'efectivo',
                numero_cuota INTEGER,
                capital REAL DEFAULT 0,
                interes REAL DEFAULT 0,
                mora REAL DEFAULT 0,
                observaciones TEXT,
                estado TEXT DEFAULT 'completado',
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
            )",
            
            // Tabla de mora
            "CREATE TABLE IF NOT EXISTS mora (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                prestamo_id INTEGER NOT NULL,
                monto_mora REAL NOT NULL,
                dias_atraso INTEGER NOT NULL,
                tasa_mora REAL DEFAULT 0.02,
                fecha_vencimiento DATE NOT NULL,
                fecha_calculo DATE NOT NULL,
                pagado INTEGER DEFAULT 0,
                fecha_pago DATE,
                observaciones TEXT,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
            )",
            
            // Tabla de configuración
            "CREATE TABLE IF NOT EXISTS configuracion (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                clave TEXT UNIQUE NOT NULL,
                valor TEXT,
                descripcion TEXT,
                tipo TEXT DEFAULT 'text',
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        ];
        
        foreach ($sql as $query) {
            $this->connection->exec($query);
        }
        
        // Insertar configuración por defecto
        $this->insertDefaultConfig();
    }
    
    /**
     * Insertar configuración por defecto
     */
    private function insertDefaultConfig() {
        $configs = [
            ['clave' => 'app_name', 'valor' => 'Sistema de Préstamos Nova Luz Pro', 'descripcion' => 'Nombre de la aplicación'],
            ['clave' => 'tasa_mora_default', 'valor' => '0.02', 'descripcion' => 'Tasa de mora por defecto (2%)'],
            ['clave' => 'moneda', 'valor' => 'RD$', 'descripcion' => 'Símbolo de moneda'],
            ['clave' => 'formato_fecha', 'valor' => 'd/m/Y', 'descripcion' => 'Formato de fecha'],
            ['clave' => 'backup_automatico', 'valor' => '1', 'descripcion' => 'Backup automático habilitado']
        ];
        
        $stmt = $this->connection->prepare(
            "INSERT OR IGNORE INTO configuracion (clave, valor, descripcion) VALUES (?, ?, ?)"
        );
        
        foreach ($configs as $config) {
            $stmt->execute([$config['clave'], $config['valor'], $config['descripcion']]);
        }
    }
}

// Función helper para obtener la conexión
function getDB() {
    return DatabaseConnection::getInstance()->getConnection();
}
?>
