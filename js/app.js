/**
 * MÓDULO JAVASCRIPT PRINCIPAL
 * Sistema de Préstamos Modular - Nova Luz Pro
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
        
        // Búsqueda en tiempo real
        document.addEventListener('input', (e) => {
            if (e.target.matches('[data-search]')) {
                const searchType = e.target.getAttribute('data-search');
                this.handleSearch(searchType, e.target.value);
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
            content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            
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
                    await this.loadReportes();
                    break;
                case 'configuracion':
                    await this.loadConfiguracion();
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
            this.showError('Error en dashboard: ' + error.message);
        }
    }
    
    /**
     * Renderizar dashboard
     */
    renderDashboard(stats) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
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
                                    <h3>${stats.prestamos.activos || 0}</h3>
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
                                    <h3>$${this.formatNumber(stats.prestamos.total_prestado || 0)}</h3>
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
                                    <h3>$${this.formatNumber(stats.prestamos.saldo_pendiente || 0)}</h3>
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
                                    <h3>$${this.formatNumber(stats.mora.mora_pendiente?.monto || 0)}</h3>
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
                                <button class="btn btn-primary" data-view="clientes" data-action="nuevo-cliente">
                                    <i class="fas fa-user-plus"></i> Nuevo Cliente
                                </button>
                                <button class="btn btn-success" data-view="prestamos" data-action="nuevo-prestamo">
                                    <i class="fas fa-handshake"></i> Nuevo Préstamo
                                </button>
                                <button class="btn btn-info" data-view="pagos" data-action="realizar-pago">
                                    <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                </button>
                                <button class="btn btn-warning" data-action="calcular-mora">
                                    <i class="fas fa-calculator"></i> Calcular Mora
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Préstamos por Vencer -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-calendar-alt"></i> Próximos Vencimientos</h5>
                        </div>
                        <div class="card-body">
                            <div id="proximos-vencimientos">
                                <p class="text-muted">Cargando...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Cargar próximos vencimientos
        this.loadProximosVencimientos();
    }
    
    /**
     * Cargar lista de clientes
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
            this.showError('Error en clientes: ' + error.message);
        }
    }
    
    /**
     * Renderizar lista de clientes
     */
    renderClientes(clientes) {
        const content = document.getElementById('main-content');
        
        content.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2><i class="fas fa-users"></i> Clientes</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" data-action="nuevo-cliente">
                        <i class="fas fa-user-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Buscar clientes..." data-search="clientes">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Préstamos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${clientes.map(cliente => `
                            <tr>
                                <td>${cliente.nombre}</td>
                                <td>${cliente.documento}</td>
                                <td>${cliente.telefono || '-'}</td>
                                <td>${cliente.email || '-'}</td>
                                <td>
                                    <span class="badge bg-primary">${cliente.total_prestamos || 0}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" data-action="ver-cliente" data-id="${cliente.id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" data-action="editar-cliente" data-id="${cliente.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" data-action="eliminar-cliente" data-id="${cliente.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }
    
    /**
     * Manejar acciones
     */
    async handleAction(action, element) {
        const id = element.getAttribute('data-id');
        
        try {
            switch (action) {
                case 'nuevo-cliente':
                    this.showClienteForm();
                    break;
                case 'editar-cliente':
                    this.showClienteForm(id);
                    break;
                case 'eliminar-cliente':
                    await this.eliminarCliente(id);
                    break;
                case 'nuevo-prestamo':
                    this.showPrestamoForm();
                    break;
                case 'realizar-pago':
                    this.showPagoForm();
                    break;
                case 'calcular-mora':
                    await this.calcularMora();
                    break;
                default:
                    console.log('Acción no implementada:', action);
            }
        } catch (error) {
            this.showError('Error ejecutando acción: ' + error.message);
        }
    }
    
    /**
     * Realizar llamada a la API
     */
    async apiCall(action, data = {}) {
        const response = await fetch(this.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ...data
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const container = document.getElementById('toast-container');
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remover del DOM después de que se oculte
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    /**
     * Mostrar error
     */
    showError(message) {
        this.showNotification(message, 'danger');
    }
    
    /**
     * Formatear números
     */
    formatNumber(number) {
        return new Intl.NumberFormat('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
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
    window.novaLuzApp = new NovaLuzApp();
});
