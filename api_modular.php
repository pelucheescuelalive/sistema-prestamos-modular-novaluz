<?php
/**
 * API Simple para Sistema de Préstamos - Solo archivos JSON
 * Completamente modular y separado
 */

// Headers CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para enviar respuesta JSON
function enviarRespuesta($exito, $datos = null, $mensaje = '', $codigo = 200) {
    http_response_code($codigo);
    echo json_encode([
        'exito' => $exito,
        'datos' => $datos,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// Función para cargar datos de archivo JSON
function cargarDatos($archivo) {
    if (!file_exists($archivo)) {
        file_put_contents($archivo, '[]');
        return [];
    }
    
    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }
    
    return $datos ?: [];
}

// Función para guardar datos en archivo JSON
function guardarDatos($archivo, $datos) {
    $json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($archivo, $json) !== false;
}

// Obtener parámetros
$accion = $_GET['action'] ?? $_POST['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

// Obtener datos JSON del cuerpo de la petición
$datosEntrada = null;
if ($metodo === 'POST' || $metodo === 'PUT') {
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $datosEntrada = json_decode($input, true);
    }
}

// Rutas de archivos
$rutaClientes = __DIR__ . '/datos_clientes.json';
$rutaPrestamos = __DIR__ . '/datos_prestamos.json';
$rutaPagos = __DIR__ . '/datos_pagos.json';

try {
    switch ($accion) {
        
        // ==================== MÓDULO CLIENTES ====================
        case 'clientes_listar':
        case 'cliente_listar':
            $clientes = cargarDatos($rutaClientes);
            enviarRespuesta(true, $clientes, 'Clientes cargados correctamente');
            break;
            
        case 'cliente_crear':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $nombre = $datosEntrada['nombre'] ?? '';
            $cedula = $datosEntrada['cedula'] ?? $datosEntrada['documento'] ?? '';
            $telefono = $datosEntrada['telefono'] ?? '';
            $email = $datosEntrada['email'] ?? '';
            $direccion = $datosEntrada['direccion'] ?? '';
            $foto_perfil = $datosEntrada['foto_perfil'] ?? '';
            $calificacion = $datosEntrada['calificacion'] ?? 0;
            
            // Validar datos requeridos
            if (empty($nombre) || empty($cedula) || empty($telefono)) {
                enviarRespuesta(false, null, 'Nombre, cédula y teléfono son requeridos');
            }
            
            $clientes = cargarDatos($rutaClientes);
            
            // Verificar si ya existe la cédula
            foreach ($clientes as $cliente) {
                if ($cliente['cedula'] === $cedula) {
                    enviarRespuesta(false, null, 'Ya existe un cliente con esta cédula');
                }
            }
            
            // Generar nuevo ID
            $nuevoId = 'CLI' . str_pad(count($clientes) + 1, 3, '0', STR_PAD_LEFT);
            
            // Crear nuevo cliente
            $nuevoCliente = [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'cedula' => $cedula,
                'telefono' => $telefono,
                'email' => $email,
                'direccion' => $direccion,
                'foto_perfil' => $foto_perfil,
                'calificacion' => (int)$calificacion,
                'fecha_registro' => date('Y-m-d H:i:s')
            ];
            
            $clientes[] = $nuevoCliente;
            
            if (guardarDatos($rutaClientes, $clientes)) {
                enviarRespuesta(true, $nuevoCliente, 'Cliente creado correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al guardar el cliente');
            }
            break;
            
        case 'cliente_editar':
        case 'cliente_actualizar':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $id = $datosEntrada['id'] ?? '';
            if (empty($id)) {
                enviarRespuesta(false, null, 'ID del cliente es requerido');
            }
            
            $clientes = cargarDatos($rutaClientes);
            $clienteEncontrado = false;
            
            for ($i = 0; $i < count($clientes); $i++) {
                if ($clientes[$i]['id'] === $id) {
                    // Actualizar campos proporcionados
                    if (isset($datosEntrada['nombre'])) $clientes[$i]['nombre'] = $datosEntrada['nombre'];
                    if (isset($datosEntrada['cedula'])) $clientes[$i]['cedula'] = $datosEntrada['cedula'];
                    if (isset($datosEntrada['telefono'])) $clientes[$i]['telefono'] = $datosEntrada['telefono'];
                    if (isset($datosEntrada['email'])) $clientes[$i]['email'] = $datosEntrada['email'];
                    if (isset($datosEntrada['direccion'])) $clientes[$i]['direccion'] = $datosEntrada['direccion'];
                    if (isset($datosEntrada['foto_perfil'])) $clientes[$i]['foto_perfil'] = $datosEntrada['foto_perfil'];
                    if (isset($datosEntrada['calificacion'])) $clientes[$i]['calificacion'] = (int)$datosEntrada['calificacion'];
                    
                    $clientes[$i]['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                enviarRespuesta(false, null, 'Cliente no encontrado');
            }
            
            if (guardarDatos($rutaClientes, $clientes)) {
                enviarRespuesta(true, $clientes[$i], 'Cliente actualizado correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al actualizar el cliente');
            }
            break;
            
        case 'actualizar_foto':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $id = $datosEntrada['id'] ?? '';
            $foto = $datosEntrada['foto_perfil'] ?? '';
            
            if (empty($id)) {
                enviarRespuesta(false, null, 'ID del cliente es requerido');
            }
            
            $clientes = cargarDatos($rutaClientes);
            $clienteEncontrado = false;
            
            for ($i = 0; $i < count($clientes); $i++) {
                if ($clientes[$i]['id'] === $id) {
                    $clientes[$i]['foto_perfil'] = $foto;
                    $clientes[$i]['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                enviarRespuesta(false, null, 'Cliente no encontrado');
            }
            
            if (guardarDatos($rutaClientes, $clientes)) {
                enviarRespuesta(true, $clientes[$i], 'Foto actualizada correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al actualizar la foto');
            }
            break;
            
        case 'actualizar_calificacion':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $id = $datosEntrada['id'] ?? '';
            $calificacion = $datosEntrada['calificacion'] ?? 0;
            
            if (empty($id)) {
                enviarRespuesta(false, null, 'ID del cliente es requerido');
            }
            
            $clientes = cargarDatos($rutaClientes);
            $clienteEncontrado = false;
            
            for ($i = 0; $i < count($clientes); $i++) {
                if ($clientes[$i]['id'] === $id) {
                    $clientes[$i]['calificacion'] = (int)$calificacion;
                    $clientes[$i]['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                enviarRespuesta(false, null, 'Cliente no encontrado');
            }
            
            if (guardarDatos($rutaClientes, $clientes)) {
                enviarRespuesta(true, $clientes[$i], 'Calificación actualizada correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al actualizar la calificación');
            }
            break;
            
        // ==================== MÓDULO PRÉSTAMOS ====================
        case 'prestamos_listar':
        case 'prestamo_listar':
            $prestamos = cargarDatos($rutaPrestamos);
            enviarRespuesta(true, $prestamos, 'Préstamos cargados correctamente');
            break;
            
        case 'prestamo_crear':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $clienteId = $datosEntrada['cliente_id'] ?? '';
            $monto = $datosEntrada['monto'] ?? 0;
            $tasa = $datosEntrada['tasa'] ?? 0;
            $plazo = $datosEntrada['plazo'] ?? 0;
            $tipo = $datosEntrada['tipo'] ?? 'mensual';
            
            if (empty($clienteId) || $monto <= 0) {
                enviarRespuesta(false, null, 'Cliente ID y monto son requeridos');
            }
            
            $prestamos = cargarDatos($rutaPrestamos);
            
            // Generar nuevo ID
            $nuevoId = 'PREST' . str_pad(count($prestamos) + 1, 3, '0', STR_PAD_LEFT);
            
            // Crear nuevo préstamo
            $nuevoPrestamo = [
                'id' => $nuevoId,
                'cliente_id' => $clienteId,
                'monto' => (float)$monto,
                'tasa' => (float)$tasa,
                'plazo' => (int)$plazo,
                'tipo' => $tipo,
                'estado' => 'activo',
                'fecha_inicio' => date('Y-m-d'),
                'fecha_registro' => date('Y-m-d H:i:s')
            ];
            
            $prestamos[] = $nuevoPrestamo;
            
            if (guardarDatos($rutaPrestamos, $prestamos)) {
                enviarRespuesta(true, $nuevoPrestamo, 'Préstamo creado correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al guardar el préstamo');
            }
            break;
            
        // ==================== MÓDULO PAGOS ====================
        case 'pagos_listar':
        case 'pago_listar':
            $pagos = cargarDatos($rutaPagos);
            enviarRespuesta(true, $pagos, 'Pagos cargados correctamente');
            break;
            
        case 'pago_crear':
            if ($metodo !== 'POST') {
                enviarRespuesta(false, null, 'Método no permitido', 405);
            }
            
            $prestamoId = $datosEntrada['prestamo_id'] ?? '';
            $monto = $datosEntrada['monto'] ?? 0;
            $fecha = $datosEntrada['fecha'] ?? date('Y-m-d');
            
            if (empty($prestamoId) || $monto <= 0) {
                enviarRespuesta(false, null, 'Préstamo ID y monto son requeridos');
            }
            
            $pagos = cargarDatos($rutaPagos);
            
            // Generar nuevo ID
            $nuevoId = 'PAGO' . str_pad(count($pagos) + 1, 3, '0', STR_PAD_LEFT);
            
            // Crear nuevo pago
            $nuevoPago = [
                'id' => $nuevoId,
                'prestamo_id' => $prestamoId,
                'monto' => (float)$monto,
                'fecha' => $fecha,
                'fecha_registro' => date('Y-m-d H:i:s')
            ];
            
            $pagos[] = $nuevoPago;
            
            if (guardarDatos($rutaPagos, $pagos)) {
                enviarRespuesta(true, $nuevoPago, 'Pago registrado correctamente');
            } else {
                enviarRespuesta(false, null, 'Error al guardar el pago');
            }
            break;
            
        // ==================== ACCIONES GENERALES ====================
        default:
            enviarRespuesta(false, null, 'Acción no reconocida: ' . $accion, 400);
            break;
    }
    
} catch (Exception $e) {
    enviarRespuesta(false, null, 'Error interno: ' . $e->getMessage(), 500);
}
?>
