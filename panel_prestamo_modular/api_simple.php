<?php
/**
 * API Simplificada para PHP Development Server
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'src/Cliente.php';
require_once 'src/Prestamo.php';
require_once 'src/Pago.php';
require_once 'src/Mora.php';
require_once 'src/Factura.php';

function sendResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$data = ($method === 'POST' || $method === 'PUT') ? json_decode(file_get_contents('php://input'), true) : null;

try {
    switch ($action) {
        // Clientes
        case 'cliente_crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $cliente = new Cliente();
            $resultado = $cliente->crear($data['nombre'], $data['telefono'], $data['direccion'], $data['cedula']);
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Cliente creado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'cliente_listar':
            $cliente = new Cliente();
            $clientes = $cliente->obtenerTodos();
            sendResponse(true, $clientes);
            break;
            
        case 'cliente_actualizar':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $cliente = new Cliente();
            $resultado = $cliente->actualizar($data['id'], $data['nombre'], $data['telefono'], $data['direccion'], $data['cedula']);
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Cliente actualizado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'cliente_eliminar':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $cliente = new Cliente();
            $resultado = $cliente->eliminar($data['id']);
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Cliente eliminado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'cliente_buscar':
            $termino = $_GET['q'] ?? '';
            $cliente = new Cliente();
            $clientes = $cliente->buscar($termino);
            sendResponse(true, $clientes);
            break;
            
        // Préstamos
        case 'prestamo_listar':
            $prestamo = new Prestamo();
            $prestamos = $prestamo->obtenerTodos();
            
            // Calcular monto actual para cada préstamo
            if ($prestamos) {
                $pago = new Pago();
                foreach ($prestamos as &$prestamo_item) {
                    $pagos = $pago->obtenerPorPrestamo($prestamo_item['id']);
                    $totalPagado = 0;
                    
                    if ($pagos) {
                        foreach ($pagos as $pago_item) {
                            $totalPagado += floatval($pago_item['monto']);
                        }
                    }
                    
                    $prestamo_item['monto_inicial'] = $prestamo_item['monto'];
                    $prestamo_item['total_pagado'] = $totalPagado;
                    $prestamo_item['monto_actual'] = max(0, floatval($prestamo_item['monto']) - $totalPagado);
                    $prestamo_item['progreso'] = floatval($prestamo_item['monto']) > 0 ? 
                        ($totalPagado / floatval($prestamo_item['monto']) * 100) : 0;
                    
                    // Calcular información adicional para el formato de tabla
                    $prestamo_item['monto_cuota'] = floatval($prestamo_item['monto']) / intval($prestamo_item['cuotas'] ?: 1);
                    
                    // Calcular cuotas vencidas (simulado por ahora)
                    $fechaCreacion = new DateTime($prestamo_item['fecha']);
                    $fechaActual = new DateTime();
                    $diasTranscurridos = $fechaActual->diff($fechaCreacion)->days;
                    
                    // Calcular según frecuencia
                    $diasPorCuota = 15; // Por defecto quincenal
                    switch($prestamo_item['frecuencia']) {
                        case 'semanal': $diasPorCuota = 7; break;
                        case 'quincenal': $diasPorCuota = 15; break;
                        case '15y30': $diasPorCuota = 15; break;
                        case 'mensual': $diasPorCuota = 30; break;
                    }
                    
                    $cuotasEsperadas = floor($diasTranscurridos / $diasPorCuota);
                    $cuotasPagadas = floor($totalPagado / $prestamo_item['monto_cuota']);
                    $prestamo_item['cuotas_vencidas'] = max(0, $cuotasEsperadas - $cuotasPagadas);
                    
                    // Calcular próxima fecha de pago
                    $proximaFecha = clone $fechaCreacion;
                    $proximaFecha->add(new DateInterval('P' . (($cuotasPagadas + 1) * $diasPorCuota) . 'D'));
                    $prestamo_item['proxima_fecha_pago'] = $proximaFecha->format('Y-m-d');
                }
            }
            
            sendResponse(true, $prestamos);
            break;
            
        case 'prestamo_crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            
            if (!$data) {
                sendResponse(false, null, 'No se recibieron datos válidos', 400);
            }
            
            // Log para debug
            error_log('Datos recibidos en prestamo_crear: ' . json_encode($data));
            
            // Validar campos requeridos básicos
            $requiredFields = ['cliente_id', 'monto', 'tasa', 'fecha'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    sendResponse(false, null, "Campo requerido faltante: $field", 400);
                }
                if ($field !== 'fecha' && ($data[$field] === '' || $data[$field] === null)) {
                    sendResponse(false, null, "Campo requerido vacío: $field", 400);
                }
            }
            
            // Para préstamos de cuota, validar que plazo esté presente
            $tipo = $data['tipo'] ?? 'interes';
            if ($tipo === 'cuota' && (!isset($data['plazo']) || $data['plazo'] <= 0)) {
                sendResponse(false, null, "Para préstamos de cuota, el plazo es requerido", 400);
            }
            
            // Para préstamos de interés, el plazo puede ser 0 o no estar presente
            $plazo = isset($data['plazo']) ? intval($data['plazo']) : 0;
            if ($tipo === 'interes' && $plazo <= 0) {
                $plazo = 1; // Valor por defecto para préstamos de interés
            }
            
            $prestamo = new Prestamo();
            $resultado = $prestamo->crear(
                $data['cliente_id'],
                $data['monto'],
                $data['tasa'],
                $plazo,
                $data['fecha'],
                $data['tipo'] ?? 'interes',
                $data['frecuencia'] ?? 'quincenal',
                $data['cuotas'] ?? 1
            );
            
            // Log del resultado
            error_log('Resultado de prestamo->crear: ' . json_encode($resultado));
            
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Préstamo creado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'prestamo_calcular':
            $monto = floatval($_GET['monto'] ?? 0);
            $tasa = floatval($_GET['tasa'] ?? 0);
            $cuotas = intval($_GET['cuotas'] ?? 1);
            $tipo = $_GET['tipo'] ?? 'interes';
            
            if ($monto <= 0 || $tasa <= 0) {
                sendResponse(false, null, 'Monto y tasa son requeridos');
                break;
            }
            
            $prestamo = new Prestamo();
            $calculo = $prestamo->calcular($monto, $tasa, $cuotas, $tipo);
            sendResponse(true, $calculo);
            break;
            
        case 'prestamo_eliminar':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $prestamo = new Prestamo();
            $resultado = $prestamo->eliminar($data['id']);
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Préstamo eliminado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'prestamo_actualizar':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $prestamo = new Prestamo();
            $resultado = $prestamo->actualizar(
                $data['id'],
                $data['cliente_id'],
                $data['monto'],
                $data['tasa'],
                $data['plazo'],
                $data['fecha'],
                $data['tipo'],
                $data['frecuencia'],
                $data['cuotas']
            );
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Préstamo actualizado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        // Pagos
        case 'pago_listar':
            $pago = new Pago();
            // Si se proporciona prestamo_id, filtrar por préstamo específico
            if (isset($_GET['prestamo_id']) && !empty($_GET['prestamo_id'])) {
                $pagos = $pago->obtenerPorPrestamo($_GET['prestamo_id']);
            } else {
                $pagos = $pago->obtenerTodos();
            }
            sendResponse(true, $pagos);
            break;
            
        case 'pago_crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
            }
            $pago = new Pago();
            $resultado = $pago->registrar(
                $data['prestamo_id'],
                $data['monto'],
                $data['fecha'],
                $data['tipo'] ?? 'abono',
                $data['observaciones'] ?? '',
                $data['desglose'] ?? []
            );
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Pago registrado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        // Dashboard
        case 'dashboard_stats':
            $cliente = new Cliente();
            $prestamo = new Prestamo();
            $pago = new Pago();
            $mora = new Mora();
            
            $stats_cliente = $cliente->obtenerEstadisticas();
            $stats_prestamo = $prestamo->obtenerEstadisticas();
            $stats_pago = $pago->obtenerEstadisticas();
            $stats_mora = $mora->obtenerEstadisticas();
            
            $stats = array_merge($stats_cliente, $stats_prestamo, $stats_pago, $stats_mora);
            sendResponse(true, $stats);
            break;
            
        default:
            sendResponse(false, null, "Acción no encontrada: $action", 404);
    }
    
} catch (Exception $e) {
    sendResponse(false, null, 'Error interno: ' . $e->getMessage(), 500);
}
?>
