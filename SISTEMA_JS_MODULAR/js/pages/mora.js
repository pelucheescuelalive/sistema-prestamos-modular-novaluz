/* ===================================================================
   🎯 PÁGINA MORA
   Gestión de clientes en mora y seguimiento de atrasos
   =================================================================== */

export class MoraPage {
  constructor(app) {
    this.app = app;
    this.prestamos = [];
    this.clientes = [];
    this.pagos = [];
    this.clientesEnMora = [];
    this.filtroActual = '';
    this.nivelMoraFiltro = '';
  }

  async render() {
    await this.loadData();
    
    return `
      <div class="mora-page">
        <!-- Encabezado -->
        <div class="page-header">
          <div class="header-left">
            <h1 class="page-title">
              <i class="fas fa-exclamation-triangle"></i>
              Gestión de Mora
            </h1>
            <p class="page-subtitle">
              Seguimiento y control de clientes con atrasos en pagos
            </p>
          </div>
          <div class="header-right">
            <button class="btn btn-warning" onclick="moraPage.enviarRecordatorios()">
              <i class="fas fa-bell"></i>
              Enviar Recordatorios
            </button>
            <button class="btn btn-danger" onclick="moraPage.generarReporteMora()">
              <i class="fas fa-file-pdf"></i>
              Reporte de Mora
            </button>
            <button class="btn btn-outline-secondary" onclick="moraPage.configurarMora()">
              <i class="fas fa-cog"></i>
              Configurar
            </button>
          </div>
        </div>

        <!-- Alertas de Mora Crítica -->
        ${this.renderAlertasCriticas()}

        <!-- Herramientas de Filtro -->
        <div class="mora-toolbar">
          <div class="search-section">
            <div class="input-group">
              <input type="text" 
                     id="buscar-mora" 
                     class="form-control" 
                     placeholder="Buscar por cliente, cédula..."
                     onkeyup="moraPage.filtrarMora(this.value)">
              <button class="btn btn-outline-primary">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
          
          <div class="filter-section">
            <select id="filtro-nivel-mora" class="form-control" onchange="moraPage.aplicarFiltroNivel(this.value)">
              <option value="">Todos los Niveles</option>
              <option value="temprana">Mora Temprana (1-7 días)</option>
              <option value="media">Mora Media (8-30 días)</option>
              <option value="alta">Mora Alta (31-90 días)</option>
              <option value="critica">Mora Crítica (+90 días)</option>
            </select>
            
            <select id="ordenar-por" class="form-control" onchange="moraPage.ordenarPor(this.value)">
              <option value="dias-desc">Más Días de Atraso</option>
              <option value="dias-asc">Menos Días de Atraso</option>
              <option value="monto-desc">Mayor Deuda</option>
              <option value="monto-asc">Menor Deuda</option>
              <option value="cliente-asc">Cliente A-Z</option>
            </select>
            
            <button class="btn btn-info" onclick="moraPage.actualizarDatos()">
              <i class="fas fa-sync"></i>
              Actualizar
            </button>
          </div>
        </div>

        <!-- Estadísticas de Mora -->
        <div class="mora-stats">
          <div class="stat-item stat-warning">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.clientesEnMora.length}</div>
              <div class="stat-label">Clientes en Mora</div>
            </div>
            <div class="stat-trend">
              <small>${this.calcularPorcentajeMora().toFixed(1)}% del total</small>
            </div>
          </div>
          
          <div class="stat-item stat-danger">
            <div class="stat-icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">$${this.calcularTotalMora().toLocaleString()}</div>
              <div class="stat-label">Total en Mora</div>
            </div>
            <div class="stat-trend">
              <small>${this.calcularPorcentajeCartera().toFixed(1)}% de la cartera</small>
            </div>
          </div>
          
          <div class="stat-item stat-info">
            <div class="stat-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.calcularPromedioAtraso()}</div>
              <div class="stat-label">Promedio Atraso</div>
            </div>
            <div class="stat-trend">
              <small>días de atraso promedio</small>
            </div>
          </div>
          
          <div class="stat-item stat-success">
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">${this.calcularTasaRecuperacion().toFixed(1)}%</div>
              <div class="stat-label">Tasa Recuperación</div>
            </div>
            <div class="stat-trend">
              <small>último mes</small>
            </div>
          </div>
        </div>

        <!-- Gráfico de Distribución de Mora -->
        <div class="mora-distribution">
          <div class="card">
            <div class="card-header">
              <h5><i class="fas fa-chart-pie"></i> Distribución por Nivel de Mora</h5>
            </div>
            <div class="card-body">
              ${this.renderDistribucionMora()}
            </div>
          </div>
        </div>

        <!-- Lista de Clientes en Mora -->
        <div class="mora-content">
          <div class="mora-table-container">
            ${this.renderTablaMora()}
          </div>
        </div>
      </div>
    `;
  }

  renderAlertasCriticas() {
    const morasCriticas = this.clientesEnMora.filter(cliente => cliente.diasAtraso > 90);
    
    if (!morasCriticas.length) {
      return '';
    }

    return `
      <div class="alertas-criticas">
        <div class="alert alert-danger">
          <div class="alert-header">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>¡ATENCIÓN!</strong> Hay ${morasCriticas.length} cliente(s) con mora crítica (+90 días)
          </div>
          <div class="alert-body">
            <div class="clientes-criticos">
              ${morasCriticas.slice(0, 3).map(cliente => `
                <span class="cliente-critico">
                  ${cliente.nombre} (${cliente.diasAtraso} días - $${cliente.montoAdeudado.toLocaleString()})
                </span>
              `).join('')}
              ${morasCriticas.length > 3 ? `<span class="mas-clientes">y ${morasCriticas.length - 3} más...</span>` : ''}
            </div>
            <div class="alert-actions">
              <button class="btn btn-sm btn-outline-light" onclick="moraPage.gestionarCriticos()">
                Gestionar Casos Críticos
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  renderDistribucionMora() {
    const niveles = {
      temprana: this.clientesEnMora.filter(c => c.diasAtraso >= 1 && c.diasAtraso <= 7).length,
      media: this.clientesEnMora.filter(c => c.diasAtraso >= 8 && c.diasAtraso <= 30).length,
      alta: this.clientesEnMora.filter(c => c.diasAtraso >= 31 && c.diasAtraso <= 90).length,
      critica: this.clientesEnMora.filter(c => c.diasAtraso > 90).length
    };

    const total = Object.values(niveles).reduce((sum, count) => sum + count, 0);

    if (total === 0) {
      return '<p class="text-success text-center"><i class="fas fa-check-circle"></i> No hay clientes en mora</p>';
    }

    return `
      <div class="distribution-chart">
        <div class="distribution-bars">
          ${Object.entries(niveles).map(([nivel, cantidad]) => {
            const porcentaje = total > 0 ? (cantidad / total) * 100 : 0;
            const color = this.getColorNivelMora(nivel);
            
            return `
              <div class="distribution-item">
                <div class="distribution-label">
                  <span class="nivel-nombre">${this.getNombreNivelMora(nivel)}</span>
                  <span class="nivel-cantidad">${cantidad} clientes</span>
                </div>
                <div class="distribution-bar">
                  <div class="bar-fill bg-${color}" style="width: ${porcentaje}%"></div>
                  <span class="bar-percentage">${porcentaje.toFixed(1)}%</span>
                </div>
              </div>
            `;
          }).join('')}
        </div>
      </div>
    `;
  }

  renderTablaMora() {
    if (!this.clientesEnMora.length) {
      return `
        <div class="empty-state">
          <div class="empty-icon text-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <h3 class="text-success">¡Excelente!</h3>
          <p>No hay clientes en mora en este momento</p>
          <button class="btn btn-primary" onclick="app.navigate('prestamos')">
            Ver Préstamos Activos
          </button>
        </div>
      `;
    }

    return `
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Préstamo</th>
              <th>Días Atraso</th>
              <th>Nivel Mora</th>
              <th>Monto Adeudado</th>
              <th>Último Contacto</th>
              <th>Próxima Acción</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${this.clientesEnMora.map(cliente => this.renderFilaMora(cliente)).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderFilaMora(clienteMora) {
    const cliente = this.clientes.find(c => c.id === clienteMora.clienteId);
    const prestamo = this.prestamos.find(p => p.id === clienteMora.prestamoId);
    const nivelMora = this.determinarNivelMora(clienteMora.diasAtraso);
    const proximaAccion = this.determinarProximaAccion(clienteMora);
    
    return `
      <tr class="mora-row nivel-${nivelMora.toLowerCase()}" data-cliente-id="${clienteMora.clienteId}">
        <td>
          <div class="cliente-info">
            <div class="cliente-avatar">👤</div>
            <div class="cliente-datos">
              <div class="cliente-nombre">${cliente?.nombre || 'Cliente no encontrado'}</div>
              <div class="cliente-contacto">
                <small><i class="fas fa-phone"></i> ${cliente?.telefono || 'Sin teléfono'}</small>
                <small><i class="fas fa-id-card"></i> ${cliente?.cedula || ''}</small>
              </div>
            </div>
          </div>
        </td>
        <td>
          <div class="prestamo-info">
            <span class="prestamo-id">#${prestamo?.id.substr(-6).toUpperCase() || 'N/A'}</span>
            <small class="d-block">
              $${prestamo?.monto.toLocaleString()} → $${prestamo?.montoTotal.toLocaleString()}
            </small>
            <small class="text-muted">
              Vencido: ${this.formatDate(prestamo?.fechaVencimiento)}
            </small>
          </div>
        </td>
        <td>
          <div class="dias-atraso">
            <span class="numero-dias text-${this.getColorNivelMora(nivelMora)}">${clienteMora.diasAtraso}</span>
            <small class="d-block">días</small>
          </div>
        </td>
        <td>
          <span class="badge badge-${this.getColorNivelMora(nivelMora)}">
            ${this.getNombreNivelMora(nivelMora)}
          </span>
        </td>
        <td class="amount">
          <span class="monto-adeudado text-danger">$${clienteMora.montoAdeudado.toLocaleString()}</span>
          <small class="d-block text-muted">
            ${this.calcularPorcentajeAdeudado(clienteMora).toFixed(1)}% del préstamo
          </small>
        </td>
        <td>
          <div class="ultimo-contacto">
            <span class="fecha-contacto">${clienteMora.ultimoContacto ? this.formatDate(clienteMora.ultimoContacto) : 'Sin contacto'}</span>
            <small class="d-block ${this.esContactoReciente(clienteMora.ultimoContacto) ? 'text-success' : 'text-warning'}">
              ${clienteMora.ultimoContacto ? this.calcularDiasDesdeContacto(clienteMora.ultimoContacto) + ' días atrás' : 'Nunca contactado'}
            </small>
          </div>
        </td>
        <td>
          <div class="proxima-accion">
            <span class="accion-tipo">${proximaAccion.tipo}</span>
            <small class="d-block text-${proximaAccion.urgencia}">
              ${proximaAccion.descripcion}
            </small>
          </div>
        </td>
        <td>
          <div class="action-buttons">
            <div class="btn-group-vertical btn-group-sm">
              <button class="btn btn-outline-primary" onclick="moraPage.contactarCliente('${clienteMora.clienteId}')" title="Contactar">
                <i class="fas fa-phone"></i>
              </button>
              <button class="btn btn-outline-success" onclick="moraPage.registrarPago('${clienteMora.prestamoId}')" title="Registrar Pago">
                <i class="fas fa-credit-card"></i>
              </button>
              <button class="btn btn-outline-info" onclick="moraPage.verHistorial('${clienteMora.clienteId}')" title="Ver Historial">
                <i class="fas fa-history"></i>
              </button>
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Más acciones">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" onclick="moraPage.enviarRecordatorio('${clienteMora.clienteId}')">
                    <i class="fas fa-paper-plane"></i> Enviar Recordatorio
                  </a>
                  <a class="dropdown-item" onclick="moraPage.programarLlamada('${clienteMora.clienteId}')">
                    <i class="fas fa-calendar-plus"></i> Programar Llamada
                  </a>
                  <a class="dropdown-item" onclick="moraPage.planPago('${clienteMora.prestamoId}')">
                    <i class="fas fa-handshake"></i> Plan de Pago
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" onclick="moraPage.marcarIncobrables('${clienteMora.prestamoId}')">
                    <i class="fas fa-ban"></i> Marcar Incobrable
                  </a>
                  <a class="dropdown-item text-danger" onclick="moraPage.iniciarCobranzaLegal('${clienteMora.clienteId}')">
                    <i class="fas fa-gavel"></i> Cobranza Legal
                  </a>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
    `;
  }

  // Métodos de carga y cálculo de datos
  async loadData() {
    try {
      this.prestamos = JSON.parse(localStorage.getItem('prestamos') || '[]');
      this.clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
      this.pagos = JSON.parse(localStorage.getItem('pagos') || '[]');
      
      // Calcular clientes en mora
      this.clientesEnMora = this.calcularClientesEnMora();
    } catch (error) {
      console.error('Error cargando datos:', error);
      this.prestamos = [];
      this.clientes = [];
      this.pagos = [];
      this.clientesEnMora = [];
    }
  }

  calcularClientesEnMora() {
    const hoy = new Date();
    const clientesEnMora = [];

    this.prestamos
      .filter(prestamo => prestamo.estado === 'activo')
      .forEach(prestamo => {
        const fechaVencimiento = new Date(prestamo.fechaVencimiento);
        
        if (hoy > fechaVencimiento) {
          const diasAtraso = Math.floor((hoy - fechaVencimiento) / (1000 * 60 * 60 * 24));
          const saldoPendiente = this.calcularSaldoPendiente(prestamo);
          
          if (saldoPendiente > 0) {
            const historialContacto = this.obtenerHistorialContacto(prestamo.clienteId);
            
            clientesEnMora.push({
              clienteId: prestamo.clienteId,
              prestamoId: prestamo.id,
              nombre: prestamo.cliente,
              diasAtraso,
              montoAdeudado: saldoPendiente,
              montoTotal: prestamo.montoTotal,
              fechaVencimiento: prestamo.fechaVencimiento,
              ultimoContacto: historialContacto.ultimoContacto,
              numeroContactos: historialContacto.numeroContactos,
              promesasPago: historialContacto.promesasPago
            });
          }
        }
      });

    return clientesEnMora.sort((a, b) => b.diasAtraso - a.diasAtraso);
  }

  calcularSaldoPendiente(prestamo) {
    const pagosDelPrestamo = this.pagos.filter(p => p.prestamoId === prestamo.id);
    const totalPagado = pagosDelPrestamo.reduce((sum, p) => sum + p.monto, 0);
    return Math.max(0, prestamo.montoTotal - totalPagado);
  }

  obtenerHistorialContacto(clienteId) {
    // En una implementación real, esto vendría de una tabla de historial de contactos
    // Por ahora, simulamos datos básicos
    return {
      ultimoContacto: null,
      numeroContactos: 0,
      promesasPago: []
    };
  }

  // Métodos de cálculo de estadísticas
  calcularPorcentajeMora() {
    const totalClientes = this.clientes.length;
    return totalClientes > 0 ? (this.clientesEnMora.length / totalClientes) * 100 : 0;
  }

  calcularTotalMora() {
    return this.clientesEnMora.reduce((total, cliente) => total + cliente.montoAdeudado, 0);
  }

  calcularPorcentajeCartera() {
    const totalCartera = this.prestamos
      .filter(p => p.estado === 'activo')
      .reduce((total, p) => total + p.montoTotal, 0);
    
    return totalCartera > 0 ? (this.calcularTotalMora() / totalCartera) * 100 : 0;
  }

  calcularPromedioAtraso() {
    if (!this.clientesEnMora.length) return 0;
    
    const totalDias = this.clientesEnMora.reduce((sum, cliente) => sum + cliente.diasAtraso, 0);
    return Math.round(totalDias / this.clientesEnMora.length);
  }

  calcularTasaRecuperacion() {
    // Calcular porcentaje de pagos recuperados en el último mes
    const unMesAtras = new Date();
    unMesAtras.setMonth(unMesAtras.getMonth() - 1);
    
    const pagosRecientes = this.pagos.filter(p => new Date(p.fecha) >= unMesAtras);
    const prestamosVencidos = this.prestamos.filter(p => 
      new Date(p.fechaVencimiento) < unMesAtras && p.estado === 'activo'
    );
    
    if (!prestamosVencidos.length) return 100;
    
    const totalRecuperado = pagosRecientes.reduce((sum, p) => sum + p.monto, 0);
    const totalEsperado = prestamosVencidos.reduce((sum, p) => sum + p.montoTotal, 0);
    
    return totalEsperado > 0 ? (totalRecuperado / totalEsperado) * 100 : 0;
  }

  // Métodos de utilidad para niveles de mora
  determinarNivelMora(diasAtraso) {
    if (diasAtraso <= 7) return 'temprana';
    if (diasAtraso <= 30) return 'media';
    if (diasAtraso <= 90) return 'alta';
    return 'critica';
  }

  getNombreNivelMora(nivel) {
    const nombres = {
      temprana: 'Temprana',
      media: 'Media',
      alta: 'Alta',
      critica: 'Crítica'
    };
    return nombres[nivel] || nivel;
  }

  getColorNivelMora(nivel) {
    const colores = {
      temprana: 'warning',
      media: 'orange',
      alta: 'danger',
      critica: 'dark'
    };
    return colores[nivel] || 'secondary';
  }

  determinarProximaAccion(clienteMora) {
    const diasAtraso = clienteMora.diasAtraso;
    const ultimoContacto = clienteMora.ultimoContacto;
    const diasSinContacto = ultimoContacto ? this.calcularDiasDesdeContacto(ultimoContacto) : 999;

    if (diasAtraso > 90) {
      return {
        tipo: 'Cobranza Legal',
        descripcion: 'Iniciar proceso legal',
        urgencia: 'danger'
      };
    } else if (diasAtraso > 30) {
      return {
        tipo: 'Visita Domiciliaria',
        descripcion: 'Contacto presencial',
        urgencia: 'danger'
      };
    } else if (diasSinContacto > 7) {
      return {
        tipo: 'Llamada Telefónica',
        descripcion: 'Contacto inmediato',
        urgencia: 'warning'
      };
    } else {
      return {
        tipo: 'Seguimiento',
        descripcion: 'Monitorear progreso',
        urgencia: 'info'
      };
    }
  }

  calcularPorcentajeAdeudado(clienteMora) {
    return (clienteMora.montoAdeudado / clienteMora.montoTotal) * 100;
  }

  esContactoReciente(fechaContacto) {
    if (!fechaContacto) return false;
    const diasDesdeContacto = this.calcularDiasDesdeContacto(fechaContacto);
    return diasDesdeContacto <= 7;
  }

  calcularDiasDesdeContacto(fechaContacto) {
    if (!fechaContacto) return 999;
    const hoy = new Date();
    const contacto = new Date(fechaContacto);
    return Math.floor((hoy - contacto) / (1000 * 60 * 60 * 24));
  }

  // Métodos de acciones
  contactarCliente(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (!cliente) return;

    const modal = new (window.ModalComponent || class {})();
    modal.show({
      title: `Contactar a ${cliente.nombre}`,
      content: `
        <div class="contacto-form">
          <div class="cliente-info mb-3">
            <h6>Información de Contacto:</h6>
            <p><i class="fas fa-phone"></i> ${cliente.telefono || 'No disponible'}</p>
            <p><i class="fas fa-envelope"></i> ${cliente.email || 'No disponible'}</p>
          </div>
          
          <div class="form-group">
            <label>Tipo de Contacto:</label>
            <select id="tipo-contacto" class="form-control">
              <option value="llamada">Llamada Telefónica</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="email">Email</option>
              <option value="visita">Visita Domiciliaria</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Resultado del Contacto:</label>
            <select id="resultado-contacto" class="form-control">
              <option value="contactado">Contactado exitosamente</option>
              <option value="no_contesta">No contesta</option>
              <option value="promesa_pago">Promesa de pago</option>
              <option value="pagara_parcial">Pagará parcialmente</option>
              <option value="dificultades">Reporta dificultades</option>
              <option value="numero_erroneo">Número erróneo</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Observaciones:</label>
            <textarea id="observaciones-contacto" class="form-control" rows="3" 
                      placeholder="Detalles del contacto realizado..."></textarea>
          </div>
          
          <div class="form-group">
            <label>Próxima Acción:</label>
            <input type="date" id="proxima-accion" class="form-control" 
                   value="${new Date(Date.now() + 7*24*60*60*1000).toISOString().split('T')[0]}">
          </div>
        </div>
      `,
      size: 'medium',
      buttons: [
        { text: 'Cancelar', type: 'secondary', action: 'close' },
        { text: 'Registrar Contacto', type: 'primary', action: 'confirm' }
      ],
      onConfirm: () => {
        // Registrar el contacto
        this.registrarContacto(clienteId);
        window.toastManager?.success('Contacto registrado correctamente');
        return true;
      }
    });
  }

  registrarContacto(clienteId) {
    // Implementar registro de contacto en localStorage o base de datos
    const contacto = {
      clienteId,
      fecha: new Date().toISOString(),
      tipo: document.getElementById('tipo-contacto')?.value,
      resultado: document.getElementById('resultado-contacto')?.value,
      observaciones: document.getElementById('observaciones-contacto')?.value,
      proximaAccion: document.getElementById('proxima-accion')?.value
    };
    
    // Guardar en historial de contactos
    const historial = JSON.parse(localStorage.getItem('historial_contactos') || '[]');
    historial.push(contacto);
    localStorage.setItem('historial_contactos', JSON.stringify(historial));
  }

  registrarPago(prestamoId) {
    this.app.navigate('pagos', { prestamoId });
  }

  verHistorial(clienteId) {
    window.toastManager?.info('Abriendo historial de contactos...');
  }

  enviarRecordatorio(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    window.toastManager?.info(`Enviando recordatorio a ${cliente?.nombre}...`);
  }

  programarLlamada(clienteId) {
    window.toastManager?.info('Programando llamada...');
  }

  planPago(prestamoId) {
    window.toastManager?.info('Creando plan de pago...');
  }

  marcarIncobrables(prestamoId) {
    window.toastManager?.warning('Marcando como incobrable...');
  }

  iniciarCobranzaLegal(clienteId) {
    window.toastManager?.warning('Iniciando proceso de cobranza legal...');
  }

  enviarRecordatorios() {
    window.toastManager?.info('Enviando recordatorios masivos...');
  }

  generarReporteMora() {
    window.toastManager?.info('Generando reporte de mora...');
  }

  configurarMora() {
    window.toastManager?.info('Abriendo configuración de mora...');
  }

  gestionarCriticos() {
    // Filtrar solo casos críticos
    this.nivelMoraFiltro = 'critica';
    this.actualizarVistaMora();
  }

  // Métodos de filtrado
  filtrarMora(termino) {
    this.filtroActual = termino.toLowerCase();
    this.actualizarVistaMora();
  }

  aplicarFiltroNivel(nivel) {
    this.nivelMoraFiltro = nivel;
    this.actualizarVistaMora();
  }

  ordenarPor(criterio) {
    const [campo, direccion] = criterio.split('-');
    this.ordenActual = { campo, direccion };
    this.actualizarVistaMora();
  }

  actualizarDatos() {
    this.loadData().then(() => {
      this.app.renderCurrentPage();
      window.toastManager?.success('Datos actualizados');
    });
  }

  actualizarVistaMora() {
    // Implementar filtrado y ordenamiento
    this.app.renderCurrentPage();
  }

  // Utilidades
  formatDate(fecha) {
    return new Date(fecha).toLocaleDateString('es-DO');
  }
}

// Instancia global
if (typeof window !== 'undefined') {
  window.moraPage = new MoraPage();
}
