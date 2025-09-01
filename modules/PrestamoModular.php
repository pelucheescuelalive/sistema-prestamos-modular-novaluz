<?php
/**
 * MÓDULO PRÉSTAMO
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

class PrestamoModular {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Crear nuevo préstamo
     */
    public function crear($datos) {
        try {
            // Validar datos requeridos
            $camposRequeridos = ['cliente_id', 'monto', 'tasa', 'tipo_prestamo', 'frecuencia', 'fecha_inicio'];
            foreach ($camposRequeridos as $campo) {
                if (empty($datos[$campo])) {
                    throw new Exception("El campo $campo es requerido");
                }
            }
            
            // Preparar datos
            $clienteId = intval($datos['cliente_id']);
            $monto = floatval($datos['monto']);
            $tasa = floatval($datos['tasa']);
            $plazo = isset($datos['plazo']) ? intval($datos['plazo']) : null;
            $numeroCuotas = isset($datos['numero_cuotas']) ? intval($datos['numero_cuotas']) : null;
            $tipoPrestamo = $datos['tipo_prestamo'];
            $frecuencia = $datos['frecuencia'];
            $fechaInicio = $datos['fecha_inicio'];
            
            // Verificar que el cliente existe
            $stmt = $this->db->prepare("SELECT nombre FROM clientes WHERE id = ? AND activo = 1");
            $stmt->execute([$clienteId]);
            $cliente = $stmt->fetch();
            if (!$cliente) {
                throw new Exception('Cliente no encontrado o inactivo');
            }
            
            // Calcular valores del préstamo
            $calculados = $this->calcularPrestamo($monto, $tasa, $plazo, $numeroCuotas, $tipoPrestamo, $frecuencia);
            
            // Calcular fecha de vencimiento
            $fechaVencimiento = $this->calcularFechaVencimiento($fechaInicio, $plazo, $numeroCuotas, $frecuencia);
            
            // Insertar préstamo
            $sql = "INSERT INTO prestamos (
                cliente_id, monto, tasa, plazo, numero_cuotas, tipo_prestamo, 
                frecuencia, fecha_inicio, fecha_vencimiento, monto_cuota, 
                total_interes, total_pagar, saldo_pendiente
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $clienteId, $monto, $tasa, $plazo, $numeroCuotas, $tipoPrestamo,
                $frecuencia, $fechaInicio, $fechaVencimiento, $calculados['monto_cuota'],
                $calculados['total_interes'], $calculados['total_pagar'], $calculados['total_pagar']
            ]);
            
            $prestamoId = $this->db->lastInsertId();
            
            logMessage("Préstamo creado exitosamente: ID $prestamoId, Cliente: {$cliente['nombre']}, Monto: $monto");
            
            return [
                'success' => true,
                'message' => 'Préstamo creado exitosamente',
                'data' => [
                    'id' => $prestamoId,
                    'cliente_nombre' => $cliente['nombre'],
                    'monto' => $monto,
                    'calculados' => $calculados
                ]
            ];
            
        } catch (Exception $e) {
            logMessage("Error creando préstamo: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Calcular valores del préstamo
     */
    public function calcularPrestamo($monto, $tasa, $plazo, $numeroCuotas, $tipoPrestamo, $frecuencia) {
        $resultado = [
            'monto_cuota' => 0,
            'total_interes' => 0,
            'total_pagar' => 0
        ];
        
        // Convertir tasa a decimal
        $tasaDecimal = $tasa / 100;
        
        if ($tipoPrestamo === 'solo_interes') {
            // Solo interés: se paga solo el interés cada período, capital al final
            $diasPorPeriodo = $this->obtenerDiasPorFrecuencia($frecuencia);
            $periodos = $plazo ? ceil($plazo / $diasPorPeriodo) : $numeroCuotas;
            
            $interesPorPeriodo = $monto * $tasaDecimal * ($diasPorPeriodo / 30); // Mensualizado
            $totalInteres = $interesPorPeriodo * $periodos;
            
            $resultado['monto_cuota'] = $interesPorPeriodo;
            $resultado['total_interes'] = $totalInteres;
            $resultado['total_pagar'] = $monto + $totalInteres;
            
        } else if ($tipoPrestamo === 'cuotas_fijas') {
            // Cuotas fijas: capital + interés distribuido
            $periodos = $numeroCuotas ?: 1;
            $tasaPorPeriodo = $this->calcularTasaPorPeriodo($tasaDecimal, $frecuencia);
            
            if ($tasaPorPeriodo > 0) {
                // Fórmula de anualidad
                $cuotaFija = $monto * ($tasaPorPeriodo * pow(1 + $tasaPorPeriodo, $periodos)) / 
                            (pow(1 + $tasaPorPeriodo, $periodos) - 1);
            } else {
                $cuotaFija = $monto / $periodos;
            }
            
            $totalPagar = $cuotaFija * $periodos;
            $totalInteres = $totalPagar - $monto;
            
            $resultado['monto_cuota'] = $cuotaFija;
            $resultado['total_interes'] = $totalInteres;
            $resultado['total_pagar'] = $totalPagar;
        }
        
        return $resultado;
    }
    
    /**
     * Obtener días por frecuencia
     */
    private function obtenerDiasPorFrecuencia($frecuencia) {
        switch ($frecuencia) {
            case 'diario': return 1;
            case 'semanal': return 7;
            case 'quincenal': return 15;
            case 'mensual': return 30;
            default: return 30;
        }
    }
    
    /**
     * Calcular tasa por período
     */
    private function calcularTasaPorPeriodo($tasaAnual, $frecuencia) {
        switch ($frecuencia) {
            case 'diario': return $tasaAnual / 365;
            case 'semanal': return $tasaAnual / 52;
            case 'quincenal': return $tasaAnual / 24;
            case 'mensual': return $tasaAnual / 12;
            default: return $tasaAnual / 12;
        }
    }
    
    /**
     * Calcular fecha de vencimiento
     */
    private function calcularFechaVencimiento($fechaInicio, $plazo, $numeroCuotas, $frecuencia) {
        if ($plazo) {
            return date('Y-m-d', strtotime($fechaInicio . " + $plazo days"));
        }
        
        if ($numeroCuotas) {
            $diasPorPeriodo = $this->obtenerDiasPorFrecuencia($frecuencia);
            $diasTotal = $numeroCuotas * $diasPorPeriodo;
            return date('Y-m-d', strtotime($fechaInicio . " + $diasTotal days"));
        }
        
        return date('Y-m-d', strtotime($fechaInicio . " + 30 days")); // Default 30 días
    }
    
    /**
     * Listar préstamos
     */
    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.*,
                    c.nombre as cliente_nombre,
                    c.documento as cliente_documento
                FROM prestamos p
                INNER JOIN clientes c ON p.cliente_id = c.id
                WHERE p.estado = 'activo'
                ORDER BY p.fecha_registro DESC
            ");
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (Exception $e) {
            logMessage("Error listando préstamos: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}

// Funciones helper para compatibilidad
function calcular_prestamo($monto, $tasa, $cuotas) {
    $prestamo = new PrestamoModular();
    return $prestamo->calcularPrestamo($monto, $tasa, null, $cuotas, 'cuotas_fijas', 'mensual');
}

function crear_prestamo($clienteId, $monto, $tasa, $cuotas, $fechaInicio = null) {
    $prestamo = new PrestamoModular();
    return $prestamo->crear([
        'cliente_id' => $clienteId,
        'monto' => $monto,
        'tasa' => $tasa,
        'numero_cuotas' => $cuotas,
        'tipo_prestamo' => 'cuotas_fijas',
        'frecuencia' => 'mensual',
        'fecha_inicio' => $fechaInicio ?: date('Y-m-d')
    ]);
}
?>
