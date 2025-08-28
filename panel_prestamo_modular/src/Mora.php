<?php
/**
 * Módulo Mora - Sistema de Préstamos Modular
 * Maneja todas las operaciones relacionadas con mora y recargos
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Mora {
    private $db;
    private $tableName = 'mora';
    
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
     * Crear tabla de mora
     */
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            prestamo_id INTEGER NOT NULL,
            monto_mora DECIMAL(10,2) NOT NULL,
            dias_atraso INTEGER NOT NULL,
            fecha_vencimiento DATE NOT NULL,
            fecha_calculo DATE NOT NULL,
            tasa_mora DECIMAL(5,2) DEFAULT 5.0,
            pagado BOOLEAN DEFAULT 0,
            fecha_pago DATE NULL,
            observaciones TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (prestamo_id) REFERENCES prestamos (id)
        )";
        
        $this->db->exec($sql);
    }
    
    /**
     * Calcular mora para un préstamo (usando lógica original)
     */
    public function calcularMora($prestamo_id, $fecha_corte = null) {
        try {
            if (!$fecha_corte) {
                $fecha_corte = date('Y-m-d');
            }
            
            // Obtener información del préstamo
            $sql = "SELECT * FROM prestamos WHERE id = :prestamo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                return ['success' => false, 'error' => 'Préstamo no encontrado'];
            }
            
            // Calcular días de atraso desde la fecha de vencimiento
            $fecha_vencimiento = new DateTime($prestamo['fecha_vencimiento']);
            $fecha_actual = new DateTime($fecha_corte);
            
            if ($fecha_actual <= $fecha_vencimiento) {
                return ['success' => true, 'mora' => 0, 'dias_atraso' => 0, 'aplica_mora' => false];
            }
            
            $dias_atraso = $fecha_actual->diff($fecha_vencimiento)->days;
            
            // Configuración de mora (usando valores del sistema original)
            $dias_gracia = 3; // 3 días de gracia como en el original
            $tasa_mora = 5.0; // 5% de mora como en el original
            
            // Verificar si aplica mora
            $dias_exceso = max(0, $dias_atraso - $dias_gracia);
            $aplica_mora = $dias_exceso > 0;
            
            if (!$aplica_mora) {
                return [
                    'success' => true,
                    'mora' => 0,
                    'dias_atraso' => $dias_atraso,
                    'dias_gracia' => $dias_gracia,
                    'aplica_mora' => false,
                    'mensaje' => 'Cliente dentro del período de gracia'
                ];
            }
            
            // Obtener cuota pendiente (usar monto del préstamo como base)
            $cuota_pendiente = $this->obtenerCuotaPendiente($prestamo_id);
            
            // Calcular mora usando la fórmula original: Mora = Cuota × (Tasa% ÷ 100)
            $monto_mora = $cuota_pendiente * ($tasa_mora / 100);
            
            return [
                'success' => true,
                'mora' => round($monto_mora, 2),
                'dias_atraso' => $dias_atraso,
                'dias_gracia' => $dias_gracia,
                'dias_exceso' => $dias_exceso,
                'aplica_mora' => true,
                'cuota_original' => $cuota_pendiente,
                'tasa_mora' => $tasa_mora,
                'total_a_pagar' => $cuota_pendiente + $monto_mora,
                'formula' => "Mora = {$cuota_pendiente} × ({$tasa_mora}% ÷ 100) = " . round($monto_mora, 2)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error calculando mora: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener cuota pendiente para cálculo de mora
     */
    private function obtenerCuotaPendiente($prestamo_id) {
        try {
            // Obtener información del préstamo
            $sql = "SELECT monto, tipo_interes FROM prestamos WHERE id = :prestamo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) return 0;
            
            // Para Solo Interés: calcular interés sobre saldo pendiente
            if ($prestamo['tipo_interes'] === 'interes') {
                $saldo_pendiente = $this->obtenerSaldoPendiente($prestamo_id);
                return $saldo_pendiente * 0.05; // 5% como en el original
            } else {
                // Para Cuota Fija: usar cuota calculada
                return $prestamo['monto'] * 0.05; // Simplificado por ahora
            }
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Registrar mora calculada
     */
    public function registrarMora($prestamo_id, $monto_mora, $dias_atraso, $fecha_vencimiento, $tasa_mora = 5.0, $observaciones = '') {
        try {
            $fecha_calculo = date('Y-m-d');
            
            $sql = "INSERT INTO {$this->tableName} 
                    (prestamo_id, monto_mora, dias_atraso, fecha_vencimiento, fecha_calculo, tasa_mora, observaciones) 
                    VALUES (:prestamo_id, :monto_mora, :dias_atraso, :fecha_vencimiento, :fecha_calculo, :tasa_mora, :observaciones)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->bindParam(':monto_mora', $monto_mora);
            $stmt->bindParam(':dias_atraso', $dias_atraso);
            $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
            $stmt->bindParam(':fecha_calculo', $fecha_calculo);
            $stmt->bindParam(':tasa_mora', $tasa_mora);
            $stmt->bindParam(':observaciones', $observaciones);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'mensaje' => 'Mora registrada correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error registrando mora: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener saldo pendiente de un préstamo
     */
    private function obtenerSaldoPendiente($prestamo_id) {
        try {
            // Obtener monto del préstamo
            $sql = "SELECT monto FROM prestamos WHERE id = :prestamo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) return 0;
            
            $monto_prestamo = $prestamo['monto'];
            
            // Obtener total pagado (solo capital)
            $sql = "SELECT SUM(monto_capital) as total_pagado FROM pagos WHERE prestamo_id = :prestamo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total_pagado = $resultado['total_pagado'] ?: 0;
            
            return $monto_prestamo - $total_pagado;
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener préstamos en mora
     */
    public function obtenerPrestamosEnMora() {
        try {
            $fecha_actual = date('Y-m-d');
            
            $sql = "SELECT p.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono 
                    FROM prestamos p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.fecha_vencimiento < :fecha_actual 
                    AND p.estado = 'activo'
                    ORDER BY p.fecha_vencimiento ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_actual', $fecha_actual);
            $stmt->execute();
            
            $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular mora para cada préstamo
            foreach ($prestamos as &$prestamo) {
                $mora_info = $this->calcularMora($prestamo['id']);
                $prestamo['mora_calculada'] = $mora_info['mora'] ?? 0;
                $prestamo['dias_atraso'] = $mora_info['dias_atraso'] ?? 0;
            }
            
            return $prestamos;
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Marcar mora como pagada
     */
    public function marcarComoPagada($mora_id) {
        try {
            $sql = "UPDATE {$this->tableName} 
                    SET pagado = 1, fecha_pago = :fecha_pago 
                    WHERE id = :id";
            
            $fecha_pago = date('Y-m-d');
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_pago', $fecha_pago);
            $stmt->bindParam(':id', $mora_id);
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Mora marcada como pagada'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error actualizando mora: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de mora
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total de mora pendiente
            $sql = "SELECT SUM(monto_mora) as total FROM {$this->tableName} WHERE pagado = 0";
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['mora_pendiente'] = $resultado['total'] ?: 0;
            
            // Préstamos en mora
            $fecha_actual = date('Y-m-d');
            $sql = "SELECT COUNT(*) as total FROM prestamos WHERE fecha_vencimiento < :fecha_actual AND estado = 'activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_actual', $fecha_actual);
            $stmt->execute();
            $stats['prestamos_mora'] = $stmt->fetchColumn();
            
            // Mora cobrada este mes
            $sql = "SELECT SUM(monto_mora) as total FROM {$this->tableName} 
                    WHERE pagado = 1 AND DATE(fecha_pago) >= DATE('now', 'start of month')";
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['mora_cobrada_mes'] = $resultado['total'] ?: 0;
            
            return $stats;
            
        } catch (Exception $e) {
            return ['mora_pendiente' => 0, 'prestamos_mora' => 0, 'mora_cobrada_mes' => 0];
        }
    }
    
    /**
     * Obtener todas las moras registradas
     */
    public function obtenerTodas() {
        try {
            $sql = "SELECT m.*, p.monto as prestamo_monto, c.nombre as cliente_nombre 
                    FROM {$this->tableName} m 
                    LEFT JOIN prestamos p ON m.prestamo_id = p.id 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    ORDER BY m.fecha_creacion DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
}

?>
