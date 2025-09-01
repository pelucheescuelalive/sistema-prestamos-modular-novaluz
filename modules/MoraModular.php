<?php
/**
 * MÓDULO MORA
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

class MoraModular {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Calcular y aplicar mora para préstamos vencidos
     */
    public function calcularMoraVencida() {
        try {
            // Obtener préstamos activos con cuotas vencidas
            $stmt = $this->db->query("
                SELECT 
                    p.*,
                    c.nombre as cliente_nombre,
                    DATE(p.fecha_proximo_pago) as fecha_vencimiento,
                    JULIANDAY('now') - JULIANDAY(p.fecha_proximo_pago) as dias_vencido
                FROM prestamos p
                INNER JOIN clientes c ON p.cliente_id = c.id
                WHERE p.estado = 'activo' 
                AND DATE(p.fecha_proximo_pago) < DATE('now')
                AND p.saldo_pendiente > 0
            ");
            
            $prestamosVencidos = $stmt->fetchAll();
            $morasAplicadas = 0;
            
            foreach ($prestamosVencidos as $prestamo) {
                $diasVencido = intval($prestamo['dias_vencido']);
                
                if ($diasVencido > 0) {
                    // Verificar si ya hay mora aplicada para esta fecha
                    $stmt = $this->db->prepare("
                        SELECT COUNT(*) as existe 
                        FROM mora 
                        WHERE prestamo_id = ? 
                        AND fecha_vencimiento = ?
                    ");
                    $stmt->execute([$prestamo['id'], $prestamo['fecha_vencimiento']]);
                    
                    if ($stmt->fetch()['existe'] == 0) {
                        $montoMora = $this->calcularMontoMora($prestamo, $diasVencido);
                        
                        if ($montoMora > 0) {
                            $this->aplicarMora($prestamo['id'], $montoMora, $prestamo['fecha_vencimiento'], $diasVencido);
                            $morasAplicadas++;
                        }
                    }
                }
            }
            
            return [
                'success' => true,
                'message' => "Se aplicaron $morasAplicadas moras automáticamente",
                'data' => [
                    'prestamos_revisados' => count($prestamosVencidos),
                    'moras_aplicadas' => $morasAplicadas
                ]
            ];
            
        } catch (Exception $e) {
            logMessage("Error calculando mora vencida: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Calcular monto de mora según política establecida
     */
    private function calcularMontoMora($prestamo, $diasVencido) {
        // Obtener configuración de mora
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'porcentaje_mora_diaria'");
        $stmt->execute();
        $porcentajeMora = floatval($stmt->fetch()['valor'] ?? 0.02); // 2% por defecto
        
        // Calcular mora sobre la cuota pendiente
        $montoCuota = floatval($prestamo['monto_cuota']);
        $montoMora = $montoCuota * ($porcentajeMora / 100) * $diasVencido;
        
        // Aplicar límite máximo de mora si está configurado
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'mora_maxima_por_cuota'");
        $stmt->execute();
        $moraMaxima = floatval($stmt->fetch()['valor'] ?? 0);
        
        if ($moraMaxima > 0 && $montoMora > $moraMaxima) {
            $montoMora = $moraMaxima;
        }
        
        return round($montoMora, 2);
    }
    
    /**
     * Aplicar mora a un préstamo
     */
    private function aplicarMora($prestamoId, $montoMora, $fechaVencimiento, $diasVencido) {
        $sql = "INSERT INTO mora (
            prestamo_id, monto_mora, fecha_vencimiento, 
            dias_vencido, fecha_aplicacion, pagado
        ) VALUES (?, ?, ?, ?, CURRENT_DATE, 0)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$prestamoId, $montoMora, $fechaVencimiento, $diasVencido]);
        
        logMessage("Mora aplicada: Préstamo $prestamoId, Monto: $montoMora, Días vencido: $diasVencido");
    }
    
    /**
     * Listar mora por préstamo
     */
    public function listarPorPrestamo($prestamoId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.*,
                    p.monto_cuota,
                    c.nombre as cliente_nombre
                FROM mora m
                INNER JOIN prestamos p ON m.prestamo_id = p.id
                INNER JOIN clientes c ON p.cliente_id = c.id
                WHERE m.prestamo_id = ?
                ORDER BY m.fecha_vencimiento DESC
            ");
            $stmt->execute([$prestamoId]);
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (Exception $e) {
            logMessage("Error listando mora por préstamo: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
    
    /**
     * Listar toda la mora pendiente
     */
    public function listarPendiente() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    m.*,
                    p.monto_cuota,
                    p.saldo_pendiente,
                    c.nombre as cliente_nombre,
                    c.documento as cliente_documento,
                    JULIANDAY('now') - JULIANDAY(m.fecha_vencimiento) as dias_total_vencido
                FROM mora m
                INNER JOIN prestamos p ON m.prestamo_id = p.id
                INNER JOIN clientes c ON p.cliente_id = c.id
                WHERE m.pagado = 0
                ORDER BY m.fecha_vencimiento ASC
            ");
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (Exception $e) {
            logMessage("Error listando mora pendiente: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
    
    /**
     * Obtener resumen de mora
     */
    public function obtenerResumen() {
        try {
            // Total mora pendiente
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_registros,
                    SUM(monto_mora) as total_monto_pendiente,
                    COUNT(DISTINCT prestamo_id) as prestamos_con_mora
                FROM mora 
                WHERE pagado = 0
            ");
            $pendiente = $stmt->fetch();
            
            // Total mora pagada este mes
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_pagadas,
                    SUM(monto_mora) as total_monto_pagado
                FROM mora 
                WHERE pagado = 1 
                AND DATE(fecha_pago) >= DATE('now', 'start of month')
            ");
            $pagadoMes = $stmt->fetch();
            
            return [
                'success' => true,
                'data' => [
                    'mora_pendiente' => [
                        'registros' => intval($pendiente['total_registros']),
                        'monto' => floatval($pendiente['total_monto_pendiente'] ?? 0),
                        'prestamos_afectados' => intval($pendiente['prestamos_con_mora'])
                    ],
                    'mora_pagada_mes' => [
                        'registros' => intval($pagadoMes['total_pagadas']),
                        'monto' => floatval($pagadoMes['total_monto_pagado'] ?? 0)
                    ]
                ]
            ];
        } catch (Exception $e) {
            logMessage("Error obteniendo resumen de mora: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Condonar mora específica
     */
    public function condonar($moraId, $motivo = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE mora 
                SET pagado = 1, 
                    fecha_pago = CURRENT_DATE,
                    observaciones = ? 
                WHERE id = ? AND pagado = 0
            ");
            $stmt->execute(['CONDONADA: ' . $motivo, $moraId]);
            
            if ($stmt->rowCount() > 0) {
                logMessage("Mora condonada: ID $moraId, Motivo: $motivo");
                return [
                    'success' => true,
                    'message' => 'Mora condonada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Mora no encontrada o ya pagada'
                ];
            }
        } catch (Exception $e) {
            logMessage("Error condonando mora: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Funciones helper para compatibilidad
function calcular_mora($prestamo_id) {
    $mora = new MoraModular();
    return $mora->calcularMoraVencida();
}

function obtener_mora_pendiente($prestamo_id) {
    $mora = new MoraModular();
    $resultado = $mora->listarPorPrestamo($prestamo_id);
    
    if ($resultado['success']) {
        $total = 0;
        foreach ($resultado['data'] as $registro) {
            if (!$registro['pagado']) {
                $total += $registro['monto_mora'];
            }
        }
        return $total;
    }
    
    return 0;
}
?>
