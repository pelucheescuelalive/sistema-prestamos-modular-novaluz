<?php
// Test directo de la API
require_once 'src/Cliente.php';

header('Content-Type: application/json');

try {
    $cliente = new Cliente();
    $resultado = $cliente->crear('peluche', '565464', '', '67897689698');
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
