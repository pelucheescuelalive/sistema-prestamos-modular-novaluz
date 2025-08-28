<?php
/**
 * Test para crear un préstamo directamente
 */

// Simular datos de un préstamo
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'prestamo_crear';

// Datos de prueba
$testData = [
    'cliente_id' => 5, // ID del cliente "peluche" 
    'monto' => 10000,
    'tasa' => 10,
    'fecha' => '2025-08-28',
    'tipo' => 'interes',
    'frecuencia' => 'quincenal',
    'plazo' => 1,
    'cuotas' => 1
];

// Simular el input JSON
file_put_contents('php://temp', json_encode($testData));

echo "=== TEST DIRECTO DE CREACION DE PRESTAMO ===\n";
echo "Datos a enviar: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

try {
    // Incluir el API
    include 'api_simple.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
