<?php
/**
 * Prueba simple de la API
 */

header('Content-Type: application/json');

// Simular respuesta de cliente_listar
$response = [
    'success' => true,
    'data' => [],
    'message' => 'API funcionando correctamente'
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
