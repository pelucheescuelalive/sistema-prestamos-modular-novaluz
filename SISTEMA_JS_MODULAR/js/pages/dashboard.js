/* ===================================================================
   🎯 PÁGINA DASHBOARD ULTRA-MODERNA
   Vista principal con estadísticas y resumen del sistema
   =================================================================== */

export class DashboardPage {
  constructor(app) {
    this.app = app;
    this.data = {
      estadisticas: {},
      prestamosRecientes: [],
      pagosRecientes: [],
      clientesEnMora: []
    };
  }

  async render() {
    await this.loadData();
    
    return `
      <div class="dashboard-page animate-fadeInUp">
        <!-- 🎯 Header del Dashboard Ultra-Moderno -->
        <div class="dashboard-header">
          <div class="welcome-section">
            <h1 class="page-title">
              <i class="fas fa-tachometer-alt"></i>
              Dashboard Nova Luz Pro
            </h1>
            <p class="page-subtitle">
              ✨ Bienvenido al Sistema de Préstamos Profesional - Vista General Ejecutiva
            </p>
          </div>
          <div class="quick-actions">
            <button class="btn btn-primary" onclick="app.navigate('clientes')">
              <i class="fas fa-user-plus"></i>
              Nuevo Cliente
            </button>
            <button class="btn btn-success" onclick="app.navigate('prestamos')">
              <i class="fas fa-money-bill-wave"></i>
              Nuevo Préstamo
            </button>
            <button class="btn btn-warning" onclick="app.navigate('pagos')">
              <i class="fas fa-credit-card"></i>
              Registrar Pago
            </button>
            <button class="btn btn-secondary" onclick="app.navigate('reportes')">
              <i class="fas fa-chart-bar"></i>
              Reportes
            </button>
          </div>
        </div>

        <!-- 📊 Tarjetas de Estadísticas Ultra-Modernas -->
        <div class="stats-grid">
          ${this.renderStatsCards()}
        </div>

        <!-- 📈 Secciones de Información -->
        <div class="dashboard-grid">
          <!-- Préstamos Recientes -->
          <div class="card animate-fadeInLeft">
            <div class="card-header">
              <div class="card-title">
                <i class="fas fa-money-bill-wave"></i>
                Préstamos Recientes
              </div>
              <button class="btn btn-outline btn-sm" onclick="app.navigate('prestamos')">
                Ver Todos
              </button>
            </div>
            <div class="card-body">
              ${this.renderPrestamosRecientes()}
            </div>
          </div>

          <!-- Pagos Recientes -->
          <div class="card animate-fadeInRight">
            <div class="card-header">
              <div class="card-title">
                <i class="fas fa-credit-card"></i>
                Pagos Recientes
              </div>
              <button class="btn btn-outline btn-sm" onclick="app.navigate('pagos')">
                Ver Todos
              </button>
            </div>
            <div class="card-body">
              ${this.renderPagosRecientes()}
            </div>
          </div>

          <!-- Clientes en Mora -->
          <div class="card animate-fadeInLeft">
            <div class="card-header">
              <div class="card-title">
                <i class="fas fa-exclamation-triangle"></i>
                Clientes en Mora
              </div>
              <button class="btn btn-outline btn-sm" onclick="app.navigate('mora')">
                Ver Detalles
              </button>
            </div>
            <div class="card-body">
              ${this.renderClientesEnMora()}
            </div>
          </div>

          <!-- Resumen Mensual -->
          <div class="card animate-fadeInRight">
            <div class="card-header">
              <div class="card-title">
                <i class="fas fa-chart-line"></i>
                Resumen del Mes
              </div>
              <button class="btn btn-outline btn-sm" onclick="app.navigate('calculadora')">
                Calculadora
              </button>
            </div>
            <div class="card-body">
              ${this.renderResumenMensual()}
            </div>
          </div>
        </div>

        <!-- 🚨 Alertas y Notificaciones -->
        <div class="dashboard-alerts animate-fadeInUp">
          ${this.renderAlertas()}
        </div>
      </div>
    `;
  }

  renderStatsCards() {
    const stats = this.data.estadisticas;
    
    return `
      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.1s">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-number">${stats.totalClientes || 0}</div>
        <div class="stat-label">Total Clientes</div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i>
          +${stats.clientesNuevos || 0} este mes
        </div>
      </div>

      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.2s">
        <div class="stat-icon" style="background: var(--success-gradient);">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-number">${stats.prestamosActivos || 0}</div>
        <div class="stat-label">Préstamos Activos</div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i>
          +${stats.prestamosNuevos || 0} activos
        </div>
      </div>

      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.3s">
        <div class="stat-icon" style="background: var(--warning-gradient);">
          <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-number">${stats.pagosHoy || 0}</div>
        <div class="stat-label">Pagos de Hoy</div>
        <div class="stat-change positive">
          <i class="fas fa-check"></i>
          ${stats.pagosCompletados || 0} pagos
        </div>
      </div>

      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.4s">
        <div class="stat-icon" style="background: var(--danger-gradient);">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-number">${stats.clientesEnMora || 0}</div>
        <div class="stat-label">Clientes en Mora</div>
        <div class="stat-change ${stats.clientesEnMora > 0 ? 'negative' : 'positive'}">
          <i class="fas fa-clock"></i>
          ${stats.diasPromedioMora || 0} días prom.
        </div>
      </div>

      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.5s">
        <div class="stat-icon" style="background: var(--secondary-gradient);">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-number">$${this.formatCurrency(stats.montoTotal || 0)}</div>
        <div class="stat-label">Monto Total</div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i>
          Cartera activa
        </div>
      </div>

      <div class="stat-card animate-fadeInUp" style="animation-delay: 0.6s">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-number">${stats.tasaRecuperacion || 95}%</div>
        <div class="stat-label">Tasa Recuperación</div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i>
          Excelente
        </div>
      </div>
    `;
  }

  renderPrestamosRecientes() {
    if (!this.data.prestamosRecientes || !this.data.prestamosRecientes.length) {
      return `
        <div class="empty-state">
          <i class="fas fa-money-bill-wave"></i>
          <h4>No hay préstamos recientes</h4>
          <p>Los préstamos aparecerán aquí cuando se registren</p>
          <button class="btn btn-primary btn-sm" onclick="app.navigate('prestamos')">
            <i class="fas fa-plus"></i>
            Crear Primer Préstamo
          </button>
        </div>
      `;
    }

    return `
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Monto</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${this.data.prestamosRecientes.map(prestamo => `
              <tr>
                <td>
                  <div class="user-info">
                    <span class="user-avatar">👤</span>
                    ${prestamo.cliente || 'Cliente N/A'}
                  </div>
                </td>
                <td class="amount">$${this.formatCurrency(prestamo.monto || 0)}</td>
                <td class="date">${this.formatDate(prestamo.fecha)}</td>
                <td>
                  <span class="badge badge-${prestamo.estado === 'activo' ? 'success' : 'warning'}">
                    ${prestamo.estado || 'Pendiente'}
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-icon btn-view" title="Ver detalles">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon btn-edit" title="Editar">
                      <i class="fas fa-edit"></i>
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

  renderPagosRecientes() {
    if (!this.data.pagosRecientes || !this.data.pagosRecientes.length) {
      return `
        <div class="empty-state">
          <i class="fas fa-credit-card"></i>
          <h4>No hay pagos recientes</h4>
          <p>Los pagos aparecerán aquí cuando se registren</p>
          <button class="btn btn-success btn-sm" onclick="app.navigate('pagos')">
            <i class="fas fa-plus"></i>
            Registrar Primer Pago
          </button>
        </div>
      `;
    }

    return `
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Monto</th>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            ${this.data.pagosRecientes.map(pago => `
              <tr>
                <td>
                  <div class="user-info">
                    <span class="user-avatar">👤</span>
                    ${pago.cliente || 'Cliente N/A'}
                  </div>
                </td>
                <td class="amount">$${this.formatCurrency(pago.monto || 0)}</td>
                <td class="date">${this.formatDate(pago.fecha)}</td>
                <td>${pago.tipo || 'Cuota'}</td>
                <td>
                  <span class="badge badge-success">
                    ${pago.estado || 'Completado'}
                  </span>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderClientesEnMora() {
    if (!this.data.clientesEnMora || !this.data.clientesEnMora.length) {
      return `
        <div class="empty-state">
          <i class="fas fa-check-circle"></i>
          <h4>¡No hay clientes en mora!</h4>
          <p>Todos los pagos están al día</p>
          <span class="badge badge-success">Estado Excelente</span>
        </div>
      `;
    }

    return `
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Días Mora</th>
              <th>Monto Vencido</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${this.data.clientesEnMora.map(cliente => `
              <tr>
                <td>
                  <div class="user-info">
                    <span class="user-avatar">👤</span>
                    ${cliente.nombre || 'Cliente N/A'}
                  </div>
                </td>
                <td>
                  <span class="badge badge-danger">
                    ${cliente.diasMora || 0} días
                  </span>
                </td>
                <td class="amount">$${this.formatCurrency(cliente.montoVencido || 0)}</td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-icon btn-warning" title="Contactar">
                      <i class="fas fa-phone"></i>
                    </button>
                    <button class="btn-icon btn-view" title="Ver detalles">
                      <i class="fas fa-eye"></i>
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

  renderResumenMensual() {
    const stats = this.data.estadisticas;
    
    return `
      <div class="monthly-summary">
        <div class="summary-grid">
          <div class="summary-item">
            <div class="summary-icon">
              <i class="fas fa-handshake"></i>
            </div>
            <div class="summary-content">
              <div class="summary-label">Préstamos Otorgados</div>
              <div class="summary-value">${stats.prestamosMes || 0}</div>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="summary-content">
              <div class="summary-label">Ingresos del Mes</div>
              <div class="summary-value">$${this.formatCurrency(stats.ingresosMes || 0)}</div>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-icon">
              <i class="fas fa-percentage"></i>
            </div>
            <div class="summary-content">
              <div class="summary-label">Tasa de Cobro</div>
              <div class="summary-value">${stats.tasaCobro || 95}%</div>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="summary-content">
              <div class="summary-label">Nuevos Clientes</div>
              <div class="summary-value">${stats.clientesNuevosMes || 0}</div>
            </div>
          </div>
        </div>

        <div class="month-progress">
          <div class="progress-label">Progreso del mes</div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: ${this.getMonthProgress()}%"></div>
          </div>
          <div class="progress-text">${this.getMonthProgress()}% completado</div>
        </div>
      </div>
    `;
  }

  renderAlertas() {
    const alertas = this.generarAlertas();
    
    if (!alertas.length) {
      return `
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <strong>Todo en orden!</strong>
          No hay alertas importantes en este momento.
        </div>
      `;
    }

    return alertas.map(alerta => `
      <div class="alert alert-${alerta.tipo}">
        <i class="fas fa-${alerta.icono}"></i>
        <strong>${alerta.titulo}</strong>
        ${alerta.mensaje}
        ${alerta.accion ? `<button class="btn btn-sm btn-outline ml-2">${alerta.accion}</button>` : ''}
      </div>
    `).join('');
  }

  async loadData() {
    try {
      // Cargar datos desde el estado global de la app
      const clientes = this.app.appState.clientes || [];
      const prestamos = this.app.appState.prestamos || [];
      const pagos = this.app.appState.pagos || [];

      // Generar estadísticas
      this.data.estadisticas = this.calcularEstadisticas(clientes, prestamos, pagos);
      
      // Datos recientes (simulados para demo)
      this.data.prestamosRecientes = this.getPrestamosRecientes(prestamos);
      this.data.pagosRecientes = this.getPagosRecientes(pagos);
      this.data.clientesEnMora = this.getClientesEnMora(clientes, prestamos);

    } catch (error) {
      console.error('Error cargando datos del dashboard:', error);
      // Datos de ejemplo para demo
      this.data.estadisticas = {
        totalClientes: 0,
        prestamosActivos: 0,
        pagosHoy: 0,
        clientesEnMora: 0,
        montoTotal: 0,
        tasaRecuperacion: 95
      };
    }
  }

  calcularEstadisticas(clientes, prestamos, pagos) {
    return {
      totalClientes: clientes.length,
      clientesNuevos: clientes.filter(c => this.esEstesMes(c.fechaRegistro)).length,
      prestamosActivos: prestamos.filter(p => p.estado === 'activo').length,
      prestamosNuevos: prestamos.filter(p => this.esEstesMes(p.fecha)).length,
      pagosHoy: pagos.filter(p => this.esHoy(p.fecha)).length,
      pagosCompletados: pagos.length,
      clientesEnMora: prestamos.filter(p => this.estaEnMora(p)).length,
      montoTotal: prestamos.reduce((total, p) => total + (p.monto || 0), 0),
      tasaRecuperacion: 95,
      ingresosMes: pagos.filter(p => this.esEstesMes(p.fecha)).reduce((total, p) => total + (p.monto || 0), 0),
      prestamosMes: prestamos.filter(p => this.esEstesMes(p.fecha)).length,
      tasaCobro: 95,
      clientesNuevosMes: clientes.filter(c => this.esEstesMes(c.fechaRegistro)).length
    };
  }

  getPrestamosRecientes(prestamos) {
    return prestamos.slice(-5).map(p => ({
      cliente: p.cliente || 'Cliente Demo',
      monto: p.monto || 1000,
      fecha: p.fecha || new Date(),
      estado: p.estado || 'activo'
    }));
  }

  getPagosRecientes(pagos) {
    return pagos.slice(-5).map(p => ({
      cliente: p.cliente || 'Cliente Demo',
      monto: p.monto || 200,
      fecha: p.fecha || new Date(),
      tipo: p.tipo || 'Cuota',
      estado: p.estado || 'completado'
    }));
  }

  getClientesEnMora(clientes, prestamos) {
    return prestamos.filter(p => this.estaEnMora(p)).slice(0, 5).map(p => ({
      nombre: p.cliente || 'Cliente Demo',
      diasMora: Math.floor(Math.random() * 30) + 1,
      montoVencido: p.monto * 0.1 || 100
    }));
  }

  generarAlertas() {
    const alertas = [];
    const stats = this.data.estadisticas;

    if (stats.clientesEnMora > 0) {
      alertas.push({
        tipo: 'warning',
        icono: 'exclamation-triangle',
        titulo: 'Clientes en Mora',
        mensaje: `Hay ${stats.clientesEnMora} clientes en mora. Se recomienda seguimiento.`,
        accion: 'Ver Detalles'
      });
    }

    if (stats.pagosHoy === 0) {
      alertas.push({
        tipo: 'info',
        icono: 'info-circle',
        titulo: 'Sin Pagos Hoy',
        mensaje: 'No se han registrado pagos el día de hoy.',
        accion: 'Registrar Pago'
      });
    }

    return alertas;
  }

  // Utility functions
  formatCurrency(amount) {
    return new Intl.NumberFormat('es-DO', {
      style: 'decimal',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount);
  }

  formatDate(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-DO');
  }

  esEstesMes(fecha) {
    if (!fecha) return false;
    const ahora = new Date();
    const fechaObj = new Date(fecha);
    return fechaObj.getMonth() === ahora.getMonth() && fechaObj.getFullYear() === ahora.getFullYear();
  }

  esHoy(fecha) {
    if (!fecha) return false;
    const hoy = new Date();
    const fechaObj = new Date(fecha);
    return fechaObj.toDateString() === hoy.toDateString();
  }

  estaEnMora(prestamo) {
    // Lógica simplificada para demo
    return Math.random() < 0.1; // 10% de probabilidad de estar en mora
  }

  getMonthProgress() {
    const now = new Date();
    const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    const currentDay = now.getDate();
    return Math.round((currentDay / daysInMonth) * 100);
  }

  async setup() {
    // Configuración inicial de la página
    this.setupEventListeners();
  }

  setupEventListeners() {
    // Event listeners específicos del dashboard
    document.addEventListener('click', (e) => {
      if (e.target.closest('.refresh-stats')) {
        this.refreshStats();
      }
    });
  }

  async refreshStats() {
    await this.loadData();
    // Re-renderizar solo las estadísticas
    const statsContainer = document.querySelector('.stats-grid');
    if (statsContainer) {
      statsContainer.innerHTML = this.renderStatsCards();
    }
  }
}
