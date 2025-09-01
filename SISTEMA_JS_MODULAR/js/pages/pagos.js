/* ===================================================================
   🎯 PÁGINA PAGOS
   Gestión de pagos de préstamos del sistema
   =================================================================== */

export class PagosPage {
  constructor(app) {
    this.app = app;
    this.pagos = [];
    this.prestamos = [];
    this.clientes = [];
    this.filtroActual = '';
    this.fechaFiltro = '';
    this.prestamoPreseleccionado = null;
  }

  async render() {
    await this.loadData();
    
    return `
      <div class="pagos-page">
        <!-- Encabezado -->
        <div class="page-header">
          <div class="header-left">
            <h1 class="page-title">
              <i class="fas fa-credit-card"></i>
              Gestión de Pagos
            </h1>
            <p class="page-subtitle">
              Registra y administra todos los pagos de préstamos
            </p>
          </div>
          <div class="header-right">
            <button class="btn btn-primary" onclick="pagosPage.mostrarFormularioPago()">
              <i class="fas fa-plus"></i>
              Registrar Pago
            </button>
            <button class="btn btn-success" onclick="pagosPage.pagoRapido()">
              <i class="fas fa-bolt"></i>
              Pago Rápido
            </button>
            <button class="btn btn-outline-secondary" onclick="pagosPage.exportarPagos()">
              <i class="fas fa-download"></i>
              Exportar
            </button>
          </div>
        </div>

        <!-- Herramientas de Filtro -->
        <div class="pagos-toolbar">
          <div class="search-section">
            <div class="input-group">
              <input type="text" 
                     id="buscar-pago" 
                     class="form-control" 
                     placeholder="Buscar por cliente, monto, recibo..."
                     onkeyup="pagosPage.filtrarPagos(this.value)">
              <button class="btn btn-outline-primary">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
          
          <div class="filter-section">
            <input type="date" 
                   id="filtro-fecha-desde" 
                   class="form-control" 
                   placeholder="Desde"
                   onchange="pagosPage.aplicarFiltroFecha()">
            
            <input type="date" 
                   id="filtro-fecha-hasta" 
                   class="form-control" 
                   placeholder="Hasta"
                   onchange="pagosPage.aplicarFiltroFecha()">
            
            <select id="filtro-periodo" class="form-control" onchange="pagosPage.aplicarFiltroPeriodo(this.value)">
              <option value="">Todos los Períodos</option>
              <option value="hoy">Hoy</option>
              <option value="ayer">Ayer</option>
              <option value="semana">Esta Semana</option>
              <option value="mes">Este Mes</option>
              <option value="año">Este Año</option>
            </select>
            
            <select id="ordenar-por" class="form-control" onchange="pagosPage.ordenarPor(this.value)">
              <option value="fecha-desc">Más Recientes</option>
              <option value="fecha-asc">Más Antiguos</option>
              <option value="monto-desc">Mayor Monto</option>
              <option value="monto-asc">Menor Monto</option>
              <option value="cliente-asc">Cliente A-Z</option>
            </select>
          </div>
        </div>

        <!-- Estadísticas de Pagos -->
        <div class="pagos-stats">
          <div class="stat-item">
            <div class="stat-icon text-success">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">$${this.calcularTotalPagos().toLocaleString()}</div>
              <div class="stat-label">Total Recaudado</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-primary">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">$${this.calcularPagosHoy().toLocaleString()}</div>
              <div class="stat-label">Pagos de Hoy</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-info">
              <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.pagos.length}</div>
              <div class="stat-label">Total Transacciones</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon text-warning">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">$${this.calcularPromedioMensual().toLocaleString()}</div>
              <div class="stat-label">Promedio Mensual</div>
            </div>
          </div>
        </div>

        <!-- Resumen de Pagos Pendientes -->
        <div class="pagos-pendientes">
          <div class="card">
            <div class="card-header">
              <h5><i class="fas fa-clock"></i> Pagos Pendientes</h5>
              <button class="btn btn-sm btn-outline-primary" onclick="pagosPage.verTodosPendientes()">
                Ver Todos
              </button>
            </div>
            <div class="card-body">
              ${this.renderPagosPendientes()}
            </div>
          </div>
        </div>

        <!-- Lista de Pagos -->
        <div class="pagos-content">
          <div class="pagos-table-container">
            ${this.renderTablaPagos()}
          </div>
        </div>
      </div>
    `;
  }

  renderPagosPendientes() {
    const prestamosActivos = this.prestamos.filter(p => p.estado === 'activo');
    const proximosVencimientos = prestamosActivos
      .map(prestamo => {
        const cliente = this.clientes.find(c => c.id === prestamo.clienteId);
        const diasVencimiento = this.calcularDiasVencimiento(prestamo);
        const saldoPendiente = this.calcularSaldoPendiente(prestamo);
        return { ...prestamo, cliente, diasVencimiento, saldoPendiente };
      })
      .filter(p => p.diasVencimiento <= 7 && p.saldoPendiente > 0)
      .sort((a, b) => a.diasVencimiento - b.diasVencimiento)
      .slice(0, 5);

    if (!proximosVencimientos.length) {
      return '<p class="text-success"><i class="fas fa-check"></i> No hay pagos pendientes próximos</p>';
    }

    return `
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Préstamo</th>
              <th>Vencimiento</th>
              <th>Saldo Pendiente</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            ${proximosVencimientos.map(prestamo => `
              <tr class="${prestamo.diasVencimiento <= 0 ? 'table-danger' : prestamo.diasVencimiento <= 3 ? 'table-warning' : ''}">
                <td>
                  <div class="cliente-info">
                    <span class="cliente-avatar">👤</span>
                    ${prestamo.cliente?.nombre || 'Cliente no encontrado'}
                  </div>
                </td>
                <td>
                  <span class="prestamo-id">#${prestamo.id.substr(-6).toUpperCase()}</span>
                  <small class="d-block">$${prestamo.monto.toLocaleString()}</small>
                </td>
                <td>
                  <div class="vencimiento-info">
                    <div class="fecha">${this.formatDate(prestamo.fechaVencimiento)}</div>
                    <small class="${prestamo.diasVencimiento <= 0 ? 'text-danger' : 'text-warning'}">
                      ${prestamo.diasVencimiento <= 0 ? 
                        `${Math.abs(prestamo.diasVencimiento)} días vencido` : 
                        `${prestamo.diasVencimiento} días restantes`}
                    </small>
                  </div>
                </td>
                <td class="amount text-warning">
                  $${prestamo.saldoPendiente.toLocaleString()}
                </td>
                <td>
                  <span class="badge badge-${prestamo.diasVencimiento <= 0 ? 'danger' : 'warning'}">
                    ${prestamo.diasVencimiento <= 0 ? 'Vencido' : 'Próximo'}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="pagosPage.registrarPagoRapido('${prestamo.id}')">
                    Pagar
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderTablaPagos() {
    if (!this.pagos.length) {
      return `
        <div class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-credit-card"></i>
          </div>
          <h3>No hay pagos registrados</h3>
          <p>Comienza registrando el primer pago</p>
          <button class="btn btn-primary" onclick="pagosPage.mostrarFormularioPago()">
            <i class="fas fa-plus"></i>
            Registrar Primer Pago
          </button>
        </div>
      `;
    }

    return `
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Recibo</th>
              <th>Cliente</th>
              <th>Préstamo</th>
              <th>Monto</th>
              <th>Fecha</th>
              <th>Método</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${this.pagos.map(pago => this.renderFilaPago(pago)).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderFilaPago(pago) {
    const prestamo = this.prestamos.find(p => p.id === pago.prestamoId);
    const cliente = this.clientes.find(c => c.id === pago.clienteId);
    
    return `
      <tr class="pago-row" data-pago-id="${pago.id}">
        <td>
          <div class="recibo-info">
            <span class="recibo-numero">#${pago.numeroRecibo || pago.id.substr(-6).toUpperCase()}</span>
            <small class="d-block text-muted">ID: ${pago.id.substr(-8)}</small>
          </div>
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
        <td>
          <div class="prestamo-info">
            <span class="prestamo-id">#${prestamo?.id.substr(-6).toUpperCase() || 'N/A'}</span>
            <small class="d-block">
              ${prestamo ? `$${prestamo.monto.toLocaleString()} (${prestamo.tasaInteres}%)` : 'Préstamo no encontrado'}
            </small>
          </div>
        </td>
        <td class="amount">
          <span class="monto-pago text-success">$${pago.monto.toLocaleString()}</span>
          ${pago.tipoPago ? `<small class="d-block text-muted">${pago.tipoPago}</small>` : ''}
        </td>
        <td class="date">
          <div class="fecha-pago">
            <div class="fecha-principal">${this.formatDate(pago.fecha)}</div>
            <small class="fecha-hora">${this.formatTime(pago.fecha)}</small>
          </div>
        </td>
        <td>
          <span class="badge badge-${this.getMetodoPagoColor(pago.metodoPago)}">
            ${this.getMetodoPagoTexto(pago.metodoPago)}
          </span>
        </td>
        <td>
          <span class="badge badge-${pago.estado === 'confirmado' ? 'success' : pago.estado === 'pendiente' ? 'warning' : 'secondary'}">
            ${pago.estado || 'confirmado'}
          </span>
        </td>
        <td>
          <div class="action-buttons">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" onclick="pagosPage.verDetallePago('${pago.id}')">
                  <i class="fas fa-eye"></i> Ver Detalles
                </a>
                <a class="dropdown-item" onclick="pagosPage.imprimirRecibo('${pago.id}')">
                  <i class="fas fa-print"></i> Imprimir Recibo
                </a>
                <a class="dropdown-item" onclick="pagosPage.enviarRecibo('${pago.id}')">
                  <i class="fas fa-paper-plane"></i> Enviar por WhatsApp
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" onclick="pagosPage.editarPago('${pago.id}')">
                  <i class="fas fa-edit"></i> Editar
                </a>
                ${pago.estado !== 'confirmado' ? `
                  <a class="dropdown-item text-success" onclick="pagosPage.confirmarPago('${pago.id}')">
                    <i class="fas fa-check"></i> Confirmar
                  </a>
                ` : ''}
                <a class="dropdown-item text-danger" onclick="pagosPage.anularPago('${pago.id}')">
                  <i class="fas fa-times"></i> Anular
                </a>
              </div>
            </div>
          </div>
        </td>
      </tr>
    `;
  }

  mostrarFormularioPago(pagoId = null, prestamoId = null) {
    const esEdicion = !!pagoId;
    const pago = esEdicion ? this.pagos.find(p => p.id === pagoId) : {};
    
    // Filtrar solo préstamos activos para nuevos pagos
    const prestamosDisponibles = esEdicion ? this.prestamos : 
      this.prestamos.filter(p => p.estado === 'activo');
    
    const formularioHTML = `
      <form id="form-pago" class="pago-form">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="pago-prestamo">Préstamo *</label>
            <select id="pago-prestamo" class="form-control" required ${esEdicion ? 'disabled' : ''}
                    onchange="pagosPage.cargarDatosPrestamo()">
              <option value="">Seleccionar Préstamo</option>
              ${prestamosDisponibles.map(prestamo => {
                const cliente = this.clientes.find(c => c.id === prestamo.clienteId);
                const saldoPendiente = this.calcularSaldoPendiente(prestamo);
                return `
                  <option value="${prestamo.id}" 
                          ${(pago.prestamoId === prestamo.id || prestamoId === prestamo.id) ? 'selected' : ''}
                          data-cliente-id="${prestamo.clienteId}"
                          data-saldo="${saldoPendiente}">
                    ${cliente?.nombre} - #${prestamo.id.substr(-6).toUpperCase()} 
                    (Saldo: $${saldoPendiente.toLocaleString()})
                  </option>
                `;
              }).join('')}
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="pago-monto">Monto del Pago (RD$) *</label>
            <div class="input-group">
              <input type="number" 
                     id="pago-monto" 
                     class="form-control" 
                     value="${pago.monto || ''}" 
                     placeholder="0.00"
                     step="0.01"
                     min="0.01"
                     required>
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" onclick="pagosPage.usarSaldoCompleto()">
                  Saldo Completo
                </button>
              </div>
            </div>
            <small id="saldo-info" class="form-text text-muted"></small>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="pago-fecha">Fecha del Pago *</label>
            <input type="date" 
                   id="pago-fecha" 
                   class="form-control" 
                   value="${pago.fecha ? pago.fecha.split('T')[0] : new Date().toISOString().split('T')[0]}" 
                   required>
          </div>
          <div class="form-group col-md-4">
            <label for="pago-hora">Hora del Pago</label>
            <input type="time" 
                   id="pago-hora" 
                   class="form-control" 
                   value="${pago.fecha ? new Date(pago.fecha).toTimeString().substr(0,5) : new Date().toTimeString().substr(0,5)}">
          </div>
          <div class="form-group col-md-4">
            <label for="pago-metodo">Método de Pago *</label>
            <select id="pago-metodo" class="form-control" required>
              <option value="">Seleccionar Método</option>
              <option value="efectivo" ${pago.metodoPago === 'efectivo' ? 'selected' : ''}>Efectivo</option>
              <option value="transferencia" ${pago.metodoPago === 'transferencia' ? 'selected' : ''}>Transferencia Bancaria</option>
              <option value="cheque" ${pago.metodoPago === 'cheque' ? 'selected' : ''}>Cheque</option>
              <option value="tarjeta" ${pago.metodoPago === 'tarjeta' ? 'selected' : ''}>Tarjeta de Débito/Crédito</option>
              <option value="deposito" ${pago.metodoPago === 'deposito' ? 'selected' : ''}>Depósito Bancario</option>
              <option value="otro" ${pago.metodoPago === 'otro' ? 'selected' : ''}>Otro</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="pago-tipo">Tipo de Pago</label>
            <select id="pago-tipo" class="form-control">
              <option value="cuota_regular" ${pago.tipoPago === 'cuota_regular' ? 'selected' : ''}>Cuota Regular</option>
              <option value="pago_parcial" ${pago.tipoPago === 'pago_parcial' ? 'selected' : ''}>Pago Parcial</option>
              <option value="pago_completo" ${pago.tipoPago === 'pago_completo' ? 'selected' : ''}>Pago Completo</option>
              <option value="pago_adelantado" ${pago.tipoPago === 'pago_adelantado' ? 'selected' : ''}>Pago Adelantado</option>
              <option value="pago_mora" ${pago.tipoPago === 'pago_mora' ? 'selected' : ''}>Pago de Mora</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="pago-recibo">Número de Recibo</label>
            <input type="text" 
                   id="pago-recibo" 
                   class="form-control" 
                   value="${pago.numeroRecibo || ''}" 
                   placeholder="Se generará automáticamente">
          </div>
        </div>
        
        <div class="form-group">
          <label for="pago-observaciones">Observaciones</label>
          <textarea id="pago-observaciones" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Notas adicionales sobre el pago...">${pago.observaciones || ''}</textarea>
        </div>
        
        <!-- Resumen del Pago -->
        <div class="pago-resumen" id="pago-resumen" style="display: none;">
          <div class="resumen-header">
            <h5><i class="fas fa-receipt"></i> Resumen del Pago</h5>
          </div>
          <div class="resumen-body" id="resumen-contenido">
            <!-- Se llena dinámicamente -->
          </div>
        </div>
        
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="pagosPage.cerrarFormulario()">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            ${esEdicion ? 'Actualizar Pago' : 'Registrar Pago'}
          </button>
          ${!esEdicion ? `
            <button type="button" class="btn btn-success" onclick="pagosPage.registrarEImprimir()">
              <i class="fas fa-print"></i>
              Registrar e Imprimir
            </button>
          ` : ''}
        </div>
      </form>
    `;

    const modal = new (window.ModalComponent || class {})();
    modal.show({
      title: esEdicion ? 'Editar Pago' : 'Registrar Nuevo Pago',
      content: formularioHTML,
      size: 'large',
      type: 'form',
      closable: true,
      onConfirm: () => this.guardarPago(pagoId)
    });

    // Configurar eventos
    setTimeout(() => {
      const form = document.getElementById('form-pago');
      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          this.guardarPago(pagoId);
        });
        
        // Cargar datos iniciales si hay préstamo preseleccionado
        if (prestamoId || pago.prestamoId) {
          this.cargarDatosPrestamo();
        }
      }
    }, 100);
  }

  cargarDatosPrestamo() {
    const prestamoSelect = document.getElementById('pago-prestamo');
    const montoInput = document.getElementById('pago-monto');
    const saldoInfo = document.getElementById('saldo-info');
    const resumenDiv = document.getElementById('pago-resumen');
    
    if (!prestamoSelect || !prestamoSelect.value) {
      if (saldoInfo) saldoInfo.textContent = '';
      if (resumenDiv) resumenDiv.style.display = 'none';
      return;
    }
    
    const selectedOption = prestamoSelect.selectedOptions[0];
    const saldoPendiente = parseFloat(selectedOption.dataset.saldo);
    const clienteId = selectedOption.dataset.clienteId;
    
    // Actualizar información del saldo
    if (saldoInfo) {
      saldoInfo.innerHTML = `
        <i class="fas fa-info-circle"></i>
        Saldo pendiente: <strong>$${saldoPendiente.toLocaleString()}</strong>
      `;
    }
    
    // Mostrar resumen
    if (resumenDiv) {
      resumenDiv.style.display = 'block';
      this.actualizarResumenPago();
    }
  }

  usarSaldoCompleto() {
    const prestamoSelect = document.getElementById('pago-prestamo');
    const montoInput = document.getElementById('pago-monto');
    
    if (prestamoSelect && prestamoSelect.value && montoInput) {
      const selectedOption = prestamoSelect.selectedOptions[0];
      const saldoPendiente = parseFloat(selectedOption.dataset.saldo);
      montoInput.value = saldoPendiente.toFixed(2);
      this.actualizarResumenPago();
    }
  }

  actualizarResumenPago() {
    const prestamoSelect = document.getElementById('pago-prestamo');
    const montoInput = document.getElementById('pago-monto');
    const resumenContenido = document.getElementById('resumen-contenido');
    
    if (!prestamoSelect?.value || !montoInput?.value || !resumenContenido) return;
    
    const prestamo = this.prestamos.find(p => p.id === prestamoSelect.value);
    const cliente = this.clientes.find(c => c.id === prestamo?.clienteId);
    const montoPago = parseFloat(montoInput.value);
    const saldoActual = this.calcularSaldoPendiente(prestamo);
    const saldoRestante = Math.max(0, saldoActual - montoPago);
    
    resumenContenido.innerHTML = `
      <div class="resumen-grid">
        <div class="resumen-item">
          <div class="resumen-label">Cliente</div>
          <div class="resumen-value">${cliente?.nombre || 'No encontrado'}</div>
        </div>
        <div class="resumen-item">
          <div class="resumen-label">Préstamo Original</div>
          <div class="resumen-value">$${prestamo?.monto.toLocaleString()}</div>
        </div>
        <div class="resumen-item">
          <div class="resumen-label">Total a Pagar</div>
          <div class="resumen-value">$${prestamo?.montoTotal.toLocaleString()}</div>
        </div>
        <div class="resumen-item">
          <div class="resumen-label">Saldo Antes del Pago</div>
          <div class="resumen-value text-warning">$${saldoActual.toLocaleString()}</div>
        </div>
        <div class="resumen-item">
          <div class="resumen-label">Monto del Pago</div>
          <div class="resumen-value text-success">$${montoPago.toLocaleString()}</div>
        </div>
        <div class="resumen-item">
          <div class="resumen-label">Saldo Restante</div>
          <div class="resumen-value ${saldoRestante === 0 ? 'text-success' : 'text-info'}">
            $${saldoRestante.toLocaleString()}
          </div>
        </div>
        ${saldoRestante === 0 ? `
          <div class="resumen-item resumen-highlight">
            <div class="resumen-label">Estado</div>
            <div class="resumen-value text-success">
              <i class="fas fa-check-circle"></i> PRÉSTAMO PAGADO COMPLETAMENTE
            </div>
          </div>
        ` : ''}
      </div>
    `;
  }

  async guardarPago(pagoId = null) {
    const form = document.getElementById('form-pago');
    if (!form) return false;

    const prestamoId = document.getElementById('pago-prestamo').value;
    const monto = parseFloat(document.getElementById('pago-monto').value);
    const fecha = document.getElementById('pago-fecha').value;
    const hora = document.getElementById('pago-hora').value;
    const metodoPago = document.getElementById('pago-metodo').value;
    const tipoPago = document.getElementById('pago-tipo').value;
    const numeroRecibo = document.getElementById('pago-recibo').value.trim();
    const observaciones = document.getElementById('pago-observaciones').value.trim();

    // Validaciones
    if (!prestamoId || !monto || !fecha || !metodoPago) {
      window.toastManager?.error('Todos los campos obligatorios deben ser completados');
      return false;
    }

    if (monto <= 0) {
      window.toastManager?.error('El monto del pago debe ser mayor a cero');
      return false;
    }

    // Validar que no exceda el saldo pendiente
    const prestamo = this.prestamos.find(p => p.id === prestamoId);
    const saldoPendiente = this.calcularSaldoPendiente(prestamo);
    
    if (monto > saldoPendiente) {
      window.toastManager?.error(`El monto del pago ($${monto.toLocaleString()}) no puede ser mayor al saldo pendiente ($${saldoPendiente.toLocaleString()})`);
      return false;
    }

    // Construir fecha completa
    const fechaCompleta = new Date(`${fecha}T${hora || '12:00'}`);
    
    const pagoData = {
      id: pagoId || this.generarId(),
      prestamoId,
      clienteId: prestamo.clienteId,
      monto,
      fecha: fechaCompleta.toISOString(),
      metodoPago,
      tipoPago: tipoPago || 'cuota_regular',
      numeroRecibo: numeroRecibo || this.generarNumeroRecibo(),
      observaciones,
      estado: 'confirmado',
      fechaCreacion: pagoId ? this.pagos.find(p => p.id === pagoId)?.fechaCreacion : new Date().toISOString(),
      fechaModificacion: new Date().toISOString()
    };

    try {
      if (pagoId) {
        // Actualizar pago existente
        const index = this.pagos.findIndex(p => p.id === pagoId);
        if (index !== -1) {
          this.pagos[index] = { ...this.pagos[index], ...pagoData };
        }
        window.toastManager?.success('Pago actualizado correctamente');
      } else {
        // Crear nuevo pago
        this.pagos.push(pagoData);
        window.toastManager?.pagoRegistrado(monto);
      }

      // Verificar si el préstamo está completamente pagado
      const nuevoSaldo = this.calcularSaldoPendiente(prestamo);
      if (nuevoSaldo <= 0.01) { // Considerar pagado si queda menos de 1 centavo
        prestamo.estado = 'pagado';
        prestamo.fechaPago = new Date().toISOString();
        localStorage.setItem('prestamos', JSON.stringify(this.prestamos));
        window.toastManager?.success('¡Préstamo pagado completamente!');
      }

      // Guardar en localStorage
      localStorage.setItem('pagos', JSON.stringify(this.pagos));
      
      // Actualizar la vista
      this.app.renderCurrentPage();
      
      return true;
    } catch (error) {
      console.error('Error guardando pago:', error);
      window.toastManager?.error('Error al guardar el pago');
      return false;
    }
  }

  // Métodos auxiliares y cálculos
  async loadData() {
    try {
      this.pagos = JSON.parse(localStorage.getItem('pagos') || '[]');
      this.prestamos = JSON.parse(localStorage.getItem('prestamos') || '[]');
      this.clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
    } catch (error) {
      console.error('Error cargando datos:', error);
      this.pagos = [];
      this.prestamos = [];
      this.clientes = [];
    }
  }

  calcularTotalPagos() {
    return this.pagos.reduce((total, pago) => total + pago.monto, 0);
  }

  calcularPagosHoy() {
    const hoy = new Date().toDateString();
    return this.pagos
      .filter(pago => new Date(pago.fecha).toDateString() === hoy)
      .reduce((total, pago) => total + pago.monto, 0);
  }

  calcularPromedioMensual() {
    // Calcular promedio de los últimos 6 meses
    const seiseMesesAtras = new Date();
    seiseMesesAtras.setMonth(seiseMesesAtras.getMonth() - 6);
    
    const pagosRecientes = this.pagos.filter(pago => 
      new Date(pago.fecha) >= seiseMesesAtras
    );
    
    const totalMeses = 6;
    const totalPagos = pagosRecientes.reduce((sum, pago) => sum + pago.monto, 0);
    
    return totalMeses > 0 ? totalPagos / totalMeses : 0;
  }

  calcularSaldoPendiente(prestamo) {
    if (!prestamo) return 0;
    
    const pagosDelPrestamo = this.pagos.filter(p => p.prestamoId === prestamo.id);
    const totalPagado = pagosDelPrestamo.reduce((sum, p) => sum + p.monto, 0);
    
    return Math.max(0, prestamo.montoTotal - totalPagado);
  }

  calcularDiasVencimiento(prestamo) {
    const hoy = new Date();
    const vencimiento = new Date(prestamo.fechaVencimiento);
    const diferencia = vencimiento - hoy;
    return Math.ceil(diferencia / (1000 * 60 * 60 * 24));
  }

  // Métodos de utilidad
  getMetodoPagoColor(metodo) {
    const colores = {
      efectivo: 'success',
      transferencia: 'info',
      cheque: 'warning',
      tarjeta: 'primary',
      deposito: 'secondary',
      otro: 'dark'
    };
    return colores[metodo] || 'secondary';
  }

  getMetodoPagoTexto(metodo) {
    const textos = {
      efectivo: 'Efectivo',
      transferencia: 'Transferencia',
      cheque: 'Cheque',
      tarjeta: 'Tarjeta',
      deposito: 'Depósito',
      otro: 'Otro'
    };
    return textos[metodo] || metodo;
  }

  generarNumeroRecibo() {
    const fecha = new Date();
    const año = fecha.getFullYear().toString().substr(-2);
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    const contador = (this.pagos.length + 1).toString().padStart(4, '0');
    return `REC${año}${mes}${contador}`;
  }

  // Métodos de acciones
  registrarPagoRapido(prestamoId) {
    this.mostrarFormularioPago(null, prestamoId);
  }

  pagoRapido() {
    // Mostrar modal de pago rápido con campos mínimos
    this.mostrarFormularioPago();
  }

  verDetallePago(pagoId) {
    window.toastManager?.info('Abriendo detalles del pago...');
  }

  imprimirRecibo(pagoId) {
    window.toastManager?.info('Preparando recibo para imprimir...');
  }

  enviarRecibo(pagoId) {
    window.toastManager?.info('Enviando recibo por WhatsApp...');
  }

  editarPago(pagoId) {
    this.mostrarFormularioPago(pagoId);
  }

  confirmarPago(pagoId) {
    // Confirmar pago pendiente
    const pago = this.pagos.find(p => p.id === pagoId);
    if (pago) {
      pago.estado = 'confirmado';
      localStorage.setItem('pagos', JSON.stringify(this.pagos));
      window.toastManager?.success('Pago confirmado correctamente');
      this.app.renderCurrentPage();
    }
  }

  anularPago(pagoId) {
    // Implementar anulación de pago
    window.toastManager?.warning('Funcionalidad de anulación en desarrollo');
  }

  exportarPagos() {
    window.toastManager?.info('Preparando exportación...');
  }

  verTodosPendientes() {
    // Filtrar por pagos pendientes
    this.estadoFiltro = 'pendiente';
    this.actualizarVistaPagos();
  }

  // Métodos de filtrado
  filtrarPagos(termino) {
    this.filtroActual = termino.toLowerCase();
    this.actualizarVistaPagos();
  }

  aplicarFiltroFecha() {
    // Implementar filtrado por rango de fechas
    this.actualizarVistaPagos();
  }

  aplicarFiltroPeriodo(periodo) {
    this.periodoFiltro = periodo;
    this.actualizarVistaPagos();
  }

  ordenarPor(criterio) {
    const [campo, direccion] = criterio.split('-');
    this.ordenActual = { campo, direccion };
    this.actualizarVistaPagos();
  }

  actualizarVistaPagos() {
    // Implementar filtrado y ordenamiento
    this.app.renderCurrentPage();
  }

  // Utilidades
  generarId() {
    return 'pago_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  formatDate(fecha) {
    return new Date(fecha).toLocaleDateString('es-DO');
  }

  formatTime(fecha) {
    return new Date(fecha).toLocaleTimeString('es-DO', { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  }
}

// Instancia global
if (typeof window !== 'undefined') {
  window.pagosPage = new PagosPage();
}
