<?php
/**
 * MÓDULO DE MORA - CÁLCULO DE PENALIZACIONES
 */

class Mora {
    private $tasaMora;
    private $diasGracia;
    
    public function __construct($tasaMora = 5, $diasGracia = 3) {
        $this->tasaMora = floatval($tasaMora);
        $this->diasGracia = intval($diasGracia);
    }
    
    /**
     * Calcular mora basándose en días de atraso
     */
    public function calcular($prestamo, $fechaPago = null) {
        $fechaPago = $fechaPago ?: date('Y-m-d');
        
        // Calcular días transcurridos desde el inicio del préstamo
        $diasTranscurridos = $this->calcularDiasTranscurridos($prestamo->getFechaInicio(), $fechaPago);
        
        // Si no hay atraso, no hay mora
        if ($diasTranscurridos <= $this->diasGracia) {
            return [
                'monto' => 0,
                'diasAtraso' => 0,
                'aplicaMora' => false,
                'razon' => "Sin atraso: $diasTranscurridos días <= {$this->diasGracia} días de gracia"
            ];
        }
        
        // Calcular mora sobre los intereses pendientes
        $capitalPendiente = $prestamo->getMonto(); // Aquí deberías obtener el capital real pendiente
        $interesesPendientes = $capitalPendiente * ($prestamo->getTasaInteres() / 100);
        $montoMora = $interesesPendientes * ($this->tasaMora / 100);
        
        $diasAtraso = $diasTranscurridos - $this->diasGracia;
        
        return [
            'monto' => round($montoMora, 2),
            'diasAtraso' => $diasAtraso,
            'aplicaMora' => true,
            'razon' => "Atraso de $diasAtraso días (total: $diasTranscurridos - gracia: {$this->diasGracia})",
            'calculo' => [
                'capitalPendiente' => $capitalPendiente,
                'interesesPendientes' => $interesesPendientes,
                'tasaMora' => $this->tasaMora,
                'formula' => "RD$$interesesPendientes × {$this->tasaMora}% = RD$$montoMora"
            ]
        ];
    }
    
    /**
     * Calcular días transcurridos entre dos fechas
     */
    private function calcularDiasTranscurridos($fechaInicio, $fechaFin) {
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        
        return $fin->diff($inicio)->days;
    }
    
    /**
     * Verificar si aplica mora para una fecha específica
     */
    public function aplicaMora($prestamo, $fechaPago = null) {
        $resultado = $this->calcular($prestamo, $fechaPago);
        return $resultado['aplicaMora'];
    }
    
    /**
     * Obtener configuración actual
     */
    public function getConfiguracion() {
        return [
            'tasaMora' => $this->tasaMora,
            'diasGracia' => $this->diasGracia
        ];
    }
    
    /**
     * Actualizar configuración
     */
    public function setConfiguracion($tasaMora, $diasGracia) {
        $this->tasaMora = floatval($tasaMora);
        $this->diasGracia = intval($diasGracia);
    }
    
    /**
     * Generar JavaScript para configuración desde localStorage
     */
    public static function obtenerConfiguracionJS() {
        return "
        const configuracionMora = {
            tasaMora: Number(localStorage.getItem('mora-tasa')) || 5,
            diasGracia: Number(localStorage.getItem('mora-dias-gracia')) || 3
        };
        ";
    }
    
    /**
     * Validar que los parámetros sean correctos
     */
    public function validar() {
        $errores = [];
        
        if ($this->tasaMora < 0 || $this->tasaMora > 100) {
            $errores[] = 'La tasa de mora debe estar entre 0% y 100%';
        }
        
        if ($this->diasGracia < 0 || $this->diasGracia > 30) {
            $errores[] = 'Los días de gracia deben estar entre 0 y 30';
        }
        
        return $errores;
    }
    
    /**
     * Generar resumen de mora para factura
     */
    public function generarResumen($resultado) {
        if (!$resultado['aplicaMora']) {
            return null;
        }
        
        return [
            'titulo' => 'Mora por Atraso',
            'monto' => $resultado['monto'],
            'diasAtraso' => $resultado['diasAtraso'],
            'descripcion' => $resultado['razon'],
            'detalle' => $resultado['calculo']['formula'] ?? '',
            'color' => '#d32f2f',
            'icono' => '⚠️'
        ];
    }
}
?>
