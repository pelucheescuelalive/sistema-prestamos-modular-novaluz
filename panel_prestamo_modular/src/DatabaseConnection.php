<?php
/**
 * Clase para manejo de base de datos JSON (fallback cuando SQLite no está disponible)
 */
class DatabaseConnection {
    private static $instance = null;
    private $dataPath;
    
    private function __construct() {
        $this->dataPath = __DIR__ . '/../data/';
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this; // Retornamos la misma instancia para compatibilidad
    }
    
    // Métodos para simular PDO con archivos JSON
    public function query($sql) {
        // SELECT todos los clientes
        if (strpos($sql, 'SELECT * FROM clientes') !== false && strpos($sql, 'WHERE') === false) {
            $data = $this->loadTable('clientes');
            return new MockStatement($data);
        }
        
        // SELECT todos los préstamos
        if (strpos($sql, 'SELECT * FROM prestamos') !== false && strpos($sql, 'WHERE') === false) {
            $data = $this->loadTable('prestamos');
            return new MockStatement($data);
        }
        
        // SELECT préstamos con JOIN de clientes
        if (strpos($sql, 'SELECT p.*, c.nombre as cliente_nombre') !== false && strpos($sql, 'FROM prestamos p') !== false) {
            $prestamos = $this->loadTable('prestamos');
            $clientes = $this->loadTable('clientes');
            
            // Simular LEFT JOIN
            foreach ($prestamos as &$prestamo) {
                $prestamo['cliente_nombre'] = 'Cliente no encontrado';
                foreach ($clientes as $cliente) {
                    if ($cliente['id'] == $prestamo['cliente_id']) {
                        $prestamo['cliente_nombre'] = $cliente['nombre'];
                        break;
                    }
                }
            }
            
            return new MockStatement($prestamos);
        }
        
        // SELECT todos los pagos
        if (strpos($sql, 'SELECT * FROM pagos') !== false && strpos($sql, 'WHERE') === false) {
            $data = $this->loadTable('pagos');
            return new MockStatement($data);
        }
        
        // Para simplificar, solo manejamos SELECT básicos
        if (strpos($sql, 'SELECT COUNT(*)') !== false && strpos($sql, 'clientes') !== false) {
            $data = $this->loadTable('clientes');
            return new MockStatement(['total' => count($data)]);
        }
        return new MockStatement([]);
    }
    
    public function prepare($sql) {
        return new MockPreparedStatement($this, $sql);
    }
    
    public function lastInsertId() {
        return $this->lastId ?? 1;
    }
    
    public function loadTable($tableName) {
        $file = $this->dataPath . $tableName . '.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true) ?: [];
        }
        return [];
    }
    
    public function saveTable($tableName, $data) {
        $file = $this->dataPath . $tableName . '.json';
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function insertRecord($tableName, $data) {
        $records = $this->loadTable($tableName);
        $data['id'] = count($records) + 1;
        $data['fecha_creacion'] = date('Y-m-d H:i:s');
        $records[] = $data;
        $this->lastId = $data['id'];
        $this->saveTable($tableName, $records);
        return $data['id'];
    }
    
    public function findRecord($tableName, $field, $value) {
        $records = $this->loadTable($tableName);
        foreach ($records as $record) {
            if (isset($record[$field]) && $record[$field] == $value) {
                return $record;
            }
        }
        return null;
    }
    
    public function deleteRecord($tableName, $field, $value) {
        $records = $this->loadTable($tableName);
        $filteredRecords = array_filter($records, function($record) use ($field, $value) {
            return !isset($record[$field]) || $record[$field] != $value;
        });
        
        // Reindexar el array para mantener índices secuenciales
        $filteredRecords = array_values($filteredRecords);
        $this->saveTable($tableName, $filteredRecords);
        
        // Retornar true si se eliminó algún registro
        return count($records) > count($filteredRecords);
    }
    
    public function updateRecord($tableName, $id, $data) {
        $records = $this->loadTable($tableName);
        $updated = false;
        
        foreach ($records as &$record) {
            if (isset($record['id']) && $record['id'] == $id) {
                // Mantener campos importantes como id y fecha_creacion
                $data['id'] = $record['id'];
                if (isset($record['fecha_creacion'])) {
                    $data['fecha_creacion'] = $record['fecha_creacion'];
                }
                
                // Actualizar el registro con los nuevos datos
                $record = array_merge($record, $data);
                $updated = true;
                break;
            }
        }
        
        if ($updated) {
            $this->saveTable($tableName, $records);
        }
        
        return $updated;
    }
}

class MockStatement {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function fetch($mode = null) {
        if (is_array($this->data) && count($this->data) > 0) {
            return $this->data[0];
        }
        return $this->data;
    }
    
    public function fetchAll($mode = null) {
        if (is_array($this->data)) {
            return $this->data;
        }
        return [$this->data];
    }
    
    public function fetchColumn() {
        if (is_array($this->data)) {
            return reset($this->data);
        }
        return $this->data;
    }
}

class MockPreparedStatement {
    private $db;
    private $sql;
    private $params = [];
    
    public function __construct($db, $sql) {
        $this->db = $db;
        $this->sql = $sql;
    }
    
    public function bindParam($param, &$value) {
        $this->params[$param] = $value;
    }
    
    public function execute($params = null) {
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
        
        // Simular INSERT
        if (strpos($this->sql, 'INSERT INTO clientes') !== false) {
            $data = [
                'nombre' => $this->params[':nombre'],
                'documento' => $this->params[':documento'],
                'telefono' => $this->params[':telefono'],
                'direccion' => $this->params[':direccion'] ?? ''
            ];
            return $this->db->insertRecord('clientes', $data);
        }
        
        // Simular INSERT para préstamos
        if (strpos($this->sql, 'INSERT INTO prestamos') !== false) {
            $data = [
                'cliente_id' => $this->params[':cliente_id'],
                'monto' => $this->params[':monto'],
                'monto_pendiente' => $this->params[':monto_pendiente'],
                'tasa' => $this->params[':tasa'],
                'plazo' => $this->params[':plazo'],
                'fecha' => $this->params[':fecha'],
                'fecha_vencimiento' => $this->params[':fecha_vencimiento'],
                'tipo' => $this->params[':tipo'],
                'frecuencia' => $this->params[':frecuencia'],
                'cuotas' => $this->params[':cuotas'],
                'estado' => 'activo',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            return $this->db->insertRecord('prestamos', $data);
        }
        
        // Simular INSERT para pagos
        if (strpos($this->sql, 'INSERT INTO pagos') !== false) {
            $data = [
                'prestamo_id' => $this->params[':prestamo_id'],
                'monto' => $this->params[':monto'],
                'tipo' => $this->params[':tipo'],
                'fecha_pago' => $this->params[':fecha_pago'],
                'observaciones' => $this->params[':observaciones'] ?? '',
                'desglose' => $this->params[':desglose'] ?? ''
            ];
            return $this->db->insertRecord('pagos', $data);
        }
        
        // Simular UPDATE
        if (strpos($this->sql, 'UPDATE clientes SET') !== false) {
            $data = [
                'nombre' => $this->params[':nombre'],
                'documento' => $this->params[':documento'],
                'telefono' => $this->params[':telefono'],
                'direccion' => $this->params[':direccion'] ?? ''
            ];
            return $this->db->updateRecord('clientes', $this->params[':id'], $data);
        }
        
        if (strpos($this->sql, 'UPDATE prestamos SET') !== false) {
            $data = [
                'cliente_id' => $this->params[':cliente_id'],
                'monto' => $this->params[':monto'],
                'monto_pendiente' => $this->params[':monto_pendiente'],
                'tasa' => $this->params[':tasa'],
                'plazo' => $this->params[':plazo'],
                'fecha' => $this->params[':fecha'],
                'fecha_vencimiento' => $this->params[':fecha_vencimiento'],
                'tipo' => $this->params[':tipo'],
                'frecuencia' => $this->params[':frecuencia'],
                'cuotas' => $this->params[':cuotas']
            ];
            return $this->db->updateRecord('prestamos', $this->params[':id'], $data);
        }
        
        if (strpos($this->sql, 'UPDATE pagos SET') !== false) {
            $data = [
                'prestamo_id' => $this->params[':prestamo_id'],
                'monto' => $this->params[':monto'],
                'tipo' => $this->params[':tipo'],
                'fecha_pago' => $this->params[':fecha_pago'],
                'observaciones' => $this->params[':observaciones'] ?? '',
                'desglose' => $this->params[':desglose'] ?? ''
            ];
            return $this->db->updateRecord('pagos', $this->params[':id'], $data);
        }
        
        // Simular DELETE
        if (strpos($this->sql, 'DELETE FROM') !== false) {
            if (strpos($this->sql, 'DELETE FROM clientes WHERE id') !== false) {
                return $this->db->deleteRecord('clientes', 'id', $this->params[':id']);
            }
            if (strpos($this->sql, 'DELETE FROM prestamos WHERE id') !== false) {
                return $this->db->deleteRecord('prestamos', 'id', $this->params[':id']);
            }
            if (strpos($this->sql, 'DELETE FROM pagos WHERE id') !== false) {
                return $this->db->deleteRecord('pagos', 'id', $this->params[':id']);
            }
        }

        return true;
    }
    
    public function fetch($mode = null) {
        // Simular SELECT por ID
        if (strpos($this->sql, 'SELECT * FROM clientes WHERE id') !== false) {
            return $this->db->findRecord('clientes', 'id', $this->params[':id']);
        }
        
        // Simular SELECT préstamo por ID con JOIN
        if (strpos($this->sql, 'SELECT p.*, c.nombre as cliente_nombre') !== false && strpos($this->sql, 'WHERE p.id') !== false) {
            $prestamo = $this->db->findRecord('prestamos', 'id', $this->params[':id']);
            if ($prestamo) {
                $clientes = $this->db->loadTable('clientes');
                $prestamo['cliente_nombre'] = 'Cliente no encontrado';
                $prestamo['cliente_documento'] = '';
                
                foreach ($clientes as $cliente) {
                    if ($cliente['id'] == $prestamo['cliente_id']) {
                        $prestamo['cliente_nombre'] = $cliente['nombre'];
                        $prestamo['cliente_documento'] = $cliente['documento'] ?? '';
                        break;
                    }
                }
            }
            return $prestamo;
        }
        
        // Simular verificación de documento
        if (strpos($this->sql, 'SELECT COUNT(*) FROM clientes WHERE documento') !== false) {
            $record = $this->db->findRecord('clientes', 'documento', $this->params[':documento']);
            return ['COUNT(*)' => $record ? 1 : 0];
        }
        
        return null;
    }
    
    public function fetchAll($mode = null) {
        // Simular SELECT todos los clientes
        if (strpos($this->sql, 'SELECT * FROM clientes') !== false && strpos($this->sql, 'WHERE') === false) {
            return $this->db->loadTable('clientes');
        }
        
        // Simular SELECT todos los préstamos con JOIN
        if (strpos($this->sql, 'SELECT p.*, c.nombre as cliente_nombre FROM prestamos p') !== false) {
            $prestamos = $this->db->loadTable('prestamos');
            $clientes = $this->db->loadTable('clientes');
            
            // Simular JOIN
            foreach ($prestamos as &$prestamo) {
                $prestamo['cliente_nombre'] = 'Cliente no encontrado';
                foreach ($clientes as $cliente) {
                    if ($cliente['id'] == $prestamo['cliente_id']) {
                        $prestamo['cliente_nombre'] = $cliente['nombre'];
                        $prestamo['cliente_documento'] = $cliente['documento'] ?? '';
                        break;
                    }
                }
            }
            return $prestamos;
        }
        
        // Simular SELECT todos los pagos con JOIN
        if (strpos($this->sql, 'SELECT p.*, pr.monto_prestado FROM pagos p') !== false) {
            $pagos = $this->db->loadTable('pagos');
            $prestamos = $this->db->loadTable('prestamos');
            
            // Simular JOIN
            foreach ($pagos as &$pago) {
                foreach ($prestamos as $prestamo) {
                    if ($prestamo['id'] == $pago['prestamo_id']) {
                        $pago['monto_prestado'] = $prestamo['monto_prestado'];
                        break;
                    }
                }
            }
            return $pagos;
        }
        
        // Simular búsqueda
        if (strpos($this->sql, 'WHERE nombre LIKE') !== false || strpos($this->sql, 'WHERE documento LIKE') !== false) {
            $termino = str_replace(['%', ':termino'], '', $this->params[':termino']);
            $records = $this->db->loadTable('clientes');
            return array_filter($records, function($record) use ($termino) {
                return stripos($record['nombre'], $termino) !== false || 
                       stripos($record['documento'], $termino) !== false;
            });
        }
        
        return [];
    }
    
    public function fetchColumn() {
        // Simular verificación de documento
        if (strpos($this->sql, 'SELECT COUNT(*) FROM clientes WHERE documento') !== false) {
            $record = $this->db->findRecord('clientes', 'documento', $this->params[':documento']);
            return $record ? 1 : 0;
        }
        
        // Simular verificación de cliente por ID
        if (strpos($this->sql, 'SELECT COUNT(*) FROM clientes WHERE id') !== false) {
            $record = $this->db->findRecord('clientes', 'id', $this->params[':id']);
            return $record ? 1 : 0;
        }
        
        $result = $this->fetch();
        if (is_array($result)) {
            return reset($result);
        }
        return $result;
    }
}
?>
