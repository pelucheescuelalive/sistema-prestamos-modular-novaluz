<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

// Definir BASE_PATH si no está definido
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Configuración para JSON como base de datos temporal
define('DB_PATH', BASE_PATH . '/data');
define('DB_TYPE', 'json');

/**
 * Clase para manejar almacenamiento JSON
 */
class JsonDatabase {
    private static $instance = null;
    private $dataPath;
    
    private function __construct() {
        $this->dataPath = DB_PATH;
        
        // Crear directorio de datos si no existe
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
        
        // Inicializar archivos JSON si no existen
        $this->initializeJsonFiles();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeJsonFiles() {
        $files = ['clientes.json', 'prestamos.json', 'pagos.json', 'mora.json', 'configuracion.json'];
        
        foreach ($files as $file) {
            $filePath = $this->dataPath . '/' . $file;
            if (!file_exists($filePath)) {
                file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
            }
        }
        
        // Inicializar configuración básica
        $configFile = $this->dataPath . '/configuracion.json';
        $config = json_decode(file_get_contents($configFile), true);
        if (empty($config)) {
            $defaultConfig = [
                'tasa_interes_default' => 15.00,
                'tasa_mora_default' => 5.00,
                'moneda' => 'RD$',
                'empresa_nombre' => 'Nova Luz Pro',
                'empresa_telefono' => '809-000-0000'
            ];
            file_put_contents($configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
        }
    }
    
    public function loadData($table) {
        $file = $this->dataPath . '/' . $table . '.json';
        if (file_exists($file)) {
            $data = file_get_contents($file);
            return json_decode($data, true) ?? [];
        }
        return [];
    }
    
    public function saveData($table, $data) {
        $file = $this->dataPath . '/' . $table . '.json';
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function insert($table, $data) {
        $allData = $this->loadData($table);
        
        // Generar ID único
        $maxId = 0;
        foreach ($allData as $item) {
            if (isset($item['id']) && $item['id'] > $maxId) {
                $maxId = $item['id'];
            }
        }
        
        $data['id'] = $maxId + 1;
        $data['fecha_registro'] = date('Y-m-d H:i:s');
        
        $allData[] = $data;
        $this->saveData($table, $allData);
        
        return $data['id'];
    }
    
    public function findAll($table, $conditions = []) {
        $data = $this->loadData($table);
        
        if (empty($conditions)) {
            return $data;
        }
        
        return array_filter($data, function($item) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) || $item[$key] != $value) {
                    return false;
                }
            }
            return true;
        });
    }
    
    public function findOne($table, $conditions) {
        $results = $this->findAll($table, $conditions);
        return !empty($results) ? array_values($results)[0] : null;
    }
    
    public function update($table, $id, $data) {
        $allData = $this->loadData($table);
        
        foreach ($allData as $index => $item) {
            if ($item['id'] == $id) {
                $data['id'] = $id;
                $data['fecha_actualizacion'] = date('Y-m-d H:i:s');
                $allData[$index] = array_merge($item, $data);
                $this->saveData($table, $allData);
                return true;
            }
        }
        
        return false;
    }
    
    public function delete($table, $id) {
        $allData = $this->loadData($table);
        
        foreach ($allData as $index => $item) {
            if ($item['id'] == $id) {
                unset($allData[$index]);
                $this->saveData($table, array_values($allData));
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Función para obtener la instancia de la base de datos JSON
 */
function getDB() {
    return JsonDatabase::getInstance();
}

/**
 * Función de logging
 */
function logMessage($message, $level = 'INFO') {
    $logFile = BASE_PATH . '/logs/system.log';
    
    // Crear directorio de logs si no existe
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
