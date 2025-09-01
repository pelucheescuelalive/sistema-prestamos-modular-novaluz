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
            if (empty($datos['nombres'])) {
                throw new Exception('Los nombres son requeridos');
            }
            if (empty($datos['apellidos'])) {
                throw new Exception('Los apellidos son requeridos');
            }
            if (empty($datos['cedula'])) {
                throw new Exception('La cédula es requerida');
            }
            if (empty($datos['celular'])) {
                throw new Exception('El celular es requerido');
            }
            
            // Verificar si la cédula ya existe
            $clienteExistente = $this->db->findOne('clientes', ['cedula' => trim($datos['cedula'])]);
            if ($clienteExistente) {
                throw new Exception('Ya existe un cliente con esta cédula');
            }
            
            // Preparar datos del cliente
            $clienteData = [
                'tipo' => isset($datos['tipo']) ? trim($datos['tipo']) : 'Persona',
                'cedula' => trim($datos['cedula']),
                'nombres' => trim($datos['nombres']),
                'apellidos' => trim($datos['apellidos']),
                'nombre_completo' => trim($datos['nombres'] . ' ' . $datos['apellidos']),
                'genero' => isset($datos['genero']) ? trim($datos['genero']) : '',
                'celular' => trim($datos['celular']),
                'telefono' => isset($datos['telefono']) ? trim($datos['telefono']) : '',
                'nacionalidad' => isset($datos['nacionalidad']) ? trim($datos['nacionalidad']) : 'Dominicano',
                'fecha_nacimiento' => isset($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                'direccion' => isset($datos['direccion']) ? trim($datos['direccion']) : '',
                'email' => isset($datos['email']) ? trim($datos['email']) : '',
                'profesion' => isset($datos['profesion']) ? trim($datos['profesion']) : '',
                'activo' => 1
            ];
            
            // Insertar cliente
            $clienteId = $this->db->insert('clientes', $clienteData);
            
            logMessage("Cliente creado exitosamente: ID $clienteId, Nombre: {$clienteData['nombre_completo']}, Cédula: {$clienteData['cedula']}");
            
            return [
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => [
                    'id' => $clienteId,
                    'nombre_completo' => $clienteData['nombre_completo'],
                    'cedula' => $clienteData['cedula']
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
            $clientes = $this->db->findAll('clientes', ['activo' => 1]);
            
            // Ordenar por nombre completo
            usort($clientes, function($a, $b) {
                return strcmp($a['nombre_completo'], $b['nombre_completo']);
            });
            
            return [
                'success' => true,
                'data' => $clientes
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
