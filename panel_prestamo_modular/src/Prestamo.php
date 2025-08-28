<?php
/**
 * Módulo Préstamo - Sistema de Préstamos Modular
 * Maneja todas las operaciones relacionadas con préstamos
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Prestamo {
    private $db;
    private $tableName = 'prestamos';
    
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
     * Crear tabla de préstamos
     */
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            monto DECIMAL(10,2) NOT NULL,
            monto_pendiente DECIMAL(10,2) NOT NULL,
            tasa DECIMAL(5,2) NOT NULL,
            plazo INTEGER NOT NULL,
            fecha DATE NOT NULL,
            fecha_vencimiento DATE,
            tipo TEXT NOT NULL DEFAULT 'interes',
            frecuencia TEXT NOT NULL DEFAULT 'quincenal',
            cuotas INTEGER NOT NULL DEFAULT 1,
            cuotas_pagadas INTEGER DEFAULT 0,
            estado TEXT DEFAULT 'activo',
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        )";
        
        $this->db->exec($sql);
    }
    
    /**
     * Crear un nuevo préstamo
     */
    public function crear($cliente_id, $monto, $tasa, $plazo, $fecha, $tipo = 'interes', $frecuencia = 'quincenal', $cuotas = 1) {
        try {
            // Validar datos requeridos
            if (empty($cliente_id) || $monto <= 0 || $tasa <= 0) {
                return ['success' => false, 'error' => 'Cliente ID, monto y tasa son requeridos y deben ser válidos'];
            }
            
            // Para préstamos de cuota, el plazo debe ser mayor a 0
            if ($tipo === 'cuota' && $plazo <= 0) {
                return ['success' => false, 'error' => 'Para préstamos de cuota, el plazo debe ser mayor a 0'];
            }
            
            // Para préstamos de interés, si no se especifica plazo, usar 1 como default
            if ($tipo === 'interes' && $plazo <= 0) {
                $plazo = 1;
            }
            
            // Verificar que el cliente existe
            if (!$this->clienteExiste($cliente_id)) {
                return ['success' => false, 'error' => 'El cliente especificado no existe'];
            }
            
            // Calcular fecha de vencimiento
            $fechaVencimiento = $this->calcularFechaVencimiento($fecha, $plazo);
            
            $sql = "INSERT INTO {$this->tableName} 
                    (cliente_id, monto, monto_pendiente, tasa, plazo, fecha, fecha_vencimiento, tipo, frecuencia, cuotas) 
                    VALUES (:cliente_id, :monto, :monto_pendiente, :tasa, :plazo, :fecha, :fecha_vencimiento, :tipo, :frecuencia, :cuotas)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':monto_pendiente', $monto); // Inicialmente igual al monto
            $stmt->bindParam(':tasa', $tasa);
            $stmt->bindParam(':plazo', $plazo);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':fecha_vencimiento', $fechaVencimiento);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':frecuencia', $frecuencia);
            $stmt->bindParam(':cuotas', $cuotas);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'mensaje' => 'Préstamo creado correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error creando préstamo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los préstamos con información del cliente
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT p.*, c.nombre as cliente_nombre 
                    FROM {$this->tableName} p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener préstamo por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT p.*, c.nombre as cliente_nombre, c.documento as cliente_documento 
                    FROM {$this->tableName} p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
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
     * Obtener préstamos activos
     */
    public function obtenerActivos() {
        try {
            $sql = "SELECT p.*, c.nombre as cliente_nombre 
                    FROM {$this->tableName} p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.estado = 'activo' 
                    ORDER BY p.fecha_vencimiento ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Calcular información del préstamo (usando las fórmulas originales del sistema)
     */
    public function calcular($monto, $tasa, $cuotas, $tipo = 'interes') {
        try {
            $resultado = [];
            
            if ($tipo === 'interes') {
                // CÁLCULO PARA SOLO INTERÉS (Fórmula original)
                // I = P × r (SOLO INTERÉS - CAPITAL NO SE REDUCE)
                $interes_por_periodo = $monto * ($tasa / 100);
                
                $resultado = [
                    'tipo' => 'Solo Interés',
                    'capital' => $monto,
                    'tasa' => $tasa,
                    'pago_minimo' => $interes_por_periodo,
                    'interes_periodo' => $interes_por_periodo,
                    'descripcion' => 'Capital NUNCA cambia en solo interés. Pago mínimo = Solo interés',
                    'ejemplo' => "Si paga más de RD$" . number_format($interes_por_periodo, 2) . " → reduce capital"
                ];
                
            } else {
                // CÁLCULO PARA CUOTA FIJA (Fórmula original con interés simple)
                // M = C × (1 + i × n) - Interés simple
                $tasa_decimal = $tasa / 100;
                $monto_total = $monto * (1 + ($tasa_decimal * $cuotas));
                $interes_total = $monto_total - $monto;
                $cuota_fija = $monto_total / $cuotas;
                
                $resultado = [
                    'tipo' => 'Cuota Fija - Interés Simple',
                    'capital' => $monto,
                    'tasa' => $tasa,
                    'cuotas' => $cuotas,
                    'cuota_fija' => $cuota_fija,
                    'monto_total' => $monto_total,
                    'interes_total' => $interes_total,
                    'formula' => "M = C × (1 + i × n)",
                    'calculo' => "{$monto} × (1 + {$tasa_decimal} × {$cuotas}) = " . number_format($monto_total, 2),
                    'descripcion' => 'Cuota fija durante todo el préstamo. En ' . $cuotas . ' pagos quedas libre de deuda'
                ];
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            return ['error' => 'Error en cálculo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Procesar pago para préstamo de Solo Interés (lógica original)
     */
    public function procesarPagoSoloInteres($prestamo_id, $monto_pago) {
        try {
            // Obtener préstamo
            $prestamo = $this->obtenerPorId($prestamo_id);
            if (!$prestamo) {
                return ['success' => false, 'error' => 'Préstamo no encontrado'];
            }
            
            // Obtener pagos previos para calcular capital pendiente
            $sql = "SELECT SUM(monto_capital) as capital_pagado FROM pagos WHERE prestamo_id = :prestamo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $capital_pagado = $resultado['capital_pagado'] ?: 0;
            $capital_pendiente = $prestamo['monto'] - $capital_pagado;
            
            // Calcular interés actual (tasa por defecto 5%)
            $tasa = 5; // Deberías obtener esto de la configuración o del préstamo
            $interes_periodo = $capital_pendiente * ($tasa / 100);
            
            // Determinar distribución del pago según lógica original
            $pago_interes = min($monto_pago, $interes_periodo);
            $pago_capital = 0;
            
            // Si el pago cubre el interés completo, el exceso va a capital
            if ($monto_pago >= $interes_periodo) {
                $pago_capital = $monto_pago - $interes_periodo;
            }
            
            // Capital pendiente después del pago
            $nuevo_capital = $capital_pendiente - $pago_capital;
            
            return [
                'success' => true,
                'desglose' => [
                    'interes' => $pago_interes,
                    'capital' => $pago_capital,
                    'mora' => 0
                ],
                'estado' => [
                    'capital_pendiente' => $nuevo_capital,
                    'nuevo_interes' => $nuevo_capital * ($tasa / 100),
                    'pago_cubierto' => $monto_pago >= $interes_periodo ? 'completo' : 'parcial'
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error procesando pago: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar monto pendiente del préstamo
     */
    public function actualizarMontoPendiente($id, $nuevoMonto) {
        try {
            $sql = "UPDATE {$this->tableName} 
                    SET monto_pendiente = :monto_pendiente, 
                        fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':monto_pendiente', $nuevoMonto);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Actualizar estado del préstamo
     */
    public function actualizarEstado($id, $estado) {
        try {
            $sql = "UPDATE {$this->tableName} 
                    SET estado = :estado, 
                        fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtener monto total de préstamos
     */
    public function obtenerMontoTotal() {
        try {
            $sql = "SELECT SUM(monto) as total FROM {$this->tableName} WHERE estado = 'activo'";
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'] ?: 0;
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener monto atrasado
     */
    public function obtenerMontoAtrasado() {
        try {
            $sql = "SELECT SUM(monto_pendiente) as total 
                    FROM {$this->tableName} 
                    WHERE estado = 'activo' 
                    AND fecha_vencimiento < DATE('now')";
            
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'] ?: 0;
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener cuotas vencidas
     */
    public function obtenerCuotasVencidas() {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM {$this->tableName} 
                    WHERE estado = 'activo' 
                    AND fecha_vencimiento < DATE('now')";
            
            $stmt = $this->db->query($sql);
            
            return $stmt->fetchColumn();
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Verificar si un cliente existe
     */
    private function clienteExiste($clienteId) {
        try {
            $sql = "SELECT COUNT(*) FROM clientes WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $clienteId);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Calcular fecha de vencimiento
     */
    private function calcularFechaVencimiento($fechaInicio, $plazo) {
        try {
            $fecha = new DateTime($fechaInicio);
            $fecha->add(new DateInterval("P{$plazo}D"));
            return $fecha->format('Y-m-d');
            
        } catch (Exception $e) {
            return $fechaInicio;
        }
    }
    
    /**
     * Obtener préstamos por cliente
     */
    public function obtenerPorCliente($clienteId) {
        try {
            $sql = "SELECT p.*, c.nombre as cliente_nombre 
                    FROM {$this->tableName} p
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.cliente_id = :cliente_id 
                    ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Actualizar un préstamo existente
     */
    public function actualizar($id, $cliente_id, $monto, $tasa, $plazo, $fecha, $tipo = 'interes', $frecuencia = 'quincenal', $cuotas = 1) {
        try {
            // Validar datos requeridos
            if (empty($id) || empty($cliente_id) || $monto <= 0 || $tasa <= 0 || $plazo <= 0) {
                return ['success' => false, 'error' => 'Todos los campos son requeridos y deben ser válidos'];
            }
            
            // Verificar que el préstamo existe
            $prestamoExistente = $this->obtenerPorId($id);
            if (!$prestamoExistente) {
                return ['success' => false, 'error' => 'El préstamo especificado no existe'];
            }
            
            // Verificar que el cliente existe
            if (!$this->clienteExiste($cliente_id)) {
                return ['success' => false, 'error' => 'El cliente especificado no existe'];
            }
            
            // Calcular fecha de vencimiento
            $fechaVencimiento = $this->calcularFechaVencimiento($fecha, $plazo);
            
            $sql = "UPDATE {$this->tableName} SET 
                    cliente_id = :cliente_id,
                    monto = :monto,
                    monto_pendiente = :monto_pendiente,
                    tasa = :tasa,
                    plazo = :plazo,
                    fecha = :fecha,
                    fecha_vencimiento = :fecha_vencimiento,
                    tipo = :tipo,
                    frecuencia = :frecuencia,
                    cuotas = :cuotas
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':monto_pendiente', $monto); // Actualizar monto pendiente
            $stmt->bindParam(':tasa', $tasa);
            $stmt->bindParam(':plazo', $plazo);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':fecha_vencimiento', $fechaVencimiento);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':frecuencia', $frecuencia);
            $stmt->bindParam(':cuotas', $cuotas);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'id' => $id,
                'mensaje' => 'Préstamo actualizado correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar un préstamo
     */
    public function eliminar($id) {
        try {
            // Verificar que el préstamo existe
            $prestamo = $this->obtenerPorId($id);
            if (!$prestamo) {
                return ['success' => false, 'error' => 'El préstamo especificado no existe'];
            }
            
            $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'mensaje' => 'Préstamo eliminado correctamente'
                ];
            } else {
                return ['success' => false, 'error' => 'Error al eliminar el préstamo'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de préstamos
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total de préstamos
            $sql = "SELECT COUNT(*) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $stats['total_prestamos'] = $stmt->fetchColumn();
            
            // Préstamos activos
            $sql = "SELECT COUNT(*) as activos FROM {$this->tableName} WHERE estado = 'activo'";
            $stmt = $this->db->query($sql);
            $stats['prestamos_activos'] = $stmt->fetchColumn();
            
            // Monto total prestado
            $sql = "SELECT SUM(monto_prestado) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['monto_total_prestado'] = $result['total'] ?: 0;
            
            // Monto pendiente
            $sql = "SELECT SUM(monto_pendiente) as total FROM {$this->tableName} WHERE estado = 'activo'";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['monto_pendiente'] = $result['total'] ?: 0;
            
            return $stats;
            
        } catch (Exception $e) {
            return ['total_prestamos' => 0, 'prestamos_activos' => 0, 'monto_total_prestado' => 0, 'monto_pendiente' => 0];
        }
    }
}

?>
