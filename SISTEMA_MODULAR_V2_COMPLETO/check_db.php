<?php
try {
    $pdo = new PDO('sqlite:prestamos.db');
    $result = $pdo->query('PRAGMA table_info(clientes)');
    echo "Estructura actual de la tabla clientes:\n";
    foreach($result as $row) {
        echo "- {$row['name']} ({$row['type']}) - Required: " . ($row['notnull'] ? 'YES' : 'NO') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
