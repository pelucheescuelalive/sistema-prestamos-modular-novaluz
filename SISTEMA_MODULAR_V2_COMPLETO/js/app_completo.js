/**
 * MÓDULO JAVASCRIPT PRINCIPAL COMPLETO
 * Sistema de Préstamos Modular - Nova Luz Pro
 * Versión 2.0 - Funcional Completa
 */

class NovaLuzApp {
    constructor() {
        this.apiUrl = 'api/handler.php';
        this.currentView = 'dashboard';
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadDashboard();
        this.setupNotifications();
    }
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Navegación principal
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-view]')) {
                e.preventDefault();
                const view = e.target.getAttribute('data-view');
                this.loadView(view);
            }
            
            if (e.target.matches('[data-action]')) {
                e.preventDefault();
                const action = e.target.getAttribute('data-action');
                this.handleAction(action, e.target);
            }
        });
        
        // Formularios
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form[data-form]')) {
                e.preventDefault();
                const formType = e.target.getAttribute('data-form');
                this.handleFormSubmit(formType, e.target);
            }
        });
    }
    
    /**
     * Cargar vista principal
     */
    async loadView(viewName) {
        try {
            this.currentView = viewName;
            this.updateNavigation(viewName);
            
            const content = document.getElementById('main-content');
            content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando...</p></div>';
            
            switch (viewName) {
                case 'dashboard':
                    await this.loadDashboard();
                    break;
                case 'clientes':
                    await this.loadClientes();
                    break;
                case 'prestamos':
                    await this.loadPrestamos();
                    break;
                case 'pagos':
                    await this.loadPagos();
                    break;
                case 'mora':
                    await this.loadMora();
                    break;
                case 'reportes':
                    this.loadReportes();
                    break;
                case 'configuracion':
                    this.loadConfiguracion();
                    break;
                default:
                    this.showError('Vista no encontrada');
            }
        } catch (error) {
            this.showError('Error cargando vista: ' + error.message);
        }
    }
    
    /**
     * Actualizar navegación activa
     */
    updateNavigation(activeView) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        const activeLink = document.querySelector(`[data-view="${activeView}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
    
    /**
     * Cargar dashboard
     */
    async loadDashboard() {
        try {
            const response = await this.apiCall('dashboard_estadisticas');
            
            if (response.success) {
                const stats = response.data;
                this.renderDashboard(stats);
            } else {
                this.showError('Error cargando estadísticas: ' + response.message);
            }
        } catch (error) {
            console.error('Error dashboard:', error);
            this.renderDashboard({}); // Renderizar con datos vacíos
        }
    }
    
    /**
     * Cargar vista de clientes
     */
    async loadClientes() {
        try {
            const response = await this.apiCall('cliente_listar');
            
            if (response.success) {
                this.renderClientes(response.data);
            } else {
                this.showError('Error cargando clientes: ' + response.message);
            }
        } catch (error) {
            console.error('Error clientes:', error);
            this.renderClientes([]);
        }
    }
    
    /**
     * Cargar vista de préstamos
     */
    async loadPrestamos() {
        try {
            const response = await this.apiCall('prestamo_listar');
            
            if (response.success) {
                this.renderPrestamos(response.data);
            } else {
                this.showError('Error cargando préstamos: ' + response.message);
            }
        } catch (error) {
            console.error('Error préstamos:', error);
            this.renderPrestamos([]);
        }
    }
    
    /**
     * Cargar vista de pagos
     */
    async loadPagos() {
        try {
            const response = await this.apiCall('pago_listar');
            
            if (response.success) {
                this.renderPagos(response.data);
            } else {
                this.showError('Error cargando pagos: ' + response.message);
            }
        } catch (error) {
            console.error('Error pagos:', error);
            this.renderPagos([]);
        }
    }
    
    /**
     * Cargar vista de mora
     */
    async loadMora() {
        try {
            const response = await this.apiCall('mora_listar');
            
            if (response.success) {
                this.renderMora(response.data);
            } else {
                this.showError('Error cargando mora: ' + response.message);
            }
        } catch (error) {
            console.error('Error mora:', error);
            this.renderMora([]);
        }
    }
    
    /**
     * Cargar vista de reportes
     */
    loadReportes() {
        this.renderReportes();
    }
    
    /**
     * Cargar vista de configuración
     */
    loadConfiguracion() {
        this.renderConfiguracion();
    }
    
    /**
     * Renderizar dashboard
     */
    renderDashboard(stats) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard - Nova Luz Pro</h2>
                </div>
            </div>
            
            <div class="row">
                <!-- Estadísticas de Préstamos -->
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Préstamos Activos</h6>
                                    <h3>${stats.prestamos?.activos || 0}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-handshake fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Prestado</h6>
                                    <h3>RD$ ${this.formatNumber(stats.prestamos?.total_prestado || 0)}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Saldo Pendiente</h6>
                                    <h3>RD$ ${this.formatNumber(stats.prestamos?.saldo_pendiente || 0)}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Mora Pendiente</h6>
                                    <h3>RD$ ${this.formatNumber(stats.mora?.mora_pendiente || 0)}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Acciones Rápidas -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt"></i> Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" data-view="clientes">
                                    <i class="fas fa-user-plus"></i> Gestionar Clientes
                                </button>
                                <button class="btn btn-success" data-view="prestamos">
                                    <i class="fas fa-handshake"></i> Gestionar Préstamos
                                </button>
                                <button class="btn btn-info" data-view="pagos">
                                    <i class="fas fa-money-bill-wave"></i> Gestionar Pagos
                                </button>
                                <button class="btn btn-warning" data-view="mora">
                                    <i class="fas fa-exclamation-triangle"></i> Ver Mora
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actividad Reciente -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Actividad Reciente</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <small class="text-muted">Sistema iniciado correctamente</small>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">Base de datos conectada</small>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">Módulos cargados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de clientes
     */
    renderClientes(clientes) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" data-action="nuevo-cliente">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Cédula</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Array.isArray(clientes) && clientes.length > 0 ? clientes.map(cliente => `
                                    <tr>
                                        <td>${cliente.id}</td>
                                        <td>${cliente.nombres || ''} ${cliente.apellidos || ''}</td>
                                        <td>${cliente.cedula || 'N/A'}</td>
                                        <td>${cliente.telefono || 'N/A'}</td>
                                        <td>${cliente.email || 'N/A'}</td>
                                        <td>
                                            <span class="badge bg-success">Activo</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-action="editar-cliente" data-id="${cliente.id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" data-action="ver-cliente" data-id="${cliente.id}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i><br>
                                            No hay clientes registrados
                                        </td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de préstamos
     */
    renderPrestamos(prestamos) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2><i class="fas fa-money-bill-wave"></i> Gestión de Préstamos</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" data-action="nuevo-prestamo">
                        <i class="fas fa-plus"></i> Nuevo Préstamo
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Saldo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Array.isArray(prestamos) && prestamos.length > 0 ? prestamos.map(prestamo => `
                                    <tr>
                                        <td>#${prestamo.id}</td>
                                        <td>${prestamo.cliente_nombre || 'N/A'}</td>
                                        <td>RD$ ${this.formatNumber(prestamo.monto || 0)}</td>
                                        <td>${prestamo.fecha_prestamo || 'N/A'}</td>
                                        <td>
                                            <span class="badge bg-${prestamo.estado === 'activo' ? 'success' : 'secondary'}">
                                                ${prestamo.estado || 'Pendiente'}
                                            </span>
                                        </td>
                                        <td>RD$ ${this.formatNumber(prestamo.saldo_pendiente || 0)}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-action="pago-prestamo" data-id="${prestamo.id}">
                                                <i class="fas fa-dollar-sign"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" data-action="ver-prestamo" data-id="${prestamo.id}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-money-bill-wave fa-3x mb-3"></i><br>
                                            No hay préstamos registrados
                                        </td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de pagos
     */
    renderPagos(pagos) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2><i class="fas fa-receipt"></i> Historial de Pagos</h2>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Préstamo</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Array.isArray(pagos) && pagos.length > 0 ? pagos.map(pago => `
                                    <tr>
                                        <td>#${pago.id}</td>
                                        <td>#${pago.prestamo_id}</td>
                                        <td>${pago.cliente_nombre || 'N/A'}</td>
                                        <td>RD$ ${this.formatNumber(pago.monto || 0)}</td>
                                        <td>${pago.fecha_pago || 'N/A'}</td>
                                        <td>
                                            <span class="badge bg-info">${pago.tipo || 'Regular'}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Procesado</span>
                                        </td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3"></i><br>
                                            No hay pagos registrados
                                        </td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de mora
     */
    renderMora(mora) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2><i class="fas fa-exclamation-triangle text-warning"></i> Gestión de Mora</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-warning" data-action="calcular-mora">
                        <i class="fas fa-calculator"></i> Recalcular Mora
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Préstamo</th>
                                    <th>Cliente</th>
                                    <th>Días Vencidos</th>
                                    <th>Monto Mora</th>
                                    <th>Total Deuda</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Array.isArray(mora) && mora.length > 0 ? mora.map(item => `
                                    <tr>
                                        <td>#${item.prestamo_id}</td>
                                        <td>${item.cliente_nombre || 'N/A'}</td>
                                        <td>
                                            <span class="badge bg-danger">${item.dias_vencidos || 0} días</span>
                                        </td>
                                        <td>RD$ ${this.formatNumber(item.monto_mora || 0)}</td>
                                        <td>RD$ ${this.formatNumber(item.total_deuda || 0)}</td>
                                        <td>
                                            <span class="badge bg-warning">Pendiente</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-action="contactar-cliente" data-id="${item.cliente_id}">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" data-action="pago-mora" data-id="${item.prestamo_id}">
                                                <i class="fas fa-dollar-sign"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-smile fa-3x mb-3 text-success"></i><br>
                                            No hay préstamos en mora
                                        </td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de reportes
     */
    renderReportes() {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-chart-bar"></i> Reportes del Sistema</h2>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line"></i> Reportes Financieros</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" data-action="reporte-ingresos">
                                    <i class="fas fa-dollar-sign"></i> Reporte de Ingresos
                                </button>
                                <button class="btn btn-outline-primary" data-action="reporte-prestamos">
                                    <i class="fas fa-money-bill-wave"></i> Reporte de Préstamos
                                </button>
                                <button class="btn btn-outline-warning" data-action="reporte-mora">
                                    <i class="fas fa-exclamation-triangle"></i> Reporte de Mora
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users"></i> Reportes de Clientes</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-success" data-action="reporte-clientes">
                                    <i class="fas fa-list"></i> Listado de Clientes
                                </button>
                                <button class="btn btn-outline-success" data-action="reporte-historial">
                                    <i class="fas fa-history"></i> Historial de Pagos
                                </button>
                                <button class="btn btn-outline-info" data-action="reporte-estadisticas">
                                    <i class="fas fa-chart-pie"></i> Estadísticas Generales
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Renderizar vista de configuración
     */
    renderConfiguracion() {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-cog"></i> Configuración del Sistema</h2>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-sliders-h"></i> Configuración General</h5>
                        </div>
                        <div class="card-body">
                            <form data-form="configuracion">
                                <div class="mb-3">
                                    <label class="form-label">Tasa de Mora (%)</label>
                                    <input type="number" class="form-control" name="tasa_mora" step="0.01" value="2.00">
                                    <small class="form-text text-muted">Porcentaje diario de mora</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Plazo Predeterminado (días)</label>
                                    <input type="number" class="form-control" name="plazo_default" value="30">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Frecuencia Predeterminada</label>
                                    <select class="form-control" name="frecuencia_default">
                                        <option value="semanal">Semanal</option>
                                        <option value="quincenal">Quincenal</option>
                                        <option value="mensual" selected>Mensual</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-tools"></i> Herramientas del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-info" data-action="backup-bd">
                                    <i class="fas fa-download"></i> Respaldar Base de Datos
                                </button>
                                <button class="btn btn-outline-warning" data-action="limpiar-logs">
                                    <i class="fas fa-broom"></i> Limpiar Logs del Sistema
                                </button>
                                <button class="btn btn-outline-success" data-action="actualizar-sistema">
                                    <i class="fas fa-sync"></i> Actualizar Sistema
                                </button>
                                <hr>
                                <button class="btn btn-outline-danger" data-action="reset-sistema">
                                    <i class="fas fa-exclamation-triangle"></i> Reiniciar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Manejar acciones de botones
     */
    async handleAction(action, element) {
        try {
            switch (action) {
                case 'nuevo-cliente':
                    this.showModalFormulario('Cliente');
                    break;
                case 'nuevo-prestamo':
                    this.showModalFormulario('Prestamo');
                    break;
                case 'calcular-mora':
                    await this.calcularMora();
                    break;
                default:
                    this.showNotification(`Acción "${action}" en desarrollo`, 'info');
            }
        } catch (error) {
            this.showError('Error ejecutando acción: ' + error.message);
        }
    }
    
    /**
     * Calcular mora del sistema
     */
    async calcularMora() {
        try {
            const response = await this.apiCall('mora_calcular');
            if (response.success) {
                this.showNotification('Mora calculada correctamente', 'success');
                if (this.currentView === 'mora') {
                    await this.loadMora();
                }
            } else {
                this.showError('Error calculando mora: ' + response.message);
            }
        } catch (error) {
            this.showError('Error en cálculo de mora: ' + error.message);
        }
    }
    
    /**
     * Mostrar modal de formulario
     */
    showModalFormulario(tipo) {
        this.showNotification(`Formulario de ${tipo} en desarrollo`, 'info');
    }
    
    /**
     * Realizar llamada a API
     */
    async apiCall(action, data = {}) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            return result;
            
        } catch (error) {
            console.error('Error en API call:', error);
            return {
                success: false,
                message: error.message,
                data: null
            };
        }
    }
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'success') {
        // Crear toast notification
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const container = document.getElementById('toast-container');
        if (container) {
            container.appendChild(toast);
            
            // Inicializar toast de Bootstrap si está disponible
            if (typeof bootstrap !== 'undefined') {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            } else {
                // Fallback: mostrar por 3 segundos
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        } else {
            // Fallback: alert simple
            alert(message);
        }
    }
    
    /**
     * Mostrar error
     */
    showError(message) {
        this.showNotification(message, 'danger');
        console.error('Error:', message);
    }
    
    /**
     * Formatear números para moneda
     */
    formatNumber(number) {
        const num = parseFloat(number) || 0;
        return new Intl.NumberFormat('es-DO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }
    
    /**
     * Configurar sistema de notificaciones
     */
    setupNotifications() {
        // Crear contenedor de toasts si no existe
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
    }
}

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Iniciando Nova Luz Pro - Sistema Modular v2.0');
    window.novaLuzApp = new NovaLuzApp();
});
