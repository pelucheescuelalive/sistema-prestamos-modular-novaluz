<?php
/**
 * Módulo Pago - Sistema de Préstamos Modular
 * Maneja todas las operaciones relacionadas con pagos
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Pago {
    private $db;
    private $tableName = 'pagos';
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    /**
     * Inicializar la base de datos SQLite
     */
    private function initDatabase() {
        try {
            $dbPath = __DIR__ . '/../data/prestamos.db';
            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Crear tabla si no existe
            $this->createTable();
            
        } catch (Exception $e) {
            throw new Exception("Error conectando a la base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Crear tabla de pagos
     */
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            prestamo_id INTEGER NOT NULL,
            monto DECIMAL(10,2) NOT NULL,
            monto_interes DECIMAL(10,2) DEFAULT 0,
            monto_capital DECIMAL(10,2) DEFAULT 0,
            monto_mora DECIMAL(10,2) DEFAULT 0,
            fecha DATE NOT NULL,
            tipo TEXT NOT NULL DEFAULT 'abono',
            observaciones TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (prestamo_id) REFERENCES prestamos (id)
        )";
        
        $this->db->exec($sql);
    }
    
    /**
     * Registrar un nuevo pago
     */
    public function registrar($prestamo_id, $monto, $fecha, $tipo = 'abono', $observaciones = '', $desglose = []) {
        try {
            // Validar datos requeridos
            if (empty($prestamo_id) || $monto <= 0) {
                return ['success' => false, 'error' => 'Préstamo ID y monto son requeridos'];
            }
            
            // Extraer desglose
            $monto_interes = isset($desglose['interes']) ? $desglose['interes'] : 0;
            $monto_capital = isset($desglose['capital']) ? $desglose['capital'] : 0;
            $monto_mora = isset($desglose['mora']) ? $desglose['mora'] : 0;
            
            $sql = "INSERT INTO {$this->tableName} 
                    (prestamo_id, monto, monto_interes, monto_capital, monto_mora, fecha, tipo, observaciones) 
                    VALUES (:prestamo_id, :monto, :monto_interes, :monto_capital, :monto_mora, :fecha, :tipo, :observaciones)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':monto_interes', $monto_interes);
            $stmt->bindParam(':monto_capital', $monto_capital);
            $stmt->bindParam(':monto_mora', $monto_mora);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':observaciones', $observaciones);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'mensaje' => 'Pago registrado correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error registrando pago: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los pagos con información del préstamo y cliente
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT p.*, pr.monto as prestamo_monto, c.nombre as cliente_nombre 
                    FROM {$this->tableName} p 
                    LEFT JOIN prestamos pr ON p.prestamo_id = pr.id 
                    LEFT JOIN clientes c ON pr.cliente_id = c.id 
                    ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener pago por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT p.*, pr.monto as prestamo_monto, c.nombre as cliente_nombre 
                    FROM {$this->tableName} p 
                    LEFT JOIN prestamos pr ON p.prestamo_id = pr.id 
                    LEFT JOIN clientes c ON pr.cliente_id = c.id 
                    WHERE p.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Obtener pagos por préstamo
     */
    public function obtenerPorPrestamo($prestamo_id) {
        try {
            $sql = "SELECT * FROM {$this->tableName} 
                    WHERE prestamo_id = :prestamo_id 
                    ORDER BY fecha DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener total de intereses generados
     */
    public function obtenerInteresesGenerados() {
        try {
            $sql = "SELECT SUM(monto_interes) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'] ?: 0;
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Eliminar pago
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Pago eliminado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error eliminando pago: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de pagos
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total de pagos
            $sql = "SELECT COUNT(*) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $stats['total_pagos'] = $stmt->fetchColumn();
            
            // Total cobrado
            $sql = "SELECT SUM(monto) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_cobrado'] = $resultado['total'] ?: 0;
            
            // Pagos este mes
            $sql = "SELECT COUNT(*) as total FROM {$this->tableName} 
                    WHERE DATE(fecha) >= DATE('now', 'start of month')";
            $stmt = $this->db->query($sql);
            $stats['pagos_mes'] = $stmt->fetchColumn();
            
            return $stats;
            
        } catch (Exception $e) {
            return ['total_pagos' => 0, 'total_cobrado' => 0, 'pagos_mes' => 0];
        }
    }
}

?>
