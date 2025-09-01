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
                tipo TEXT DEFAULT 'Persona',
                cedula TEXT UNIQUE,
                nombres TEXT NOT NULL,
                apellidos TEXT NOT NULL,
                nombre_completo TEXT NOT NULL,
                genero TEXT,
                celular TEXT NOT NULL,
                telefono TEXT,
                nacionalidad TEXT DEFAULT 'Dominicano',
                fecha_nacimiento DATE,
                direccion TEXT,
                email TEXT,
                profesion TEXT,
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Tabla de préstamos
            "CREATE TABLE IF NOT EXISTS prestamos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cliente_id INTEGER NOT NULL,
                monto REAL NOT NULL,
                tasa_interes REAL NOT NULL,
                numero_cuotas INTEGER NOT NULL,
                cuota_mensual REAL NOT NULL,
                saldo_pendiente REAL NOT NULL,
                estado TEXT DEFAULT 'activo',
                fecha_solicitud DATE NOT NULL,
                fecha_aprobacion DATE,
                fecha_primer_pago DATE,
                observaciones TEXT,
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id)
            )",
            
            // Tabla de pagos
            "CREATE TABLE IF NOT EXISTS pagos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                prestamo_id INTEGER NOT NULL,
                numero_cuota INTEGER NOT NULL,
                monto_cuota REAL NOT NULL,
                monto_pagado REAL NOT NULL,
                fecha_vencimiento DATE NOT NULL,
                fecha_pago DATE,
                estado TEXT DEFAULT 'pendiente',
                metodo_pago TEXT,
                referencia TEXT,
                observaciones TEXT,
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
            )",
            
            // Tabla de mora
            "CREATE TABLE IF NOT EXISTS mora (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                pago_id INTEGER NOT NULL,
                prestamo_id INTEGER NOT NULL,
                dias_mora INTEGER NOT NULL,
                monto_mora REAL NOT NULL,
                tasa_mora REAL DEFAULT 5.00,
                estado TEXT DEFAULT 'activa',
                fecha_inicio DATE NOT NULL,
                fecha_fin DATE,
                observaciones TEXT,
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (pago_id) REFERENCES pagos(id),
                FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
            )",
            
            // Tabla de configuración
            "CREATE TABLE IF NOT EXISTS configuracion (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                clave TEXT UNIQUE NOT NULL,
                valor TEXT NOT NULL,
                descripcion TEXT,
                tipo TEXT DEFAULT 'string',
                activo INTEGER DEFAULT 1,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        ];
        
        foreach ($sql as $statement) {
            $this->connection->exec($statement);
        }
        
        // Insertar configuraciones básicas
        $this->insertDefaultConfig();
    }
    
    /**
     * Insertar configuraciones por defecto
     */
    private function insertDefaultConfig() {
        $configuraciones = [
            ['tasa_interes_default', '15.00', 'Tasa de interés por defecto (%)', 'decimal'],
            ['tasa_mora_default', '5.00', 'Tasa de mora por defecto (%)', 'decimal'],
            ['moneda', 'RD$', 'Moneda del sistema', 'string'],
            ['empresa_nombre', 'Nova Luz Pro', 'Nombre de la empresa', 'string'],
            ['empresa_telefono', '809-000-0000', 'Teléfono de la empresa', 'string']
        ];
        
        $stmt = $this->connection->prepare("INSERT OR IGNORE INTO configuracion (clave, valor, descripcion, tipo) VALUES (?, ?, ?, ?)");
        foreach ($configuraciones as $config) {
            $stmt->execute($config);
        }
    }
}

/**
 * Función para obtener la conexión a la base de datos
 */
function getDB() {
    return DatabaseConnection::getInstance()->getConnection();
}
?>
