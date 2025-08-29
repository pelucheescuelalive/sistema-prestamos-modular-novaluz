<?php
/**
 * Test HTTP directo para simular la llamada del frontend
 */

$url = 'http://localhost:8882/api_simple.php?action=prestamo_crear';
$data = [
    'cliente_id' => 5,
    'monto' => 10000,
    'tasa' => 10,
    'fecha' => '2025-08-28',
    'tipo' => 'interes',
    'frecuencia' => 'quincenal',
    'plazo' => 1,
    'cuotas' => 1
];

echo "=== TEST HTTP DIRECTO ===\n";
echo "URL: $url\n";
echo "Datos: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
        ],
        'content' => json_encode($data)
    ]
]);

echo "Enviando request...\n";
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: No se pudo conectar al servidor\n";
    $error = error_get_last();
    echo "Detalle del error: " . $error['message'] . "\n";
} else {
    echo "Respuesta recibida:\n";
    echo $response . "\n";
    
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "\nRespuesta decodificada:\n";
        echo json_encode($jsonResponse, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "\nERROR: La respuesta no es JSON válido\n";
    }
}

?>
