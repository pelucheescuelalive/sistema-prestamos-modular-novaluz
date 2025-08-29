<?php
/**
 * MÓDULO DE PRÉSTAMO - GESTIÓN DE PRÉSTAMOS
 */

class Prestamo {
    private $id;
    private $clienteId;
    private $monto;
    private $tipo; // 'interes' (solo interés) o 'fijo' (interés fijo)
    private $tasaInteres;
    private $plazo;
    private $fechaInicio;
    private $fechaVencimiento;
    private $estado; // 'activo', 'pagado', 'vencido'
    private $cuotaMensual;
    private $observaciones;
    
    public function __construct($datos = []) {
        $this->id = $datos['id'] ?? null;
        $this->clienteId = $datos['clienteId'] ?? '';
        $this->monto = floatval($datos['monto'] ?? 0);
        $this->tipo = $datos['tipo'] ?? 'interes';
        $this->tasaInteres = floatval($datos['tasaInteres'] ?? 0);
        $this->plazo = intval($datos['plazo'] ?? 0);
        $this->fechaInicio = $datos['fechaInicio'] ?? date('Y-m-d');
        $this->fechaVencimiento = $datos['fechaVencimiento'] ?? '';
        $this->estado = $datos['estado'] ?? 'activo';
        $this->cuotaMensual = floatval($datos['cuotaMensual'] ?? 0);
        $this->observaciones = $datos['observaciones'] ?? '';
        
        // Calcular cuota si no está definida
        if ($this->cuotaMensual == 0) {
            $this->calcularCuota();
        }
        
        // Calcular fecha de vencimiento si no está definida
        if (empty($this->fechaVencimiento)) {
            $this->calcularFechaVencimiento();
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getClienteId() { return $this->clienteId; }
    public function getMonto() { return $this->monto; }
    public function getTipo() { return $this->tipo; }
    public function getTasaInteres() { return $this->tasaInteres; }
    public function getPlazo() { return $this->plazo; }
    public function getFechaInicio() { return $this->fechaInicio; }
    public function getFechaVencimiento() { return $this->fechaVencimiento; }
    public function getEstado() { return $this->estado; }
    public function getCuotaMensual() { return $this->cuotaMensual; }
    public function getObservaciones() { return $this->observaciones; }
    
    // Setters
    public function setEstado($estado) { $this->estado = $estado; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }
    
    /**
     * Calcular cuota mensual según el tipo de préstamo
     */
    private function calcularCuota() {
        if ($this->tipo === 'interes') {
            // Solo Interés: cuota = capital × tasa
            $this->cuotaMensual = $this->monto * ($this->tasaInteres / 100);
        } else {
            // Interés Fijo: cuota = (capital × tasa) + (capital / plazo)
            $interesMensual = $this->monto * ($this->tasaInteres / 100);
            $capitalMensual = $this->monto / $this->plazo;
            $this->cuotaMensual = $interesMensual + $capitalMensual;
        }
    }
    
    /**
     * Calcular fecha de vencimiento
     */
    private function calcularFechaVencimiento() {
        $fecha = new DateTime($this->fechaInicio);
        $fecha->add(new DateInterval("P{$this->plazo}M")); // Agregar meses
        $this->fechaVencimiento = $fecha->format('Y-m-d');
    }
    
    /**
     * Calcular capital pendiente basado en pagos
     */
    public function calcularCapitalPendiente($pagos = []) {
        $capitalPendiente = $this->monto;
        
        foreach ($pagos as $pago) {
            if ($this->tipo === 'interes') {
                // Solo Interés: aplicar lógica de Pago
                $interesesRequeridos = $capitalPendiente * ($this->tasaInteres / 100);
                
                if ($pago['monto'] > $interesesRequeridos) {
                    $exceso = $pago['monto'] - $interesesRequeridos;
                    $capitalPendiente -= $exceso;
                }
            } else {
                // Interés Fijo: toda la cuota reduce el capital programado
                $capitalMensual = $this->monto / $this->plazo;
                $capitalPendiente -= $capitalMensual;
            }
            
            // No puede ser negativo
            if ($capitalPendiente < 0) {
                $capitalPendiente = 0;
                break;
            }
        }
        
        return round($capitalPendiente, 2);
    }
    
    /**
     * Verificar si el préstamo está vencido
     */
    public function estaVencido() {
        return new DateTime() > new DateTime($this->fechaVencimiento);
    }
    
    /**
     * Calcular días de atraso
     */
    public function calcularDiasAtraso($fechaPago = null) {
        $fechaPago = $fechaPago ?: date('Y-m-d');
        $fechaInicio = new DateTime($this->fechaInicio);
        $fechaActual = new DateTime($fechaPago);
        
        return $fechaActual->diff($fechaInicio)->days;
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'clienteId' => $this->clienteId,
            'monto' => $this->monto,
            'tipo' => $this->tipo,
            'tasaInteres' => $this->tasaInteres,
            'plazo' => $this->plazo,
            'fechaInicio' => $this->fechaInicio,
            'fechaVencimiento' => $this->fechaVencimiento,
            'estado' => $this->estado,
            'cuotaMensual' => $this->cuotaMensual,
            'observaciones' => $this->observaciones
        ];
    }
    
    /**
     * Validar datos del préstamo
     */
    public function validar() {
        $errores = [];
        
        if (empty($this->clienteId)) {
            $errores[] = 'Debe seleccionar un cliente';
        }
        
        if ($this->monto <= 0) {
            $errores[] = 'El monto debe ser mayor a 0';
        }
        
        if ($this->tasaInteres <= 0) {
            $errores[] = 'La tasa de interés debe ser mayor a 0';
        }
        
        if ($this->plazo <= 0) {
            $errores[] = 'El plazo debe ser mayor a 0';
        }
        
        if (!in_array($this->tipo, ['interes', 'fijo'])) {
            $errores[] = 'Tipo de préstamo inválido';
        }
        
        return $errores;
    }
    
    /**
     * Generar ID único
     */
    public static function generarId() {
        return 'prest_' . time() . '_' . rand(1000, 9999);
    }
}
?>
