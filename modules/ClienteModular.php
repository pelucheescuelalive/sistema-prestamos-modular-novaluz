<?php
/**
 * MÓDULO CLIENTE
 * Sistema de Préstamos Modular - Nova Luz Pro
 * 
 * Funciones para gestión de clientes
 */

class ClienteNuevo {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Crear nuevo cliente
     * 
     * @param array $datos Datos del cliente
     * @return array Resultado de la operación
     */
    public function crear($datos) {
        try {
            // Validar datos requeridos
            if (empty($datos['nombre'])) {
                throw new Exception('El nombre es requerido');
            }
            
            // Preparar datos
            $nombre = trim($datos['nombre']);
            $documento = isset($datos['documento']) ? trim($datos['documento']) : null;
            $telefono = isset($datos['telefono']) ? trim($datos['telefono']) : null;
            $email = isset($datos['email']) ? trim($datos['email']) : null;
            $direccion = isset($datos['direccion']) ? trim($datos['direccion']) : null;
            $genero = isset($datos['genero']) ? $datos['genero'] : 'no_especificado';
            $calificacion = isset($datos['calificacion']) ? floatval($datos['calificacion']) : 5.0;
            
            // Verificar si el documento ya existe
            if ($documento) {
                $stmt = $this->db->prepare("SELECT id FROM clientes WHERE documento = ?");
                $stmt->execute([$documento]);
                if ($stmt->fetch()) {
                    throw new Exception('Ya existe un cliente con este documento');
                }
            }
            
            // Insertar cliente
            $sql = "INSERT INTO clientes (nombre, documento, telefono, email, direccion, genero, calificacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$nombre, $documento, $telefono, $email, $direccion, $genero, $calificacion]);
            
            $clienteId = $this->db->lastInsertId();
            
            logMessage("Cliente creado exitosamente: ID $clienteId, Nombre: $nombre");
            
            return [
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => [
                    'id' => $clienteId,
                    'nombre' => $nombre,
                    'documento' => $documento
                ]
            ];
            
        } catch (Exception $e) {
            logMessage("Error creando cliente: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar todos los clientes activos
     * 
     * @return array Lista de clientes
     */
    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    c.*,
                    COUNT(p.id) as total_prestamos,
                    COALESCE(SUM(p.saldo_pendiente), 0) as saldo_total
                FROM clientes c
                LEFT JOIN prestamos p ON c.id = p.cliente_id AND p.estado = 'activo'
                WHERE c.activo = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC
            ");
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll()
            ];
        } catch (Exception $e) {
            logMessage("Error listando clientes: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}

// Funciones helper para compatibilidad
function crear_cliente_nuevo($nombre, $documento = null, $telefono = null, $email = null, $direccion = null) {
    $cliente = new ClienteNuevo();
    return $cliente->crear([
        'nombre' => $nombre,
        'documento' => $documento,
        'telefono' => $telefono,
        'email' => $email,
        'direccion' => $direccion
    ]);
}

function listar_clientes_nuevo() {
    $cliente = new ClienteNuevo();
    return $cliente->listar();
}
?>
