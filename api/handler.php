<?php
/**
 * API HANDLER
 * Sistema de Préstamos Modular - Nova Luz Pro
 * Maneja todas las peticiones AJAX del frontend
 */

// Configurar headers para API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir configuración y módulos
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../modules/ClienteModular.php';
require_once '../modules/PrestamoModular.php';
require_once '../modules/PagoModular.php';
require_once '../modules/MoraModular.php';

// Función para enviar respuesta JSON
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Obtener datos de la petición
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

try {
    // Inicializar módulos
    $clienteModular = new ClienteModular();
    $prestamoModular = new PrestamoModular();
    $pagoModular = new PagoModular();
    $moraModular = new MoraModular();
    
    switch ($action) {
        // =============== CLIENTES ===============
        case 'cliente_crear':
            $resultado = $clienteModular->crear(
                $input['nombre'],
                $input['documento'],
                $input['telefono'] ?? '',
                $input['direccion'] ?? '',
                $input['email'] ?? ''
            );
            sendResponse($resultado);
            break;
            
        case 'cliente_listar':
            $resultado = $clienteModular->listar();
            sendResponse($resultado);
            break;
            
        case 'cliente_obtener':
            $resultado = $clienteModular->obtener($input['id']);
            sendResponse($resultado);
            break;
            
        case 'cliente_actualizar':
            $resultado = $clienteModular->actualizar(
                $input['id'],
                $input['nombre'],
                $input['documento'],
                $input['telefono'] ?? '',
                $input['direccion'] ?? '',
                $input['email'] ?? ''
            );
            sendResponse($resultado);
            break;
            
        case 'cliente_eliminar':
            $resultado = $clienteModular->eliminar($input['id']);
            sendResponse($resultado);
            break;
            
        case 'cliente_buscar':
            $resultado = $clienteModular->buscar($input['termino']);
            sendResponse($resultado);
            break;
            
        // =============== PRÉSTAMOS ===============
        case 'prestamo_crear':
            $resultado = $prestamoModular->crear(
                $input['cliente_id'],
                $input['monto'],
                $input['tasa_interes'],
                $input['numero_cuotas'],
                $input['frecuencia_pago'],
                $input['fecha_inicio'] ?? null,
                $input['descripcion'] ?? ''
            );
            sendResponse($resultado);
            break;
            
        case 'prestamo_listar':
            $filtros = [
                'estado' => $input['estado'] ?? null,
                'cliente_id' => $input['cliente_id'] ?? null,
                'fecha_desde' => $input['fecha_desde'] ?? null,
                'fecha_hasta' => $input['fecha_hasta'] ?? null
            ];
            $resultado = $prestamoModular->listar($filtros);
            sendResponse($resultado);
            break;
            
        case 'prestamo_obtener':
            $resultado = $prestamoModular->obtener($input['id']);
            sendResponse($resultado);
            break;
            
        case 'prestamo_actualizar':
            $resultado = $prestamoModular->actualizar(
                $input['id'],
                $input['monto'],
                $input['tasa_interes'],
                $input['numero_cuotas'],
                $input['frecuencia_pago'],
                $input['descripcion'] ?? ''
            );
            sendResponse($resultado);
            break;
            
        case 'prestamo_cancelar':
            $resultado = $prestamoModular->cancelar($input['id'], $input['motivo'] ?? '');
            sendResponse($resultado);
            break;
            
        case 'prestamo_estadisticas':
            $resultado = $prestamoModular->obtenerEstadisticas();
            sendResponse($resultado);
            break;
            
        // =============== PAGOS ===============
        case 'pago_realizar':
            $resultado = $pagoModular->realizar(
                $input['prestamo_id'],
                $input['monto'],
                $input['fecha'] ?? null,
                $input['metodo'] ?? 'efectivo'
            );
            sendResponse($resultado);
            break;
            
        case 'pago_listar':
            $resultado = $pagoModular->listar();
            sendResponse($resultado);
            break;
            
        // =============== MORA ===============
        case 'mora_calcular':
            $resultado = $moraModular->calcularMoraVencida();
            sendResponse($resultado);
            break;
            
        case 'mora_listar_pendiente':
            $resultado = $moraModular->listarPendiente();
            sendResponse($resultado);
            break;
            
        case 'mora_por_prestamo':
            $resultado = $moraModular->listarPorPrestamo($input['prestamo_id']);
            sendResponse($resultado);
            break;
            
        case 'mora_resumen':
            $resultado = $moraModular->obtenerResumen();
            sendResponse($resultado);
            break;
            
        case 'mora_condonar':
            $resultado = $moraModular->condonar($input['mora_id'], $input['motivo'] ?? '');
            sendResponse($resultado);
            break;
            
        // =============== DASHBOARD ===============
        case 'dashboard_estadisticas':
            // Combinar estadísticas de todos los módulos
            $prestamos = $prestamoModular->obtenerEstadisticas();
            $mora = $moraModular->obtenerResumen();
            
            $estadisticas = [
                'success' => true,
                'data' => [
                    'prestamos' => $prestamos['success'] ? $prestamos['data'] : [],
                    'mora' => $mora['success'] ? $mora['data'] : []
                ]
            ];
            sendResponse($estadisticas);
            break;
            
        // =============== CONFIGURACIÓN ===============
        case 'config_obtener':
            $db = getDB();
            $stmt = $db->query("SELECT clave, valor FROM configuracion");
            $config = [];
            while ($row = $stmt->fetch()) {
                $config[$row['clave']] = $row['valor'];
            }
            sendResponse(['success' => true, 'data' => $config]);
            break;
            
        case 'config_actualizar':
            $db = getDB();
            foreach ($input['configuracion'] as $clave => $valor) {
                $stmt = $db->prepare("
                    INSERT OR REPLACE INTO configuracion (clave, valor) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$clave, $valor]);
            }
            sendResponse(['success' => true, 'message' => 'Configuración actualizada']);
            break;
            
        default:
            sendResponse([
                'success' => false,
                'message' => 'Acción no reconocida: ' . $action
            ], 400);
            break;
    }
    
} catch (Exception $e) {
    logMessage("Error en API Handler: " . $e->getMessage(), 'ERROR');
    sendResponse([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ], 500);
}
?>
