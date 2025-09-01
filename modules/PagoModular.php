<?php
/**
 * MÓDULO PAGO
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

class PagoModular {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Realizar pago de préstamo
     */
    public function realizar($prestamoId, $monto, $fecha = null, $metodo = 'efectivo') {
        try {
            if (!$fecha) {
                $fecha = date('Y-m-d');
            }
            
            // Obtener información del préstamo
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre as cliente_nombre 
                FROM prestamos p 
                INNER JOIN clientes c ON p.cliente_id = c.id 
                WHERE p.id = ? AND p.estado = 'activo'
            ");
            $stmt->execute([$prestamoId]);
            $prestamo = $stmt->fetch();
            
            if (!$prestamo) {
                throw new Exception('Préstamo no encontrado o inactivo');
            }
            
            if ($monto <= 0) {
                throw new Exception('El monto del pago debe ser mayor a cero');
            }
            
            if ($monto > $prestamo['saldo_pendiente']) {
                throw new Exception('El monto del pago no puede ser mayor al saldo pendiente');
            }
            
            // Calcular distribución del pago
            $distribucion = $this->calcularDistribucionPago($prestamo, $monto);
            
            // Obtener número de cuota
            $stmt = $this->db->prepare("SELECT COUNT(*) + 1 as numero_cuota FROM pagos WHERE prestamo_id = ?");
            $stmt->execute([$prestamoId]);
            $numeroCuota = $stmt->fetch()['numero_cuota'];
            
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // Insertar el pago
            $sql = "INSERT INTO pagos (
                prestamo_id, monto, fecha_pago, tipo_pago, metodo_pago, 
                numero_cuota, capital, interes, mora
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $prestamoId, $monto, $fecha, 'cuota', $metodo,
                $numeroCuota, $distribucion['capital'], $distribucion['interes'], $distribucion['mora']
            ]);
            
            // Actualizar saldo del préstamo
            $nuevoSaldo = $prestamo['saldo_pendiente'] - $monto;
            $nuevasCuotasPagadas = $prestamo['cuotas_pagadas'] + 1;
            
            $sql = "UPDATE prestamos SET 
                    saldo_pendiente = ?, 
                    cuotas_pagadas = ?,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$nuevoSaldo, $nuevasCuotasPagadas, $prestamoId]);
            
            // Si el saldo es cero, marcar como completado
            if ($nuevoSaldo <= 0.01) { // Permitir pequeña diferencia por redondeo
                $stmt = $this->db->prepare("UPDATE prestamos SET estado = 'completado' WHERE id = ?");
                $stmt->execute([$prestamoId]);
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            logMessage("Pago realizado exitosamente: Préstamo $prestamoId, Monto: $monto");
            
            return [
                'success' => true,
                'message' => 'Pago realizado exitosamente',
                'data' => [
                    'numero_cuota' => $numeroCuota,
                    'monto_pagado' => $monto,
                    'saldo_pendiente' => $nuevoSaldo,
                    'distribucion' => $distribucion,
                    'completado' => $nuevoSaldo <= 0.01
                ]
            ];
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            logMessage("Error realizando pago: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Calcular distribución del pago (capital, interés, mora)
     */
    private function calcularDistribucionPago($prestamo, $montoPago) {
        $distribucion = [
            'capital' => 0,
            'interes' => 0,
            'mora' => 0
        ];
        
        // Primero verificar si hay mora pendiente
        $stmt = $this->db->prepare("
            SELECT SUM(monto_mora) as total_mora 
            FROM mora 
            WHERE prestamo_id = ? AND pagado = 0
        ");
        $stmt->execute([$prestamo['id']]);
        $moraPendiente = floatval($stmt->fetch()['total_mora'] ?? 0);
        
        $montoRestante = $montoPago;
        
        // 1. Primero pagar mora
        if ($moraPendiente > 0) {
            $pagoMora = min($montoRestante, $moraPendiente);
            $distribucion['mora'] = $pagoMora;
            $montoRestante -= $pagoMora;
            
            // Marcar mora como pagada
            $this->pagarMora($prestamo['id'], $pagoMora);
        }
        
        // 2. Luego pagar interés
        if ($montoRestante > 0) {
            // Calcular interés de la cuota
            $interesEstimado = $prestamo['monto_cuota'] - ($prestamo['monto'] / $prestamo['numero_cuotas']);
            $pagoInteres = min($montoRestante, $interesEstimado);
            $distribucion['interes'] = $pagoInteres;
            $montoRestante -= $pagoInteres;
        }
        
        // 3. Finalmente pagar capital
        if ($montoRestante > 0) {
            $distribucion['capital'] = $montoRestante;
        }
        
        return $distribucion;
    }
    
    /**
     * Marcar mora como pagada
     */
    private function pagarMora($prestamoId, $montoPagado) {
        $stmt = $this->db->prepare("
            UPDATE mora 
            SET pagado = 1, fecha_pago = CURRENT_DATE 
            WHERE prestamo_id = ? AND pagado = 0 
            ORDER BY fecha_vencimiento ASC 
            LIMIT 1
        ");
        $stmt->execute([$prestamoId]);
    }
    
    /**
     * Listar todos los pagos
     */
    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.*,
                    pr.monto as monto_prestamo,
                    c.nombre as cliente_nombre,
                    c.documento as cliente_documento
                FROM pagos p
                INNER JOIN prestamos pr ON p.prestamo_id = pr.id
                INNER JOIN clientes c ON pr.cliente_id = c.id
                ORDER BY p.fecha_pago DESC
            ");
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (Exception $e) {
            logMessage("Error listando todos los pagos: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}

// Funciones helper para compatibilidad
function realizar_pago($prestamoId, $monto, $saldoPendiente) {
    $pago = new PagoModular();
    $resultado = $pago->realizar($prestamoId, $monto);
    
    if ($resultado['success']) {
        return $resultado['data']['saldo_pendiente'];
    }
    
    return $saldoPendiente; // Retorna el mismo saldo si hay error
}
?>
