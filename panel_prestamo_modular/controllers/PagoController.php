<?php
/**
 * CONTROLADOR DE PAGOS - MANEJA LA LÓGICA DE PAGOS USANDO LOS MÓDULOS
 */

require_once 'modules/Cliente.php';
require_once 'modules/Prestamo.php';
require_once 'modules/Pago.php';
require_once 'modules/Mora.php';
require_once 'modules/Factura.php';

class PagoController {
    
    /**
     * Procesar un pago nuevo
     */
    public static function procesarPago($datosPago) {
        try {
            // 1. Validar datos de entrada
            $errores = self::validarDatosPago($datosPago);
            if (!empty($errores)) {
                return ['success' => false, 'errors' => $errores];
            }
            
            // 2. Obtener préstamo y cliente
            $prestamo = new Prestamo($datosPago['prestamo']);
            $cliente = new Cliente($datosPago['cliente']);
            
            // 3. Calcular mora si aplica
            $mora = new Mora(
                $datosPago['configuracion']['tasaMora'] ?? 5,
                $datosPago['configuracion']['diasGracia'] ?? 3
            );
            $resultadoMora = $mora->calcular($prestamo, $datosPago['fechaPago']);
            
            // 4. Aplicar pago usando TU LÓGICA EXACTA
            $capitalPendiente = $prestamo->calcularCapitalPendiente($datosPago['pagosAnteriores'] ?? []);
            $interesesRequeridos = $capitalPendiente * ($prestamo->getTasaInteres() / 100);
            
            $resultadoPago = Pago::aplicarPago(
                $capitalPendiente,
                $interesesRequeridos,
                $resultadoMora['monto'],
                $datosPago['monto']
            );
            
            // 5. Generar factura
            $factura = new Factura();
            $facturaGenerada = $factura->generar(
                $prestamo,
                $cliente->toArray(),
                $datosPago,
                $resultadoPago,
                $resultadoMora
            );
            
            // 6. Preparar respuesta
            return [
                'success' => true,
                'pago' => $resultadoPago,
                'mora' => $resultadoMora,
                'factura' => $facturaGenerada,
                'log' => self::generarLogPago($prestamo, $resultadoPago, $resultadoMora)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
    
    /**
     * Validar datos de entrada
     */
    private static function validarDatosPago($datos) {
        $errores = [];
        
        if (empty($datos['monto']) || $datos['monto'] <= 0) {
            $errores[] = 'El monto del pago debe ser mayor a 0';
        }
        
        if (empty($datos['prestamo'])) {
            $errores[] = 'Datos del préstamo requeridos';
        }
        
        if (empty($datos['cliente'])) {
            $errores[] = 'Datos del cliente requeridos';
        }
        
        if (empty($datos['fechaPago'])) {
            $datos['fechaPago'] = date('Y-m-d');
        }
        
        return $errores;
    }
    
    /**
     * Generar log detallado del pago
     */
    private static function generarLogPago($prestamo, $resultadoPago, $resultadoMora) {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'prestamo' => [
                'id' => $prestamo->getId(),
                'tipo' => $prestamo->getTipo(),
                'monto' => $prestamo->getMonto(),
                'tasa' => $prestamo->getTasaInteres()
            ],
            'calculo' => [
                'capitalInicial' => $resultadoPago['capitalInicial'],
                'interesesRequeridos' => $resultadoPago['interesesRequeridos'],
                'montoPagado' => $resultadoPago['montoPagado'],
                'interesesPagados' => $resultadoPago['interesesPagados'],
                'capitalPagado' => $resultadoPago['capitalPagado'],
                'capitalFinal' => $resultadoPago['capitalFinal']
            ],
            'mora' => [
                'aplicaMora' => $resultadoMora['aplicaMora'],
                'monto' => $resultadoMora['monto'],
                'diasAtraso' => $resultadoMora['diasAtraso'] ?? 0,
                'razon' => $resultadoMora['razon']
            ],
            'validaciones' => [
                'pagoSuficiente' => $resultadoPago['pagoSuficiente'],
                'puedeAbonarCapital' => $resultadoPago['puedeAbonarCapital'],
                'formulaCorrecta' => self::validarFormula($resultadoPago)
            ]
        ];
        
        return $log;
    }
    
    /**
     * Validar que la fórmula se aplicó correctamente
     */
    private static function validarFormula($resultado) {
        $validaciones = [];
        
        // Validar que capital pagado + capital final = capital inicial
        $totalCapital = $resultado['capitalPagado'] + $resultado['capitalFinal'];
        $validaciones['capitalConsistente'] = abs($totalCapital - $resultado['capitalInicial']) < 0.01;
        
        // Validar que si hay mora, no hay capital pagado
        if ($resultado['hayMora']) {
            $validaciones['moraImpideCapital'] = $resultado['capitalPagado'] == 0;
        }
        
        // Validar que intereses pagados <= intereses requeridos
        $validaciones['interesesValidos'] = $resultado['interesesPagados'] <= $resultado['interesesRequeridos'];
        
        return $validaciones;
    }
    
    /**
     * Generar JavaScript para integración con frontend
     */
    public static function generarJavaScript() {
        return "
        class PagoProcessor {
            static async procesarPago(datos) {
                try {
                    const response = await fetch('controllers/PagoController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'procesar_pago',
                            datos: datos
                        })
                    });
                    
                    return await response.json();
                } catch (error) {
                    console.error('Error procesando pago:', error);
                    return { success: false, error: error.message };
                }
            }
            
            static validarDatos(datos) {
                const errores = [];
                
                if (!datos.monto || datos.monto <= 0) {
                    errores.push('El monto debe ser mayor a 0');
                }
                
                if (!datos.prestamo || !datos.prestamo.id) {
                    errores.push('Debe seleccionar un préstamo');
                }
                
                return errores;
            }
        }
        
        // Función global para procesar pagos desde el frontend
        async function procesarPagoModular(datosPago) {
            const errores = PagoProcessor.validarDatos(datosPago);
            if (errores.length > 0) {
                alert('Errores: ' + errores.join(', '));
                return;
            }
            
            const resultado = await PagoProcessor.procesarPago(datosPago);
            
            if (resultado.success) {
                console.log('✅ Pago procesado correctamente:', resultado);
                
                // Mostrar factura
                if (resultado.factura && resultado.factura.canvas) {
                    eval(resultado.factura.canvas);
                    const canvas = generarFacturaCanvas();
                    
                    // Mostrar en modal
                    mostrarFacturaModal(canvas, resultado.factura.html);
                }
                
                return resultado;
            } else {
                console.error('❌ Error procesando pago:', resultado);
                alert('Error: ' + (resultado.error || resultado.errors?.join(', ')));
                return null;
            }
        }
        ";
    }
}

// Si se llama directamente via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input['action'] === 'procesar_pago') {
        echo json_encode(PagoController::procesarPago($input['datos']));
    } else {
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }
    exit;
}
?>
