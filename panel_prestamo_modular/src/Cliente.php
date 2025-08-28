<?php
/**
 * Módulo Cliente - Sistema de Préstamos Modular
 * Maneja todas las operaciones relacionadas con clientes
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Cliente {
    private $db;
    private $tableName = 'clientes';
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    /**
     * Crear un nuevo cliente
     */
    public function crear($nombre, $telefono, $direccion, $cedula) {
        try {
            // Validar datos requeridos
            if (empty($nombre) || empty($cedula) || empty($telefono)) {
                return ['success' => false, 'error' => 'Nombre, cédula y teléfono son requeridos'];
            }
            
            $sql = "INSERT INTO {$this->tableName} (nombre, documento, telefono, direccion) 
                    VALUES (:nombre, :documento, :telefono, :direccion)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $cedula);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':direccion', $direccion);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'mensaje' => 'Cliente creado correctamente'
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
