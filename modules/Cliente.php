<?php
/**
 * MÓDULO DE CLIENTE - GESTIÓN DE DATOS DE CLIENTES
 */

class Cliente {
    private $id;
    private $nombre;
    private $cedula;
    private $telefono;
    private $direccion;
    private $email;
    private $fechaRegistro;
    
    public function __construct($datos = []) {
        $this->id = $datos['id'] ?? null;
        $this->nombre = $datos['nombre'] ?? '';
        $this->cedula = $datos['cedula'] ?? '';
        $this->telefono = $datos['telefono'] ?? '';
        $this->direccion = $datos['direccion'] ?? '';
        $this->email = $datos['email'] ?? '';
        $this->fechaRegistro = $datos['fechaRegistro'] ?? date('Y-m-d H:i:s');
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getCedula() { return $this->cedula; }
    public function getTelefono() { return $this->telefono; }
    public function getDireccion() { return $this->direccion; }
    public function getEmail() { return $this->email; }
    public function getFechaRegistro() { return $this->fechaRegistro; }
    
    // Setters
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function setEmail($email) { $this->email = $email; }
    
    /**
     * Convertir a array para almacenamiento
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'cedula' => $this->cedula,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'email' => $this->email,
            'fechaRegistro' => $this->fechaRegistro
        ];
    }
    
    /**
     * Validar datos del cliente
     */
    public function validar() {
        $errores = [];
        
        if (empty($this->nombre)) {
            $errores[] = 'El nombre es obligatorio';
        }
        
        if (empty($this->cedula)) {
            $errores[] = 'La cédula es obligatoria';
        } elseif (!$this->validarCedula($this->cedula)) {
            $errores[] = 'Formato de cédula inválido';
        }
        
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Formato de email inválido';
        }
        
        return $errores;
    }
    
    /**
     * Validar formato de cédula dominicana
     */
    private function validarCedula($cedula) {
        // Remover guiones y espacios
        $cedula = preg_replace('/[^0-9]/', '', $cedula);
        
        // Debe tener 11 dígitos
        return strlen($cedula) === 11 && is_numeric($cedula);
    }
    
    /**
     * Guardar cliente en localStorage (JavaScript)
     */
    public static function guardar($cliente) {
        $clientes = self::obtenerTodos();
        
        if ($cliente->getId()) {
            // Actualizar existente
            foreach ($clientes as $key => $clienteExistente) {
                if ($clienteExistente['id'] === $cliente->getId()) {
                    $clientes[$key] = $cliente->toArray();
                    break;
                }
            }
        } else {
            // Crear nuevo
            $cliente->id = self::generarId();
            $clientes[] = $cliente->toArray();
        }
        
        return json_encode($clientes);
    }
    
    /**
     * Obtener todos los clientes
     */
    public static function obtenerTodos() {
        // Esta función devuelve JavaScript para obtener de localStorage
        return "JSON.parse(localStorage.getItem('clientes')) || []";
    }
    
    /**
     * Buscar cliente por ID
     */
    public static function buscarPorId($id) {
        return "(() => {
            const clientes = JSON.parse(localStorage.getItem('clientes')) || [];
            return clientes.find(c => c.id === '$id') || null;
        })()";
    }
    
    /**
     * Generar ID único
     */
    private static function generarId() {
        return 'cli_' . time() . '_' . rand(1000, 9999);
    }
}
?>
