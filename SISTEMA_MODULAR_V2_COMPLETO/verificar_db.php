<?php
try {
    $db = new PDO('sqlite:prestamos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si la tabla existe
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='clientes'");
    $table = $result->fetch();
    
    if ($table) {
        echo "La tabla 'clientes' existe.\n";
        
        // Contar clientes
        $result = $db->query("SELECT COUNT(*) as count FROM clientes");
        $count = $result->fetch();
        echo "Número de clientes: " . $count['count'] . "\n";
        
        // Mostrar estructura de la tabla
        $result = $db->query("PRAGMA table_info(clientes)");
        $columns = $result->fetchAll();
        echo "Estructura de la tabla:\n";
        foreach ($columns as $column) {
            echo "- " . $column['name'] . " (" . $column['type'] . ")\n";
        }
        
        // Mostrar algunos clientes si existen
        if ($count['count'] > 0) {
            $result = $db->query("SELECT * FROM clientes LIMIT 5");
            $clientes = $result->fetchAll();
            echo "\nPrimeros clientes:\n";
            print_r($clientes);
        }
        
    } else {
        echo "La tabla 'clientes' NO existe.\n";
        echo "Tablas existentes:\n";
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $result->fetchAll();
        foreach ($tables as $table) {
            echo "- " . $table['name'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
