<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SistemaPrestamoPro - Panel Administrativo Modular</title>
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
            color: #555;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            position: relative;
        }
        .tab-btn.active, .tab-btn:hover {
            background: #2196f3;
            color: #fff;
            transform: translateY(-2px);
        }
        
        /* Main Content */
        .main-content { padding: 32px; }
        .tab-content { display: none; }
        
        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card.green { border-left-color: #43a047; }
        .stat-card.red { border-left-color: #e53935; }
        .stat-card.orange { border-left-color: #fb8c00; }
        .stat-card.blue { border-left-color: #1e88e5; }
        .stat-card.gray { border-left-color: #757575; }
        .stat-title { display: block; font-size: 1em; color: #666; margin-bottom: 8px; }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }
        
        /* Tables */
        .table-container {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .data-table th {
            background: #2196f3;
            color: #fff;
            font-weight: bold;
        }
        .data-table tr:hover { background: #f5f5f5; }
        .data-table tr.seleccionado { background: #e3f2fd !important; font-weight: bold; }
        .data-table tr { transition: background-color 0.2s; }
        .data-table tr.vencido { background: #ffebee !important; }
        .data-table tr.vencido:hover { background: #ffcdd2 !important; }
        
        /* Forms */
        .form-container {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2196f3;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary { background: #2196f3; color: #fff; }
        .btn-success { background: #43a047; color: #fff; }
        .btn-warning { background: #fb8c00; color: #fff; }
        .btn-danger { background: #e53935; color: #fff; }
        .btn-info { background: #00acc1; color: #fff; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        
        /* Messages */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-weight: bold;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Calculator */
        .calculator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
        .calculator-input {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .calculator-result {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        
        /* Estilos específicos para Mora */
        .mora-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        .modal-content {
            background-color: #fff;
            margin: 2% auto;
            padding: 0;
            border-radius: 12px;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .modal-header {
            background: #2196f3;
            color: #fff;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body { padding: 20px; }
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }
        .close {
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }
        .close:hover { opacity: 0.7; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-bar { flex-direction: column; text-align: center; }
            .menu-tabs { flex-wrap: wrap; }
            .main-content { padding: 16px; }
            .stats-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .calculator-grid { grid-template-columns: 1fr; }
        }
        
        /* Loading animation */
        .loading {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2196f3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Estilos específicos para Mora */
        .mora-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .mora-config, .mora-calculator {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .mora-config h3 {
            color: #d32f2f;
            border-bottom: 2px solid #d32f2f;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .mora-calculator h3 {
            color: #1976d2;
            border-bottom: 2px solid #1976d2;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .mora-list {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .mora-list h3 {
            color: #ff9800;
            border-bottom: 2px solid #ff9800;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }
        
        .alert-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            color: #1976d2;
        }
        
        .alert-success {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            color: #2e7d32;
        }
        
        .alert-warning {
            background: #fff3e0;
            border: 1px solid #ff9800;
            color: #f57c00;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875em;
        }
        
        .btn-outline-danger {
            background: transparent;
            border: 1px solid #e53935;
            color: #e53935;
        }
        
        .btn-outline-primary {
            background: transparent;
            border: 1px solid #2196f3;
            color: #2196f3;
        }
        
        .btn-outline-danger:hover {
            background: #e53935;
            color: #fff;
        }
        
        .btn-outline-primary:hover {
            background: #2196f3;
            color: #fff;
        }
        
        .btn-secondary {
            background: #757575;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background: #616161;
        }
        
        .btn-editar, .btn-eliminar {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 4px 8px;
            margin: 0 2px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .btn-editar:hover {
            background: #fff3cd;
            transform: scale(1.1);
        }
        
        .btn-eliminar:hover {
            background: #f8d7da;
            transform: scale(1.1);
        }
        
        .info-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #17a2b8;
        }
        
        /* Cliente Cards Styles - Estilo Galería con Fotos */
        .clientes-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        
        .cliente-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            border: 2px solid transparent;
        }
        
        .cliente-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #2196f3;
        }
        
        .cliente-foto-container {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }
        
        .cliente-foto {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e0e0e0;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2em;
            color: #666;
        }
        
        .cliente-foto img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .cliente-status {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        
        .cliente-status.activo { background: #4caf50; }
        .cliente-status.inactivo { background: #f44336; }
        
        .cliente-info {
            text-align: center;
        }
        
        .cliente-nombre {
            font-size: 1.1em;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .cliente-detalles {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        
        .cliente-stats {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.85em;
        }
        
        .cliente-stat {
            text-align: center;
        }
        
        .cliente-stat-value {
            font-weight: bold;
            color: #2196f3;
            display: block;
        }
        
        .cliente-stat-label {
            color: #666;
            font-size: 0.8em;
        }
        
        .cliente-rating {
            text-align: center;
            margin: 10px 0;
        }
        
        .stars {
            color: #ffc107;
            font-size: 1.2em;
            margin-bottom: 5px;
        }
        
        .rating-value {
            font-size: 0.9em;
            color: #666;
        }
        
        /* Estilos para sistema de calificaciones */
        .rating-input {
            display: flex;
            gap: 2px;
        }
        
        .rating-input .star {
            cursor: pointer;
            font-size: 1.5em;
            color: #ddd;
            transition: color 0.3s ease;
            user-select: none;
        }
        
        .rating-input .star:hover,
        .rating-input .star.active {
            color: #ffc107;
        }
        
        .rating-input .star.editable {
            cursor: pointer;
        }
        
        .rating-input .star.editable:hover {
            color: #ff9800;
            transform: scale(1.1);
        }
        
        /* Efectos adicionales para estrellas */
        .rating-input .star {
            transition: all 0.2s ease;
        }
        
        .rating-input .star:hover ~ .star {
            color: #ddd !important;
        }
        
        .cliente-acciones {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }
        
        .btn-accion {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.85em;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .btn-ver {
            background: #2196f3;
            color: white;
        }
        
        .btn-ver:hover {
            background: #1976d2;
        }
        
        .btn-opciones {
            background: #f5f5f5;
            color: #666;
            position: relative;
        }
        
        .btn-opciones:hover {
            background: #e0e0e0;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 20px;
            max-width: 400px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1em;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #2196f3;
            background: #fff;
        }
        
        .search-box::after {
            content: '🔍';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
        }
        
        .filtros-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filtro-select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            font-size: 0.9em;
        }
        
        /* Modal Styles */
        .modal-cliente {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }
        
        .modal-content-cliente {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .modal-header-cliente {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 1.5em;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .modal-foto-grande {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 15px auto;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: #666;
            overflow: hidden;
        }
        
        .modal-foto-grande img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .modal-body-cliente {
            padding: 25px;
        }
        
        .info-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .info-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            border: none;
            background: none;
            color: #666;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .info-tab.active {
            color: #2196f3;
            border-bottom: 2px solid #2196f3;
        }
        
        .tab-content-info {
            display: none;
        }
        
        .tab-content-info.active {
            display: block;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            min-width: 120px;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        .info-action {
            margin-left: 10px;
        }
        
        .btn-mini {
            padding: 4px 8px;
            font-size: 0.8em;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .opciones-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 150px;
            z-index: 100;
            display: none;
        }
        
        .opciones-menu.active {
            display: block;
        }
        
        .opcion-item {
            padding: 10px 15px;
            cursor: pointer;
            color: #333;
            font-size: 0.9em;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            transition: background 0.3s;
        }
        
        .opcion-item:hover {
            background: #f5f5f5;
        }
        
        .opcion-item.eliminar {
            color: #f44336;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .clientes-gallery {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
            }
            
            .filtros-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .modal-content-cliente {
                width: 95%;
                margin: 20px;
            }
        }
        
        /* Animaciones para autocompletado de tasas */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes fadeInScale {
            0% { 
                opacity: 0; 
                transform: scale(0.8); 
            }
            100% { 
                opacity: 1; 
                transform: scale(1); 
            }
        }
        
        .form-control.auto-updated {
            animation: fadeInScale 0.5s ease-out;
        }
        
        .indicador-auto {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7em;
            font-weight: bold;
            margin-left: 8px;
            animation: pulse 2s infinite;
        }
    </style>
</head>
    <div class="header-bar">
        <div class="header-left">
            <span class="header-icon">💰</span>
            <span class="header-title">SistemaPrestamoPro Modular</span>
            <span class="header-desc">Aplicación Web - Datos Guardados Localmente</span>
        </div>
        <div class="header-right">
            <div id="sistema-estado" class="sistema-estado">🔄 Cargando...</div>
            <span class="header-user">👤 Usuario: Administrador</span>
            <span class="header-country">📍 República Dominicana</span>
        </div>
    </div>
    
    <div class="menu-tabs">
        <button class="tab-btn active" onclick="showTab('dashboard')">📊 Dashboard</button>
        <button class="tab-btn" onclick="showTab('clientes')">👥 Clientes</button>
        <button class="tab-btn" onclick="showTab('prestamos')">💼 Préstamos</button>
        <button class="tab-btn" onclick="showTab('pagos')">💳 Pagos</button>
        <button class="tab-btn" onclick="showTab('mora')">⚠️ Mora</button>
        <button class="tab-btn" onclick="showTab('calculadora')">🧮 Calculadora</button>
        <button class="tab-btn" onclick="showTab('reportes')">📋 Reportes</button>
        <button class="tab-btn" onclick="showTab('configuracion')">⚙️ Configuración</button>
    </div>
    
    <div class="main-content">
        <!-- Dashboard Tab -->
        <div id="tab-dashboard" class="tab-content" style="display: block;">
            <h2 class="section-title">📊 Estadísticas Generales</h2>
            <div class="stats-grid">
                <div class="stat-card green">
                    <span class="stat-title">👥 Total Clientes</span>
                    <span class="stat-value" id="total-clientes">0</span>
                </div>
                <div class="stat-card red">
                    <span class="stat-title">💼 Préstamos Activos</span>
                    <span class="stat-value" id="prestamos-activos">0</span>
                </div>
                <div class="stat-card orange">
                    <span class="stat-title">💰 Monto Total</span>
                    <span class="stat-value" id="monto-total">RD$0.00</span>
                </div>
                <div class="stat-card blue">
                    <span class="stat-title">📈 Intereses Generados</span>
                    <span class="stat-value" id="intereses-generados">RD$0.00</span>
                </div>
                <div class="stat-card gray">
                    <span class="stat-title">⚠️ Monto Atrasado</span>
                    <span class="stat-value" id="monto-atrasado">RD$0.00</span>
                </div>
                <div class="stat-card orange">
                    <span class="stat-title">📅 Cuotas Vencidas</span>
                    <span class="stat-value" id="cuotas-vencidas">0 cuotas</span>
                </div>
            </div>
            
            <div class="table-container">
                <h3>📅 Préstamos por Vencer (30 días)</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Monto Préstamo</th>
                            <th>Pago Mínimo</th>
                            <th>Vencimiento</th>
                            <th>Días Restantes</th>
                        </tr>
                    </thead>
                    <tbody id="prestamos-vencer">
                        <tr><td colspan="5" style="text-align: center; color: #666;">No hay préstamos por vencer</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Clientes Tab -->
        <div id="tab-clientes" class="tab-content">
            <h2 class="section-title">👥 Gestión de Clientes</h2>
            
            <!-- VISTA DE LISTA DE CLIENTES CON GALERÍA (Por defecto) -->
            <div id="vista-lista-clientes">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h3>Lista de Clientes</h3>
                        <p style="color: #666; margin: 5px 0 0 0;" id="contador-clientes">0 clientes registrados</p>
                    </div>
                    <button type="button" class="btn btn-success" onclick="mostrarFormularioNuevoCliente()">
                        ➕ Nuevo Cliente
                    </button>
                </div>
                
                <!-- Filtros y Búsqueda -->
                <div class="filtros-container">
                    <div class="search-box">
                        <input type="text" placeholder="Buscar clientes..." id="buscar-clientes" onkeyup="filtrarClientes()">
                    </div>
                    <select class="filtro-select" id="filtro-calificacion" onchange="filtrarClientes()">
                        <option value="">Todas las calificaciones</option>
                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4">⭐⭐⭐⭐ (4+)</option>
                        <option value="3">⭐⭐⭐ (3+)</option>
                        <option value="2">⭐⭐ (2+)</option>
                        <option value="1">⭐ (1+)</option>
                    </select>
                    <select class="filtro-select" id="filtro-con-foto" onchange="filtrarClientes()">
                        <option value="">Todos</option>
                        <option value="con-foto">Con foto</option>
                        <option value="sin-foto">Sin foto</option>
                    </select>
                </div>
                
                <!-- Galería de Clientes -->
                <div class="clientes-gallery" id="clientes-gallery">
                    <div style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">
                        No hay clientes registrados
                    </div>
                </div>
            </div>
            
            <!-- VISTA DE FORMULARIO NUEVO CLIENTE (Oculta por defecto) -->
            <div id="vista-nuevo-cliente" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Registrar Nuevo Cliente</h3>
                    <button type="button" class="btn btn-secondary" onclick="volverAListaClientes()">
                        ← Volver a Lista
                    </button>
                </div>
                
                <div class="form-container">
                    <form id="form-cliente">
                        <input type="hidden" id="cliente-id-edit" value="">
                        
                        <!-- Sección de Foto de Perfil -->
                        <div class="form-row">
                            <div class="form-group" style="text-align: center;">
                                <label>Foto de Perfil</label>
                                <div style="margin: 15px 0;">
                                    <div class="cliente-foto" id="preview-foto" style="width: 120px; height: 120px; margin: 0 auto; font-size: 3em;">
                                        👤
                                    </div>
                                    <input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="previsualizarFoto(this)">
                                    <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
                                        <button type="button" class="btn btn-info" onclick="document.getElementById('input-foto').click()">
                                            📷 Seleccionar Foto
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="eliminarFotoPrevisualizacion()">
                                            🗑️ Quitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información Básica -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre Completo *</label>
                                <input type="text" class="form-control" id="cliente-nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Documento/Cédula *</label>
                                <input type="text" class="form-control" id="cliente-documento" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono *</label>
                                <input type="text" class="form-control" id="cliente-telefono" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="cliente-email">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Dirección</label>
                                <textarea class="form-control" id="cliente-direccion" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <!-- Calificación -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Calificación Inicial</label>
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <div class="rating-input" id="rating-input">
                                        <span class="star" data-rating="1">⭐</span>
                                        <span class="star" data-rating="2">⭐</span>
                                        <span class="star" data-rating="3">⭐</span>
                                        <span class="star" data-rating="4">⭐</span>
                                        <span class="star" data-rating="5">⭐</span>
                                    </div>
                                    <span id="rating-display">0.0</span>
                                    <input type="hidden" id="cliente-calificacion" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 12px; margin-top: 20px;">
                            <button type="submit" class="btn btn-success">✅ Crear Cliente</button>
                            <button type="button" class="btn btn-secondary" onclick="volverAListaClientes()">❌ Cancelar</button>
                            <button type="button" class="btn btn-info" onclick="testearCliente()">🧪 Probar Sistema</button>
                        </div>
                    </form>
                    <div id="mensaje-cliente"></div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Detalles del Cliente -->
        <div id="modal-cliente" class="modal-cliente">
            <div class="modal-content-cliente">
                <div class="modal-header-cliente">
                    <button class="modal-close" onclick="cerrarModalCliente()">×</button>
                    <div class="modal-foto-grande" id="modal-foto-cliente">
                        👤
                    </div>
                    <h3 id="modal-nombre-cliente">Nombre del Cliente</h3>
                    <div class="cliente-rating">
                        <div class="stars" id="modal-rating-cliente">⭐⭐⭐⭐⭐</div>
                        <div class="rating-value" id="modal-rating-valor">0.0</div>
                    </div>
                </div>
                <div class="modal-body-cliente">
                    <div class="info-tabs">
                        <button class="info-tab active" onclick="cambiarTabInfo('datos')">DATOS</button>
                        <button class="info-tab" onclick="cambiarTabInfo('prestamos')">PRÉSTAMOS</button>
                    </div>
                    
                    <div id="tab-datos" class="tab-content-info active">
                        <div class="info-row">
                            <span class="info-label">Celular:</span>
                            <span class="info-value" id="modal-telefono">+1829669047</span>
                            <div class="info-action">
                                <button class="btn-mini" style="background: #25d366; color: white;" onclick="abrirWhatsApp()">💬</button>
                                <button class="btn-mini" style="background: #2196f3; color: white;" onclick="llamarCliente()">📞</button>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value" id="modal-telefono2">+1829669047</span>
                            <div class="info-action">
                                <button class="btn-mini" style="background: #2196f3; color: white;" onclick="llamarCliente()">📞</button>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dirección:</span>
                            <span class="info-value" id="modal-direccion">J76P+4PC, 23000 Higüey, República Dominicana</span>
                            <div class="info-action">
                                <button class="btn-mini" style="background: #34a853; color: white;" onclick="abrirMaps()">🗺️</button>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value" id="modal-email">cliente@email.com</span>
                            <div class="info-action">
                                <button class="btn-mini" style="background: #ea4335; color: white;" onclick="abrirEmail()">📧</button>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Calificación:</span>
                            <div class="info-value">
                                <div class="rating-input" id="modal-rating-editable">
                                    <span class="star editable" data-rating="1" onclick="actualizarCalificacion(1)">⭐</span>
                                    <span class="star editable" data-rating="2" onclick="actualizarCalificacion(2)">⭐</span>
                                    <span class="star editable" data-rating="3" onclick="actualizarCalificacion(3)">⭐</span>
                                    <span class="star editable" data-rating="4" onclick="actualizarCalificacion(4)">⭐</span>
                                    <span class="star editable" data-rating="5" onclick="actualizarCalificacion(5)">⭐</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tab-prestamos-cliente" class="tab-content-info">
                        <div id="prestamos-cliente-detalle">
                            <!-- Se cargarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu de Opciones del Cliente -->
        <div id="opciones-menu-cliente" class="opciones-menu">
            <button class="opcion-item" onclick="crearPrestamoCliente()">📄 Crear Préstamo</button>
            <button class="opcion-item" onclick="mostrarPrestamosCliente()">💼 Mostrar Préstamos</button>
            <button class="opcion-item" onclick="editarCliente()">✏️ Editar</button>
            <button class="opcion-item eliminar" onclick="eliminarCliente()">🗑️ Eliminar</button>
        </div>
        
        <!-- Préstamos Tab -->
        <div id="tab-prestamos" class="tab-content">
            <h2 class="section-title">💼 Gestión de Préstamos</h2>
            
            <!-- VISTA DE LISTA DE PRÉSTAMOS (Por defecto) -->
            <div id="vista-lista-prestamos">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Lista de Préstamos</h3>
                    <button type="button" class="btn btn-success" onclick="mostrarFormularioNuevoPrestamo()">
                        ➕ Nuevo Préstamo
                    </button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Clientes</th>
                                <th>Referencia</th>
                                <th>Balance Pendiente</th>
                                <th>Monto Cuota</th>
                                <th>Mora</th>
                                <th>Próximo Pago</th>
                                <th>Capital Pendiente</th>
                                <th>Tipo de Préstamo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="lista-prestamos">
                            <tr><td colspan="9" style="text-align: center; color: #666;">No hay préstamos registrados</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- VISTA DE FORMULARIO NUEVO PRÉSTAMO (Oculta por defecto) -->
            <div id="vista-nuevo-prestamo" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Crear Nuevo Préstamo</h3>
                    <button type="button" class="btn btn-secondary" onclick="volverAListaPrestamos()">
                        ← Volver a Lista
                    </button>
                </div>
                
                <div class="form-container">
                <h3>Crear Nuevo Préstamo</h3>
                <form id="form-prestamo">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cliente</label>
                            <select class="form-control" id="prestamo-cliente" required>
                                <option value="">Selecciona un cliente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Monto</label>
                            <input type="number" class="form-control" id="prestamo-monto" required oninput="actualizarCalculadoraEnTiempoReal()">
                        </div>
                        <div class="form-group">
                            <label>Tasa (%) 
                                <span id="indicador-tasa-auto" style="display: none; color: #4caf50; font-size: 0.8em; font-weight: bold;">
                                    ⚡ Auto
                                </span>
                            </label>
                            <input type="number" class="form-control" id="prestamo-tasa" step="0.1" required oninput="actualizarCalculadoraEnTiempoReal()">
                        </div>
                        <div class="form-group" id="grupo-plazo">
                            <label>
                                Plazo (días)
                                <span id="indicador-plazo-opcional" style="display: none; color: #ff9800; font-size: 0.8em; font-weight: bold;">
                                    📝 Opcional
                                </span>
                                <span id="indicador-plazo-auto" style="display: none; color: #4caf50; font-size: 0.8em; font-weight: bold;">
                                    🧮 Auto-calculado
                                </span>
                            </label>
                            <input type="number" class="form-control" id="prestamo-plazo" required oninput="manejarCambioPlazoManual(); actualizarCalculadoraEnTiempoReal()">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" class="form-control" id="prestamo-fecha" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Préstamo</label>
                            <select class="form-control" id="prestamo-tipo" onchange="actualizarTasaAutomatica(); toggleCamposOpcionales()">
                                <option value="interes">Solo Interés</option>
                                <option value="cuota">Interés Fijo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Frecuencia Pago</label>
                            <select class="form-control" id="prestamo-frecuencia" onchange="calcularDiasAutomatico(); actualizarTasaAutomatica()">
                                <option value="quincenal">Quincenal</option>
                                <option value="15y30">15 y 30</option>
                                <option value="mensual">Mensual</option>
                                <option value="semanal">Semanal</option>
                            </select>
                        </div>
                        <div class="form-group" id="grupo-cuotas">
                            <label>
                                Número de Cuotas
                                <span id="indicador-cuotas-opcional" style="display: none; color: #ff9800; font-size: 0.8em; font-weight: bold;">
                                    📝 Opcional
                                </span>
                            </label>
                            <input type="number" class="form-control" id="prestamo-cuotas" required oninput="calcularDiasAutomatico(); actualizarCalculadoraEnTiempoReal()">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <button type="submit" class="btn btn-success">✅ Crear Préstamo</button>
                        <button type="button" class="btn btn-secondary" onclick="volverAListaPrestamos()">❌ Cancelar</button>
                        <button type="button" class="btn btn-info" onclick="testearPrestamo()">🧪 Probar Sistema</button>
                        <button type="button" class="btn btn-warning" onclick="probarAutocompletado()">⚡ Probar Autocompletado</button>
                    </div>
                </form>
                <div id="mensaje-prestamo"></div>
                
                <!-- INFORMACIÓN SOBRE MODALIDADES -->
                <div id="info-modalidad" style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8; display: none;">
                    <div id="info-solo-interes" style="display: none;">
                        <h6 style="margin: 0 0 8px 0; color: #e53935;">🔴 Modalidad Solo Interés</h6>
                        <ul style="margin: 0; color: #666; font-size: 13px;">
                            <li>✅ Los campos <strong>Plazo</strong> y <strong>Cuotas</strong> son opcionales</li>
                            <li>💰 Se paga solo el interés cada período</li>
                            <li>🔄 El capital permanece igual hasta que se decida cancelar</li>
                            <li>📈 Ideal para préstamos flexibles sin plazo fijo</li>
                        </ul>
                    </div>
                    <div id="info-cuota-fija" style="display: none;">
                        <h6 style="margin: 0 0 8px 0; color: #2196f3;">📊 Modalidad Cuota Fija</h6>
                        <ul style="margin: 0; color: #666; font-size: 13px;">
                            <li>⚠️ Los campos <strong>Plazo</strong> y <strong>Cuotas</strong> son obligatorios</li>
                            <li>💳 Cuota fija durante todo el préstamo</li>
                            <li>📅 Plazo definido desde el inicio</li>
                            <li>📊 Ideal para préstamos con pagos predecibles</li>
                        </ul>
                    </div>
                </div>
                
                <!-- CALCULADORA COMPACTA Y ELEGANTE -->
                <div class="calculadora-container" style="margin-top: 15px; padding: 12px; background: #e8f5e8; border: 1px solid #4caf50; border-radius: 8px; color: #2e7d32;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <span style="font-size: 1.1em;">🧮</span>
                        <strong style="font-size: 0.95em;">Calculadora de Préstamos</strong>
                    </div>
                    
                    <div class="calculo-resultado" id="resultado-calculo" style="background: rgba(255,255,255,0.7); padding: 10px; border-radius: 6px; font-size: 0.9em;">
                        <div style="text-align: center; color: #666; font-style: italic; font-size: 0.85em;">
                            📊 Completa los campos para ver los cálculos automáticos...
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
        <!-- Pagos Tab -->
        <div id="tab-pagos" class="tab-content">
            <h2>💳 Gestión de Pagos</h2>
            
            <div class="form-container">
                <h3>Registrar Pago</h3>
                <form id="form-pago">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Préstamo</label>
                            <select class="form-control" id="pago-prestamo" required>
                                <option value="">Selecciona un préstamo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Monto Pago</label>
                            <input type="number" class="form-control" id="pago-monto" step="0.01" required>
                        </div>
                        
                        <!-- NUEVO: Selector de cuotas para préstamos atrasados -->
                        <div id="selector-cuotas" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                            <h4 style="color: #d32f2f; margin-bottom: 15px;">🚨 Préstamo con Cuotas Vencidas</h4>
                            <div class="form-group">
                                <label><strong>Selecciona cuántas cuotas pagar:</strong></label>
                                <select class="form-control" id="cuotas-a-pagar" onchange="actualizarMontoCuotas()">
                                    <option value="">Selecciona...</option>
                                </select>
                            </div>
                            <div id="desglose-cuotas" style="margin-top: 15px;"></div>
                            <div id="total-a-pagar" style="margin-top: 15px; padding: 10px; background: #e3f2fd; border-radius: 5px; font-weight: bold;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" class="form-control" id="pago-fecha" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo Pago</label>
                            <select class="form-control" id="pago-tipo">
                                <option value="abono">Abono</option>
                                <option value="cuota">Cuota Completa</option>
                                <option value="capital">Solo Capital</option>
                                <option value="interes">Solo Interés</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" class="form-control" id="pago-observaciones">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <button type="submit" class="btn btn-success">✅ Registrar Pago</button>
                        <button type="button" class="btn btn-danger" onclick="eliminarPago()">🗑️ Eliminar Pago</button>
                        <button type="button" class="btn btn-primary" onclick="simularPago()">🔍 Simular Pago</button>
                        <button type="button" class="btn btn-info" onclick="generarFacturaManual()">📄 Generar Factura</button>
                    </div>
                </form>
                <div id="mensaje-pago"></div>
            </div>
            
            <div class="table-container">
                <h3>Historial de Pagos</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Préstamo</th>
                            <th>Cliente</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-pagos">
                        <tr><td colspan="7" style="text-align: center; color: #666;">No hay pagos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Mora Tab -->
        <div id="tab-mora" class="tab-content">
            <h2>⚠️ Gestión de Mora</h2>
            
            <div class="mora-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <!-- Configuración de Mora -->
                <div class="mora-config">
                    <h3>⚙️ Configuración de Mora</h3>
                    <div class="form-card">
                        <div class="form-group">
                            <label>📅 Días de Gracia</label>
                            <input type="number" class="form-control" id="mora-dias-gracia" value="3" min="0" max="30">
                            <small>Días sin penalidad después del vencimiento</small>
                        </div>
                        
                        <div class="form-group">
                            <label>📈 Tasa de Mora (%)</label>
                            <input type="number" class="form-control" id="mora-tasa" value="5" step="0.1" min="0" max="100">
                            <small>Porcentaje aplicado sobre la cuota pendiente</small>
                        </div>
                        
                        <div class="form-group">
                            <label>🎯 Tipo de Aplicación</label>
                            <select class="form-control" id="mora-tipo">
                                <option value="cuota">Sobre la cuota pendiente</option>
                                <option value="capital">Sobre el capital pendiente</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="guardarConfiguracionMora()">
                            💾 Guardar Configuración
                        </button>
                    </div>
                </div>
                
                <!-- Calculadora de Mora -->
                <div class="mora-calculator">
                    <h3>🧮 Calculadora de Mora</h3>
                    <div class="form-card">
                        <div class="form-group">
                            <label>💰 Cuota Pendiente</label>
                            <input type="number" class="form-control" id="calc-mora-cuota" step="0.01">
                        </div>
                        
                        <div class="form-group">
                            <label>📅 Días de Retraso</label>
                            <input type="number" class="form-control" id="calc-mora-dias" min="0">
                        </div>
                        
                        <button type="button" class="btn btn-success" onclick="calcularMora()">
                            🔢 Calcular Mora
                        </button>
                        
                        <div id="resultado-mora" class="mt-3"></div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Préstamos con Mora -->
            <div class="mora-list">
                <h3>📋 Préstamos con Mora Pendiente</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cliente / Tipo</th>
                                <th>Préstamo / Capital</th>
                                <th>Cuotas Atrasadas</th>
                                <th>Monto Pendiente</th>
                                <th>Mora Acumulada</th>
                                <th>Vencimiento Más Antiguo</th>
                                <th>Total a Pagar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="lista-prestamos-mora">
                            <tr><td colspan="8" style="text-align: center; color: #666;">No hay préstamos con mora</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Calculadora Tab -->
        <div id="tab-calculadora" class="tab-content">
            <h2>🧮 Calculadora de Préstamos</h2>
            
            <div class="calculator-grid">
                <div class="calculator-input">
                    <h3>📊 Calculadora de Préstamos para Clientes</h3>
                    <form id="form-calculadora">
                        <div class="form-group">
                            <label>💰 Monto del Préstamo</label>
                            <input type="number" class="form-control" id="calc-monto" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>📈 Tasa de Interés (%)</label>
                            <input type="number" class="form-control" id="calc-tasa" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label>📅 Frecuencia de Pago</label>
                            <select class="form-control" id="calc-frecuencia" onchange="autocompletarTasaCalculadora()">
                                <option value="quincenal">Quincenal</option>
                                <option value="15y30">15 y 30</option>
                                <option value="mensual">Mensual</option>
                                <option value="semanal">Semanal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>🔢 Número de Cuotas</label>
                            <input type="number" class="form-control" id="calc-cuotas" required>
                        </div>
                        <div class="form-group">
                            <label>⚙️ Tipo de Préstamo</label>
                            <div>
                                <label><input type="radio" name="calc-tipo" value="interes" onchange="autocompletarTasaCalculadora()"> 🔴 INTERÉS (solo intereses)</label><br>
                                <label><input type="radio" name="calc-tipo" value="cuota" checked onchange="autocompletarTasaCalculadora()"> 📊 INTERÉS FIJO (cuota fija)</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="calcularPrestamo()">📊 CALCULAR PRÉSTAMO</button>
                        
                        <!-- BOTONES DE PRUEBA RÁPIDA -->
                        <div style="margin-top: 12px; border-top: 1px solid #eee; padding-top: 12px;">
                            <small style="color: #666;">🚀 Pruebas Rápidas:</small><br>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="probarInteresRapido()">🔴 Probar Interés</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="probarCuotaRapido()">📊 Probar Cuota</button>
                        </div>
                    </form>
                </div>
                
                <div class="calculator-result">
                    <h3>📋 Información Detallada para el Cliente</h3>
                    <div id="resultado-calculadora">
                        <div style="text-align: center; color: #666; padding: 40px;">
                            Ingresa los datos y presiona "Calcular Préstamo" para ver los resultados
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reportes Tab -->
        <div id="tab-reportes" class="tab-content">
            <h2 class="section-title">📋 Reportes y Estadísticas</h2>
            
            <div class="form-container">
                <h3>Generar Reportes</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #666;">Los reportes serán implementados en la siguiente versión del sistema modular.</p>
                </div>
            </div>
        </div>
        
        <!-- Configuración Tab -->
        <div id="tab-configuracion" class="tab-content">
            <h2>⚙️ Configuración del Sistema</h2>
            
            <div class="form-container">
                <h3>📊 Tasas de Interés Predeterminadas</h3>
                <p style="color: #666;">Configura las tasas que se aplicarán automáticamente según la modalidad y frecuencia de pago:</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- CONFIGURACIÓN MODALIDAD INTERÉS -->
                    <div style="border: 2px solid #e53935; border-radius: 8px; padding: 16px; background: #ffebee;">
                        <h4 style="color: #e53935; margin: 0 0 16px 0;">🔴 Modalidad INTERÉS (Solo Interés)</h4>
                        <form id="form-config-interes">
                            <div class="form-group">
                                <label>🗓️ Tasa Semanal (%)</label>
                                <input type="number" class="form-control" id="config-interes-semanal" value="5" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>📅 Tasa Quincenal (%)</label>
                                <input type="number" class="form-control" id="config-interes-quincenal" value="10" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>� Tasa 15 y 30 (%)</label>
                                <input type="number" class="form-control" id="config-interes-15y30" value="10" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>�📆 Tasa Mensual (%)</label>
                                <input type="number" class="form-control" id="config-interes-mensual" value="20" step="0.1" required>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">✅ Guardar Interés</button>
                        </form>
                    </div>
                    
                    <!-- CONFIGURACIÓN MODALIDAD CUOTA -->
                    <div style="border: 2px solid #2196f3; border-radius: 8px; padding: 16px; background: #e3f2fd;">
                        <h4 style="color: #2196f3; margin: 0 0 16px 0;">📊 Modalidad CUOTA (Interés Simple)</h4>
                        <form id="form-config-cuota">
                            <div class="form-group">
                                <label>🗓️ Tasa Semanal (%)</label>
                                <input type="number" class="form-control" id="config-cuota-semanal" value="5" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>📅 Tasa Quincenal (%)</label>
                                <input type="number" class="form-control" id="config-cuota-quincenal" value="10" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>� Tasa 15 y 30 (%)</label>
                                <input type="number" class="form-control" id="config-cuota-15y30" value="10" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label>�📆 Tasa Mensual (%)</label>
                                <input type="number" class="form-control" id="config-cuota-mensual" value="20" step="0.1" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">✅ Guardar Cuota</button>
                        </form>
                    </div>
                </div>
                
                <!-- BOTONES GENERALES -->
                <div style="display: flex; gap: 12px; margin-top: 20px; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="restaurarConfiguracionPredeterminada()">🔄 Restaurar Todo</button>
                    <button type="button" class="btn btn-info" onclick="probarConfiguracion()">🧪 Probar Configuración</button>
                    <button type="button" class="btn btn-success" onclick="exportarConfiguracion()">📤 Exportar Config</button>
                </div>
                
                <div id="mensaje-configuracion" style="margin-top: 16px;"></div>
                
                <!-- RESUMEN VISUAL DE TASAS AUTOMÁTICAS -->
                <div class="info-container" style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, #e8f5e8, #f1f8e9); border-radius: 8px; border-left: 4px solid #4caf50;">
                    <h4 style="margin: 0 0 12px 0; color: #2e7d32;">⚡ Tasas de Autocompletado Actuales</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <h5 style="color: #e53935; margin-bottom: 8px;">🔴 Solo Interés:</h5>
                            <div style="font-size: 14px; line-height: 1.8;">
                                <div>🗓️ Semanal: <strong id="resumen-interes-semanal">5%</strong></div>
                                <div>📅 Quincenal: <strong id="resumen-interes-quincenal">10%</strong></div>
                                <div>📆 Mensual: <strong id="resumen-interes-mensual">20%</strong></div>
                            </div>
                        </div>
                        <div>
                            <h5 style="color: #2196f3; margin-bottom: 8px;">📊 Cuota Fija:</h5>
                            <div style="font-size: 14px; line-height: 1.8;">
                                <div>🗓️ Semanal: <strong id="resumen-cuota-semanal">5%</strong></div>
                                <div>📅 Quincenal: <strong id="resumen-cuota-quincenal">10%</strong></div>
                                <div>📆 Mensual: <strong id="resumen-cuota-mensual">20%</strong></div>
                            </div>
                        </div>
                    </div>
                    <hr style="margin: 12px 0;">
                    <p style="margin: 0; color: #2e7d32; font-size: 14px; text-align: center;">
                        <strong>💡 Estas tasas se aplicarán automáticamente al crear préstamos según la frecuencia y tipo seleccionado</strong>
                    </p>
                </div>
            </div>
            
            <div class="info-container" style="margin-top: 20px; padding: 16px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8;">
                <h4 style="margin: 0 0 12px 0; color: #17a2b8;">💡 Diferencias entre Modalidades</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <h5 style="color: #e53935;">🔴 Modalidad INTERÉS:</h5>
                        <ul style="margin: 0; color: #666; font-size: 14px;">
                            <li>Pago mínimo = Solo interés</li>
                            <li>Exceso reduce el capital</li>
                            <li>Interés sobre capital restante</li>
                            <li>Tasas más altas (flexible)</li>
                        </ul>
                    </div>
                    <div>
                        <h5 style="color: #2196f3;">📊 Modalidad CUOTA:</h5>
                        <ul style="margin: 0; color: #666; font-size: 14px;">
                            <li>Cuota fija durante todo el préstamo</li>
                            <li>Interés simple (no capitaliza)</li>
                            <li>Pagos predecibles</li>
                            <li>Tasas más bajas (fijo)</li>
                        </ul>
                    </div>
                </div>
                <hr style="margin: 12px 0;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <strong>Autocompletado:</strong> Al cambiar frecuencia o modalidad, la tasa se autocompleta según la configuración correspondiente.
                </p>
                
                <hr style="margin: 12px 0;">
                <h5 style="color: #ff9800; margin: 8px 0;">📅 Diferencias entre Frecuencias de Pago:</h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div style="background: #fff3e0; padding: 8px; border-radius: 4px; border-left: 3px solid #ff9800;">
                        <strong>🔄 Quincenal:</strong><br>
                        • Pago cada 15 días desde la fecha de creación<br>
                        • Fechas variables según día de inicio
                    </div>
                    <div style="background: #e8f5e8; padding: 8px; border-radius: 4px; border-left: 3px solid #4caf50;">
                        <strong>📊 15 y 30:</strong><br>
                        • Pagos fijos los días 15 y 30 de cada mes<br>
                        • Fechas siempre iguales independiente del inicio
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE RESPUESTA -->
    <div id="modalRespuesta" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span>✅ Operación Exitosa</span>
                <button type="button" class="close" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="contenidoModal">
                    <div class="loading"></div>
                    <p>Procesando solicitud...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let clientes = [];
        let prestamos = [];
        let pagos = [];
        
        // Función para actualizar estado del sistema
        function actualizarEstadoSistema(estado, mensaje) {
            const indicador = document.getElementById('sistema-estado');
            if (!indicador) return;
            
            // Remover todas las clases de estado
            indicador.classList.remove('cargando', 'listo', 'error');
            
            // Agregar nueva clase y mensaje
            indicador.classList.add(estado);
            indicador.textContent = mensaje;
        }
        
        // Función de verificación del sistema
        function verificarSistema() {
            console.log('🔧 Verificando estado del sistema...');
            actualizarEstadoSistema('cargando', '🔧 Verificando...');
            
            // Verificar elementos DOM esenciales
            const elementosEsenciales = [
                'tab-dashboard',
                'tab-clientes', 
                'tab-prestamos',
                'tab-pagos'
            ];
            
            let errores = [];
            
            elementosEsenciales.forEach(id => {
                if (!document.getElementById(id)) {
                    errores.push(`Elemento ${id} no encontrado`);
                }
            });
            
            // Verificar funciones esenciales
            const funcionesEsenciales = [
                'showTab',
                'cargarClientes',
                'cargarPrestamos', 
                'cargarPagos'
            ];
            
            funcionesEsenciales.forEach(func => {
                if (typeof window[func] !== 'function') {
                    errores.push(`Función ${func} no definida`);
                }
            });
            
            if (errores.length > 0) {
                console.error('❌ Errores encontrados:', errores);
                actualizarEstadoSistema('error', '❌ Errores detectados');
                return false;
            } else {
                console.log('✅ Sistema verificado correctamente');
                actualizarEstadoSistema('listo', '✅ Sistema listo');
                return true;
            }
        }
        
        // Función principal de navegación
        function showTab(tabName) {
            try {
                console.log('Cambiando a tab:', tabName);
                
                // Remover clase activa de todos los botones
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                
                // Ocultar todos los contenidos
                document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
                
                // Activar el botón correspondiente
                const buttons = document.querySelectorAll('.tab-btn');
                buttons.forEach(btn => {
                    if (btn.getAttribute('onclick') === `showTab('${tabName}')`) {
                        btn.classList.add('active');
                    }
                });
                
                // Mostrar el contenido correspondiente
                const tabContent = document.getElementById(`tab-${tabName}`);
                if (tabContent) {
                    tabContent.style.display = 'block';
                } else {
                    console.error('Tab no encontrado:', `tab-${tabName}`);
                }
                
                // Cargar datos específicos del tab
                switch(tabName) {
                    case 'dashboard':
                        actualizarDashboard();
                        break;
                    case 'clientes':
                        cargarClientes();
                        // Asegurar que se muestre la vista de lista por defecto
                        volverAListaClientes();
                        break;
                    case 'prestamos':
                        cargarPrestamos();
                        // Asegurar que se muestre la vista de lista por defecto
                        volverAListaPrestamos();
                        break;
                    case 'pagos':
                        cargarPagos();
                        break;
                }
                
            } catch (error) {
                console.error('Error en showTab:', error);
                alert('Error en navegación: ' + error.message);
            }
        }
        
        // Función para llamar al backend modular
        async function llamarBackend(modulo, accion, datos = {}) {
            try {
                console.log(`Llamando al backend: ${modulo}/${accion}`, datos);
                
                const actionName = `${modulo}_${accion}`;
                const response = await fetch(`api_simple.php?action=${actionName}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(datos)
                });
                
                const resultado = await response.json();
                console.log(`Resultado de ${modulo}/${accion}:`, resultado);
                
                return resultado;
                
            } catch (error) {
                console.error('Error en llamada al backend:', error);
                return { success: false, error: 'Error de conexión: ' + error.message };
            }
        }
        
        // Funciones del modal
        function abrirModal() {
            document.getElementById('modalRespuesta').style.display = 'block';
            document.getElementById('contenidoModal').innerHTML = `
                <div class="loading"></div>
                <p>Procesando solicitud...</p>
            `;
        }
        
        function cerrarModal() {
            document.getElementById('modalRespuesta').style.display = 'none';
        }
        
        function mostrarResultadoEnModal(titulo, mensaje) {
            const contenido = document.getElementById('contenidoModal');
            
            // Si se pasa un solo parámetro que es un objeto (formato anterior)
            if (typeof titulo === 'object' && titulo !== null && mensaje === undefined) {
                const resultado = titulo;
                
                if (resultado.success) {
                    contenido.innerHTML = `
                        <div style="color: #4caf50; text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                            <h3 style="margin: 0; color: #2e7d32;">¡Operación Exitosa!</h3>
                        </div>
                        <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border: 1px solid #4caf50;">
                            <p style="margin: 0; font-size: 16px; text-align: center; color: #2e7d32;">
                                ${resultado.mensaje || resultado.message || 'La operación se completó correctamente'}
                            </p>
                        </div>
                    `;
                } else {
                    contenido.innerHTML = `
                        <div style="color: #f44336; text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
                            <h3 style="margin: 0; color: #c62828;">Error en la Operación</h3>
                        </div>
                        <div style="background: #ffebee; padding: 20px; border-radius: 8px; border: 1px solid #f44336;">
                            <p style="margin: 0; font-size: 16px; text-align: center; color: #c62828;">
                                ${resultado.error || resultado.message || 'Error desconocido'}
                            </p>
                        </div>
                    `;
                }
            } else {
                // Nuevo formato con título y mensaje separados
                const esExito = titulo && titulo.includes('✅');
                
                if (esExito) {
                    contenido.innerHTML = `
                        <div style="color: #4caf50; text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                            <h3 style="margin: 0; color: #2e7d32;">${titulo}</h3>
                        </div>
                        <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border: 1px solid #4caf50;">
                            <p style="margin: 0; font-size: 16px; text-align: center; color: #2e7d32;">
                                ${mensaje}
                            </p>
                        </div>
                    `;
                } else {
                    contenido.innerHTML = `
                        <div style="color: #f44336; text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
                            <h3 style="margin: 0; color: #c62828;">${titulo}</h3>
                        </div>
                        <div style="background: #ffebee; padding: 20px; border-radius: 8px; border: 1px solid #f44336;">
                            <p style="margin: 0; font-size: 16px; text-align: center; color: #c62828;">
                                ${mensaje}
                            </p>
                        </div>
                    `;
                }
            }
        }
        
        // Función para mostrar mensajes
        function mostrarMensaje(contenedor, mensaje, tipo = 'success') {
            const div = document.getElementById(contenedor);
            if (div) {
                div.innerHTML = `<div class="message ${tipo}">${mensaje}</div>`;
                setTimeout(() => div.innerHTML = '', 3000);
            }
        }
        
        // =================== MÓDULO CLIENTES AVANZADO CON FOTOS ===================
        
        // Variables globales para clientes (usando las variables ya declaradas arriba)
        let clienteSeleccionado = null;
        let clienteModalActual = null;
        let fotoSeleccionada = null;
        
        // Manejar envío del formulario de cliente
        document.getElementById('form-cliente').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const isEdit = document.getElementById('cliente-id-edit').value !== '';
            
            const clienteData = {
                nombre: document.getElementById('cliente-nombre').value,
                cedula: document.getElementById('cliente-documento').value,
                telefono: document.getElementById('cliente-telefono').value,
                email: document.getElementById('cliente-email').value,
                direccion: document.getElementById('cliente-direccion').value,
                calificacion: parseInt(document.querySelector('.rating-input').dataset.rating || '0')
            };
            
            try {
                abrirModal();
                
                let resultado;
                
                if (isEdit) {
                    // Modo edición
                    clienteData.id = document.getElementById('cliente-id-edit').value;
                    const response = await fetch('api_modular.php?action=cliente_editar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(clienteData)
                    });
                    resultado = await response.json();
                } else {
                    // Modo creación
                    const response = await fetch('api_modular.php?action=cliente_crear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(clienteData)
                    });
                    resultado = await response.json();
                }
                
                console.log('Resultado cliente:', resultado);
                
                if (resultado.exito) {
                    const clienteId = isEdit ? clienteData.id : resultado.datos.id;
                    
                    // Subir foto si se seleccionó una
                    if (fotoSeleccionada) {
                        try {
                            await subirFoto(clienteId, fotoSeleccionada);
                        } catch (fotoError) {
                            console.error('Error subiendo foto:', fotoError);
                        }
                    }
                    
                    // Actualizar calificación si se cambió
                    const rating = parseInt(document.getElementById('cliente-calificacion').value);
                    if (rating > 0) {
                        try {
                            await fetch('api_simple.php?action=cliente_actualizar_calificacion', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    id: clienteId,
                                    calificacion: rating
                                })
                            });
                        } catch (ratingError) {
                            console.error('Error actualizando calificación:', ratingError);
                        }
                    }
                    
                    cerrarModal();
                    mostrarResultadoEnModal('✅ Operación Exitosa', 
                        isEdit ? 'Cliente actualizado correctamente' : 'Cliente creado correctamente');
                    
                    // Volver a la lista después de un delay
                    setTimeout(() => {
                        volverAListaClientes();
                    }, 1500);
                    
                } else {
                    cerrarModal();
                    mostrarResultadoEnModal('❌ Error', 
                        'Error al ' + (isEdit ? 'actualizar' : 'crear') + ' cliente: ' + (resultado.error || 'Error desconocido'));
                }
                
            } catch (error) {
                cerrarModal();
                console.error('Error:', error);
                mostrarResultadoEnModal('❌ Error', 'Error de conexión');
            }
        });
        
        // Función para mostrar formulario de nuevo cliente
        function mostrarFormularioNuevoCliente() {
            document.getElementById('vista-lista-clientes').style.display = 'none';
            document.getElementById('vista-nuevo-cliente').style.display = 'block';
            
            // Limpiar formulario
            document.getElementById('form-cliente').reset();
            document.getElementById('cliente-id-edit').value = '';
            resetearFormularioCliente();
            
            console.log('📋 Mostrando formulario nuevo cliente');
        }
        
        // Función para volver a lista de clientes
        function volverAListaClientes() {
            document.getElementById('vista-nuevo-cliente').style.display = 'none';
            document.getElementById('vista-lista-clientes').style.display = 'block';
            
            resetearFormularioCliente();
            cargarClientes(); // Recargar la lista
            
            console.log('📋 Volviendo a lista de clientes');
        }
        
        // Resetear formulario de cliente
        function resetearFormularioCliente() {
            document.getElementById('form-cliente').reset();
            document.getElementById('cliente-id-edit').value = '';
            
            // Resetear elementos específicos del nuevo sistema
            if (document.getElementById('preview-foto')) {
                document.getElementById('preview-foto').innerHTML = '👤';
            }
            if (document.getElementById('rating-display')) {
                document.getElementById('rating-display').textContent = '0.0';
            }
            if (document.getElementById('cliente-calificacion')) {
                document.getElementById('cliente-calificacion').value = '0';
            }
            fotoSeleccionada = null;
            
            // Resetear estrellas
            document.querySelectorAll('#rating-input .star').forEach(star => {
                star.style.color = '#ddd';
            });
            
            // Limpiar mensajes
            const mensajeDiv = document.getElementById('mensaje-cliente');
            if (mensajeDiv) {
                mensajeDiv.innerHTML = '';
            }
        }
        
        // Cargar clientes en formato galería
        async function cargarClientes() {
            try {
                console.log('🔄 Cargando clientes...');
                const resultado = await fetch('api_modular.php?action=clientes_listar');
                const data = await resultado.json();
                
                console.log('📡 Respuesta API clientes:', data);
                
                if (data.exito) {
                    clientes = data.datos || [];
                    console.log('✅ Clientes cargados:', clientes);
                    
                    // Si no hay clientes, crear datos de prueba
                    if (clientes.length === 0) {
                        console.log('📝 No hay clientes, creando datos de prueba...');
                        clientes = crearClientesPrueba();
                    }
                    
                    mostrarClientesEnGaleria(clientes);
                    actualizarContadorClientes();
                } else {
                    console.error('❌ Error cargando clientes:', data.mensaje);
                    console.log('📝 Creando datos de prueba...');
                    clientes = crearClientesPrueba();
                    mostrarClientesEnGaleria(clientes);
                }
            } catch (error) {
                console.error('❌ Error cargando clientes:', error);
                console.log('📝 Creando datos de prueba por error...');
                clientes = crearClientesPrueba();
                mostrarClientesEnGaleria(clientes);
            }
        }
        
        // Crear clientes de prueba para testing
        function crearClientesPrueba() {
            return [
                {
                    id: 'CLI001',
                    cliente_id: 'CLI001',
                    nombre: 'María Elena Rodríguez',
                    telefono: '809-555-1234',
                    documento: '001-0123456-7',
                    email: 'maria.rodriguez@email.com',
                    direccion: 'Calle Principal #123, Santo Domingo',
                    calificacion: 5.0,
                    activo: true,
                    foto_perfil: null
                },
                {
                    id: 'CLI002',
                    cliente_id: 'CLI002',
                    nombre: 'Juan Carlos Méndez',
                    telefono: '809-555-5678',
                    documento: '001-9876543-2',
                    email: 'juan.mendez@email.com',
                    direccion: 'Av. Independencia #456, Santiago',
                    calificacion: 4.5,
                    activo: true,
                    foto_perfil: null
                },
                {
                    id: 'CLI003',
                    cliente_id: 'CLI003',
                    nombre: 'Ana Patricia Jiménez',
                    telefono: '809-555-9012',
                    documento: '001-5555555-5',
                    email: 'ana.jimenez@email.com',
                    direccion: 'Calle Duarte #789, La Vega',
                    calificacion: 3.8,
                    activo: true,
                    foto_perfil: null
                }
            ];
        }
        
        // Mostrar clientes en formato galería
        function mostrarClientesEnGaleria(clientesData) {
            const gallery = document.getElementById('clientes-gallery');
            
            if (!gallery) {
                console.error('Gallery element not found');
                return;
            }
            
            if (clientesData.length === 0) {
                gallery.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">
                        <div style="font-size: 3em; margin-bottom: 20px;">👥</div>
                        <h3>No hay clientes registrados</h3>
                        <p>¡Comienza creando tu primer cliente!</p>
                        <button class="btn btn-success" onclick="mostrarFormularioNuevoCliente()">
                            ➕ Crear Primer Cliente
                        </button>
                    </div>
                `;
                return;
            }
            
            gallery.innerHTML = clientesData.map(cliente => {
                const rating = parseFloat(cliente.calificacion || 0);
                const stars = generarEstrellas(rating);
                const statusClass = cliente.activo !== false ? 'activo' : 'inactivo';
                
                // Determinar la foto
                let fotoHtml = '👤';
                if (cliente.foto_perfil) {
                    fotoHtml = `<img src="${cliente.foto_perfil}" alt="${cliente.nombre}" onerror="this.parentElement.innerHTML='👤'">`;
                }
                
                // Asegurar que el cliente tenga un ID válido
                const clienteId = cliente.id || cliente.cliente_id || `CLI_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                console.log('🔍 Generando tarjeta para cliente:', { id: clienteId, nombre: cliente.nombre });
                
                return `
                    <div class="cliente-card" onclick="abrirModalCliente('${clienteId}')" data-cliente-id="${clienteId}">
                        <div class="cliente-foto-container">
                            <div class="cliente-foto">
                                ${fotoHtml}
                            </div>
                            <div class="cliente-status ${statusClass}"></div>
                        </div>
                        <div class="cliente-info">
                            <div class="cliente-nombre">${cliente.nombre || 'Sin nombre'}</div>
                            <div class="cliente-detalles">
                                ${cliente.telefono || 'Sin teléfono'}<br>
                                ${cliente.documento || 'Sin documento'}
                            </div>
                            <div class="cliente-rating" onclick="event.stopPropagation(); mostrarRatingModal('${clienteId}')">
                                <div class="stars rating-interactive">${stars}</div>
                                <div class="rating-value">${rating.toFixed(1)}</div>
                            </div>
                            <div class="cliente-stats">
                                <div class="cliente-stat">
                                    <span class="cliente-stat-value">RD$37,600</span>
                                    <span class="cliente-stat-label">Capital</span>
                                </div>
                                <div class="cliente-stat">
                                    <span class="cliente-stat-value">RD$1,050</span>
                                    <span class="cliente-stat-label">Balance</span>
                                </div>
                            </div>
                            <div class="cliente-acciones">
                                <button class="btn-accion btn-ver" onclick="event.stopPropagation(); abrirModalCliente('${clienteId}')">
                                    👁️ Ver
                                </button>
                                <button class="btn-accion btn-opciones" onclick="event.stopPropagation(); mostrarOpcionesCliente('${clienteId}', event)">
                                    ⋮ OPCIONES
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Generar estrellas de calificación
        function generarEstrellas(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    stars += '⭐';
                } else if (i - 0.5 <= rating) {
                    stars += '⭐';
                } else {
                    stars += '☆';
                }
            }
            return stars;
        }
        
        // Actualizar contador de clientes
        function actualizarContadorClientes() {
            const contador = document.getElementById('contador-clientes');
            if (contador) {
                const total = clientes.length;
                const conFoto = clientes.filter(c => c.foto_perfil).length;
                contador.textContent = `${total} clientes registrados (${conFoto} con foto)`;
            }
        }
        
        // Filtrar clientes
        function filtrarClientes() {
            const busqueda = document.getElementById('buscar-clientes')?.value.toLowerCase() || '';
            const filtroCalificacion = document.getElementById('filtro-calificacion')?.value || '';
            const filtroFoto = document.getElementById('filtro-con-foto')?.value || '';
            
            let clientesFiltrados = clientes.filter(cliente => {
                // Filtro de búsqueda
                const coincideBusqueda = !busqueda || 
                    cliente.nombre.toLowerCase().includes(busqueda) ||
                    cliente.telefono.includes(busqueda) ||
                    cliente.documento.includes(busqueda);
                
                // Filtro de calificación
                let coincideCalificacion = true;
                if (filtroCalificacion) {
                    const calificacion = parseFloat(cliente.calificacion || 0);
                    coincideCalificacion = calificacion >= parseFloat(filtroCalificacion);
                }
                
                // Filtro de foto
                let coincideFoto = true;
                if (filtroFoto === 'con-foto') {
                    coincideFoto = !!cliente.foto_perfil;
                } else if (filtroFoto === 'sin-foto') {
                    coincideFoto = !cliente.foto_perfil;
                }
                
                return coincideBusqueda && coincideCalificacion && coincideFoto;
            });
            
            mostrarClientesEnGaleria(clientesFiltrados);
        }
        
        // Previsualizar foto antes de subir
        function previsualizarFoto(input) {
            if (input.files && input.files[0]) {
                const archivo = input.files[0];
                
                // Validar tipo de archivo
                if (!archivo.type.match('image.*')) {
                    alert('Por favor selecciona solo archivos de imagen');
                    input.value = '';
                    return;
                }
                
                // Validar tamaño (máximo 5MB)
                if (archivo.size > 5 * 1024 * 1024) {
                    alert('La imagen es demasiado grande. Máximo 5MB');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Mostrar modal de recorte
                    mostrarModalRecorte(e.target.result, archivo);
                };
                
                reader.readAsDataURL(archivo);
            }
        }
        
        // Modal de recorte de imagen
        function mostrarModalRecorte(imagenDataUrl, archivo) {
            const modal = document.createElement('div');
            modal.id = 'modal-recorte';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 10px; max-width: 500px; max-height: 90vh; overflow-y: auto;">
                    <h3 style="margin-top: 0;">📷 Ajustar Foto de Perfil</h3>
                    <p style="color: #666; margin-bottom: 20px;">Haz clic y arrastra para posicionar la imagen como desees</p>
                    
                    <div style="width: 300px; height: 300px; border: 2px dashed #ccc; margin: 0 auto 20px auto; overflow: hidden; position: relative; border-radius: 10px;">
                        <img id="imagen-recorte" src="${imagenDataUrl}" style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            cursor: move;
                            transition: transform 0.1s;
                        ">
                    </div>
                    
                    <div style="margin-bottom: 20px; text-align: center;">
                        <label style="display: block; margin-bottom: 10px;">Zoom:</label>
                        <input type="range" id="zoom-slider" min="100" max="300" value="100" style="width: 200px;">
                        <span id="zoom-value">100%</span>
                    </div>
                    
                    <div style="text-align: center; display: flex; gap: 10px; justify-content: center;">
                        <button onclick="confirmarRecorte()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                            ✅ Usar Esta Foto
                        </button>
                        <button onclick="cancelarRecorte()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                            ❌ Cancelar
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Variables para el arrastre
            let isDragging = false;
            let startX, startY, currentX = 0, currentY = 0;
            
            const imagen = document.getElementById('imagen-recorte');
            const zoomSlider = document.getElementById('zoom-slider');
            const zoomValue = document.getElementById('zoom-value');
            
            // Event listeners para zoom
            zoomSlider.addEventListener('input', function() {
                const zoom = this.value;
                imagen.style.transform = `translate(${currentX}px, ${currentY}px) scale(${zoom/100})`;
                zoomValue.textContent = zoom + '%';
            });
            
            // Event listeners para arrastre
            imagen.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - currentX;
                startY = e.clientY - currentY;
                imagen.style.cursor = 'grabbing';
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                currentX = e.clientX - startX;
                currentY = e.clientY - startY;
                
                const zoom = zoomSlider.value / 100;
                imagen.style.transform = `translate(${currentX}px, ${currentY}px) scale(${zoom})`;
            });
            
            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    imagen.style.cursor = 'move';
                }
            });
            
            // Guardar referencia al archivo original
            modal.archivoOriginal = archivo;
        }
        
        // Confirmar recorte
        function confirmarRecorte() {
            const modal = document.getElementById('modal-recorte');
            const imagen = document.getElementById('imagen-recorte');
            
            // Obtener la imagen procesada como URL de datos
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = 300;
            canvas.height = 300;
            
            // Dibujar la imagen en el canvas con las transformaciones aplicadas
            const img = new Image();
            img.onload = function() {
                // Calcular la transformación
                const transform = imagen.style.transform;
                const zoom = document.getElementById('zoom-slider').value / 100;
                
                // Aplicar zoom y centrado
                const scale = zoom;
                const imgWidth = img.naturalWidth * scale;
                const imgHeight = img.naturalHeight * scale;
                
                // Dibujar imagen centrada y escalada
                ctx.drawImage(img, 
                    (canvas.width - imgWidth) / 2, 
                    (canvas.height - imgHeight) / 2, 
                    imgWidth, 
                    imgHeight
                );
                
                // Convertir a blob
                canvas.toBlob(function(blob) {
                    // Crear archivo desde blob
                    const archivoRecortado = new File([blob], 'foto_perfil.jpg', { type: 'image/jpeg' });
                    
                    // Mostrar preview en el formulario
                    canvas.toDataURL('image/jpeg', 0.8);
                    const preview = document.getElementById('preview-foto');
                    if (preview) {
                        preview.innerHTML = `<img src="${canvas.toDataURL('image/jpeg', 0.8)}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                        fotoSeleccionada = archivoRecortado;
                    }
                    
                    // Cerrar modal
                    document.body.removeChild(modal);
                }, 'image/jpeg', 0.8);
            };
            
            img.src = imagen.src;
        }
        
        // Cancelar recorte
        function cancelarRecorte() {
            const modal = document.getElementById('modal-recorte');
            const input = document.getElementById('input-foto');
            
            // Limpiar input
            if (input) input.value = '';
            
            // Cerrar modal
            document.body.removeChild(modal);
        }
        
        // Eliminar foto de previsualización
        function eliminarFotoPrevisualizacion() {
            const preview = document.getElementById('preview-foto');
            const input = document.getElementById('input-foto');
            
            if (preview) preview.innerHTML = '👤';
            if (input) input.value = '';
            fotoSeleccionada = null;
        }
        
        // Subir foto a servidor
        async function subirFoto(clienteId, archivo) {
            try {
                const formData = new FormData();
                formData.append('foto', archivo);
                formData.append('cliente_id', clienteId);
                
                const response = await fetch('upload_foto.php', {
                    method: 'POST',
                    body: formData
                });
                
                const resultado = await response.json();
                
                if (resultado.success) {
                    // Actualizar foto en la base de datos
                    await fetch('api_simple.php?action=cliente_actualizar_foto', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: clienteId,
                            foto_perfil: resultado.url
                        })
                    });
                    
                    return resultado.url;
                } else {
                    throw new Error(resultado.error || 'Error subiendo foto');
                }
                
            } catch (error) {
                console.error('Error subiendo foto:', error);
                throw error;
            }
        }
        
        // Sistema de calificación por estrellas
        function actualizarRatingInput(rating) {
            const clienteCalificacion = document.getElementById('cliente-calificacion');
            const ratingDisplay = document.getElementById('rating-display');
            
            if (clienteCalificacion) clienteCalificacion.value = rating;
            if (ratingDisplay) ratingDisplay.textContent = rating + '.0';
            
            const stars = document.querySelectorAll('#rating-input .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
        
        function previsualizarRating(rating) {
            const stars = document.querySelectorAll('#rating-input .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
        
        // Funciones auxiliares para rating en modal
        function previsualizarRatingModal(rating) {
            const stars = document.querySelectorAll('#modal-rating-editable .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
        
        function actualizarRatingModal(rating) {
            const stars = document.querySelectorAll('#modal-rating-editable .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
        
        // ================= MODAL DE CLIENTE =================
        
        // Abrir modal de cliente
        async function abrirModalCliente(clienteId) {
            console.log('🔍 Abriendo modal para cliente ID:', clienteId);
            
            const cliente = clientes.find(c => c.id == clienteId);
            if (!cliente) {
                console.error('❌ Cliente no encontrado:', clienteId);
                alert('Cliente no encontrado');
                return;
            }
            
            console.log('✅ Cliente encontrado:', cliente);
            clienteModalActual = cliente;
            
            // Llenar información del modal
            const nombreElement = document.getElementById('modal-nombre-cliente');
            if (nombreElement) {
                nombreElement.textContent = cliente.nombre;
                console.log('✅ Nombre actualizado');
            }
            
            const telefonoElement = document.getElementById('modal-telefono');
            if (telefonoElement) {
                telefonoElement.textContent = cliente.telefono;
            }
            
            const telefono2Element = document.getElementById('modal-telefono2');
            if (telefono2Element) {
                telefono2Element.textContent = cliente.telefono;
            }
            
            const direccionElement = document.getElementById('modal-direccion');
            if (direccionElement) {
                direccionElement.textContent = cliente.direccion || 'No especificada';
            }
            
            const emailElement = document.getElementById('modal-email');
            if (emailElement) {
                emailElement.textContent = cliente.email || 'No especificado';
            }
            
            // Foto de perfil
            const modalFoto = document.getElementById('modal-foto-cliente');
            if (modalFoto) {
                if (cliente.foto_perfil) {
                    modalFoto.innerHTML = `<img src="${cliente.foto_perfil}" alt="${cliente.nombre}">`;
                } else {
                    modalFoto.innerHTML = '👤';
                }
                console.log('✅ Foto actualizada');
            }
            
            // Calificación
            const rating = parseFloat(cliente.calificacion || 0);
            const modalRatingElement = document.getElementById('modal-rating-cliente');
            if (modalRatingElement) {
                modalRatingElement.innerHTML = generarEstrellas(rating);
            }
            
            const modalValorElement = document.getElementById('modal-rating-valor');
            if (modalValorElement) {
                modalValorElement.textContent = rating.toFixed(1);
            }
            
            // Configurar estrellas editables del modal
            setTimeout(() => {
                actualizarRatingModal(rating);
            }, 100);
            
            // Mostrar modal
            const modal = document.getElementById('modal-cliente');
            if (modal) {
                modal.style.display = 'block';
                console.log('✅ Modal mostrado');
            } else {
                console.error('❌ Modal no encontrado');
                alert('Error: Modal no encontrado');
            }
        }
        
        // Cerrar modal de cliente
        function cerrarModalCliente() {
            const modal = document.getElementById('modal-cliente');
            if (modal) {
                modal.style.display = 'none';
            }
            clienteModalActual = null;
        }
        
        // Cambiar pestaña en el modal
        function cambiarTabInfo(tab) {
            // Actualizar botones
            document.querySelectorAll('.info-tab').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`.info-tab[onclick*="${tab}"]`);
            if (activeBtn) activeBtn.classList.add('active');
            
            // Actualizar contenido
            document.querySelectorAll('.tab-content-info').forEach(content => {
                content.classList.remove('active');
            });
            
            // Usar el ID correcto para pestañas de cliente
            let activeContentId = `tab-${tab}`;
            if (tab === 'prestamos') {
                activeContentId = 'tab-prestamos-cliente';
            }
            
            const activeContent = document.getElementById(activeContentId);
            if (activeContent) activeContent.classList.add('active');
        }
        
        // Actualizar calificación de cliente
        async function actualizarCalificacion(rating) {
            if (!clienteModalActual) return;
            
            try {
                const response = await fetch('api_simple.php?action=cliente_actualizar_calificacion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: clienteModalActual.id,
                        calificacion: rating
                    })
                });
                
                const resultado = await response.json();
                
                if (resultado.success) {
                    // Actualizar cliente en memoria
                    clienteModalActual.calificacion = rating;
                    const clienteIndex = clientes.findIndex(c => c.id == clienteModalActual.id);
                    if (clienteIndex !== -1) {
                        clientes[clienteIndex].calificacion = rating;
                    }
                    
                    // Actualizar UI
                    const modalRating = document.getElementById('modal-rating-cliente');
                    const modalValor = document.getElementById('modal-rating-valor');
                    
                    if (modalRating) modalRating.innerHTML = generarEstrellas(rating);
                    if (modalValor) modalValor.textContent = rating.toFixed(1);
                    
                    // Recargar galería
                    mostrarClientesEnGaleria(clientes);
                } else {
                    alert('Error actualizando calificación: ' + resultado.error);
                }
            } catch (error) {
                console.error('Error actualizando calificación:', error);
                alert('Error de conexión');
            }
        }
        
        // ================= SISTEMA DE CALIFICACIONES =================
        
        // Mostrar modal de rating
        function mostrarRatingModal(clienteId) {
            console.log('⭐ Abriendo modal de rating para cliente:', clienteId);
            
            const cliente = clientes.find(c => (c.id || c.cliente_id) === clienteId);
            if (!cliente) {
                console.error('❌ Cliente no encontrado para rating:', clienteId);
                return;
            }
            
            const ratingActual = parseFloat(cliente.calificacion || 0);
            
            const modalHtml = `
                <div id="rating-modal" class="modal-overlay" style="display: flex; align-items: center; justify-content: center;">
                    <div class="modal-content" style="max-width: 400px; text-align: center;">
                        <h3>⭐ Calificar Cliente</h3>
                        <div style="margin: 20px 0;">
                            <h4>${cliente.nombre}</h4>
                            <p>Calificación actual: ${ratingActual.toFixed(1)} estrellas</p>
                        </div>
                        
                        <div class="rating-selector" style="margin: 30px 0;">
                            <div class="stars-selector" style="font-size: 2em; margin: 20px 0;">
                                ${[1,2,3,4,5].map(num => `
                                    <span class="star-btn" data-rating="${num}" 
                                          style="cursor: pointer; color: ${num <= ratingActual ? '#ffd700' : '#ddd'}; margin: 0 5px;"
                                          onmouseover="previsualizarRating(${num})"
                                          onmouseout="restaurarRating(${ratingActual})"
                                          onclick="aplicarRating('${clienteId}', ${num})">⭐</span>
                                `).join('')}
                            </div>
                            <p id="rating-preview">Selecciona una calificación</p>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn btn-secondary" onclick="cerrarRatingModal()">Cancelar</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal anterior si existe
            const modalExistente = document.getElementById('rating-modal');
            if (modalExistente) {
                modalExistente.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
        
        // Previsualizar rating
        function previsualizarRating(rating) {
            const stars = document.querySelectorAll('.star-btn');
            const preview = document.getElementById('rating-preview');
            
            stars.forEach((star, index) => {
                star.style.color = (index + 1) <= rating ? '#ffd700' : '#ddd';
            });
            
            const descripciones = ['', 'Muy Malo', 'Malo', 'Regular', 'Bueno', 'Excelente'];
            preview.textContent = descripciones[rating] || 'Selecciona una calificación';
        }
        
        // Restaurar rating visual
        function restaurarRating(ratingOriginal) {
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                star.style.color = (index + 1) <= ratingOriginal ? '#ffd700' : '#ddd';
            });
            document.getElementById('rating-preview').textContent = 'Selecciona una calificación';
        }
        
        // Aplicar nueva calificación
        async function aplicarRating(clienteId, nuevoRating) {
            console.log('💾 Aplicando rating:', { clienteId, nuevoRating });
            
            try {
                // Actualizar en el array local
                const cliente = clientes.find(c => (c.id || c.cliente_id) === clienteId);
                if (cliente) {
                    cliente.calificacion = nuevoRating;
                }
                
                // Aquí iría la llamada al backend para guardar
                // const resultado = await llamarBackend('clientes', 'actualizar_rating', { id: clienteId, rating: nuevoRating });
                
                // Mostrar confirmación
                alert(`✅ Calificación actualizada: ${nuevoRating} estrellas`);
                
                // Recargar la galería
                mostrarClientesEnGaleria(clientes);
                
                // Cerrar modal
                cerrarRatingModal();
                
            } catch (error) {
                console.error('❌ Error actualizando rating:', error);
                alert('❌ Error al actualizar la calificación');
            }
        }
        
        // Cerrar modal de rating
        function cerrarRatingModal() {
            const modal = document.getElementById('rating-modal');
            if (modal) {
                modal.remove();
            }
        }
        
        // Mostrar menú de opciones
        function mostrarOpcionesCliente(clienteId, event) {
            console.log('⚙️ Mostrando opciones para cliente:', clienteId);
            
            let menu = document.getElementById('opciones-menu-cliente');
            
            // Si no existe el menú, crearlo
            if (!menu) {
                console.log('📝 Creando menú de opciones...');
                const menuHtml = `
                    <div id="opciones-menu-cliente" class="opciones-menu">
                        <div class="opcion-item" onclick="editarCliente()">
                            ✏️ Editar Cliente
                        </div>
                        <div class="opcion-item" onclick="verPrestamoCliente()">
                            💼 Ver Préstamos
                        </div>
                        <div class="opcion-item" onclick="crearPrestamoCliente()">
                            ➕ Crear Préstamo
                        </div>
                        <div class="opcion-item eliminar" onclick="eliminarCliente()">
                            🗑️ Eliminar Cliente
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', menuHtml);
                menu = document.getElementById('opciones-menu-cliente');
                
                // Agregar estilos si no existen
                if (!document.getElementById('opciones-menu-styles')) {
                    const styles = `
                        <style id="opciones-menu-styles">
                        .opciones-menu {
                            position: absolute;
                            background: white;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 1000;
                            display: none;
                            min-width: 180px;
                        }
                        .opciones-menu.active {
                            display: block;
                        }
                        .opcion-item {
                            padding: 12px 16px;
                            cursor: pointer;
                            border-bottom: 1px solid #eee;
                            transition: background-color 0.2s;
                        }
                        .opcion-item:hover {
                            background-color: #f5f5f5;
                        }
                        .opcion-item:last-child {
                            border-bottom: none;
                        }
                        .opcion-item.eliminar {
                            color: #dc3545;
                        }
                        .opcion-item.eliminar:hover {
                            background-color: #ffe6e6;
                        }
                        </style>
                    `;
                    document.head.insertAdjacentHTML('beforeend', styles);
                }
            }
            
            // Posicionar menú
            const rect = event.target.getBoundingClientRect();
            menu.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            menu.style.left = (rect.left + window.scrollX) + 'px';
            menu.classList.add('active');
            menu.dataset.clienteId = clienteId;
            
            console.log('✅ Menu de opciones mostrado en:', {
                top: menu.style.top,
                left: menu.style.left,
                clienteId: clienteId
            });
            
            // Cerrar menú al hacer clic fuera
            setTimeout(() => {
                document.addEventListener('click', function closeMenu(e) {
                    if (!menu.contains(e.target) && !e.target.classList.contains('btn-opciones')) {
                        menu.classList.remove('active');
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }, 100);
        }
        
        // Editar cliente
        function editarCliente() {
            const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
            const menu = document.getElementById('opciones-menu-cliente');
            if (menu) menu.classList.remove('active');
            
            console.log('✏️ Editando cliente:', clienteId);
            
            const cliente = clientes.find(c => (c.id || c.cliente_id) === clienteId);
            if (!cliente) {
                alert('❌ Cliente no encontrado');
                return;
            }
            
            // Mostrar formulario de edición
            const formularioHtml = `
                <div id="editar-cliente-modal" class="modal-overlay" style="display: flex;">
                    <div class="modal-content" style="max-width: 500px; width: 100%;">
                        <h3>✏️ Editar Cliente</h3>
                        <form id="form-editar-cliente">
                            <div class="form-group">
                                <label>Nombre Completo:</label>
                                <input type="text" id="edit-nombre" value="${cliente.nombre || ''}" required>
                            </div>
                            <div class="form-group">
                                <label>Teléfono:</label>
                                <input type="tel" id="edit-telefono" value="${cliente.telefono || ''}" required>
                            </div>
                            <div class="form-group">
                                <label>Documento:</label>
                                <input type="text" id="edit-documento" value="${cliente.documento || ''}" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" id="edit-email" value="${cliente.email || ''}">
                            </div>
                            <div class="form-group">
                                <label>Dirección:</label>
                                <textarea id="edit-direccion">${cliente.direccion || ''}</textarea>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" onclick="cerrarModalEditarCliente()">Cancelar</button>
                                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', formularioHtml);
            
            // Manejar envío del formulario
            document.getElementById('form-editar-cliente').addEventListener('submit', async (e) => {
                e.preventDefault();
                await guardarEdicionCliente(clienteId);
            });
        }
        
        // Guardar edición de cliente
        async function guardarEdicionCliente(clienteId) {
            const datos = {
                nombre: document.getElementById('edit-nombre').value,
                telefono: document.getElementById('edit-telefono').value,
                documento: document.getElementById('edit-documento').value,
                email: document.getElementById('edit-email').value,
                direccion: document.getElementById('edit-direccion').value
            };
            
            console.log('💾 Guardando edición de cliente:', { clienteId, datos });
            
            // Actualizar en el array local
            const cliente = clientes.find(c => (c.id || c.cliente_id) === clienteId);
            if (cliente) {
                Object.assign(cliente, datos);
            }
            
            alert('✅ Cliente actualizado correctamente');
            mostrarClientesEnGaleria(clientes);
            cerrarModalEditarCliente();
        }
        
        // Cerrar modal de edición
        function cerrarModalEditarCliente() {
            const modal = document.getElementById('editar-cliente-modal');
            if (modal) modal.remove();
        }
        
        // Ver préstamos del cliente
        function verPrestamoCliente() {
            const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
            const menu = document.getElementById('opciones-menu-cliente');
            if (menu) menu.classList.remove('active');
            
            console.log('💼 Viendo préstamos del cliente:', clienteId);
            
            // Cambiar a tab de préstamos
            showTab('prestamos');
            
            // Aquí se podría filtrar préstamos por cliente
            setTimeout(() => {
                alert(`🔍 Mostrando préstamos del cliente ${clienteId}`);
            }, 500);
        }
        
        // Eliminar cliente
        function eliminarCliente() {
            const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
            const menu = document.getElementById('opciones-menu-cliente');
            if (menu) menu.classList.remove('active');
            
            const cliente = clientes.find(c => (c.id || c.cliente_id) === clienteId);
            if (!cliente) {
                alert('❌ Cliente no encontrado');
                return;
            }
            
            if (confirm(`⚠️ ¿Está seguro de eliminar al cliente "${cliente.nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                console.log('🗑️ Eliminando cliente:', clienteId);
                
                // Remover del array local
                const index = clientes.findIndex(c => (c.id || c.cliente_id) === clienteId);
                if (index > -1) {
                    clientes.splice(index, 1);
                }
                
                alert('✅ Cliente eliminado correctamente');
                mostrarClientesEnGaleria(clientes);
            }
        }
        
        // Crear préstamo para cliente
        function crearPrestamoCliente() {
            const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
            const menu = document.getElementById('opciones-menu-cliente');
            if (menu) menu.classList.remove('active');
            
            console.log('➕ Creando préstamo para cliente:', clienteId);
            
            // Cambiar a pestaña de préstamos
            showTab('prestamos');
            
            // Simular crear préstamo
            setTimeout(() => {
                alert(`➕ Creando nuevo préstamo para cliente ${clienteId}`);
            }, 500);
        }
        
        // ================= MODAL DE CLIENTE =================
            document.getElementById('cliente-id-edit').value = clienteId;
            document.getElementById('cliente-nombre').value = cliente.nombre;
            document.getElementById('cliente-documento').value = cliente.documento;
            document.getElementById('cliente-telefono').value = cliente.telefono;
            
            const emailField = document.getElementById('cliente-email');
            const direccionField = document.getElementById('cliente-direccion');
            
            if (emailField) emailField.value = cliente.email || '';
            if (direccionField) direccionField.value = cliente.direccion || '';
            
            // Calificación
            const rating = parseInt(cliente.calificacion || 0);
            actualizarRatingInput(rating);
            
            // Foto
            if (cliente.foto_perfil) {
                const preview = document.getElementById('preview-foto');
                if (preview) {
                    preview.innerHTML = `<img src="${cliente.foto_perfil}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                }
            }
            
            // Cambiar título y botón
            const titulo = document.querySelector('#vista-nuevo-cliente h3');
            const boton = document.querySelector('#form-cliente button[type="submit"]');
            
            if (titulo) titulo.textContent = 'Editar Cliente';
            if (boton) boton.innerHTML = '💾 Actualizar Cliente';
        }
        
        async function eliminarCliente() {
            const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
            const menu = document.getElementById('opciones-menu-cliente');
            if (menu) menu.classList.remove('active');
            
            const cliente = clientes.find(c => c.id == clienteId);
            if (!cliente) return;
            
            if (confirm(`¿Estás seguro de que deseas eliminar al cliente "${cliente.nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                try {
                    const response = await fetch('api_simple.php?action=cliente_eliminar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: clienteId })
                    });
                    
                    const resultado = await response.json();
                    
                    if (resultado.success) {
                        cargarClientes(); // Recargar lista
                        cerrarModalCliente(); // Cerrar modal si está abierto
                        alert('Cliente eliminado correctamente');
                    } else {
                        alert('Error eliminando cliente: ' + resultado.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error de conexión');
                }
            }
        }
        
        // ================= ACCIONES EXTERNAS =================
        
        function abrirWhatsApp() {
            if (!clienteModalActual) return;
            const telefono = clienteModalActual.telefono.replace(/[^0-9]/g, '');
            window.open(`https://wa.me/1${telefono}`, '_blank');
        }
        
        function llamarCliente() {
            if (!clienteModalActual) return;
            window.open(`tel:${clienteModalActual.telefono}`, '_self');
        }
        
        function abrirMaps() {
            if (!clienteModalActual || !clienteModalActual.direccion) return;
            const direccion = encodeURIComponent(clienteModalActual.direccion);
            window.open(`https://www.google.com/maps/search/?api=1&query=${direccion}`, '_blank');
        }
        
        function abrirEmail() {
            if (!clienteModalActual || !clienteModalActual.email) return;
            window.open(`mailto:${clienteModalActual.email}`, '_self');
        }
        
        // Función de prueba
        async function testearCliente() {
            const clientePrueba = {
                nombre: 'Sandra Quintina Cotuy Castillo',
                cedula: '00112345678',
                telefono: '+18296690047',
                email: 'sandra.cotuy@email.com',
                direccion: 'J76P+4PC, 23000 Higüey, República Dominicana'
            };
            
            // Llenar formulario con datos de prueba
            document.getElementById('cliente-nombre').value = clientePrueba.nombre;
            document.getElementById('cliente-documento').value = clientePrueba.cedula;
            document.getElementById('cliente-telefono').value = clientePrueba.telefono;
            
            const emailField = document.getElementById('cliente-email');
            const direccionField = document.getElementById('cliente-direccion');
            
            if (emailField) emailField.value = clientePrueba.email;
            if (direccionField) direccionField.value = clientePrueba.direccion;
            
            // Asignar calificación de prueba
            actualizarRatingInput(3);
        }
        
        // =================== FIN MÓDULO CLIENTES ===================
        
        // FUNCIONES PARA MANEJAR VISTAS DE CLIENTES (MÓDULO INDEPENDIENTE)
        function mostrarFormularioNuevoCliente() {
            // Ocultar vista de lista
            document.getElementById('vista-lista-clientes').style.display = 'none';
            // Mostrar vista de formulario
            document.getElementById('vista-nuevo-cliente').style.display = 'block';
            
            // Limpiar formulario por si acaso
            document.getElementById('form-cliente').reset();
            
            // Resetear modo edición si estaba activo
            editandoCliente = null;
            clienteIdEditar = null;
            
            // Asegurar que el botón esté en modo "Crear"
            const submitBtn = document.querySelector('#vista-nuevo-cliente button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '✅ Crear Cliente';
                submitBtn.style.backgroundColor = '';
            }
            
            console.log('📝 Mostrando formulario de nuevo cliente');
        }
        
        function volverAListaClientes() {
            // Mostrar vista de lista
            document.getElementById('vista-lista-clientes').style.display = 'block';
            // Ocultar vista de formulario
            document.getElementById('vista-nuevo-cliente').style.display = 'none';
            
            // Limpiar formulario
            document.getElementById('form-cliente').reset();
            
            // Limpiar mensajes
            const mensajeDiv = document.getElementById('mensaje-cliente');
            if (mensajeDiv) {
                mensajeDiv.innerHTML = '';
            }
            
            // Resetear variables de edición
            editandoCliente = null;
            clienteIdEditar = null;
            
            console.log('📋 Volviendo a lista de clientes');
        }
        
        // Función para seleccionar cliente (usando variable ya declarada arriba)
        function seleccionarCliente(clienteId) {
            // Remover selección anterior
            document.querySelectorAll('#lista-clientes tr').forEach(tr => {
                tr.classList.remove('selected');
            });
            
            // Agregar selección actual
            const fila = document.querySelector(`#lista-clientes tr[data-cliente-id="${clienteId}"]`);
            if (fila) {
                fila.classList.add('selected');
                clienteSeleccionado = clientes.find(c => c.id == clienteId);
                console.log('Cliente seleccionado:', clienteSeleccionado);
                
                // Mostrar información del cliente seleccionado
                mostrarMensaje('mensaje-cliente', `Cliente seleccionado: ${clienteSeleccionado.nombre}`, 'success');
            }
        }
        
        // Función para editar cliente
        async function editarCliente(clienteId) {
            const cliente = clientes.find(c => c.id == clienteId);
            if (!cliente) {
                mostrarMensaje('mensaje-cliente', 'Cliente no encontrado', 'error');
                return;
            }
            
            // Llenar el formulario con los datos del cliente
            document.getElementById('cliente-id-edit').value = clienteId;
            document.getElementById('cliente-nombre').value = cliente.nombre;
            document.getElementById('cliente-documento').value = cliente.documento;
            document.getElementById('cliente-telefono').value = cliente.telefono;
            document.getElementById('cliente-direccion').value = cliente.direccion || '';
            
            // Cambiar el botón de crear por actualizar
            const form = document.getElementById('form-cliente');
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '💾 Actualizar Cliente';
            submitBtn.style.backgroundColor = '#ff9800';
            
            // Mostrar botón cancelar
            document.getElementById('btn-cancelar').style.display = 'inline-block';
            
            // Cambiar el handler del formulario temporalmente
            form.dataset.editMode = 'true';
            
            // Scroll al formulario
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
            
            mostrarMensaje('mensaje-cliente', `Editando cliente: ${cliente.nombre}`, 'info');
        }
        
        // Función para cancelar edición
        function cancelarEdicion() {
            const form = document.getElementById('form-cliente');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Restaurar botón original
            submitBtn.innerHTML = '✅ Crear Cliente';
            submitBtn.style.backgroundColor = '#4CAF50';
            
            // Remover campo de ID y modo edición
            const idField = document.getElementById('cliente-id-edit');
            if (idField) {
                idField.remove();
            }
            form.dataset.editMode = 'false';
            
            // Limpiar formulario
            form.reset();
            
            mostrarMensaje('mensaje-cliente', 'Edición cancelada', 'info');
        }
        
        async function testearCliente() {
            const clientePrueba = {
                nombre: 'Juan Pérez Modular',
                documento: '00112345678',
                telefono: '809-555-1234',
                email: 'juan.modular@email.com',
                direccion: 'Calle Principal #123, Santiago'
            };
            
            await llamarBackend('cliente', 'crear', clientePrueba);
        }
        
        // MÓDULO PRÉSTAMOS
        document.getElementById('form-prestamo').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const isEditMode = form.dataset.editMode === 'true';
            const idField = document.getElementById('prestamo-id-edit');
            
            const prestamoData = {
                cliente_id: document.getElementById('prestamo-cliente').value,
                monto: parseFloat(document.getElementById('prestamo-monto').value),
                tasa: parseFloat(document.getElementById('prestamo-tasa').value),
                fecha: document.getElementById('prestamo-fecha').value,
                tipo: document.getElementById('prestamo-tipo').value,
                frecuencia: document.getElementById('prestamo-frecuencia').value
            };
            
            // Solo agregar plazo y cuotas si no están vacíos o si es modalidad "cuota"
            const tipoPrestamo = document.getElementById('prestamo-tipo').value;
            const plazoValue = document.getElementById('prestamo-plazo').value;
            const cuotasValue = document.getElementById('prestamo-cuotas').value;
            
            // Siempre incluir plazo, incluso si es 0 o vacío
            prestamoData.plazo = parseInt(plazoValue) || 0;
            
            // Siempre incluir cuotas, incluso si es 0 o vacío  
            prestamoData.cuotas = parseInt(cuotasValue) || 1;
            
            console.log('📋 Datos del préstamo a enviar:', prestamoData);
            
            // Mostrar modal de loading
            abrirModal();
            
            let resultado;
            
            if (isEditMode && idField) {
                // Modo edición
                prestamoData.id = idField.value;
                resultado = await llamarBackend('prestamo', 'actualizar', prestamoData);
                
                if (resultado.success) {
                    mostrarResultadoEnModal('✅ Operación Exitosa', 'Préstamo actualizado correctamente');
                    cancelarEdicionPrestamo();
                    cargarPrestamos();
                } else {
                    mostrarResultadoEnModal('❌ Error', 'Error al actualizar préstamo: ' + (resultado.error || resultado.message || 'Error desconocido'));
                }
            } else {
                // Modo creación
                resultado = await llamarBackend('prestamo', 'crear', prestamoData);
                
                if (resultado.success) {
                    mostrarResultadoEnModal('✅ Operación Exitosa', 'Préstamo creado correctamente');
                    document.getElementById('form-prestamo').reset();
                    cargarPrestamos();
                    
                    // Regresar a la lista de préstamos después de crear exitosamente
                    setTimeout(() => {
                        volverAListaPrestamos();
                    }, 1500); // Esperar 1.5 segundos para que el usuario vea el mensaje
                } else {
                    mostrarResultadoEnModal('❌ Error', 'Error al crear préstamo: ' + (resultado.error || resultado.message || 'Error desconocido'));
                }
            }
        });
        
        async function cargarPrestamos() {
            try {
                console.log('Cargando préstamos...');
                const resultado = await fetch('api_modular.php?action=prestamos_listar');
                const data = await resultado.json();
                
                console.log('Respuesta API préstamos:', data);
                
                if (data.exito) {
                    prestamos = data.datos || [];
                    console.log('Préstamos cargados:', prestamos);
                    const tbody = document.getElementById('lista-prestamos');
                    
                    if (prestamos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; color: #666;">No hay préstamos registrados</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = prestamos.map(prestamo => {
                        const montoInicial = parseFloat(prestamo.monto || 0);
                        const montoActual = parseFloat(prestamo.monto || 0);
                        const progreso = 0; // Para nuevos préstamos
                        const cuotaVencida = 0; // Para nuevos préstamos
                        
                        // Determinar el tipo de préstamo
                        let tipoPrestamo = 'A Cuota';
                        if (prestamo.tipo === 'interes' || prestamo.tipo_prestamo === 'solo_interes' || prestamo.es_solo_interes === '1') {
                            tipoPrestamo = 'Solo Interés';
                        }
                        
                        // Calcular la mora
                        let mora = 0;
                        if (cuotaVencida > 0) {
                            mora = cuotaVencida * parseFloat(prestamo.monto_cuota || 0);
                        }
                        
                        // Formatear fecha del próximo pago
                        let proximoPago = prestamo.proxima_fecha_pago || prestamo.fecha;
                        if (proximoPago) {
                            const fecha = new Date(proximoPago);
                            proximoPago = fecha.toLocaleDateString('es-DO', {
                                day: '2-digit',
                                month: '2-digit', 
                                year: 'numeric'
                            });
                        }
                        
                        // Color de fondo según las cuotas vencidas (como en la imagen)
                        let colorFila = '';
                        let claseVencido = '';
                        if (cuotaVencida >= 7) {
                            colorFila = 'background-color: #ffebee;'; // Rojo claro
                            claseVencido = ' vencido';
                        }
                        
                        return `
                        <tr class="${claseVencido}" style="${colorFila}">
                            <td style="color: #d32f2f; font-weight: bold;">
                                ${prestamo.cliente_nombre || 'N/A'}
                                ${cuotaVencida > 0 ? '<br><small style="color: #d32f2f; font-size: 0.8em;">Cuotas Vencidas: ' + cuotaVencida + '</small>' : ''}
                            </td>
                            <td>RD$${montoInicial.toFixed(2)}</td>
                            <td>RD$${montoActual.toFixed(2)}</td>
                            <td>RD$${parseFloat(prestamo.monto_cuota || 0).toFixed(2)}</td>
                            <td style="color: ${mora > 0 ? '#d32f2f' : '#333'};">RD$${mora.toFixed(0)}</td>
                            <td>${proximoPago}</td>
                            <td>RD$${montoActual.toFixed(2)}</td>
                            <td>
                                <span style="background: ${tipoPrestamo === 'Solo Interés' ? '#ff9800' : '#4caf50'}; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.75em;">
                                    ${tipoPrestamo}
                                </span>
                            </td>
                            <td>
                                <button class="btn-editar" onclick="editarPrestamo(${prestamo.id})" title="Editar préstamo">
                                    ✏️
                                </button>
                                <button class="btn-eliminar" onclick="eliminarPrestamo(${prestamo.id})" title="Eliminar préstamo">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                        `;
                    }).join('');
                    
                    // Actualizar select de préstamos en la sección de pagos
                    actualizarSelectPrestamos();
                }
            } catch (error) {
                console.error('Error cargando préstamos:', error);
            }
        }
        
        function actualizarSelectPrestamos() {
            const select = document.getElementById('pago-prestamo');
            select.innerHTML = '<option value="">Selecciona un préstamo</option>';
            
            prestamos.forEach(prestamo => {
                const option = document.createElement('option');
                option.value = prestamo.id;
                option.textContent = `${prestamo.cliente_nombre} - RD$${parseFloat(prestamo.monto).toFixed(2)}`;
                select.appendChild(option);
            });
        }
        
        async function testearPrestamo() {
            // Primero cargar clientes para obtener uno
            await cargarClientes();
            
            if (clientes.length === 0) {
                mostrarMensaje('mensaje-prestamo', 'Primero crea un cliente', 'error');
                return;
            }
            
            const prestamoPrueba = {
                cliente_id: clientes[0].id,
                monto: 50000,
                tasa: 10,
                plazo: 30,
                fecha: new Date().toISOString().split('T')[0],
                tipo: 'interes',
                frecuencia: 'quincenal',
                cuotas: 8
            };
            
            await llamarBackend('prestamo', 'crear', prestamoPrueba);
        }
        
        // Función para editar préstamo
        async function editarPrestamo(prestamoId) {
            const prestamo = prestamos.find(p => p.id == prestamoId);
            if (!prestamo) {
                mostrarResultadoEnModal('❌ Error', 'Préstamo no encontrado');
                return;
            }
            
            // Llenar el formulario con los datos del préstamo
            document.getElementById('prestamo-cliente').value = prestamo.cliente_id;
            document.getElementById('prestamo-monto').value = prestamo.monto;
            document.getElementById('prestamo-tasa').value = prestamo.tasa;
            document.getElementById('prestamo-plazo').value = prestamo.plazo;
            document.getElementById('prestamo-fecha').value = prestamo.fecha;
            document.getElementById('prestamo-tipo').value = prestamo.tipo;
            document.getElementById('prestamo-frecuencia').value = prestamo.frecuencia;
            document.getElementById('prestamo-cuotas').value = prestamo.cuotas;
            
            // Crear campo oculto para el ID si no existe
            let idField = document.getElementById('prestamo-id-edit');
            if (!idField) {
                idField = document.createElement('input');
                idField.type = 'hidden';
                idField.id = 'prestamo-id-edit';
                idField.name = 'prestamo-id-edit';
                document.getElementById('form-prestamo').appendChild(idField);
            }
            idField.value = prestamoId;
            
            // Cambiar el botón de crear por actualizar
            const form = document.getElementById('form-prestamo');
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '💾 Actualizar Préstamo';
            submitBtn.style.backgroundColor = '#ff9800';
            
            // Mostrar botón cancelar
            document.getElementById('btn-cancelar-prestamo').style.display = 'inline-block';
            
            // Cambiar el handler del formulario temporalmente
            form.dataset.editMode = 'true';
            
            // Scroll al formulario
            document.querySelector('#tab-prestamos .form-container').scrollIntoView({ behavior: 'smooth' });
            
            // Cambiar a la pestaña de préstamos
            showTab('prestamos');
            
            mostrarResultadoEnModal('✅ Modo Edición', `Editando préstamo #${prestamoId} de ${prestamo.cliente_nombre}`);
        }
        
        // Función para eliminar préstamo
        async function eliminarPrestamo(prestamoId) {
            const prestamo = prestamos.find(p => p.id == prestamoId);
            if (!prestamo) {
                mostrarResultadoEnModal('❌ Error', 'Préstamo no encontrado');
                return;
            }
            
            if (confirm(`¿Estás seguro de que deseas eliminar el préstamo #${prestamoId}?\n\nCliente: ${prestamo.cliente_nombre}\nMonto: RD$${parseFloat(prestamo.monto).toFixed(2)}\n\nEsta acción no se puede deshacer.`)) {
                try {
                    abrirModal();
                    
                    const response = await fetch(`api_simple.php?action=prestamo_eliminar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: prestamoId })
                    });
                    
                    const resultado = await response.json();
                    cerrarModal();
                    
                    if (resultado.success) {
                        mostrarResultadoEnModal('✅ Operación Exitosa', `Préstamo #${prestamoId} eliminado correctamente`);
                        cargarPrestamos(); // Recargar la lista
                    } else {
                        mostrarResultadoEnModal('❌ Error', 'Error al eliminar préstamo: ' + (resultado.message || resultado.error || 'Error desconocido'));
                    }
                } catch (error) {
                    cerrarModal();
                    console.error('Error eliminando préstamo:', error);
                    mostrarResultadoEnModal('❌ Error', 'Error de conexión al eliminar préstamo');
                }
            }
        }
        
        // Función para cancelar edición de préstamo
        function cancelarEdicionPrestamo() {
            const form = document.getElementById('form-prestamo');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Restaurar botón original
            submitBtn.innerHTML = '💰 Crear Préstamo';
            submitBtn.style.backgroundColor = '#4CAF50';
            
            // Remover campo de ID y modo edición
            const idField = document.getElementById('prestamo-id-edit');
            if (idField) {
                idField.remove();
            }
            form.dataset.editMode = 'false';
            
            // Limpiar formulario
            form.reset();
            
            // Ocultar botón cancelar si existe
            const btnCancelar = document.getElementById('btn-cancelar-prestamo');
            if (btnCancelar) {
                btnCancelar.style.display = 'none';
            }
            
            mostrarResultadoEnModal('ℹ️ Información', 'Edición de préstamo cancelada');
        }
        
        // FUNCIONES PARA MANEJAR VISTAS DE PRÉSTAMOS
        function mostrarFormularioNuevoPrestamo() {
            // Ocultar vista de lista
            document.getElementById('vista-lista-prestamos').style.display = 'none';
            // Mostrar vista de formulario
            document.getElementById('vista-nuevo-prestamo').style.display = 'block';
            
            // Limpiar formulario por si acaso
            document.getElementById('form-prestamo').reset();
            
            // Establecer fecha de hoy por defecto
            const fechaInput = document.getElementById('prestamo-fecha');
            if (fechaInput) {
                const today = new Date().toISOString().split('T')[0];
                fechaInput.value = today;
            }
            
            console.log('📝 Mostrando formulario de nuevo préstamo');
        }
        
        function volverAListaPrestamos() {
            // Mostrar vista de lista
            document.getElementById('vista-lista-prestamos').style.display = 'block';
            // Ocultar vista de formulario
            document.getElementById('vista-nuevo-prestamo').style.display = 'none';
            
            // Limpiar formulario
            document.getElementById('form-prestamo').reset();
            
            // Limpiar mensajes
            const mensajeDiv = document.getElementById('mensaje-prestamo');
            if (mensajeDiv) {
                mensajeDiv.innerHTML = '';
            }
            
            console.log('📋 Volviendo a lista de préstamos');
        }
        
        // MÓDULO PAGOS
        document.getElementById('form-pago').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const pagoData = {
                prestamo_id: document.getElementById('pago-prestamo').value,
                monto: parseFloat(document.getElementById('pago-monto').value),
                fecha: document.getElementById('pago-fecha').value,
                tipo: document.getElementById('pago-tipo').value,
                observaciones: document.getElementById('pago-observaciones').value
            };
            
            const resultado = await llamarBackend('pago', 'procesar', pagoData);
            
            if (resultado.success) {
                mostrarMensaje('mensaje-pago', 'Pago registrado correctamente');
                document.getElementById('form-pago').reset();
                cargarPagos();
                cargarPrestamos(); // Actualizar préstamos
            } else {
                mostrarMensaje('mensaje-pago', 'Error al registrar pago: ' + resultado.error, 'error');
            }
        });
        
        async function cargarPagos() {
            try {
                console.log('Cargando pagos...');
                const resultado = await fetch('api_modular.php?action=pagos_listar');
                const data = await resultado.json();
                
                console.log('Respuesta API pagos:', data);
                
                if (data.exito) {
                    pagos = data.datos || [];
                    console.log('Pagos cargados:', pagos);
                    const tbody = document.getElementById('lista-pagos');
                    
                    if (pagos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #666;">No hay pagos registrados</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = pagos.map(pago => `
                        <tr>
                            <td>${pago.id}</td>
                            <td>${pago.prestamo_id}</td>
                            <td>${pago.cliente_nombre || 'N/A'}</td>
                            <td>RD$${parseFloat(pago.monto).toFixed(2)}</td>
                            <td>${pago.fecha}</td>
                            <td>${pago.tipo}</td>
                        </tr>
                    `).join('');
                }
            } catch (error) {
                console.error('Error cargando pagos:', error);
            }
        }
        
        async function testearPago() {
            // Primero cargar préstamos para obtener uno
            await cargarPrestamos();
            
            if (prestamos.length === 0) {
                mostrarMensaje('mensaje-pago', 'Primero crea un préstamo', 'error');
                return;
            }
            
            const pagoPrueba = {
                prestamo_id: prestamos[0].id,
                monto: 5000,
                fecha: new Date().toISOString().split('T')[0],
                tipo: 'abono',
                observaciones: 'Pago de prueba del sistema modular'
            };
            
            await llamarBackend('pago', 'procesar', pagoPrueba);
        }
        
        // DASHBOARD - Usando datos locales
        async function actualizarDashboard() {
            try {
                console.log('Actualizando dashboard...');
                
                // Usar datos locales que ya están cargados
                const totalClientes = clientes.length;
                const prestamosActivos = prestamos.length;
                const montoTotal = prestamos.reduce((total, prestamo) => total + (parseFloat(prestamo.monto) || 0), 0);
                const totalPagos = pagos.length;
                
                document.getElementById('total-clientes').textContent = totalClientes;
                document.getElementById('prestamos-activos').textContent = prestamosActivos;
                document.getElementById('monto-total').textContent = `RD$${montoTotal.toFixed(2)}`;
                document.getElementById('intereses-generados').textContent = 'RD$0.00'; // Por ahora
                document.getElementById('monto-atrasado').textContent = 'RD$0.00'; // Por ahora
                document.getElementById('cuotas-vencidas').textContent = '0 cuotas'; // Por ahora
                
                console.log('Dashboard actualizado:', {
                    clientes: totalClientes,
                    prestamos: prestamosActivos,
                    monto: montoTotal
                });
                
            } catch (error) {
                console.error('Error actualizando dashboard:', error);
                // Valores predeterminados si hay error
                document.getElementById('total-clientes').textContent = clientes.length;
                document.getElementById('prestamos-activos').textContent = prestamos.length;
            }
        }
        
        // Cargar select de clientes
        async function cargarClientesSelect() {
            await cargarClientes();
            const select = document.getElementById('prestamo-cliente');
            select.innerHTML = '<option value="">Selecciona un cliente</option>';
            
            clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = cliente.nombre;
                select.appendChild(option);
            });
        }
        
        // Establecer fecha actual por defecto
        function establecerFechasActuales() {
            const fechaHoy = new Date().toISOString().split('T')[0];
            document.getElementById('prestamo-fecha').value = fechaHoy;
            document.getElementById('pago-fecha').value = fechaHoy;
        }
        
        // Inicialización al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Sistema modular iniciado');
            actualizarEstadoSistema('cargando', '🔄 Iniciando...');
            
            // Verificar estado del sistema primero
            if (!verificarSistema()) {
                console.error('⚠️ Sistema con errores - continuando con carga limitada');
                actualizarEstadoSistema('error', '⚠️ Errores detectados');
            }
            
            try {
                // Inicialización básica
                establecerFechasActuales();
                actualizarEstadoSistema('cargando', '📊 Cargando datos...');
                
                // Cargar datos principales de forma secuencial para evitar errores
                console.log('📊 Cargando datos del sistema...');
                
                cargarClientes().then(() => {
                    console.log('✅ Clientes cargados');
                    actualizarEstadoSistema('cargando', '💼 Cargando préstamos...');
                    return cargarPrestamos();
                }).then(() => {
                    console.log('✅ Préstamos cargados');
                    actualizarEstadoSistema('cargando', '💳 Cargando pagos...');
                    return cargarPagos();
                }).then(() => {
                    console.log('✅ Pagos cargados');
                    actualizarDashboard();
                    cargarClientesSelect();
                    actualizarEstadoSistema('listo', '🎉 Sistema listo');
                    console.log('🎉 Sistema completamente cargado');
                }).catch(error => {
                    console.error('❌ Error cargando datos:', error);
                    actualizarEstadoSistema('error', '❌ Error de carga');
                    // Intentar cargar datos mínimos
                    actualizarDashboard();
                });
                
                // Configurar componentes adicionales
                setTimeout(() => {
                    try {
                        configurarCalculadoraInteligente();
                        inicializarSistemaCalificaciones();
                        console.log('🔧 Componentes adicionales configurados');
                    } catch (error) {
                        console.error('⚠️ Error configurando componentes adicionales:', error);
                    }
                }, 1000);
                
            } catch (error) {
                console.error('💥 Error crítico en inicialización:', error);
                actualizarEstadoSistema('error', '💥 Error crítico');
            }
            
            // Cerrar modal al hacer click fuera
            window.onclick = function(event) {
                const modal = document.getElementById('modalRespuesta');
                if (event.target === modal) {
                    cerrarModal();
                }
                
                const modalCliente = document.getElementById('modalCliente');
                if (event.target === modalCliente) {
                    cerrarModalCliente();
                }
            };
        });
        
        // Función para inicializar el sistema de calificaciones
        function inicializarSistemaCalificaciones() {
            console.log('Inicializando sistema de calificaciones...');
            
            // Event listeners para estrellas en formulario de creación
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('star') && e.target.closest('#rating-input')) {
                    const rating = parseInt(e.target.dataset.rating);
                    actualizarRatingInput(rating);
                }
                
                // Estrellas editables en modal
                if (e.target.classList.contains('star') && e.target.classList.contains('editable')) {
                    const rating = parseInt(e.target.dataset.rating);
                    actualizarCalificacion(rating);
                }
            });
            
            // Event listeners para hover en formulario
            document.addEventListener('mouseover', function(e) {
                if (e.target.classList.contains('star') && e.target.closest('#rating-input')) {
                    const rating = parseInt(e.target.dataset.rating);
                    previsualizarRating(rating);
                }
                
                // Hover en estrellas editables del modal
                if (e.target.classList.contains('star') && e.target.classList.contains('editable')) {
                    const rating = parseInt(e.target.dataset.rating);
                    previsualizarRatingModal(rating);
                }
            });
            
            // Event listeners para salir del hover
            document.addEventListener('mouseout', function(e) {
                if (e.target.classList.contains('star') && e.target.closest('#rating-input')) {
                    const currentRating = parseInt(document.getElementById('cliente-calificacion').value) || 0;
                    actualizarRatingInput(currentRating);
                }
                
                // Salir de hover en modal
                if (e.target.classList.contains('star') && e.target.classList.contains('editable')) {
                    if (clienteModalActual) {
                        const currentRating = parseFloat(clienteModalActual.calificacion || 0);
                        actualizarRatingModal(currentRating);
                    }
                }
            });
        }
        
        // Función para actualizar la calificación de un cliente
        function actualizarCalificacionCliente(idCliente, calificacion) {
            console.log(`Actualizando calificación del cliente ${idCliente} a ${calificacion} estrellas`);
            
            fetch('api_modular.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    accion: 'actualizar_calificacion',
                    id: idCliente,
                    calificacion: calificacion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exito) {
                    console.log('Calificación actualizada correctamente');
                    
                    // Actualizar las estrellas visualmente en el modal
                    const contenedorEstrellas = document.querySelector(`[data-cliente-id="${idCliente}"] .calificacion-estrellas`);
                    if (contenedorEstrellas) {
                        actualizarVisualizacionEstrellas(contenedorEstrellas, calificacion);
                    }
                    
                    // Actualizar en la galería también
                    cargarClientes();
                } else {
                    console.error('Error al actualizar calificación:', data.mensaje || 'Error desconocido');
                    alert('Error al actualizar la calificación');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al actualizar la calificación');
            });
        }
        
        // Función para actualizar la visualización de estrellas
        function actualizarVisualizacionEstrellas(contenedor, calificacion) {
            const estrellas = contenedor.querySelectorAll('.estrella');
            estrellas.forEach((estrella, index) => {
                if (index < calificacion) {
                    estrella.classList.add('activa');
                } else {
                    estrella.classList.remove('activa');
                }
            });
        }
        
        // CALCULADORA INTELIGENTE EN TIEMPO REAL
        function configurarCalculadoraInteligente() {
            // Campos que disparan el recálculo
            const campos = ['prestamo-monto', 'prestamo-tasa', 'prestamo-cuotas', 'prestamo-tipo', 'prestamo-frecuencia'];
            
            campos.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (campo) {
                    campo.addEventListener('input', calcularEnTiempoReal);
                    campo.addEventListener('change', calcularEnTiempoReal);
                }
            });
            
            // Cálculo inicial
            calcularEnTiempoReal();
        }
        
        function calcularEnTiempoReal() {
            const monto = parseFloat(document.getElementById('prestamo-monto').value) || 0;
            const tasa = parseFloat(document.getElementById('prestamo-tasa').value) || 0;
            const cuotas = parseInt(document.getElementById('prestamo-cuotas').value) || 1;
            const tipo = document.getElementById('prestamo-tipo').value || 'interes';
            const frecuencia = document.getElementById('prestamo-frecuencia').value || 'quincenal';
            
            const resultadoDiv = document.getElementById('resultado-calculo');
            
            if (monto <= 0 || tasa <= 0) {
                resultadoDiv.innerHTML = `
                    <div style="text-align: center; color: #666; font-style: italic;">
                        Ingresa el monto y la tasa para ver los cálculos automáticos...
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            if (tipo === 'interes') {
                // CÁLCULO SOLO INTERÉS
                const interesPorPeriodo = monto * (tasa / 100);
                
                html = `
                    <div style="border-left: 4px solid #2196f3; padding-left: 15px;">
                        <h4 style="color: #1976d2; margin-bottom: 10px;">💰 Solo Interés</h4>
                        <div style="font-size: 1.1em; line-height: 1.8;">
                            <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                                <strong>Capital:</strong> RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                                <strong>Interés por período:</strong> RD$${number_format(interesPorPeriodo, 2)}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // CÁLCULO CUOTA FIJA - FÓRMULA CORRECTA
                // M = P/n + (P · r)
                // Donde: M = Cuota, P = Capital, n = Número de cuotas, r = Tasa decimal
                const tasaDecimal = tasa / 100;
                const capitalPorCuota = monto / cuotas; // P/n
                const interesFijoPorCuota = monto * tasaDecimal; // P · r
                const cuotaFija = capitalPorCuota + interesFijoPorCuota; // M
                const totalIntereses = interesFijoPorCuota * cuotas;
                const montoTotal = monto + totalIntereses;
                
                html = `
                    <div style="border-left: 4px solid #4caf50; padding-left: 15px;">
                        <h4 style="color: #388e3c; margin-bottom: 10px;">📈 Cuota Fija</h4>
                        <div style="font-size: 1.1em; line-height: 1.8;">
                            <div style="background: #e8f5e8; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                                <strong>Capital:</strong> RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: #fff3e0; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                                <strong>Interés total:</strong> RD$${number_format(totalIntereses, 2)}
                            </div>
                            <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                                <strong>Cuota fija:</strong> RD$${number_format(cuotaFija, 2)} (${cuotas} cuotas)
                            </div>
                        </div>
                    </div>
                `;
            }
            
            resultadoDiv.innerHTML = html;
        }
        
        // Función auxiliar para formatear números
        function number_format(number, decimals) {
            return new Intl.NumberFormat('es-DO', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }
        
        // Función de prueba general
        async function probarSistemaCompleto() {
            console.log('Iniciando prueba completa del sistema...');
            
            // 1. Crear cliente de prueba
            await testearCliente();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // 2. Crear préstamo de prueba
            await testearPrestamo();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // 3. Crear pago de prueba
            await testearPago();
            
            console.log('Prueba completa finalizada');
        }
        
        // Función global para pruebas rápidas
        window.probarSistemaCompleto = probarSistemaCompleto;
        
        // FUNCIONES ADICIONALES PARA COMPATIBILIDAD CON EL SISTEMA ORIGINAL
        
        // Funciones de Mora
        function guardarConfiguracionMora() {
            const diasGracia = document.getElementById('mora-dias-gracia').value;
            const tasaMora = document.getElementById('mora-tasa').value;
            const tipoMora = document.getElementById('mora-tipo').value;
            
            localStorage.setItem('configuracionMora', JSON.stringify({
                diasGracia: diasGracia,
                tasaMora: tasaMora,
                tipo: tipoMora
            }));
            
            mostrarMensaje('resultado-mora', 'Configuración de mora guardada correctamente');
        }
        
        function calcularMora() {
            const cuotaPendiente = parseFloat(document.getElementById('calc-mora-cuota').value) || 0;
            const diasRetraso = parseInt(document.getElementById('calc-mora-dias').value) || 0;
            const tasaMora = parseFloat(document.getElementById('mora-tasa').value) || 5;
            
            if (cuotaPendiente <= 0 || diasRetraso <= 0) {
                document.getElementById('resultado-mora').innerHTML = '<div class="alert alert-warning">Ingresa valores válidos</div>';
                return;
            }
            
            const moraCalculada = cuotaPendiente * (tasaMora / 100) * diasRetraso;
            const totalAPagar = cuotaPendiente + moraCalculada;
            
            document.getElementById('resultado-mora').innerHTML = `
                <div class="alert alert-info">
                    <h5>💰 Resultado del Cálculo</h5>
                    <p><strong>Cuota Pendiente:</strong> RD$${cuotaPendiente.toFixed(2)}</p>
                    <p><strong>Días de Retraso:</strong> ${diasRetraso} días</p>
                    <p><strong>Mora Calculada:</strong> RD$${moraCalculada.toFixed(2)}</p>
                    <p><strong>Total a Pagar:</strong> RD$${totalAPagar.toFixed(2)}</p>
                </div>
            `;
        }
        
        // Funciones de Calculadora
        function calcularPrestamo() {
            const monto = parseFloat(document.getElementById('calc-monto').value) || 0;
            const tasa = parseFloat(document.getElementById('calc-tasa').value) || 0;
            const cuotas = parseInt(document.getElementById('calc-cuotas').value) || 0;
            const tipo = document.querySelector('input[name="calc-tipo"]:checked')?.value || 'cuota';
            
            if (monto <= 0 || tasa <= 0 || cuotas <= 0) {
                document.getElementById('resultado-calculadora').innerHTML = `
                    <div class="alert alert-warning">
                        Por favor ingresa todos los valores requeridos
                    </div>
                `;
                return;
            }
            
            let resultadoHTML = '';
            
            if (tipo === 'interes') {
                // Cálculo para Solo Interés
                const interesPorPeriodo = monto * (tasa / 100);
                const totalIntereses = interesPorPeriodo * cuotas;
                const totalAPagar = monto + totalIntereses;
                
                resultadoHTML = `
                    <div class="alert alert-info">
                        <h4 style="color: #e53935;">🔴 Préstamo Solo Interés</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <p><strong>💰 Capital:</strong> RD$${monto.toLocaleString()}</p>
                                <p><strong>📈 Tasa:</strong> ${tasa}%</p>
                                <p><strong>🔢 Cuotas:</strong> ${cuotas}</p>
                            </div>
                            <div>
                                <p><strong>⭐ Pago por período:</strong> RD$${interesPorPeriodo.toFixed(2)}</p>
                                <p><strong>📊 Total intereses:</strong> RD$${totalIntereses.toFixed(2)}</p>
                                <p><strong>💵 Total a pagar:</strong> RD$${totalAPagar.toFixed(2)}</p>
                            </div>
                        </div>
                        <div class="alert alert-warning" style="margin-top: 10px;">
                            <strong>📝 Nota:</strong> En esta modalidad, cada pago cubre solo los intereses. 
                            Si paga más del interés, el exceso reduce el capital.
                        </div>
                    </div>
                `;
            } else {
                // Cálculo para Cuota Fija
                const interesTotal = monto * (tasa / 100);
                const montoConInteres = monto + interesTotal;
                const cuotaFija = montoConInteres / cuotas;
                
                resultadoHTML = `
                    <div class="alert alert-info">
                        <h4 style="color: #2196f3;">📊 Préstamo Cuota Fija</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <p><strong>💰 Capital:</strong> RD$${monto.toLocaleString()}</p>
                                <p><strong>📈 Tasa:</strong> ${tasa}%</p>
                                <p><strong>🔢 Cuotas:</strong> ${cuotas}</p>
                            </div>
                            <div>
                                <p><strong>⭐ Cuota fija:</strong> RD$${cuotaFija.toFixed(2)}</p>
                                <p><strong>📊 Total intereses:</strong> RD$${interesTotal.toFixed(2)}</p>
                                <p><strong>💵 Total a pagar:</strong> RD$${montoConInteres.toFixed(2)}</p>
                            </div>
                        </div>
                        <div class="alert alert-success" style="margin-top: 10px;">
                            <strong>📝 Nota:</strong> En esta modalidad, el cliente paga una cuota fija de 
                            RD$${cuotaFija.toFixed(2)} durante ${cuotas} períodos.
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('resultado-calculadora').innerHTML = resultadoHTML;
        }
        
        function probarInteresRapido() {
            document.getElementById('calc-monto').value = '50000';
            document.getElementById('calc-tasa').value = '10';
            document.getElementById('calc-cuotas').value = '8';
            document.querySelector('input[name="calc-tipo"][value="interes"]').checked = true;
            calcularPrestamo();
        }
        
        function probarCuotaRapido() {
            document.getElementById('calc-monto').value = '100000';
            document.getElementById('calc-tasa').value = '15';
            document.getElementById('calc-cuotas').value = '12';
            document.querySelector('input[name="calc-tipo"][value="cuota"]').checked = true;
            calcularPrestamo();
        }
        
        function autocompletarTasaCalculadora() {
            // Función placeholder para autocompletar tasas en calculadora
            console.log('Autocompletando tasa en calculadora...');
        }
        
        // Funciones de Configuración
        function restaurarConfiguracionPredeterminada() {
            document.getElementById('config-interes-semanal').value = '5';
            document.getElementById('config-interes-quincenal').value = '10';
            document.getElementById('config-interes-15y30').value = '10';
            document.getElementById('config-interes-mensual').value = '20';
            document.getElementById('config-cuota-semanal').value = '5';
            document.getElementById('config-cuota-quincenal').value = '10';
            document.getElementById('config-cuota-15y30').value = '10';
            document.getElementById('config-cuota-mensual').value = '20';
            
            // Actualizar resumen y autocompletado
            actualizarResumenTasas();
            actualizarTasaAutomatica();
            
            mostrarMensaje('mensaje-configuracion', '✅ Configuración restaurada a valores predeterminados');
        }
        
        function probarConfiguracion() {
            const config = {
                interes: {
                    semanal: document.getElementById('config-interes-semanal').value,
                    quincenal: document.getElementById('config-interes-quincenal').value,
                    '15y30': document.getElementById('config-interes-15y30').value,
                    mensual: document.getElementById('config-interes-mensual').value
                },
                cuota: {
                    semanal: document.getElementById('config-cuota-semanal').value,
                    quincenal: document.getElementById('config-cuota-quincenal').value,
                    '15y30': document.getElementById('config-cuota-15y30').value,
                    mensual: document.getElementById('config-cuota-mensual').value
                }
            };
            
            mostrarMensaje('mensaje-configuracion', 'Configuración probada: ' + JSON.stringify(config));
        }
        
        function exportarConfiguracion() {
            const config = {
                interes: {
                    semanal: document.getElementById('config-interes-semanal').value,
                    quincenal: document.getElementById('config-interes-quincenal').value,
                    '15y30': document.getElementById('config-interes-15y30').value,
                    mensual: document.getElementById('config-interes-mensual').value
                },
                cuota: {
                    semanal: document.getElementById('config-cuota-semanal').value,
                    quincenal: document.getElementById('config-cuota-quincenal').value,
                    '15y30': document.getElementById('config-cuota-15y30').value,
                    mensual: document.getElementById('config-cuota-mensual').value
                }
            };
            
            const dataStr = JSON.stringify(config, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'configuracion_prestamos.json';
            link.click();
            
            mostrarMensaje('mensaje-configuracion', 'Configuración exportada correctamente');
        }
        
        // Funciones de Pagos adicionales
        function eliminarPago() {
            mostrarMensaje('mensaje-pago', 'Función de eliminar pago será implementada', 'info');
        }
        
        function simularPago() {
            const prestamoId = document.getElementById('pago-prestamo').value;
            const monto = document.getElementById('pago-monto').value;
            
            if (!prestamoId || !monto) {
                mostrarMensaje('mensaje-pago', 'Selecciona un préstamo e ingresa un monto', 'error');
                return;
            }
            
            mostrarMensaje('mensaje-pago', `Simulando pago de RD$${monto} para préstamo ${prestamoId}`, 'info');
        }
        
        function generarFacturaManual() {
            mostrarMensaje('mensaje-pago', 'Función de generar factura será implementada', 'info');
        }
        
        // ============================================================================
        // FUNCIONES DE AUTOCOMPLETADO DE TASAS Y CALCULADORA EN TIEMPO REAL
        // ============================================================================
        
        // Función para actualizar automáticamente la tasa según la frecuencia y tipo
        function actualizarTasaAutomatica() {
            const frecuencia = document.getElementById('prestamo-frecuencia').value;
            const tipo = document.getElementById('prestamo-tipo').value;
            const tasaInput = document.getElementById('prestamo-tasa');
            
            if (!frecuencia || !tipo || !tasaInput) {
                console.log('Campos no encontrados para autocompletado');
                return;
            }
            
            let tasaAutomatica = 0;
            
            // Configuración de tasas predeterminadas (fallback si no hay elementos de configuración)
            const tasasPredeterminadas = {
                interes: {
                    semanal: 5,
                    quincenal: 10,
                    '15y30': 10,
                    mensual: 20
                },
                cuota: {
                    semanal: 5,
                    quincenal: 10,
                    '15y30': 10,
                    mensual: 20
                }
            };
            
            // Intentar obtener configuración desde los campos, sino usar predeterminadas
            const configuracionTasas = {
                interes: {
                    semanal: parseFloat(document.getElementById('config-interes-semanal')?.value) || tasasPredeterminadas.interes.semanal,
                    quincenal: parseFloat(document.getElementById('config-interes-quincenal')?.value) || tasasPredeterminadas.interes.quincenal,
                    '15y30': parseFloat(document.getElementById('config-interes-15y30')?.value) || tasasPredeterminadas.interes['15y30'],
                    mensual: parseFloat(document.getElementById('config-interes-mensual')?.value) || tasasPredeterminadas.interes.mensual
                },
                cuota: {
                    semanal: parseFloat(document.getElementById('config-cuota-semanal')?.value) || tasasPredeterminadas.cuota.semanal,
                    quincenal: parseFloat(document.getElementById('config-cuota-quincenal')?.value) || tasasPredeterminadas.cuota.quincenal,
                    '15y30': parseFloat(document.getElementById('config-cuota-15y30')?.value) || tasasPredeterminadas.cuota['15y30'],
                    mensual: parseFloat(document.getElementById('config-cuota-mensual')?.value) || tasasPredeterminadas.cuota.mensual
                }
            };
            
            // Seleccionar la tasa automática
            if (tipo === 'interes') {
                tasaAutomatica = configuracionTasas.interes[frecuencia];
            } else if (tipo === 'cuota') {
                tasaAutomatica = configuracionTasas.cuota[frecuencia];
            }
            
            // Actualizar el campo de tasa
            if (tasaAutomatica && tasaAutomatica > 0) {
                const tasaAnterior = tasaInput.value;
                tasaInput.value = tasaAutomatica;
                
                // Mostrar indicador visual de cambio automático solo si cambió
                if (tasaAnterior != tasaAutomatica) {
                    // Agregar clase de animación
                    tasaInput.classList.add('auto-updated');
                    tasaInput.style.background = 'linear-gradient(135deg, #e8f5e8, #f1f8e9)';
                    tasaInput.style.border = '2px solid #4caf50';
                    tasaInput.style.boxShadow = '0 0 10px rgba(76, 175, 80, 0.3)';
                    
                    // Mostrar indicador "Auto" con animación
                    const indicador = document.getElementById('indicador-tasa-auto');
                    if (indicador) {
                        indicador.style.display = 'inline';
                        indicador.className = 'indicador-auto';
                    }
                    
                    // Remover efectos después de 4 segundos
                    setTimeout(() => {
                        tasaInput.classList.remove('auto-updated');
                        tasaInput.style.background = '';
                        tasaInput.style.border = '';
                        tasaInput.style.boxShadow = '';
                        
                        if (indicador) {
                            indicador.style.display = 'none';
                            indicador.className = '';
                        }
                    }, 4000);
                    
                    console.log(`🎯 Tasa actualizada automáticamente: ${frecuencia} + ${tipo} = ${tasaAutomatica}%`);
                    
                    // Mostrar mensaje informativo más detallado
                    const tipoTexto = tipo === 'interes' ? 'Solo Interés' : 'Cuota Fija';
                    const frecuenciaTexto = frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1);
                    const mensaje = `⚡ Tasa ajustada automáticamente a ${tasaAutomatica}% para ${tipoTexto} ${frecuenciaTexto}`;
                    mostrarMensaje('mensaje-prestamo', mensaje, 'success');
                }
            }
            
            // Actualizar la calculadora en tiempo real
            actualizarCalculadoraEnTiempoReal();
        }
        
        // Función para manejar campos opcionales según el tipo de préstamo
        function toggleCamposOpcionales() {
            const tipoPrestamo = document.getElementById('prestamo-tipo').value;
            const plazoInput = document.getElementById('prestamo-plazo');
            const cuotasInput = document.getElementById('prestamo-cuotas');
            const indicadorPlazo = document.getElementById('indicador-plazo-opcional');
            const indicadorCuotas = document.getElementById('indicador-cuotas-opcional');
            const infoModalidad = document.getElementById('info-modalidad');
            const infoSoloInteres = document.getElementById('info-solo-interes');
            const infoCuotaFija = document.getElementById('info-cuota-fija');
            
            if (tipoPrestamo === 'interes') {
                // Solo Interés: Campos opcionales
                plazoInput.removeAttribute('required');
                cuotasInput.removeAttribute('required');
                
                // Mostrar indicadores de opcional
                if (indicadorPlazo) {
                    indicadorPlazo.style.display = 'inline';
                    indicadorPlazo.style.color = '#ff9800';
                }
                if (indicadorCuotas) {
                    indicadorCuotas.style.display = 'inline';
                    indicadorCuotas.style.color = '#ff9800';
                }
                
                // Estilo visual para campos opcionales
                plazoInput.style.border = '2px dashed #ff9800';
                cuotasInput.style.border = '2px dashed #ff9800';
                plazoInput.style.backgroundColor = '#fff8e1';
                cuotasInput.style.backgroundColor = '#fff8e1';
                
                // Mostrar información específica
                if (infoModalidad) infoModalidad.style.display = 'block';
                if (infoSoloInteres) infoSoloInteres.style.display = 'block';
                if (infoCuotaFija) infoCuotaFija.style.display = 'none';
                
                console.log('🔄 Modo Solo Interés: Plazo y cuotas son opcionales');
                mostrarMensaje('mensaje-prestamo', '💡 En "Solo Interés": Los campos Plazo y Cuotas son opcionales', 'info');
                
            } else if (tipoPrestamo === 'cuota') {
                // Cuota Fija: Campos obligatorios
                plazoInput.setAttribute('required', 'required');
                cuotasInput.setAttribute('required', 'required');
                
                // Ocultar indicadores
                if (indicadorPlazo) indicadorPlazo.style.display = 'none';
                if (indicadorCuotas) indicadorCuotas.style.display = 'none';
                
                // Restablecer estilo normal
                plazoInput.style.border = '';
                cuotasInput.style.border = '';
                plazoInput.style.backgroundColor = '';
                cuotasInput.style.backgroundColor = '';
                
                // Mostrar información específica
                if (infoModalidad) infoModalidad.style.display = 'block';
                if (infoSoloInteres) infoSoloInteres.style.display = 'none';
                if (infoCuotaFija) infoCuotaFija.style.display = 'block';
                
                console.log('📊 Modo Cuota Fija: Plazo y cuotas son obligatorios');
                mostrarMensaje('mensaje-prestamo', '📊 En "Cuota Fija": Los campos Plazo y Cuotas son obligatorios', 'info');
            }
        }
        
        // Función para calcular automáticamente los días basado en cuotas y frecuencia
        function calcularDiasAutomatico() {
            const cuotas = parseInt(document.getElementById('prestamo-cuotas').value) || 0;
            const frecuencia = document.getElementById('prestamo-frecuencia').value;
            const plazoInput = document.getElementById('prestamo-plazo');
            const indicadorPlazoAuto = document.getElementById('indicador-plazo-auto');
            const indicadorPlazoOpcional = document.getElementById('indicador-plazo-opcional');
            
            if (cuotas <= 0) {
                plazoInput.value = '';
                plazoInput.style.backgroundColor = '';
                plazoInput.style.border = '';
                if (indicadorPlazoAuto) indicadorPlazoAuto.style.display = 'none';
                return;
            }
            
            let diasPorCuota = 0;
            let nombreFrecuencia = '';
            
            // Calcular días según la frecuencia
            switch (frecuencia) {
                case 'semanal':
                    diasPorCuota = 7;
                    nombreFrecuencia = 'Semanal';
                    break;
                case 'quincenal':
                    diasPorCuota = 15;
                    nombreFrecuencia = 'Quincenal';
                    break;
                case '15y30':
                    diasPorCuota = 15; // Promedio para 15 y 30
                    nombreFrecuencia = '15 y 30';
                    break;
                case 'mensual':
                    diasPorCuota = 30;
                    nombreFrecuencia = 'Mensual';
                    break;
                default:
                    diasPorCuota = 15; // Por defecto quincenal
                    nombreFrecuencia = 'Quincenal';
            }
            
            const totalDias = cuotas * diasPorCuota;
            
            // Actualizar el campo de plazo
            plazoInput.value = totalDias;
            
            // Estilo visual para indicar que es automático
            plazoInput.style.backgroundColor = '#e8f5e8';
            plazoInput.style.border = '2px solid #4caf50';
            
            // Mostrar indicador de auto-cálculo
            if (indicadorPlazoAuto) indicadorPlazoAuto.style.display = 'inline';
            if (indicadorPlazoOpcional) indicadorPlazoOpcional.style.display = 'none';
            
            // Mostrar mensaje informativo
            const mensajeDiv = document.getElementById('mensaje-prestamo');
            if (mensajeDiv) {
                mensajeDiv.innerHTML = `
                    <div style="background: #e8f5e8; border: 1px solid #4caf50; border-radius: 8px; padding: 12px; margin: 10px 0; color: #2e7d32;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.2em;">🧮</span>
                            <strong>Cálculo Automático de Plazo:</strong>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.95em;">
                            📅 <strong>${cuotas} cuotas ${nombreFrecuencia}</strong> = <strong>${totalDias} días</strong><br>
                            📊 Cálculo: ${cuotas} cuotas × ${diasPorCuota} días por cuota = ${totalDias} días totales
                        </div>
                    </div>
                `;
            }
            
            console.log(`🧮 Cálculo automático: ${cuotas} cuotas ${nombreFrecuencia} = ${totalDias} días`);
        }
        
        // Función para manejar cuando el usuario edita manualmente el plazo
        function manejarCambioPlazoManual() {
            const plazoInput = document.getElementById('prestamo-plazo');
            const indicadorPlazoAuto = document.getElementById('indicador-plazo-auto');
            const mensajeDiv = document.getElementById('mensaje-prestamo');
            
            // Quitar estilo de auto-cálculo
            plazoInput.style.backgroundColor = '';
            plazoInput.style.border = '';
            
            // Ocultar indicador de auto-cálculo
            if (indicadorPlazoAuto) indicadorPlazoAuto.style.display = 'none';
            
            // Limpiar mensaje de auto-cálculo
            if (mensajeDiv && mensajeDiv.innerHTML.includes('Cálculo Automático')) {
                mensajeDiv.innerHTML = `
                    <div style="background: #fff3e0; border: 1px solid #ff9800; border-radius: 8px; padding: 12px; margin: 10px 0; color: #f57c00;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.2em;">✏️</span>
                            <strong>Plazo Manual:</strong>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.95em;">
                            📝 Has editado manualmente el plazo. El auto-cálculo se ha deshabilitado.<br>
                            💡 <em>Tip: Cambia el número de cuotas para reactivar el cálculo automático.</em>
                        </div>
                    </div>
                `;
            }
            
            console.log('✏️ Usuario editó manualmente el plazo - Auto-cálculo deshabilitado');
        }
        
        // Función para actualizar la calculadora en tiempo real
        function actualizarCalculadoraEnTiempoReal() {
            const monto = parseFloat(document.getElementById('prestamo-monto').value) || 0;
            const tasa = parseFloat(document.getElementById('prestamo-tasa').value) || 0;
            const cuotas = parseInt(document.getElementById('prestamo-cuotas').value) || 0;
            const tipo = document.getElementById('prestamo-tipo').value || 'interes';
            const frecuencia = document.getElementById('prestamo-frecuencia').value || 'quincenal';
            
            const resultadoDiv = document.getElementById('resultado-calculo');
            
            // Si faltan datos, mostrar mensaje de espera
            if (monto <= 0 || tasa <= 0 || cuotas <= 0) {
                resultadoDiv.innerHTML = `
                    <div style="text-align: center; color: #666; font-style: italic; font-size: 0.85em;">
                        📊 Completa los campos para ver los cálculos automáticos...
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            if (tipo === 'interes') {
                // CÁLCULO SOLO INTERÉS
                const interesPorPeriodo = monto * (tasa / 100);
                const totalIntereses = interesPorPeriodo * cuotas;
                const totalAPagar = monto + totalIntereses;
                
                html = `
                    <div style="border-left: 3px solid #e53935; padding-left: 8px;">
                        <div style="font-weight: bold; color: #c62828; margin-bottom: 8px; font-size: 0.9em;">
                            🔴 Solo Interés - ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85em;">
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #4caf50;">
                                <strong>💰 Capital:</strong> RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #ff9800;">
                                <strong>📈 Interés total:</strong> RD$${number_format(totalIntereses, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #9c27b0;">
                                <strong>⭐ Pago c/período:</strong> RD$${number_format(interesPorPeriodo, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #2196f3;">
                                <strong>💵 Total a pagar:</strong> RD$${number_format(totalAPagar, 2)}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // CÁLCULO CUOTA FIJA - FÓRMULA CORRECTA: M = P/n + (P · r)
                const tasaDecimal = tasa / 100;
                const capitalPorCuota = monto / cuotas; // P/n - Parte del capital por cuota
                const interesFijoPorCuota = monto * tasaDecimal; // P · r - Interés fijo por cuota
                const cuotaFija = capitalPorCuota + interesFijoPorCuota; // M - Cuota total
                const totalIntereses = interesFijoPorCuota * cuotas;
                const montoTotal = monto + totalIntereses;
                
                html = `
                    <div style="border-left: 3px solid #2196f3; padding-left: 8px;">
                        <div style="font-weight: bold; color: #1976d2; margin-bottom: 8px; font-size: 0.9em;">
                            📊 Cuota Fija - ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85em;">
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #4caf50;">
                                <strong>💰 Capital:</strong> RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #ff9800;">
                                <strong>📈 Interés total:</strong> RD$${number_format(totalIntereses, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #9c27b0;">
                                <strong>⭐ Cuota fija:</strong> RD$${number_format(cuotaFija, 2)}
                            </div>
                            <div style="background: rgba(255,255,255,0.8); padding: 6px; border-radius: 4px; border-left: 2px solid #2196f3;">
                                <strong>💵 Total a pagar:</strong> RD$${number_format(montoTotal, 2)}
                            </div>
                        </div>
                    </div>
                `;
            }
            
            resultadoDiv.innerHTML = html;
        }
        
        // Función para obtener la configuración de tasas actual
        function obtenerConfiguracionTasas() {
            // Valores predeterminados
            const predeterminados = {
                interes: { semanal: 5, quincenal: 10, '15y30': 10, mensual: 20 },
                cuota: { semanal: 5, quincenal: 10, '15y30': 10, mensual: 20 }
            };
            
            return {
                interes: {
                    semanal: parseFloat(document.getElementById('config-interes-semanal')?.value) || predeterminados.interes.semanal,
                    quincenal: parseFloat(document.getElementById('config-interes-quincenal')?.value) || predeterminados.interes.quincenal,
                    '15y30': parseFloat(document.getElementById('config-interes-15y30')?.value) || predeterminados.interes['15y30'],
                    mensual: parseFloat(document.getElementById('config-interes-mensual')?.value) || predeterminados.interes.mensual
                },
                cuota: {
                    semanal: parseFloat(document.getElementById('config-cuota-semanal')?.value) || predeterminados.cuota.semanal,
                    quincenal: parseFloat(document.getElementById('config-cuota-quincenal')?.value) || predeterminados.cuota.quincenal,
                    '15y30': parseFloat(document.getElementById('config-cuota-15y30')?.value) || predeterminados.cuota['15y30'],
                    mensual: parseFloat(document.getElementById('config-cuota-mensual')?.value) || predeterminados.cuota.mensual
                }
            };
        }
        
        // Función para inicializar el sistema de autocompletado
        function inicializarAutocompletado() {
            console.log('Inicializando sistema de autocompletado de tasas...');
            
            // Establecer valores por defecto si es la primera vez
            actualizarTasaAutomatica();
            actualizarResumenTasas();
            
            // Agregar eventos a los campos de configuración para actualizar en tiempo real
            const configIds = [
                'config-interes-semanal', 'config-interes-quincenal', 'config-interes-15y30', 'config-interes-mensual',
                'config-cuota-semanal', 'config-cuota-quincenal', 'config-cuota-15y30', 'config-cuota-mensual'
            ];
            
            configIds.forEach(id => {
                const elemento = document.getElementById(id);
                if (elemento) {
                    elemento.addEventListener('input', () => {
                        console.log(`Configuración ${id} actualizada:`, elemento.value);
                        actualizarTasaAutomatica();
                        actualizarResumenTasas();
                    });
                }
            });
            
            console.log('Sistema de autocompletado inicializado correctamente');
        }
        
        // Función para actualizar el resumen visual de tasas
        function actualizarResumenTasas() {
            const config = obtenerConfiguracionTasas();
            
            // Actualizar resumen de tasas de interés
            const resumenIntereses = {
                semanal: document.getElementById('resumen-interes-semanal'),
                quincenal: document.getElementById('resumen-interes-quincenal'),
                mensual: document.getElementById('resumen-interes-mensual')
            };
            
            // Actualizar resumen de tasas de cuota
            const resumenCuotas = {
                semanal: document.getElementById('resumen-cuota-semanal'),
                quincenal: document.getElementById('resumen-cuota-quincenal'),
                mensual: document.getElementById('resumen-cuota-mensual')
            };
            
            // Aplicar valores con animación
            Object.keys(resumenIntereses).forEach(frecuencia => {
                const elemento = resumenIntereses[frecuencia];
                if (elemento) {
                    const nuevaValor = config.interes[frecuencia] + '%';
                    if (elemento.textContent !== nuevaValor) {
                        elemento.style.animation = 'pulse 0.5s ease-in-out';
                        elemento.textContent = nuevaValor;
                        setTimeout(() => elemento.style.animation = '', 500);
                    }
                }
            });
            
            Object.keys(resumenCuotas).forEach(frecuencia => {
                const elemento = resumenCuotas[frecuencia];
                if (elemento) {
                    const nuevaValor = config.cuota[frecuencia] + '%';
                    if (elemento.textContent !== nuevaValor) {
                        elemento.style.animation = 'pulse 0.5s ease-in-out';
                        elemento.textContent = nuevaValor;
                        setTimeout(() => elemento.style.animation = '', 500);
                    }
                }
            });
        }
        
        // Inicializar cuando la página esté cargada
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando sistema de préstamos...');
            
            // Configurar fecha actual por defecto
            const fechaActual = new Date().toISOString().split('T')[0];
            const fechaInput = document.getElementById('prestamo-fecha');
            if (fechaInput) {
                fechaInput.value = fechaActual;
            }
            
            // Inicializar autocompletado
            setTimeout(() => {
                inicializarAutocompletado();
                toggleCamposOpcionales(); // Configurar campos según tipo inicial
            }, 500);
            
            // Mostrar tab por defecto
            showTab('dashboard');
        });
        
        // Función para probar el autocompletado de tasas
        function probarAutocompletado() {
            console.log('🧪 Iniciando prueba de autocompletado...');
            
            // Mostrar tab de préstamos
            showTab('prestamos');
            
            // Esperar un momento para que se cargue la página
            setTimeout(() => {
                // Rellenar campos básicos
                document.getElementById('prestamo-monto').value = '50000';
                document.getElementById('prestamo-plazo').value = '30';
                document.getElementById('prestamo-cuotas').value = '8';
                
                let paso = 1;
                const totalPasos = 6;
                
                function ejecutarPaso(pasoActual) {
                    console.log(`Ejecutando paso ${pasoActual} de ${totalPasos}...`);
                    
                    switch(pasoActual) {
                        case 1:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Quincenal + Solo Interés (debería ser 10%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'quincenal';
                            document.getElementById('prestamo-tipo').value = 'interes';
                            actualizarTasaAutomatica();
                            break;
                            
                        case 2:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Mensual + Cuota Fija (debería ser 8%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'mensual';
                            document.getElementById('prestamo-tipo').value = 'cuota';
                            actualizarTasaAutomatica();
                            break;
                            
                        case 3:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Semanal + Solo Interés (debería ser 5%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'semanal';
                            document.getElementById('prestamo-tipo').value = 'interes';
                            actualizarTasaAutomatica();
                            break;
                            
                        case 4:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Semanal + Cuota Fija (debería ser 3%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'semanal';
                            document.getElementById('prestamo-tipo').value = 'cuota';
                            actualizarTasaAutomatica();
                            break;
                            
                        case 5:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Quincenal + Cuota Fija (debería ser 5%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'quincenal';
                            document.getElementById('prestamo-tipo').value = 'cuota';
                            actualizarTasaAutomatica();
                            break;
                            
                        case 6:
                            mostrarMensaje('mensaje-prestamo', `📋 Paso ${pasoActual}/${totalPasos}: Probando Mensual + Solo Interés (debería ser 20%)`, 'info');
                            document.getElementById('prestamo-frecuencia').value = 'mensual';
                            document.getElementById('prestamo-tipo').value = 'interes';
                            actualizarTasaAutomatica();
                            
                            // Mensaje final
                            setTimeout(() => {
                                mostrarMensaje('mensaje-prestamo', '✅ ¡Prueba completada! El autocompletado funciona correctamente. Observa cómo cambian las tasas y los cálculos automáticamente.', 'success');
                            }, 2000);
                            return; // No programar más pasos
                    }
                    
                    // Programar el siguiente paso
                    if (pasoActual < totalPasos) {
                        setTimeout(() => ejecutarPaso(pasoActual + 1), 2500);
                    }
                }
                
                // Iniciar la secuencia de pruebas
                ejecutarPaso(1);
                
            }, 500);
        }
        
    </script>
</body>
</html>
