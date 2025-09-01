<?php
/**
 * VALIDADORES
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

class Validadores {
    
    /**
     * Validar datos de cliente
     */
    public static function validarCliente($datos) {
        $errores = [];
        
        // Nombre requerido
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores[] = 'El nombre no puede exceder 100 caracteres';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['nombre'])) {
            $errores[] = 'El nombre solo puede contener letras y espacios';
        }
        
        // Documento requerido
        if (empty($datos['documento'])) {
            $errores[] = 'El documento es requerido';
        } elseif (!validarDocumento($datos['documento'])) {
            $errores[] = 'El formato del documento no es válido';
        }
        
        // Teléfono opcional pero validado
        if (!empty($datos['telefono']) && !validarTelefono($datos['telefono'])) {
            $errores[] = 'El formato del teléfono no es válido';
        }
        
        // Email opcional pero validado
        if (!empty($datos['email']) && !validarEmail($datos['email'])) {
            $errores[] = 'El formato del email no es válido';
        }
        
        // Dirección opcional pero con límite
        if (!empty($datos['direccion']) && strlen($datos['direccion']) > 200) {
            $errores[] = 'La dirección no puede exceder 200 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Validar datos de préstamo
     */
    public static function validarPrestamo($datos) {
        $errores = [];
        
        // Cliente ID requerido
        if (empty($datos['cliente_id'])) {
            $errores[] = 'El cliente es requerido';
        } elseif (!is_numeric($datos['cliente_id']) || $datos['cliente_id'] <= 0) {
            $errores[] = 'ID de cliente no válido';
        }
        
        // Monto requerido y validado
        if (empty($datos['monto'])) {
            $errores[] = 'El monto es requerido';
        } elseif (!is_numeric($datos['monto'])) {
            $errores[] = 'El monto debe ser un número válido';
        } elseif (floatval($datos['monto']) <= 0) {
            $errores[] = 'El monto debe ser mayor a cero';
        } elseif (floatval($datos['monto']) > 1000000) {
            $errores[] = 'El monto no puede exceder $1,000,000';
        }
        
        // Tasa de interés validada
        if (empty($datos['tasa_interes'])) {
            $errores[] = 'La tasa de interés es requerida';
        } elseif (!is_numeric($datos['tasa_interes'])) {
            $errores[] = 'La tasa de interés debe ser un número válido';
        } elseif (floatval($datos['tasa_interes']) < 0) {
            $errores[] = 'La tasa de interés no puede ser negativa';
        } elseif (floatval($datos['tasa_interes']) > 100) {
            $errores[] = 'La tasa de interés no puede exceder 100%';
        }
        
        // Número de cuotas validado
        if (empty($datos['numero_cuotas'])) {
            $errores[] = 'El número de cuotas es requerido';
        } elseif (!is_numeric($datos['numero_cuotas'])) {
            $errores[] = 'El número de cuotas debe ser un número válido';
        } elseif (intval($datos['numero_cuotas']) <= 0) {
            $errores[] = 'El número de cuotas debe ser mayor a cero';
        } elseif (intval($datos['numero_cuotas']) > 360) {
            $errores[] = 'El número de cuotas no puede exceder 360';
        }
        
        // Frecuencia de pago validada
        $frecuenciasValidas = ['semanal', 'quincenal', 'mensual', 'bimestral', 'trimestral'];
        if (empty($datos['frecuencia_pago'])) {
            $errores[] = 'La frecuencia de pago es requerida';
        } elseif (!in_array(strtolower($datos['frecuencia_pago']), $frecuenciasValidas)) {
            $errores[] = 'Frecuencia de pago no válida. Opciones: ' . implode(', ', $frecuenciasValidas);
        }
        
        // Fecha de inicio validada
        if (!empty($datos['fecha_inicio'])) {
            if (!esFechaValida($datos['fecha_inicio'])) {
                $errores[] = 'La fecha de inicio no es válida';
            } else {
                $fechaInicio = new DateTime($datos['fecha_inicio']);
                $hoy = new DateTime();
                $diferencia = $hoy->diff($fechaInicio)->days;
                
                if ($fechaInicio < $hoy->sub(new DateInterval('P30D'))) {
                    $errores[] = 'La fecha de inicio no puede ser anterior a 30 días';
                }
                
                if ($fechaInicio > $hoy->add(new DateInterval('P365D'))) {
                    $errores[] = 'La fecha de inicio no puede ser posterior a 1 año';
                }
            }
        }
        
        // Descripción opcional pero con límite
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $errores[] = 'La descripción no puede exceder 500 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Validar datos de pago
     */
    public static function validarPago($datos) {
        $errores = [];
        
        // Préstamo ID requerido
        if (empty($datos['prestamo_id'])) {
            $errores[] = 'El ID del préstamo es requerido';
        } elseif (!is_numeric($datos['prestamo_id']) || $datos['prestamo_id'] <= 0) {
            $errores[] = 'ID de préstamo no válido';
        }
        
        // Monto requerido y validado
        if (empty($datos['monto'])) {
            $errores[] = 'El monto del pago es requerido';
        } elseif (!is_numeric($datos['monto'])) {
            $errores[] = 'El monto debe ser un número válido';
        } elseif (floatval($datos['monto']) <= 0) {
            $errores[] = 'El monto debe ser mayor a cero';
        } elseif (floatval($datos['monto']) > 100000) {
            $errores[] = 'El monto del pago no puede exceder $100,000';
        }
        
        // Fecha de pago validada
        if (!empty($datos['fecha'])) {
            if (!esFechaValida($datos['fecha'])) {
                $errores[] = 'La fecha del pago no es válida';
            } else {
                $fechaPago = new DateTime($datos['fecha']);
                $hoy = new DateTime();
                
                if ($fechaPago > $hoy) {
                    $errores[] = 'La fecha del pago no puede ser futura';
                }
                
                if ($fechaPago < $hoy->sub(new DateInterval('P1Y'))) {
                    $errores[] = 'La fecha del pago no puede ser anterior a 1 año';
                }
            }
        }
        
        // Método de pago validado
        $metodosValidos = ['efectivo', 'transferencia', 'cheque', 'tarjeta', 'deposito'];
        if (!empty($datos['metodo']) && !in_array(strtolower($datos['metodo']), $metodosValidos)) {
            $errores[] = 'Método de pago no válido. Opciones: ' . implode(', ', $metodosValidos);
        }
        
        return $errores;
    }
    
    /**
     * Validar configuración del sistema
     */
    public static function validarConfiguracion($datos) {
        $errores = [];
        
        // Validar porcentaje de mora
        if (isset($datos['porcentaje_mora_diaria'])) {
            $porcentaje = floatval($datos['porcentaje_mora_diaria']);
            if ($porcentaje < 0 || $porcentaje > 10) {
                $errores[] = 'El porcentaje de mora debe estar entre 0% y 10%';
            }
        }
        
        // Validar mora máxima
        if (isset($datos['mora_maxima_por_cuota'])) {
            $moraMaxima = floatval($datos['mora_maxima_por_cuota']);
            if ($moraMaxima < 0 || $moraMaxima > 10000) {
                $errores[] = 'La mora máxima debe estar entre $0 y $10,000';
            }
        }
        
        // Validar días de gracia
        if (isset($datos['dias_gracia_mora'])) {
            $diasGracia = intval($datos['dias_gracia_mora']);
            if ($diasGracia < 0 || $diasGracia > 30) {
                $errores[] = 'Los días de gracia deben estar entre 0 y 30';
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar campos de búsqueda
     */
    public static function validarBusqueda($termino) {
        $errores = [];
        
        if (empty($termino)) {
            $errores[] = 'El término de búsqueda es requerido';
        } elseif (strlen($termino) < 2) {
            $errores[] = 'El término de búsqueda debe tener al menos 2 caracteres';
        } elseif (strlen($termino) > 100) {
            $errores[] = 'El término de búsqueda no puede exceder 100 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Validar rangos de fecha
     */
    public static function validarRangoFechas($fechaDesde, $fechaHasta) {
        $errores = [];
        
        if (!empty($fechaDesde) && !esFechaValida($fechaDesde)) {
            $errores[] = 'La fecha desde no es válida';
        }
        
        if (!empty($fechaHasta) && !esFechaValida($fechaHasta)) {
            $errores[] = 'La fecha hasta no es válida';
        }
        
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $desde = new DateTime($fechaDesde);
            $hasta = new DateTime($fechaHasta);
            
            if ($desde > $hasta) {
                $errores[] = 'La fecha desde no puede ser posterior a la fecha hasta';
            }
            
            $diferencia = $desde->diff($hasta)->days;
            if ($diferencia > 365) {
                $errores[] = 'El rango de fechas no puede exceder 1 año';
            }
        }
        
        return $errores;
    }
    
    /**
     * Sanitizar y limpiar datos de entrada
     */
    public static function sanitizarDatos($datos) {
        $datosSanitizados = [];
        
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $datosSanitizados[$clave] = sanitizarInput($valor);
            } elseif (is_numeric($valor)) {
                $datosSanitizados[$clave] = $valor;
            } elseif (is_array($valor)) {
                $datosSanitizados[$clave] = self::sanitizarDatos($valor);
            } else {
                $datosSanitizados[$clave] = $valor;
            }
        }
        
        return $datosSanitizados;
    }
    
    /**
     * Validar ID numérico
     */
    public static function validarId($id, $nombre = 'ID') {
        if (empty($id)) {
            return ["El $nombre es requerido"];
        }
        
        if (!is_numeric($id) || intval($id) <= 0) {
            return ["El $nombre debe ser un número válido mayor a cero"];
        }
        
        return [];
    }
    
    /**
     * Validar que un valor esté en una lista de opciones
     */
    public static function validarOpcion($valor, $opciones, $nombre = 'opción') {
        if (empty($valor)) {
            return ["La $nombre es requerida"];
        }
        
        if (!in_array($valor, $opciones)) {
            return ["$nombre no válida. Opciones disponibles: " . implode(', ', $opciones)];
        }
        
        return [];
    }
}
?>
