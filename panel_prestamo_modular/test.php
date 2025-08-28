<?php
// Archivo de prueba para debuggear la API
header('Content-Type: application/json');

try {
    echo json_encode(['success' => true, 'message' => 'API funcionando']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
