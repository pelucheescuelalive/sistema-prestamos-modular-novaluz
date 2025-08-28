<?php
/**
 * Script para actualizar la estructura de la base de datos JSON
 * Agrega campos para foto de perfil y calificación de clientes
 */

require_once __DIR__ . '/src/DatabaseConnection.php';

try {
    $db = DatabaseConnection::getInstance();
    
    // Cargar datos existentes de clientes
    $dataPath = __DIR__ . '/data/clientes.json';
    $clientes = [];
    
    if (file_exists($dataPath)) {
        $contenido = file_get_contents($dataPath);
        $clientes = json_decode($contenido, true) ?: [];
    }
    
    $updated = false;
    
    // Actualizar cada cliente para incluir nuevos campos si no existen
    foreach ($clientes as &$cliente) {
        if (!isset($cliente['foto_perfil'])) {
            $cliente['foto_perfil'] = null;
            $updated = true;
        }
        if (!isset($cliente['calificacion'])) {
            $cliente['calificacion'] = 0.0;
            $updated = true;
        }
        if (!isset($cliente['email'])) {
            $cliente['email'] = '';
            $updated = true;
        }
    }
    
    // Guardar cambios si hubo actualizaciones
    if ($updated) {
        file_put_contents($dataPath, json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "✅ Estructura de clientes actualizada con nuevos campos\n";
    } else {
        echo "ℹ️ Los clientes ya tienen todos los campos necesarios\n";
    }
    
    echo "✅ Base de datos JSON actualizada correctamente\n";
    echo "📁 Campos disponibles: nombre, documento, telefono, direccion, foto_perfil, calificacion, email\n";
    
} catch (Exception $e) {
    echo "❌ Error actualizando base de datos: " . $e->getMessage() . "\n";
}
?>
