<?php
require_once 'src/Cliente.php';

$cliente = new Cliente();
$clientes = $cliente->obtenerTodos();

echo "Número de clientes: " . count($clientes) . "\n";
echo "Clientes encontrados:\n";
print_r($clientes);

// Si no hay clientes, vamos a crear uno de prueba
if (empty($clientes)) {
    echo "\nNo hay clientes, creando cliente de prueba...\n";
    $resultado = $cliente->crear('Juan Pérez', '555-1234', 'Calle 123', '12345678');
    print_r($resultado);
    
    // Verificar nuevamente
    $clientes = $cliente->obtenerTodos();
    echo "\nDespués de crear cliente de prueba:\n";
    print_r($clientes);
}
?>
