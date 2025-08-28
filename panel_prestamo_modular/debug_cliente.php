<?php
// Test directo de crear cliente
require_once 'src/Cliente.php';

header('Content-Type: application/json');

try {
    $cliente = new Cliente();
    
    // Verificar si ya existe
    $existentes = $cliente->obtenerTodos();
    echo "Clientes existentes: " . json_encode($existentes) . "\n";
    
    $resultado = $cliente->crear('peluche', '565464', '', '67897689698');
    echo "Resultado creación: " . json_encode($resultado) . "\n";
    
    // Verificar después
    $existentes = $cliente->obtenerTodos();
    echo "Clientes después: " . json_encode($existentes) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
