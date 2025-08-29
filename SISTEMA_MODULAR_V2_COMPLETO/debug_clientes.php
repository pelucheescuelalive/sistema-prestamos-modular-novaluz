<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando test de clientes...\n";

try {
    require_once 'src/Cliente.php';
    echo "Cliente.php cargado correctamente\n";
    
    $cliente = new Cliente();
    echo "Instancia de Cliente creada\n";
    
    $clientes = $cliente->obtenerTodos();
    echo "obtenerTodos() ejecutado\n";
    
    echo "Número de clientes: " . count($clientes) . "\n";
    echo "Datos:\n";
    print_r($clientes);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
