<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SistemaPrestamoPro - Panel Administrativo Modular</title>
    
    <!-- ESTILOS BASE -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; }
        
        /* Header */
        .header-bar {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-left { display: flex; align-items: center; gap: 18px; }
        .header-icon { font-size: 2em; }
        .header-title { font-size: 1.7em; font-weight: bold; letter-spacing: 1px; }
        .header-desc { font-size: 1em; opacity: 0.8; }
        .header-right { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; font-size: 1em; }
        .sistema-estado { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 0.8em; 
            font-weight: bold;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .sistema-estado.cargando { background: rgba(255, 193, 7, 0.8); color: #856404; }
        .sistema-estado.listo { background: rgba(40, 167, 69, 0.8); color: #155724; }
        .sistema-estado.error { background: rgba(220, 53, 69, 0.8); color: #721c24; }
        
        /* Menu Tabs */
        .menu-tabs {
            background: #f5f5f5;
            border-bottom: 3px solid #2196f3;
            display: flex;
            gap: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 16px 32px;
            font-size: 1.1em;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tab-btn:hover {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
            transform: translateY(-2px);
        }
        .tab-btn.active {
            background: #2196f3;
            color: white;
            border-bottom: 3px solid #1976d2;
        }
        
        /* Contenido principal */
        .main-content {
            padding: 20px;
            min-height: calc(100vh - 140px);
        }
        
        /* Vistas */
        .vista {
            display: none;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            min-height: 600px;
        }
        
        .vista h2 {
            margin: 0 0 20px 0;
            color: #1f2937;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Botones comunes */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background: #2196f3;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1976d2;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }
        
        /* Modal base */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 90%;
            max-height: 90%;
            overflow-y: auto;
        }
    </style>
    
    <!-- CARGAR ESTILOS MODULARES -->
    <link rel="stylesheet" href="modules/cliente.css">
    <link rel="stylesheet" href="modules/rating.css">
</head>
<body>
    <!-- HEADER -->
    <div class="header-bar">
        <div class="header-left">
            <div class="header-icon">💰</div>
            <div>
                <div class="header-title">SistemaPrestamoPro</div>
                <div class="header-desc">Modular</div>
            </div>
        </div>
        <div class="header-right">
            <div>🌐 Aplicación Web - Datos Guardados Localmente</div>
            <div>👤 Usuario: Administrador</div>
            <div>📍 República Dominicana</div>
            <div class="sistema-estado listo">✅ Sistema Listo</div>
        </div>
    </div>

    <!-- NAVEGACIÓN -->
    <nav class="menu-tabs">
        <button class="tab-btn active" onclick="showTab('dashboard')">
            📊 Dashboard
        </button>
        <button class="tab-btn" onclick="showTab('clientes')">
            👥 Clientes
        </button>
        <button class="tab-btn" onclick="showTab('prestamos')">
            💼 Préstamos
        </button>
        <button class="tab-btn" onclick="showTab('pagos')">
            💳 Pagos
        </button>
        <button class="tab-btn" onclick="showTab('mora')">
            ⚠️ Mora
        </button>
        <button class="tab-btn" onclick="showTab('calculadora')">
            🧮 Calculadora
        </button>
        <button class="tab-btn" onclick="showTab('reportes')">
            📊 Reportes
        </button>
        <button class="tab-btn" onclick="showTab('configuracion')">
            ⚙️ Con
        </button>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        
        <!-- VISTA DASHBOARD -->
        <div id="vista-dashboard" class="vista" style="display: block;">
            <h2>📊 Estadísticas Generales</h2>
            <div id="dashboard-content">
                <div class="loading">🔄 Cargando estadísticas...</div>
            </div>
        </div>

        <!-- VISTA CLIENTES -->
        <div id="vista-clientes" class="vista">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>👥 Gestión de Clientes</h2>
                <button class="btn btn-primary" onclick="showTab('nuevo-cliente')">
                    ➕ Nuevo Cliente
                </button>
            </div>
            <div id="clientes-gallery">
                <div class="loading">🔄 Cargando clientes...</div>
            </div>
        </div>

        <!-- VISTA NUEVO CLIENTE -->
        <div id="vista-nuevo-cliente" class="vista">
            <h2>➕ Agregar Nuevo Cliente</h2>
            <div id="form-nuevo-cliente">
                <div class="loading">🔄 Cargando formulario...</div>
            </div>
        </div>

        <!-- VISTA PRÉSTAMOS -->
        <div id="vista-prestamos" class="vista">
            <h2>💼 Gestión de Préstamos</h2>
            <div id="prestamos-content">
                <div class="loading">🔄 Cargando préstamos...</div>
            </div>
        </div>

        <!-- VISTA PAGOS -->
        <div id="vista-pagos" class="vista">
            <h2>💳 Gestión de Pagos</h2>
            <div id="pagos-content">
                <div class="loading">🔄 Cargando pagos...</div>
            </div>
        </div>

        <!-- VISTA MORA -->
        <div id="vista-mora" class="vista">
            <h2>⚠️ Gestión de Mora</h2>
            <div id="mora-content">
                <div class="loading">🔄 Cargando información de mora...</div>
            </div>
        </div>

        <!-- VISTA CALCULADORA -->
        <div id="vista-calculadora" class="vista">
            <h2>🧮 Calculadora de Préstamos</h2>
            <div id="calculadora-content">
                <div class="loading">🔄 Cargando calculadora...</div>
            </div>
        </div>

        <!-- VISTA REPORTES -->
        <div id="vista-reportes" class="vista">
            <h2>📊 Reportes y Análisis</h2>
            <div id="reportes-content">
                <div class="loading">🔄 Cargando reportes...</div>
            </div>
        </div>

        <!-- VISTA CONFIGURACIÓN -->
        <div id="vista-configuracion" class="vista">
            <h2>⚙️ Configuración del Sistema</h2>
            <div id="configuracion-content">
                <div class="loading">🔄 Cargando configuración...</div>
            </div>
        </div>

    </main>

    <!-- CARGAR MÓDULOS JAVASCRIPT -->
    <script src="modules/navegacion.js"></script>
    <script src="modules/cliente.js"></script>
    <script src="modules/rating.js"></script>
    
    <!-- SCRIPT BASE -->
    <script>
        // Estado del sistema
        console.log('🚀 Sistema Modular de Préstamos Nova Luz iniciado');
        
        // Verificar carga de módulos
        window.addEventListener('load', function() {
            console.log('✅ Todos los módulos cargados');
            
            // Verificar funciones principales
            const funcionesRequeridas = ['showTab', 'cargarClientes', 'mostrarRatingModal'];
            const funcionesFaltantes = funcionesRequeridas.filter(fn => typeof window[fn] !== 'function');
            
            if (funcionesFaltantes.length > 0) {
                console.warn('⚠️ Funciones faltantes:', funcionesFaltantes);
            } else {
                console.log('✅ Todas las funciones principales disponibles');
            }
        });
    </script>
</body>
</html>
