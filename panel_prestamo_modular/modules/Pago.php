<?php
/**
 * MÓDULO DE PAGO - LÓGICA CORRECTA SEGÚN TU ESPECIFICACIÓN
 * 
 * REGLAS:
 * 1. Pago se aplica PRIMERO a intereses
 * 2. Mora es PENALIZACIÓN ADICIONAL, no parte del pago
 * 3. Solo el EXCESO (después de intereses) va al capital
 * 4. Si hay mora, NO hay abono al capital
 */

class Pago {
    
    /**
     * Aplicar pago siguiendo tu lógica exacta
     * 
     * @param float $capitalPendiente Capital pendiente
     * @param float $interesesPendientes Intereses pendientes  
     * @param float $mora Mora como penalización adicional
     * @param float $montoPago Monto que se está pagando
     * @return array Resultado del pago aplicado
     */
    public static function aplicarPago($capitalPendiente, $interesesPendientes, $mora, $montoPago) {
        $resultado = [
            'capitalInicial' => $capitalPendiente,
            'interesesRequeridos' => $interesesPendientes,
            'moraCalculada' => $mora,
            'montoPagado' => $montoPago,
            
            // Resultados del pago
            'interesesPagados' => 0,
            'capitalPagado' => 0,
            'moraPagada' => $mora,
            
            // Estado final
            'capitalFinal' => $capitalPendiente,
            'interesesPendientesFinal' => $interesesPendientes,
            
            // Validaciones
            'pagoSuficiente' => false,
            'hayMora' => $mora > 0,
            'puedeAbonarCapital' => false
        ];
        
        // PASO 1: EL PAGO SE APLICA PRIMERO A LOS INTERESES
        if ($montoPago >= $interesesPendientes) {
            // Se cubren los intereses completos
            $resultado['interesesPagados'] = $interesesPendientes;
            $resultado['interesesPendientesFinal'] = 0;
            $resultado['pagoSuficiente'] = true;
            
            // Calcular exceso después de cubrir intereses
            $exceso = $montoPago - $interesesPendientes;
            
            // PASO 2: VERIFICAR SI HAY MORA
            if ($mora > 0) {
                // HAY MORA: NO se permite abono al capital
                $resultado['capitalPagado'] = 0;
                $resultado['capitalFinal'] = $capitalPendiente; // Sin cambio
                $resultado['puedeAbonarCapital'] = false;
                
                self::log("🚨 HAY MORA: El exceso de RD$$exceso NO va al capital");
                
            } else {
                // SIN MORA: El exceso se aplica al capital
                $resultado['capitalPagado'] = min($exceso, $capitalPendiente);
                $resultado['capitalFinal'] = $capitalPendiente - $resultado['capitalPagado'];
                $resultado['puedeAbonarCapital'] = true;
                
                self::log("✅ SIN MORA: Exceso de RD$$exceso aplicado al capital");
            }
            
        } else {
            // El pago no cubre ni los intereses completos
            $resultado['interesesPagados'] = $montoPago;
            $resultado['interesesPendientesFinal'] = $interesesPendientes - $montoPago;
            $resultado['capitalPagado'] = 0;
            $resultado['capitalFinal'] = $capitalPendiente; // Sin cambio
            
            self::log("⚠️ PAGO INSUFICIENTE: Solo cubre RD$$montoPago de RD$$interesesPendientes requeridos");
        }
        
        // Validar resultados
        $resultado = self::validarResultados($resultado);
        
        return $resultado;
    }
    
    /**
     * Calcular mora sobre intereses pendientes
     */
    public static function calcularMora($capitalPendiente, $tasaInteres, $tasaMora, $diasAtraso, $diasGracia = 3) {
        if ($diasAtraso <= $diasGracia) {
            return 0; // Sin mora
        }
        
        $interesesPendientes = $capitalPendiente * ($tasaInteres / 100);
        $mora = $interesesPendientes * ($tasaMora / 100);
        
        self::log("📊 MORA CALCULADA: Capital RD$$capitalPendiente × $tasaInteres% = RD$$interesesPendientes × $tasaMora% = RD$$mora");
        
        return round($mora, 2);
    }
    
    /**
     * Validar que los resultados sean consistentes
     */
    private static function validarResultados($resultado) {
        // Validar que el capital final no sea negativo
        if ($resultado['capitalFinal'] < 0) {
            $resultado['capitalFinal'] = 0;
        }
        
        // Validar que los intereses pendientes no sean negativos
        if ($resultado['interesesPendientesFinal'] < 0) {
            $resultado['interesesPendientesFinal'] = 0;
        }
        
        // Validar consistencia: capital pagado + capital final = capital inicial
        $totalCapital = $resultado['capitalPagado'] + $resultado['capitalFinal'];
        if (abs($totalCapital - $resultado['capitalInicial']) > 0.01) {
            self::log("⚠️ INCONSISTENCIA: Capital pagado + final ≠ inicial");
        }
        
        return $resultado;
    }
    
    /**
     * Generar desglose para factura
     */
    public static function generarDesglose($resultado) {
        $desglose = [];
        
        // Siempre mostrar interés
        if ($resultado['interesesPagados'] > 0) {
            $desglose[] = [
                'concepto' => 'Interés',
                'monto' => $resultado['interesesPagados'],
                'color' => '#1565c0'
            ];
        }
        
        // Mostrar capital solo si se pagó
        if ($resultado['capitalPagado'] > 0) {
            $desglose[] = [
                'concepto' => 'Abono a capital',
                'monto' => $resultado['capitalPagado'],
                'color' => '#1565c0'
            ];
        }
        
        // Mostrar mora como penalización separada
        if ($resultado['hayMora'] && $resultado['moraCalculada'] > 0) {
            $desglose[] = [
                'concepto' => 'Mora (penalización)',
                'monto' => $resultado['moraCalculada'],
                'color' => '#d32f2f'
            ];
        }
        
        return $desglose;
    }
    
    /**
     * Logging para debug
     */
    private static function log($mensaje) {
        // En producción esto podría escribir a un archivo de log
        error_log("[PAGO] $mensaje");
    }
}
?>
