<?php
/**
 * Módulo Cliente - Sistema de Préstamos Modular
 * Maneja todas las operaciones relacionadas con clientes incluyendo fotos de perfil
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Cliente {
    private $db;
    private $tableName = 'clientes';
    private $dataPath;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
        $this->dataPath = __DIR__ . '/../data/clientes.json';
        
        // Crear directorio data si no existe
        $dataDir = dirname($this->dataPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }
    
    /**
     * Crear un nuevo cliente
     */
    public function crear($nombre, $telefono, $direccion, $cedula, $email = '', $foto_perfil = null) {
        try {
            // Validar datos requeridos
            if (empty($nombre) || empty($cedula) || empty($telefono)) {
                return ['success' => false, 'error' => 'Nombre, cédula y teléfono son requeridos'];
            }
            
            // Cargar clientes existentes
            $clientes = $this->cargarClientes();
            
            // Verificar si ya existe la cédula
            foreach ($clientes as $cliente) {
                if ($cliente['documento'] === $cedula) {
                    return ['success' => false, 'error' => 'Ya existe un cliente con esta cédula'];
                }
            }
            
            // Generar nuevo ID
            $nuevoId = $this->generarNuevoId($clientes);
            
            // Crear nuevo cliente
            $nuevoCliente = [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'documento' => $cedula,
                'telefono' => $telefono,
                'direccion' => $direccion ?: '',
                'email' => $email ?: '',
                'foto_perfil' => $foto_perfil,
                'calificacion' => 0.0,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];
            
            // Agregar a la lista y guardar
            $clientes[] = $nuevoCliente;
            $this->guardarClientes($clientes);
            
            return [
                'success' => true,
                'id' => $nuevoId,
                'mensaje' => 'Cliente creado correctamente',
                'cliente' => $nuevoCliente
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error creando cliente: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los clientes
     */
    public function obtenerTodos() {
        try {
            $clientes = $this->cargarClientes();
            
            // Ordenar por fecha de creación descendente
            usort($clientes, function($a, $b) {
                return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
            });
            
            return $clientes;
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId($id) {
        try {
            $clientes = $this->cargarClientes();
            
            foreach ($clientes as $cliente) {
                if ($cliente['id'] == $id) {
                    return $cliente;
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Actualizar cliente
     */
    public function actualizar($id, $nombre, $telefono, $direccion, $cedula, $email = '') {
        try {
            $clientes = $this->cargarClientes();
            $clienteEncontrado = false;
            
            // Verificar si el documento ya existe para otro cliente
            foreach ($clientes as $cliente) {
                if ($cliente['documento'] === $cedula && $cliente['id'] != $id) {
                    return ['success' => false, 'error' => 'Ya existe un cliente con esta cédula'];
                }
            }
            
            // Actualizar cliente
            foreach ($clientes as &$cliente) {
                if ($cliente['id'] == $id) {
                    $cliente['nombre'] = $nombre;
                    $cliente['documento'] = $cedula;
                    $cliente['telefono'] = $telefono;
                    $cliente['direccion'] = $direccion ?: '';
                    $cliente['email'] = $email ?: '';
                    $cliente['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                return ['success' => false, 'error' => 'Cliente no encontrado'];
            }
            
            $this->guardarClientes($clientes);
            
            return [
                'success' => true,
                'mensaje' => 'Cliente actualizado correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error actualizando cliente: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar foto de perfil
     */
    public function actualizarFoto($id, $rutaFoto) {
        try {
            $clientes = $this->cargarClientes();
            $clienteEncontrado = false;
            
            foreach ($clientes as &$cliente) {
                if ($cliente['id'] == $id) {
                    $cliente['foto_perfil'] = $rutaFoto;
                    $cliente['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                return ['success' => false, 'error' => 'Cliente no encontrado'];
            }
            
            $this->guardarClientes($clientes);
            
            return [
                'success' => true,
                'mensaje' => 'Foto de perfil actualizada correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error actualizando foto: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar calificación
     */
    public function actualizarCalificacion($id, $calificacion) {
        try {
            if ($calificacion < 0 || $calificacion > 5) {
                return ['success' => false, 'error' => 'La calificación debe estar entre 0 y 5'];
            }
            
            $clientes = $this->cargarClientes();
            $clienteEncontrado = false;
            
            foreach ($clientes as &$cliente) {
                if ($cliente['id'] == $id) {
                    $cliente['calificacion'] = floatval($calificacion);
                    $cliente['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    $clienteEncontrado = true;
                    break;
                }
            }
            
            if (!$clienteEncontrado) {
                return ['success' => false, 'error' => 'Cliente no encontrado'];
            }
            
            $this->guardarClientes($clientes);
            
            return [
                'success' => true,
                'mensaje' => 'Calificación actualizada correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error actualizando calificación: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar cliente
     */
    public function eliminar($id) {
        try {
            $clientes = $this->cargarClientes();
            $clienteEncontrado = false;
            
            // Filtrar el cliente a eliminar
            $clientesFiltrados = array_filter($clientes, function($cliente) use ($id, &$clienteEncontrado) {
                if ($cliente['id'] == $id) {
                    $clienteEncontrado = true;
                    return false;
                }
                return true;
            });
            
            if (!$clienteEncontrado) {
                return ['success' => false, 'error' => 'Cliente no encontrado'];
            }
            
            // Reindexar array
            $clientesFiltrados = array_values($clientesFiltrados);
            $this->guardarClientes($clientesFiltrados);
            
            return [
                'success' => true,
                'mensaje' => 'Cliente eliminado correctamente'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error eliminando cliente: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cargar clientes desde archivo JSON
     */
    private function cargarClientes() {
        if (file_exists($this->dataPath)) {
            $contenido = file_get_contents($this->dataPath);
            return json_decode($contenido, true) ?: [];
        }
        return [];
    }
    
    /**
     * Guardar clientes en archivo JSON
     */
    private function guardarClientes($clientes) {
        file_put_contents($this->dataPath, json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Generar nuevo ID
     */
    private function generarNuevoId($clientes) {
        if (empty($clientes)) {
            return 1;
        }
        
        $maxId = 0;
        foreach ($clientes as $cliente) {
            if ($cliente['id'] > $maxId) {
                $maxId = $cliente['id'];
            }
        }
        
        return $maxId + 1;
    }
    
    /**
     * Obtener estadísticas de clientes
     */
    public function obtenerEstadisticas() {
        try {
            $clientes = $this->cargarClientes();
            
            return [
                'total' => count($clientes),
                'con_foto' => count(array_filter($clientes, function($c) { return !empty($c['foto_perfil']); })),
                'con_email' => count(array_filter($clientes, function($c) { return !empty($c['email']); })),
                'calificacion_promedio' => $this->calcularCalificacionPromedio($clientes)
            ];
            
        } catch (Exception $e) {
            return ['total' => 0, 'con_foto' => 0, 'con_email' => 0, 'calificacion_promedio' => 0];
        }
    }
    
    /**
     * Calcular calificación promedio
     */
    private function calcularCalificacionPromedio($clientes) {
        if (empty($clientes)) return 0;
        
        $total = array_sum(array_column($clientes, 'calificacion'));
        return round($total / count($clientes), 1);
    }
}
?>
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT * FROM {$this->tableName} ORDER BY fecha_creacion DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Actualizar cliente
     */
    public function actualizar($id, $nombre, $telefono, $direccion, $cedula) {
        try {
            // Verificar si el documento ya existe para otro cliente
            if ($this->existeDocumento($cedula, $id)) {
                return ['success' => false, 'error' => 'Ya existe un cliente con esta cédula'];
            }
            
            $sql = "UPDATE {$this->tableName} SET 
                    nombre = :nombre, 
                    documento = :documento, 
                    telefono = :telefono, 
                    direccion = :direccion,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $cedula);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':direccion', $direccion);
            
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Cliente actualizado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error actualizando cliente: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar cliente
     */
    public function eliminar($id) {
        try {
            // Verificar que no tenga préstamos activos
            if ($this->tienePrestamosActivos($id)) {
                return ['success' => false, 'error' => 'No se puede eliminar cliente con préstamos activos'];
            }
            
            $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Cliente eliminado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error eliminando cliente: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar si existe un documento
     */
    private function existeDocumento($documento, $excluirId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE documento = :documento";
            if ($excluirId) {
                $sql .= " AND id != :excluir_id";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':documento', $documento);
            if ($excluirId) {
                $stmt->bindParam(':excluir_id', $excluirId);
            }
            
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Verificar si el cliente tiene préstamos activos
     */
    private function tienePrestamosActivos($clienteId) {
        try {
            $sql = "SELECT COUNT(*) FROM prestamos WHERE cliente_id = :cliente_id AND estado = 'activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (Exception $e) {
            return false; // En caso de error, permitir eliminación
        }
    }
    
    /**
     * Buscar clientes por nombre o documento
     */
    public function buscar($termino) {
        try {
            $sql = "SELECT * FROM {$this->tableName} 
                    WHERE nombre LIKE :termino OR documento LIKE :termino 
                    ORDER BY nombre";
            
            $stmt = $this->db->prepare($sql);
            $terminoBusqueda = "%$termino%";
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de clientes
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total de clientes
            $sql = "SELECT COUNT(*) as total FROM {$this->tableName}";
            $stmt = $this->db->query($sql);
            $stats['total'] = $stmt->fetchColumn();
            
            // Clientes nuevos este mes
            $sql = "SELECT COUNT(*) as nuevos FROM {$this->tableName} 
                    WHERE DATE(fecha_creacion) >= DATE('now', 'start of month')";
            $stmt = $this->db->query($sql);
            $stats['nuevos_mes'] = $stmt->fetchColumn();
            
            return $stats;
            
        } catch (Exception $e) {
            return ['total' => 0, 'nuevos_mes' => 0];
        }
    }
}

?>
