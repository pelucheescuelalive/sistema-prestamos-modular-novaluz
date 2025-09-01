/* ===================================================================
   🎯 PÁGINA PRÉSTAMOS
   Gestión completa de préstamos del sistema
   =================================================================== */

export class PrestamosPage {
  constructor(app) {
    this.app = app;
    this.prestamos = [];
    this.clientes = [];
    this.filtroActual = '';
    this.estadoFiltro = '';
    this.ordenActual = { campo: 'fecha', direccion: 'desc' };
    this.clientePreseleccionado = null;
  }

  async render() {
    await this.loadData();
    
    return `
      <div class="prestamos-page">
        <!-- Encabezado -->
        <div class="page-header">
          <div class="header-left">
            <h1 class="page-title">
              <i class="fas fa-money-bill-wave"></i>
              Gestión de Préstamos
            </h1>
            <p class="page-subtitle">
              Administra todos los préstamos otorgados y su seguimiento
            </p>
          </div>
          <div class="header-right">
            <button class="btn btn-primary" onclick="prestamosPage.mostrarFormularioPrestamo()">
              <i class="fas fa-plus"></i>
              Nuevo Préstamo
            </button>
            <button class="btn btn-success" onclick="prestamosPage.calculadoraPrestamo()">
              <i class="fas fa-calculator"></i>
              Calculadora
            </button>
            <button class="btn btn-outline-secondary" onclick="prestamosPage.exportarPrestamos()">
              <i class="fas fa-download"></i>
              Exportar
            </button>
          </div>
        </div>

        <!-- Herramientas de Filtro -->
        <div class="prestamos-toolbar">
          <div class="search-section">
            <div class="input-group">
              <input type="text" 
                     id="buscar-prestamo" 
                     class="form-control" 
                     placeholder="Buscar por cliente, monto, ID..."
                     onkeyup="prestamosPage.filtrarPrestamos(this.value)">
              <button class="btn btn-outline-primary">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
          
          <div class="filter-section">
            <select id="filtro-estado" class="form-control" onchange="prestamosPage.aplicarFiltroEstado(this.value)">
              <option value="">Todos los Estados</option>
              <option value="activo">Activos</option>
              <option value="pagado">Pagados</option>
              <option value="vencido">Vencidos</option>
              <option value="cancelado">Cancelados</option>
            </select>
            
            <select id="filtro-periodo" class="form-control" onchange="prestamosPage.aplicarFiltroPeriodo(this.value)">
              <option value="">Todos los Períodos</option>
              <option value="hoy">Hoy</option>
              <option value="semana">Esta Semana</option>
              <option value="mes">Este Mes</option>
              <option value="año">Este Año</option>
            </select>
            
            <select id="ordenar-por" class="form-control" onchange="prestamosPage.ordenarPor(this.value)">
              <option value="fecha-desc">Más Recientes</option>
              <option value="fecha-asc">Más Antiguos</option>
              <option value="monto-desc">Mayor Monto</option>
              <option value="monto-asc">Menor Monto</option>
              <option value="cliente-asc">Cliente A-Z</option>
              <option value="vencimiento-asc">Próximo Vencimiento</option>
            </select>
          </div>
        </div>

        <!-- Estadísticas de Préstamos -->
        <div class="prestamos-stats">
          <div class="stat-item">
            <div class="stat-icon text-primary">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">$${this.calcularTotalPrestamos().toLocaleString()}</div>
              <div class="stat-label">Total Prestado</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-success">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.contarPrestamosPorEstado('activo')}</div>
              <div class="stat-label">Préstamos Activos</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-warning">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.contarPrestamosPorEstado('vencido')}</div>
              <div class="stat-label">Vencidos</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-info">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.contarPrestamosHoy()}</div>
              <div class="stat-label">Nuevos Hoy</div>
            </div>
          </div>
        </div>

        <!-- Lista de Préstamos -->
        <div class="prestamos-content">
          <div class="prestamos-table-container">
            ${this.renderTablaPrestamos()}
          </div>
        </div>
      </div>
    `;
  }

  renderTablaPrestamos() {
    if (!this.prestamos.length) {
      return `
        <div class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <h3>No hay préstamos registrados</h3>
          <p>Comienza otorgando tu primer préstamo</p>
          <button class="btn btn-primary" onclick="prestamosPage.mostrarFormularioPrestamo()">
            <i class="fas fa-plus"></i>
            Crear Primer Préstamo
          </button>
        </div>
      `;
    }

    return `
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Monto</th>
              <th>Interés</th>
              <th>Total</th>
              <th>Fecha</th>
              <th>Vencimiento</th>
              <th>Estado</th>
              <th>Progreso</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${this.prestamos.map(prestamo => this.renderFilaPrestamo(prestamo)).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderFilaPrestamo(prestamo) {
    const cliente = this.clientes.find(c => c.id === prestamo.clienteId);
    const progreso = this.calcularProgresoPago(prestamo);
    const estadoBadge = this.getEstadoBadge(prestamo);
    const diasVencimiento = this.calcularDiasVencimiento(prestamo);
    
    return `
      <tr class="prestamo-row ${prestamo.estado}" data-prestamo-id="${prestamo.id}">
        <td>
          <span class="prestamo-id">#${prestamo.id.substr(-6).toUpperCase()}</span>
        </td>
        <td>
          <div class="cliente-info">
            <div class="cliente-avatar">👤</div>
            <div class="cliente-datos">
              <div class="cliente-nombre">${cliente?.nombre || 'Cliente no encontrado'}</div>
              <div class="cliente-cedula">${cliente?.cedula || ''}</div>
            </div>
          </div>
        </td>
        <td class="amount">
          <span class="monto-principal">$${prestamo.monto.toLocaleString()}</span>
        </td>
        <td class="interest">
          <span class="tasa-interes">${prestamo.tasaInteres}%</span>
          <small class="periodo-pago">${prestamo.tipoPago}</small>
        </td>
        <td class="total">
          <span class="monto-total">$${prestamo.montoTotal.toLocaleString()}</span>
        </td>
        <td class="date">
          ${this.formatDate(prestamo.fecha)}
        </td>
        <td class="due-date ${diasVencimiento <= 0 ? 'overdue' : diasVencimiento <= 7 ? 'warning' : ''}">
          <div class="vencimiento-info">
            <div class="fecha-vencimiento">${this.formatDate(prestamo.fechaVencimiento)}</div>
            <small class="dias-restantes">
              ${diasVencimiento > 0 ? `${diasVencimiento} días` : `${Math.abs(diasVencimiento)} días atraso`}
            </small>
          </div>
        </td>
        <td>
          ${estadoBadge}
        </td>
        <td>
          <div class="progress-container">
            <div class="progress">
              <div class="progress-bar ${progreso.clase}" 
                   style="width: ${progreso.porcentaje}%"></div>
            </div>
            <small class="progress-text">${progreso.porcentaje.toFixed(1)}%</small>
          </div>
        </td>
        <td>
          <div class="action-buttons">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" onclick="prestamosPage.verDetallePrestamo('${prestamo.id}')">
                  <i class="fas fa-eye"></i> Ver Detalles
                </a>
                <a class="dropdown-item" onclick="prestamosPage.registrarPago('${prestamo.id}')">
                  <i class="fas fa-credit-card"></i> Registrar Pago
                </a>
                <a class="dropdown-item" onclick="prestamosPage.verHistorialPagos('${prestamo.id}')">
                  <i class="fas fa-history"></i> Historial Pagos
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" onclick="prestamosPage.editarPrestamo('${prestamo.id}')">
                  <i class="fas fa-edit"></i> Editar
                </a>
                <a class="dropdown-item" onclick="prestamosPage.generarRecibo('${prestamo.id}')">
                  <i class="fas fa-receipt"></i> Generar Recibo
                </a>
                ${prestamo.estado === 'activo' ? `
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-warning" onclick="prestamosPage.marcarVencido('${prestamo.id}')">
                    <i class="fas fa-exclamation-triangle"></i> Marcar Vencido
                  </a>
                  <a class="dropdown-item text-danger" onclick="prestamosPage.cancelarPrestamo('${prestamo.id}')">
                    <i class="fas fa-times"></i> Cancelar
                  </a>
                ` : ''}
              </div>
            </div>
          </div>
        </td>
      </tr>
    `;
  }

  mostrarFormularioPrestamo(prestamoId = null, clienteId = null) {
    const esEdicion = !!prestamoId;
    const prestamo = esEdicion ? this.prestamos.find(p => p.id === prestamoId) : {};
    
    const formularioHTML = `
      <form id="form-prestamo" class="prestamo-form">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="prestamo-cliente">Cliente *</label>
            <select id="prestamo-cliente" class="form-control" required ${esEdicion ? 'disabled' : ''}>
              <option value="">Seleccionar Cliente</option>
              ${this.clientes.map(cliente => `
                <option value="${cliente.id}" 
                        ${(prestamo.clienteId === cliente.id || clienteId === cliente.id) ? 'selected' : ''}>
                  ${cliente.nombre} - ${cliente.cedula}
                </option>
              `).join('')}
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="prestamo-monto">Monto del Préstamo (RD$) *</label>
            <input type="number" 
                   id="prestamo-monto" 
                   class="form-control" 
                   value="${prestamo.monto || ''}" 
                   placeholder="0.00"
                   step="0.01"
                   min="100"
                   required
                   onchange="prestamosPage.calcularPrestamo()">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="prestamo-tasa">Tasa de Interés (%) *</label>
            <input type="number" 
                   id="prestamo-tasa" 
                   class="form-control" 
                   value="${prestamo.tasaInteres || '10'}" 
                   placeholder="10.00"
                   step="0.01"
                   min="0"
                   max="100"
                   required
                   onchange="prestamosPage.calcularPrestamo()">
          </div>
          <div class="form-group col-md-4">
            <label for="prestamo-plazo">Plazo *</label>
            <input type="number" 
                   id="prestamo-plazo" 
                   class="form-control" 
                   value="${prestamo.plazo || '30'}" 
                   placeholder="30"
                   min="1"
                   required
                   onchange="prestamosPage.calcularPrestamo()">
          </div>
          <div class="form-group col-md-4">
            <label for="prestamo-tipo-plazo">Tipo de Plazo *</label>
            <select id="prestamo-tipo-plazo" class="form-control" required onchange="prestamosPage.calcularPrestamo()">
              <option value="dias" ${prestamo.tipoPlazo === 'dias' ? 'selected' : ''}>Días</option>
              <option value="semanas" ${prestamo.tipoPlazo === 'semanas' ? 'selected' : ''}>Semanas</option>
              <option value="meses" ${prestamo.tipoPlazo === 'meses' ? 'selected' : ''}>Meses</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="prestamo-tipo-pago">Tipo de Pago *</label>
            <select id="prestamo-tipo-pago" class="form-control" required onchange="prestamosPage.calcularPrestamo()">
              <option value="unico" ${prestamo.tipoPago === 'unico' ? 'selected' : ''}>Pago Único</option>
              <option value="semanal" ${prestamo.tipoPago === 'semanal' ? 'selected' : ''}>Pagos Semanales</option>
              <option value="quincenal" ${prestamo.tipoPago === 'quincenal' ? 'selected' : ''}>Pagos Quincenales</option>
              <option value="mensual" ${prestamo.tipoPago === 'mensual' ? 'selected' : ''}>Pagos Mensuales</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="prestamo-fecha">Fecha del Préstamo *</label>
            <input type="date" 
                   id="prestamo-fecha" 
                   class="form-control" 
                   value="${prestamo.fecha ? prestamo.fecha.split('T')[0] : new Date().toISOString().split('T')[0]}" 
                   required
                   onchange="prestamosPage.calcularPrestamo()">
          </div>
        </div>
        
        <!-- Calculadora en tiempo real -->
        <div class="prestamo-calculadora">
          <div class="calc-header">
            <h5><i class="fas fa-calculator"></i> Resumen del Préstamo</h5>
          </div>
          <div class="calc-body" id="prestamo-resumen">
            ${this.renderResumenPrestamo()}
          </div>
        </div>
        
        <div class="form-group">
          <label for="prestamo-observaciones">Observaciones</label>
          <textarea id="prestamo-observaciones" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Notas adicionales sobre el préstamo...">${prestamo.observaciones || ''}</textarea>
        </div>
        
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="prestamosPage.cerrarFormulario()">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            ${esEdicion ? 'Actualizar Préstamo' : 'Crear Préstamo'}
          </button>
        </div>
      </form>
    `;

    const modal = new (window.ModalComponent || class {})();
    modal.show({
      title: esEdicion ? 'Editar Préstamo' : 'Nuevo Préstamo',
      content: formularioHTML,
      size: 'xlarge',
      type: 'form',
      closable: true,
      onConfirm: () => this.guardarPrestamo(prestamoId)
    });

    // Configurar eventos y cálculos iniciales
    setTimeout(() => {
      const form = document.getElementById('form-prestamo');
      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          this.guardarPrestamo(prestamoId);
        });
        this.calcularPrestamo(); // Cálculo inicial
      }
    }, 100);
  }

  calcularPrestamo() {
    const monto = parseFloat(document.getElementById('prestamo-monto')?.value) || 0;
    const tasa = parseFloat(document.getElementById('prestamo-tasa')?.value) || 0;
    const plazo = parseInt(document.getElementById('prestamo-plazo')?.value) || 0;
    const tipoPlazo = document.getElementById('prestamo-tipo-plazo')?.value || 'dias';
    const tipoPago = document.getElementById('prestamo-tipo-pago')?.value || 'unico';
    const fecha = document.getElementById('prestamo-fecha')?.value;

    if (!monto || !tasa || !plazo || !fecha) return;

    // Calcular interés y total
    const interes = (monto * tasa) / 100;
    const montoTotal = monto + interes;
    
    // Calcular fecha de vencimiento
    const fechaPrestamo = new Date(fecha);
    const fechaVencimiento = new Date(fechaPrestamo);
    
    switch (tipoPlazo) {
      case 'dias':
        fechaVencimiento.setDate(fechaVencimiento.getDate() + plazo);
        break;
      case 'semanas':
        fechaVencimiento.setDate(fechaVencimiento.getDate() + (plazo * 7));
        break;
      case 'meses':
        fechaVencimiento.setMonth(fechaVencimiento.getMonth() + plazo);
        break;
    }

    // Calcular pagos
    let numeroPagos = 1;
    let montoPago = montoTotal;
    
    if (tipoPago !== 'unico') {
      const diasPlazo = Math.ceil((fechaVencimiento - fechaPrestamo) / (1000 * 60 * 60 * 24));
      
      switch (tipoPago) {
        case 'semanal':
          numeroPagos = Math.ceil(diasPlazo / 7);
          break;
        case 'quincenal':
          numeroPagos = Math.ceil(diasPlazo / 15);
          break;
        case 'mensual':
          numeroPagos = Math.ceil(diasPlazo / 30);
          break;
      }
      
      montoPago = montoTotal / numeroPagos;
    }

    // Actualizar resumen
    const resumen = document.getElementById('prestamo-resumen');
    if (resumen) {
      resumen.innerHTML = `
        <div class="resumen-grid">
          <div class="resumen-item">
            <div class="resumen-label">Monto Principal</div>
            <div class="resumen-value text-primary">$${monto.toLocaleString()}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Interés (${tasa}%)</div>
            <div class="resumen-value text-info">$${interes.toLocaleString()}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Total a Pagar</div>
            <div class="resumen-value text-success">$${montoTotal.toLocaleString()}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Fecha Vencimiento</div>
            <div class="resumen-value">${this.formatDate(fechaVencimiento.toISOString())}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Tipo de Pago</div>
            <div class="resumen-value">${tipoPago === 'unico' ? 'Pago Único' : tipoPago.charAt(0).toUpperCase() + tipoPago.slice(1)}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">${tipoPago === 'unico' ? 'Monto a Pagar' : 'Pago por Cuota'}</div>
            <div class="resumen-value text-warning">$${montoPago.toFixed(2)}</div>
          </div>
          ${numeroPagos > 1 ? `
            <div class="resumen-item">
              <div class="resumen-label">Número de Pagos</div>
              <div class="resumen-value">${numeroPagos}</div>
            </div>
          ` : ''}
        </div>
      `;
    }
  }

  renderResumenPrestamo() {
    return `
      <div class="calc-placeholder">
        <i class="fas fa-calculator"></i>
        <p>Complete los datos para ver el resumen</p>
      </div>
    `;
  }

  async guardarPrestamo(prestamoId = null) {
    const form = document.getElementById('form-prestamo');
    if (!form) return false;

    const clienteId = document.getElementById('prestamo-cliente').value;
    const monto = parseFloat(document.getElementById('prestamo-monto').value);
    const tasaInteres = parseFloat(document.getElementById('prestamo-tasa').value);
    const plazo = parseInt(document.getElementById('prestamo-plazo').value);
    const tipoPlazo = document.getElementById('prestamo-tipo-plazo').value;
    const tipoPago = document.getElementById('prestamo-tipo-pago').value;
    const fecha = document.getElementById('prestamo-fecha').value;
    const observaciones = document.getElementById('prestamo-observaciones').value.trim();

    // Validaciones
    if (!clienteId || !monto || !tasaInteres || !plazo || !fecha) {
      window.toastManager?.error('Todos los campos obligatorios deben ser completados');
      return false;
    }

    if (monto < 100) {
      window.toastManager?.error('El monto mínimo del préstamo es RD$100');
      return false;
    }

    // Calcular datos del préstamo
    const interes = (monto * tasaInteres) / 100;
    const montoTotal = monto + interes;
    
    const fechaPrestamo = new Date(fecha);
    const fechaVencimiento = new Date(fechaPrestamo);
    
    switch (tipoPlazo) {
      case 'dias':
        fechaVencimiento.setDate(fechaVencimiento.getDate() + plazo);
        break;
      case 'semanas':
        fechaVencimiento.setDate(fechaVencimiento.getDate() + (plazo * 7));
        break;
      case 'meses':
        fechaVencimiento.setMonth(fechaVencimiento.getMonth() + plazo);
        break;
    }

    const cliente = this.clientes.find(c => c.id === clienteId);
    
    const prestamoData = {
      id: prestamoId || this.generarId(),
      clienteId,
      cliente: cliente?.nombre || '',
      monto,
      tasaInteres,
      interes,
      montoTotal,
      plazo,
      tipoPlazo,
      tipoPago,
      fecha: fechaPrestamo.toISOString(),
      fechaVencimiento: fechaVencimiento.toISOString(),
      observaciones,
      estado: 'activo',
      fechaCreacion: prestamoId ? this.prestamos.find(p => p.id === prestamoId)?.fechaCreacion : new Date().toISOString(),
      fechaModificacion: new Date().toISOString()
    };

    try {
      if (prestamoId) {
        // Actualizar préstamo existente
        const index = this.prestamos.findIndex(p => p.id === prestamoId);
        if (index !== -1) {
          this.prestamos[index] = { ...this.prestamos[index], ...prestamoData };
        }
        window.toastManager?.success('Préstamo actualizado correctamente');
      } else {
        // Crear nuevo préstamo
        this.prestamos.push(prestamoData);
        window.toastManager?.prestamoCreado(cliente?.nombre || 'Cliente', monto);
      }

      // Guardar en localStorage
      localStorage.setItem('prestamos', JSON.stringify(this.prestamos));
      
      // Actualizar la vista
      this.app.renderCurrentPage();
      
      return true;
    } catch (error) {
      console.error('Error guardando préstamo:', error);
      window.toastManager?.error('Error al guardar el préstamo');
      return false;
    }
  }

  // Métodos auxiliares y cálculos
  async loadData() {
    try {
      this.prestamos = JSON.parse(localStorage.getItem('prestamos') || '[]');
      this.clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
    } catch (error) {
      console.error('Error cargando datos:', error);
      this.prestamos = [];
      this.clientes = [];
    }
  }

  calcularTotalPrestamos() {
    return this.prestamos.reduce((total, prestamo) => total + prestamo.monto, 0);
  }

  contarPrestamosPorEstado(estado) {
    return this.prestamos.filter(prestamo => {
      if (estado === 'vencido') {
        return this.estaVencido(prestamo) && prestamo.estado === 'activo';
      }
      return prestamo.estado === estado;
    }).length;
  }

  contarPrestamosHoy() {
    const hoy = new Date().toDateString();
    return this.prestamos.filter(prestamo => 
      new Date(prestamo.fecha).toDateString() === hoy
    ).length;
  }

  calcularProgresoPago(prestamo) {
    const pagos = JSON.parse(localStorage.getItem('pagos') || '[]');
    const pagosDelPrestamo = pagos.filter(p => p.prestamoId === prestamo.id);
    const totalPagado = pagosDelPrestamo.reduce((sum, p) => sum + p.monto, 0);
    const porcentaje = Math.min((totalPagado / prestamo.montoTotal) * 100, 100);
    
    let clase = 'bg-danger';
    if (porcentaje >= 75) clase = 'bg-success';
    else if (porcentaje >= 50) clase = 'bg-info';
    else if (porcentaje >= 25) clase = 'bg-warning';
    
    return { porcentaje, clase };
  }

  getEstadoBadge(prestamo) {
    if (this.estaVencido(prestamo) && prestamo.estado === 'activo') {
      return '<span class="badge badge-danger">Vencido</span>';
    }
    
    const badges = {
      activo: '<span class="badge badge-success">Activo</span>',
      pagado: '<span class="badge badge-info">Pagado</span>',
      cancelado: '<span class="badge badge-secondary">Cancelado</span>',
      vencido: '<span class="badge badge-danger">Vencido</span>'
    };
    
    return badges[prestamo.estado] || badges.activo;
  }

  calcularDiasVencimiento(prestamo) {
    const hoy = new Date();
    const vencimiento = new Date(prestamo.fechaVencimiento);
    const diferencia = vencimiento - hoy;
    return Math.ceil(diferencia / (1000 * 60 * 60 * 24));
  }

  estaVencido(prestamo) {
    return new Date() > new Date(prestamo.fechaVencimiento);
  }

  // Métodos de filtrado y ordenamiento
  filtrarPrestamos(termino) {
    this.filtroActual = termino.toLowerCase();
    this.actualizarVistaPrestamos();
  }

  aplicarFiltroEstado(estado) {
    this.estadoFiltro = estado;
    this.actualizarVistaPrestamos();
  }

  aplicarFiltroPeriodo(periodo) {
    this.periodoFiltro = periodo;
    this.actualizarVistaPrestamos();
  }

  ordenarPor(criterio) {
    const [campo, direccion] = criterio.split('-');
    this.ordenActual = { campo, direccion };
    this.actualizarVistaPrestamos();
  }

  actualizarVistaPrestamos() {
    // Implementar filtrado y ordenamiento
    this.app.renderCurrentPage();
  }

  // Métodos de acciones
  verDetallePrestamo(prestamoId) {
    window.toastManager?.info('Abriendo detalles del préstamo...');
  }

  registrarPago(prestamoId) {
    this.app.navigate('pagos', { prestamoId });
  }

  verHistorialPagos(prestamoId) {
    window.toastManager?.info('Abriendo historial de pagos...');
  }

  editarPrestamo(prestamoId) {
    this.mostrarFormularioPrestamo(prestamoId);
  }

  generarRecibo(prestamoId) {
    window.toastManager?.info('Generando recibo...');
  }

  marcarVencido(prestamoId) {
    // Implementar marcado como vencido
  }

  cancelarPrestamo(prestamoId) {
    // Implementar cancelación
  }

  calculadoraPrestamo() {
    this.app.navigate('calculadora');
  }

  exportarPrestamos() {
    window.toastManager?.info('Preparando exportación...');
  }

  // Utilidades
  generarId() {
    return 'prestamo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  formatDate(fecha) {
    return new Date(fecha).toLocaleDateString('es-DO');
  }
}

// Instancia global
if (typeof window !== 'undefined') {
  window.prestamosPage = new PrestamosPage();
}
