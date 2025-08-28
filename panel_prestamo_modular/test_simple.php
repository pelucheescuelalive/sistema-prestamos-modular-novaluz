<?php
/**
 * Test simple para verificar las clases
 */

echo "=== TEST DE VERIFICACION ===\n";

// Verificar que las clases existen
require_once 'src/Cliente.php';
require_once 'src/Prestamo.php';

echo "1. Clases cargadas correctamente\n";

// Test de cliente
$cliente = new Cliente();
$clientes = $cliente->obtenerTodos();
echo "2. Clientes encontrados: " . count($clientes) . "\n";
if (count($clientes) > 0) {
    echo "   - Primer cliente: " . $clientes[0]['nombre'] . " (ID: " . $clientes[0]['id'] . ")\n";
}

// Test de préstamo
$prestamo = new Prestamo();
echo "3. Clase Prestamo creada\n";

// Intentar crear un préstamo simple
$resultado = $prestamo->crear(
    5, // cliente_id existente
    10000, // monto
    10, // tasa
    1, // plazo
    '2025-08-28', // fecha
    'interes', // tipo
    'quincenal', // frecuencia
    1 // cuotas
);

echo "4. Resultado de crear préstamo:\n";
echo json_encode($resultado, JSON_PRETTY_PRINT) . "\n";

?>
