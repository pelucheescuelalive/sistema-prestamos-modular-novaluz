<?php
/**
 * SISTEMA DE PRÉSTAMOS MODULAR - NOVA LUZ PRO
 * Archivo Principal - Maneja todos los módulos del sistema
 * 
 * @author Nova Luz Pro
 * @version 2.0.0
 * @date 28 de agosto de 2025
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Préstamos Nova Luz Pro');
define('APP_VERSION', '2.0.0');
define('BASE_PATH', __DIR__);

// Incluir archivos de configuración
require_once 'config/database.php';
require_once 'config/app.php';

// Incluir archivos de módulos principales
require_once 'modules/ClienteModular.php';
require_once 'modules/PrestamoModular.php';
require_once 'modules/PagoModular.php';
require_once 'modules/MoraModular.php';

// Incluir utilidades
require_once 'utils/helpers.php';
require_once 'utils/validators.php';

// Manejar solicitudes AJAX
if (isset($_GET['action']) || isset($_POST['action'])) {
    require_once 'api/handler.php';
    exit;
}

// Variables para la vista
$pageTitle = APP_NAME;
$currentDate = date('d/m/Y');
$systemStatus = 'Activo';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS Framework y Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos Modulares -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    
    <style>
        :root {
            --primary-color: #2196F3;
            --secondary-color: #1976D2;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .main-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .module-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
        }
        
        .status-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-coins me-2"></i>
                <?php echo APP_NAME; ?>
            </a>
            
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="fas fa-calendar me-1"></i>
                    <?php echo $currentDate; ?>
                </span>
                <span class="status-badge">
                    <i class="fas fa-circle me-1"></i>
                    <?php echo $systemStatus; ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="container main-container">
        
        <!-- HEADER DE BIENVENIDA -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="module-card p-4 text-center">
                    <h1 class="display-6 mb-3">
                        <i class="fas fa-home text-primary me-2"></i>
                        Panel Administrativo
                    </h1>
                    <p class="lead text-muted">
                        Gestiona clientes, préstamos, pagos y reportes de manera integral
                    </p>
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary" id="total-clientes">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </h3>
                                <small class="text-muted">Clientes Activos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success" id="total-prestamos">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </h3>
                                <small class="text-muted">Préstamos Activos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning" id="total-pendiente">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </h3>
                                <small class="text-muted">Monto Pendiente</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info" id="total-cobrado">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </h3>
                                <small class="text-muted">Total Cobrado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MÓDULOS PRINCIPALES -->
        <div class="row">
            
            <!-- MÓDULO CLIENTES -->
            <div class="col-lg-6 mb-4">
                <div class="module-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Gestión de Clientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="clientes-module">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                <p>Cargando módulo de clientes...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MÓDULO PRÉSTAMOS -->
            <div class="col-lg-6 mb-4">
                <div class="module-card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-hand-holding-usd me-2"></i>
                            Gestión de Préstamos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="prestamos-module">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i>
                                <p>Cargando módulo de préstamos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MÓDULO PAGOS -->
            <div class="col-lg-6 mb-4">
                <div class="module-card h-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Gestión de Pagos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="pagos-module">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-warning mb-3"></i>
                                <p>Cargando módulo de pagos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MÓDULO MORA -->
            <div class="col-lg-6 mb-4">
                <div class="module-card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Gestión de Mora
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="mora-module">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-danger mb-3"></i>
                                <p>Cargando módulo de mora...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- MÓDULO REPORTES -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="module-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Reportes y Análisis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="reportes-module">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-info mb-3"></i>
                                <p>Cargando módulo de reportes...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">
                <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - 
                © <?php echo date('Y'); ?> Nova Luz Pro. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- MODALES -->
    <div id="modal-container"></div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Scripts Modulares -->
    <script src="assets/js/app.js"></script>
    <!-- JavaScript Principal de la aplicación modular -->
    <script src="js/app.js"></script>
    
    <script>
        // Inicializar aplicación
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Iniciando Sistema de Préstamos Modular v<?php echo APP_VERSION; ?>');
            
            // La aplicación se inicializa automáticamente desde app.js
            console.log('✅ Aplicación Nova Luz Pro cargada correctamente');
        });
    </script>
</body>
</html>
