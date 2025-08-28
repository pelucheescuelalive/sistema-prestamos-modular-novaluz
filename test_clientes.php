<?php
/**
 * Test directo de la función cliente_listar
 */

header('Content-Type: application/json');

try {
    require_once 'src/Cliente.php';
    
    $cliente = new Cliente();
    $clientes = $cliente->obtenerTodos();
    
    $response = [
        'success' => true,
        'data' => $clientes,
        'message' => 'Clientes obtenidos correctamente',
        'count' => count($clientes)
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'data' => null,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>
