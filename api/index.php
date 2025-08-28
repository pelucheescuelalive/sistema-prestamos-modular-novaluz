<?php
/**
 * API Router para Sistema de Préstamos Modular
 * Maneja todas las rutas de la API y las dirige a los módulos correspondientes
 */

// Evitar cualquier salida antes del JSON
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar requests OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir archivos necesarios
require_once '../src/Cliente.php';
require_once '../src/Prestamo.php';
require_once '../src/Pago.php';
require_once '../src/Mora.php';
require_once '../src/Factura.php';

// Función para obtener datos JSON del request
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

// Función para enviar respuesta JSON
function sendResponse($success, $data = null, $message = '', $code = 200) {
    // Limpiar cualquier salida anterior
    ob_clean();
    
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Parse de la URL
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Extraer parámetros de la URL
$modulo = isset($segments[1]) ? $segments[1] : '';
$accion = isset($segments[2]) ? $segments[2] : '';
$id = isset($segments[3]) ? $segments[3] : null;

// Obtener datos del request
$method = $_SERVER['REQUEST_METHOD'];
$data = ($method === 'POST' || $method === 'PUT') ? getJsonInput() : null;

try {
    switch ($modulo) {
        case 'cliente':
            handleClienteRequest($accion, $data, $id, $method);
            break;
            
        case 'prestamo':
            handlePrestamoRequest($accion, $data, $id, $method);
            break;
            
        case 'pago':
            handlePagoRequest($accion, $data, $id, $method);
            break;
            
        case 'mora':
            handleMoraRequest($accion, $data, $id, $method);
            break;
            
        case 'factura':
            handleFacturaRequest($accion, $data, $id, $method);
            break;
            
        case 'dashboard':
            handleDashboardRequest($accion, $data, $id, $method);
            break;
            
        default:
            sendResponse(false, null, "Módulo no encontrado: $modulo", 404);
    }
} catch (Exception $e) {
    sendResponse(false, null, 'Error interno: ' . $e->getMessage(), 500);
}

/**
 * Manejar requests del módulo Cliente
 */
function handleClienteRequest($accion, $data, $id, $method) {
    $cliente = new Cliente();
    
    switch ($accion) {
        case 'crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $cliente->crear($data['nombre'], $data['telefono'], $data['direccion'], $data['cedula']);
            
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Cliente creado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'listar':
            $clientes = $cliente->obtenerTodos();
            sendResponse(true, $clientes);
            break;
            
        case 'obtener':
            if (!$id) {
                sendResponse(false, null, 'ID requerido', 400);
                return;
            }
            
            $clienteData = $cliente->obtenerPorId($id);
            if ($clienteData) {
                sendResponse(true, $clienteData);
            } else {
                sendResponse(false, null, 'Cliente no encontrado', 404);
            }
            break;
            
        case 'actualizar':
            if ($method !== 'PUT') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $cliente->actualizar($id, $data['nombre'], $data['telefono'], $data['direccion'], $data['cedula']);
            if ($resultado['success']) {
                sendResponse(true, null, $resultado['mensaje']);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'eliminar':
            if ($method !== 'DELETE') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $cliente->eliminar($id);
            if ($resultado['success']) {
                sendResponse(true, null, $resultado['mensaje']);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'buscar':
            $termino = $_GET['q'] ?? '';
            $clientes = $cliente->buscar($termino);
            sendResponse(true, $clientes);
            break;
            
        default:
            sendResponse(false, null, "Acción no válida para cliente: $accion", 400);
    }
}

/**
 * Manejar requests del módulo Préstamo
 */
function handlePrestamoRequest($accion, $data, $id, $method) {
    $prestamo = new Prestamo();
    
    switch ($accion) {
        case 'crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $prestamo->crear(
                $data['cliente_id'],
                $data['monto'],
                $data['tipo_interes'],
                $data['fecha'],
                $data['fecha_vencimiento'],
                $data['observaciones'] ?? ''
            );
            
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Préstamo creado correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'listar':
            $prestamos = $prestamo->obtenerTodos();
            sendResponse(true, $prestamos);
            break;
            
        case 'obtener':
            if (!$id) {
                sendResponse(false, null, 'ID requerido', 400);
                return;
            }
            
            $prestamoData = $prestamo->obtenerPorId($id);
            if ($prestamoData) {
                sendResponse(true, $prestamoData);
            } else {
                sendResponse(false, null, 'Préstamo no encontrado', 404);
            }
            break;
            
        case 'por_cliente':
            if (!$id) {
                sendResponse(false, null, 'ID de cliente requerido', 400);
                return;
            }
            
            $prestamos = $prestamo->obtenerPorCliente($id);
            sendResponse(true, $prestamos);
            break;
            
        case 'calcular':
            $calculo = $prestamo->calcular(
                $data['monto'],
                $data['tasa'] ?: 5,
                $data['cuotas'] ?: 12,
                $data['tipo_interes'] ?: 'interes'
            );
            sendResponse(true, $calculo);
            break;
            
        case 'estadisticas':
            $stats = $prestamo->obtenerEstadisticas();
            sendResponse(true, $stats);
            break;
            
        default:
            sendResponse(false, null, "Acción no válida para préstamo: $accion", 400);
    }
}

/**
 * Manejar requests del módulo Pago
 */
function handlePagoRequest($accion, $data, $id, $method) {
    $pago = new Pago();
    
    switch ($accion) {
        case 'crear':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
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
            
        case 'listar':
            $pagos = $pago->obtenerTodos();
            sendResponse(true, $pagos);
            break;
            
        case 'obtener':
            if (!$id) {
                sendResponse(false, null, 'ID requerido', 400);
                return;
            }
            
            $pagoData = $pago->obtenerPorId($id);
            if ($pagoData) {
                sendResponse(true, $pagoData);
            } else {
                sendResponse(false, null, 'Pago no encontrado', 404);
            }
            break;
            
        case 'por_prestamo':
            if (!$id) {
                sendResponse(false, null, 'ID de préstamo requerido', 400);
                return;
            }
            
            $pagos = $pago->obtenerPorPrestamo($id);
            sendResponse(true, $pagos);
            break;
            
        case 'eliminar':
            if ($method !== 'DELETE') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $pago->eliminar($id);
            if ($resultado['success']) {
                sendResponse(true, null, $resultado['mensaje']);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'estadisticas':
            $stats = $pago->obtenerEstadisticas();
            sendResponse(true, $stats);
            break;
            
        default:
            sendResponse(false, null, "Acción no válida para pago: $accion", 400);
    }
}

/**
 * Manejar requests del módulo Mora
 */
function handleMoraRequest($accion, $data, $id, $method) {
    $mora = new Mora();
    
    switch ($accion) {
        case 'calcular':
            if (!$id) {
                sendResponse(false, null, 'ID de préstamo requerido', 400);
                return;
            }
            
            $fecha_corte = $data['fecha_corte'] ?? null;
            $resultado = $mora->calcularMora($id, $fecha_corte);
            
            if ($resultado['success']) {
                sendResponse(true, $resultado);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'registrar':
            if ($method !== 'POST') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $mora->registrarMora(
                $data['prestamo_id'],
                $data['monto_mora'],
                $data['dias_atraso'],
                $data['fecha_vencimiento'],
                $data['tasa_mora'] ?? 5.0,
                $data['observaciones'] ?? ''
            );
            
            if ($resultado['success']) {
                sendResponse(true, $resultado, 'Mora registrada correctamente');
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'prestamos_mora':
            $prestamos = $mora->obtenerPrestamosEnMora();
            sendResponse(true, $prestamos);
            break;
            
        case 'marcar_pagada':
            if ($method !== 'PUT') {
                sendResponse(false, null, 'Método no permitido', 405);
                return;
            }
            
            $resultado = $mora->marcarComoPagada($id);
            if ($resultado['success']) {
                sendResponse(true, null, $resultado['mensaje']);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'listar':
            $moras = $mora->obtenerTodas();
            sendResponse(true, $moras);
            break;
            
        case 'estadisticas':
            $stats = $mora->obtenerEstadisticas();
            sendResponse(true, $stats);
            break;
            
        default:
            sendResponse(false, null, "Acción no válida para mora: $accion", 400);
    }
}

/**
 * Manejar requests del módulo Factura
 */
function handleFacturaRequest($accion, $data, $id, $method) {
    $factura = new Factura();
    
    switch ($accion) {
        case 'generar_prestamo':
            if (!$id) {
                sendResponse(false, null, 'ID de préstamo requerido', 400);
                return;
            }
            
            $resultado = $factura->generarFacturaPrestamo($id);
            if ($resultado['success']) {
                sendResponse(true, $resultado);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'generar_recibo':
            if (!$id) {
                sendResponse(false, null, 'ID de pago requerido', 400);
                return;
            }
            
            $resultado = $factura->generarReciboPago($id);
            if ($resultado['success']) {
                sendResponse(true, $resultado);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        case 'estado_cuenta':
            if (!$id) {
                sendResponse(false, null, 'ID de préstamo requerido', 400);
                return;
            }
            
            $resultado = $factura->generarEstadoCuenta($id);
            if ($resultado['success']) {
                sendResponse(true, $resultado);
            } else {
                sendResponse(false, null, $resultado['error'], 400);
            }
            break;
            
        default:
            sendResponse(false, null, "Acción no válida para factura: $accion", 400);
    }
}

/**
 * Manejar requests del módulo Dashboard
 */
function handleDashboardRequest($accion, $data, $id, $method) {
    switch ($accion) {
        case 'estadisticas':
            // Obtener estadísticas de todos los módulos
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
            sendResponse(false, null, "Acción no válida para dashboard: $accion", 400);
    }
}

?>
