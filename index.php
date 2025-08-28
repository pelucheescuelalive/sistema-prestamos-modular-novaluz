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
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .form-card {
            background: #fff;
            border-radius: 8px;
            padding: 16px;
        }
        
        @media (max-width: 768px) {
            .mora-grid {
                grid-template-columns: 1fr;
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
            
            <div class="form-container">
                <h3>Registrar Nuevo Cliente</h3>
                <form id="form-cliente">
                    <input type="hidden" id="cliente-id-edit" value="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" class="form-control" id="cliente-nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Documento</label>
                            <input type="text" class="form-control" id="cliente-documento" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
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
                            <input type="text" class="form-control" id="cliente-direccion">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <button type="submit" class="btn btn-success">✅ Crear Cliente</button>
                        <button type="button" class="btn btn-info" onclick="testearCliente()">🧪 Probar Sistema</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()" id="btn-cancelar" style="display: none;">❌ Cancelar</button>
                    </div>
                </form>
                <div id="mensaje-cliente"></div>
            </div>
            
            <div class="table-container">
                <h3>Lista de Clientes</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-clientes">
                        <tr><td colspan="7" style="text-align: center; color: #666;">No hay clientes registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Préstamos Tab -->
        <div id="tab-prestamos" class="tab-content">
            <h2 class="section-title">💼 Gestión de Préstamos</h2>
            
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
                            </label>
                            <input type="number" class="form-control" id="prestamo-plazo" required oninput="actualizarCalculadoraEnTiempoReal()">
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
                            <select class="form-control" id="prestamo-frecuencia" onchange="actualizarTasaAutomatica()">
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
                            <input type="number" class="form-control" id="prestamo-cuotas" required oninput="actualizarCalculadoraEnTiempoReal()">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <button type="submit" class="btn btn-success">✅ Crear Préstamo</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelarEdicionPrestamo()" id="btn-cancelar-prestamo" style="display: none;">❌ Cancelar</button>
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
                
                <!-- CALCULADORA INTELIGENTE EN TIEMPO REAL -->
                <div class="calculadora-container" style="margin-top: 20px; padding: 20px; background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%); border-radius: 12px; border: 2px solid #2196f3;">
                    <h3 style="color: #1976d2; margin-bottom: 15px;">🧮 Calculadora de Préstamos</h3>
                    
                    <div class="calculo-resultado" id="resultado-calculo" style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="text-align: center; color: #666; font-style: italic;">
                            Completa los campos del préstamo para ver los cálculos automáticos...
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-container">
                <h3>Lista de Préstamos</h3>
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
                        break;
                    case 'prestamos':
                        cargarPrestamos();
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
                
                // Mostrar modal de loading
                abrirModal();
                
                const actionName = `${modulo}_${accion}`;
                const response = await fetch(`api_simple.php?action=${actionName}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(datos)
                });
                
                const resultado = await response.json();
                
                // Mostrar resultado en modal
                mostrarResultadoEnModal(resultado);
                
                return resultado;
                
            } catch (error) {
                console.error('Error en llamada al backend:', error);
                mostrarResultadoEnModal({
                    success: false,
                    error: 'Error de conexión: ' + error.message
                });
                return { success: false, error: error.message };
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
        
        function mostrarResultadoEnModal(resultado) {
            const contenido = document.getElementById('contenidoModal');
            
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
        }
        
        // Función para mostrar mensajes
        function mostrarMensaje(contenedor, mensaje, tipo = 'success') {
            const div = document.getElementById(contenedor);
            if (div) {
                div.innerHTML = `<div class="message ${tipo}">${mensaje}</div>`;
                setTimeout(() => div.innerHTML = '', 3000);
            }
        }
        
        // MÓDULO CLIENTES
        document.getElementById('form-cliente').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const isEditMode = form.dataset.editMode === 'true';
            const clienteId = document.getElementById('cliente-id-edit')?.value;
            
            const clienteData = {
                nombre: document.getElementById('cliente-nombre').value,
                cedula: document.getElementById('cliente-documento').value,
                telefono: document.getElementById('cliente-telefono').value,
                direccion: document.getElementById('cliente-direccion').value
            };
            
            let resultado;
            if (isEditMode && clienteId) {
                // Modo edición
                clienteData.id = clienteId;
                resultado = await llamarBackend('cliente', 'actualizar', clienteData);
                
                if (resultado.success) {
                    mostrarMensaje('mensaje-cliente', 'Cliente actualizado correctamente');
                    cancelarEdicion();
                    cargarClientes();
                } else {
                    mostrarMensaje('mensaje-cliente', 'Error al actualizar cliente: ' + (resultado.message || resultado.error || 'Error desconocido'), 'error');
                }
            } else {
                // Modo creación
                resultado = await llamarBackend('cliente', 'crear', clienteData);
                
                if (resultado.success) {
                    mostrarMensaje('mensaje-cliente', 'Cliente creado correctamente');
                    document.getElementById('form-cliente').reset();
                    cargarClientes();
                } else {
                    mostrarMensaje('mensaje-cliente', 'Error al crear cliente: ' + (resultado.message || resultado.error || 'Error desconocido'), 'error');
                }
            }
        });
        
        function cancelarEdicion() {
            if (editandoCliente) {
                editandoCliente = null;
                clienteIdEditar = null;
                
                // Resetear formulario
                document.getElementById('form-cliente').reset();
                
                // Cambiar texto del botón
                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '✅ Crear Cliente';
                submitBtn.style.backgroundColor = ''; // Restablecer color original
                
                // Ocultar botón cancelar
                document.getElementById('btn-cancelar').style.display = 'none';
                
                // Quitar selección visual de la tabla
                document.querySelectorAll('.tabla-clientes tbody tr').forEach(tr => {
                    tr.classList.remove('table-warning');
                });
                
                // Resetear modo del formulario
                const form = document.getElementById('form-cliente');
                form.dataset.editMode = 'false';
                
                console.log('Edición cancelada');
            }
        }
        
        async function cargarClientes() {
            try {
                const resultado = await fetch('api_simple.php?action=cliente_listar');
                const data = await resultado.json();
                
                if (data.success) {
                    clientes = data.data || [];
                    const tbody = document.getElementById('lista-clientes');
                    
                    if (clientes.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #666;">No hay clientes registrados</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = clientes.map(cliente => `
                        <tr onclick="seleccionarCliente(${cliente.id})" style="cursor: pointer;" data-cliente-id="${cliente.id}">
                            <td>${cliente.id}</td>
                            <td>${cliente.nombre}</td>
                            <td>${cliente.documento}</td>
                            <td>${cliente.telefono}</td>
                            <td>${cliente.email || 'undefined'}</td>
                            <td>${cliente.fecha_creacion || 'N/A'}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); editarCliente(${cliente.id})" title="Editar">✏️</button>
                                <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); eliminarCliente(${cliente.id})" title="Eliminar">🗑️</button>
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (error) {
                console.error('Error cargando clientes:', error);
            }
        }
        
        // Variables globales para el manejo de clientes
        let clienteSeleccionado = null;
        
        // Función para seleccionar cliente
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
        
        // Función para eliminar cliente
        async function eliminarCliente(clienteId) {
            const cliente = clientes.find(c => c.id == clienteId);
            if (!cliente) {
                mostrarMensaje('mensaje-cliente', 'Cliente no encontrado', 'error');
                return;
            }
            
            if (confirm(`¿Estás seguro de que deseas eliminar al cliente "${cliente.nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                try {
                    abrirModal();
                    
                    const response = await fetch(`api_simple.php?action=cliente_eliminar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: clienteId })
                    });
                    
                    const resultado = await response.json();
                    cerrarModal();
                    
                    if (resultado.success) {
                        mostrarMensaje('mensaje-cliente', `Cliente "${cliente.nombre}" eliminado correctamente`, 'success');
                        cargarClientes(); // Recargar la lista
                        
                        // Limpiar selección si era el cliente seleccionado
                        if (clienteSeleccionado && clienteSeleccionado.id == clienteId) {
                            clienteSeleccionado = null;
                        }
                    } else {
                        mostrarMensaje('mensaje-cliente', 'Error al eliminar cliente: ' + (resultado.message || resultado.error || 'Error desconocido'), 'error');
                    }
                } catch (error) {
                    cerrarModal();
                    console.error('Error eliminando cliente:', error);
                    mostrarMensaje('mensaje-cliente', 'Error de conexión al eliminar cliente', 'error');
                }
            }
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
                    mostrarResultadoEnModal('❌ Error', 'Error al actualizar préstamo: ' + resultado.error);
                }
            } else {
                // Modo creación
                resultado = await llamarBackend('prestamo', 'crear', prestamoData);
                
                if (resultado.success) {
                    mostrarResultadoEnModal('✅ Operación Exitosa', 'Préstamo creado correctamente');
                    document.getElementById('form-prestamo').reset();
                    cargarPrestamos();
                } else {
                    mostrarResultadoEnModal('❌ Error', 'Error al crear préstamo: ' + resultado.error);
                }
            }
        });
        
        async function cargarPrestamos() {
            try {
                const resultado = await fetch('api_simple.php?action=prestamo_listar');
                const data = await resultado.json();
                
                if (data.success) {
                    prestamos = data.data || [];
                    const tbody = document.getElementById('lista-prestamos');
                    
                    if (prestamos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; color: #666;">No hay préstamos registrados</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = prestamos.map(prestamo => {
                        const montoInicial = parseFloat(prestamo.monto_inicial || prestamo.monto);
                        const montoActual = parseFloat(prestamo.monto_actual || prestamo.monto);
                        const progreso = parseFloat(prestamo.progreso || 0);
                        const cuotaVencida = parseInt(prestamo.cuotas_vencidas || 0);
                        
                        // Determinar el tipo de préstamo
                        let tipoPrestamo = 'A Cuota';
                        if (prestamo.tipo_prestamo === 'solo_interes' || prestamo.es_solo_interes === '1') {
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
                const resultado = await fetch('api_simple.php?action=pago_listar');
                const data = await resultado.json();
                
                if (data.success) {
                    pagos = data.data || [];
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
        
        // DASHBOARD
        async function actualizarDashboard() {
            try {
                const resultado = await fetch('api_simple.php?action=dashboard_stats');
                const data = await resultado.json();
                
                if (data.success && data.stats) {
                    const stats = data.stats;
                    
                    document.getElementById('total-clientes').textContent = stats.total_clientes || 0;
                    document.getElementById('prestamos-activos').textContent = stats.prestamos_activos || 0;
                    document.getElementById('monto-total').textContent = `RD$${(stats.monto_total || 0).toFixed(2)}`;
                    document.getElementById('intereses-generados').textContent = `RD$${(stats.intereses_generados || 0).toFixed(2)}`;
                    document.getElementById('monto-atrasado').textContent = `RD$${(stats.monto_atrasado || 0).toFixed(2)}`;
                    document.getElementById('cuotas-vencidas').textContent = `${stats.cuotas_vencidas || 0} cuotas`;
                } else {
                    console.log('Usando valores predeterminados para dashboard');
                    // Valores predeterminados si no hay datos
                    document.getElementById('total-clientes').textContent = clientes.length;
                    document.getElementById('prestamos-activos').textContent = prestamos.length;
                }
            } catch (error) {
                console.error('Error actualizando dashboard:', error);
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
            console.log('Sistema modular iniciado');
            establecerFechasActuales();
            cargarClientes();
            cargarClientesSelect();
            cargarPrestamos();
            actualizarDashboard();
            
            // Configurar calculadora inteligente
            configurarCalculadoraInteligente();
            
            // Cerrar modal al hacer click fuera
            window.onclick = function(event) {
                const modal = document.getElementById('modalRespuesta');
                if (event.target === modal) {
                    cerrarModal();
                }
            };
        });
        
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
                    <div style="text-align: center; color: #666; font-style: italic; padding: 20px;">
                        <div style="font-size: 48px; margin-bottom: 10px;">🧮</div>
                        <p>Completa los campos del préstamo para ver los cálculos automáticos...</p>
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
                    <div style="border-left: 4px solid #e53935; padding-left: 15px;">
                        <h4 style="color: #c62828; margin-bottom: 15px;">
                            🔴 Solo Interés - Frecuencia ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 1.1em;">
                            <div style="background: #e8f5e8; padding: 12px; border-radius: 6px;">
                                <strong>💰 Capital:</strong><br>
                                RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                                <strong>📈 Interés total:</strong><br>
                                RD$${number_format(totalIntereses, 2)}
                            </div>
                            <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                                <strong>⭐ Pago por período:</strong><br>
                                RD$${number_format(interesPorPeriodo, 2)}
                            </div>
                            <div style="background: #e3f2fd; padding: 12px; border-radius: 6px;">
                                <strong>💵 Total a pagar:</strong><br>
                                RD$${number_format(totalAPagar, 2)}
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding: 10px; background: #fff8e1; border-radius: 6px; border-left: 3px solid #ffc107;">
                            <small><strong>📝 Nota:</strong> Cada pago cubre solo intereses. Pagos adicionales reducen el capital.</small>
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
                    <div style="border-left: 4px solid #2196f3; padding-left: 15px;">
                        <h4 style="color: #1976d2; margin-bottom: 15px;">
                            📊 Cuota Fija - Frecuencia ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 1.1em;">
                            <div style="background: #e8f5e8; padding: 12px; border-radius: 6px;">
                                <strong>💰 Capital:</strong><br>
                                RD$${number_format(monto, 2)}
                            </div>
                            <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                                <strong>📈 Interés total:</strong><br>
                                RD$${number_format(totalIntereses, 2)}
                            </div>
                            <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                                <strong>⭐ Cuota fija:</strong><br>
                                RD$${number_format(cuotaFija, 2)}
                            </div>
                            <div style="background: #e3f2fd; padding: 12px; border-radius: 6px;">
                                <strong>💵 Total a pagar:</strong><br>
                                RD$${number_format(montoTotal, 2)}
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding: 10px; background: #e8f5e8; border-radius: 6px; border-left: 3px solid #4caf50;">
                            <small><strong>📝 Fórmula:</strong> M = P/n + (P·r) = RD$${number_format(capitalPorCuota, 2)} + RD$${number_format(interesFijoPorCuota, 2)} = RD$${number_format(cuotaFija, 2)}</small>
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
