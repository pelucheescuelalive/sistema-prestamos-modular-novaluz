/* ===================================================================
   👥 PÁGINA DE GESTIÓN DE CLIENTES - VERSIÓN COMPLETA FUNCIONAL
   Sistema completo de CRUD para clientes con información de garantes
   =================================================================== */

export class ClientesPage {
  constructor(app) {
    this.app = app;
    this.clientes = this.cargarClientes();
    this.vista = 'lista'; // 'lista', 'formulario', 'detalles'
    this.clienteSeleccionado = null;
    this.esEdicion = false;
    console.log('👥 Módulo de Clientes inicializado correctamente');
  }

  async render() {
    console.log(`🎨 Renderizando vista: ${this.vista}`);
    
    switch (this.vista) {
      case 'formulario':
        return this.renderFormulario();
      case 'detalles':
        return this.renderDetalles();
      default:
        return this.renderLista();
    }
  }

  // 💾 Gestión de Datos
  cargarClientes() {
    try {
      return JSON.parse(localStorage.getItem('novaluz_clientes')) || [];
    } catch (error) {
      console.error('Error cargando clientes:', error);
      return [];
    }
  }

  guardarClientes() {
    try {
      localStorage.setItem('novaluz_clientes', JSON.stringify(this.clientes));
      return true;
    } catch (error) {
      console.error('Error guardando clientes:', error);
      return false;
    }
  }

  // 📋 Vista Principal - Lista de Clientes
  renderLista() {
    const stats = this.obtenerEstadisticas();
    
    return `
      <div class="clientes-container animate-fadeInUp">
        <!-- Header -->
        <div class="page-header">
          <div class="header-content">
            <div class="header-info">
              <h1 class="page-title">
                <i class="fas fa-users"></i>
                Gestión de Clientes
              </h1>
              <p class="page-subtitle">Administra la información completa de tus clientes y garantes</p>
            </div>
            <div class="header-actions">
              <button class="btn btn-primary btn-lg" onclick="window.clientesPage.irAFormulario()">
                <i class="fas fa-user-plus"></i>
                Nuevo Cliente
              </button>
            </div>
          </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-mini">
          <div class="stat-mini">
            <i class="fas fa-users"></i>
            <div class="stat-info">
              <span class="stat-number">${stats.total}</span>
              <span class="stat-label">Total Clientes</span>
            </div>
          </div>
          <div class="stat-mini">
            <i class="fas fa-user-shield"></i>
            <div class="stat-info">
              <span class="stat-number">${stats.conGarante}</span>
              <span class="stat-label">Con Garante</span>
            </div>
          </div>
          <div class="stat-mini">
            <i class="fas fa-venus-mars"></i>
            <div class="stat-info">
              <span class="stat-number">${stats.masculinos}M / ${stats.femeninos}F</span>
              <span class="stat-label">Distribución</span>
            </div>
          </div>
        </div>

        <!-- Contenido -->
        ${this.clientes.length === 0 ? this.renderEstadoVacio() : this.renderTablaClientes()}
      </div>
    `;
  }

  // 📝 Vista Formulario
  renderFormulario() {
    const cliente = this.clienteSeleccionado || {};
    const titulo = this.esEdicion ? 'Editar Cliente' : 'Nuevo Cliente';
    const botonTexto = this.esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente';
    
    return `
      <div class="clientes-container animate-fadeInUp">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-user-edit"></i>
              ${titulo}
            </h2>
            <button class="btn btn-ghost" onclick="window.clientesPage.volverALista()">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="card-body">
            <form id="form-cliente" onsubmit="window.clientesPage.procesarFormulario(event)">
              
              <!-- Información Personal -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-user"></i>
                  Información Personal
                </h3>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="${cliente.nombre || ''}" placeholder="Nombre del cliente">
                  </div>
                  <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" required 
                           value="${cliente.apellido || ''}" placeholder="Apellido del cliente">
                  </div>
                  <div class="form-group">
                    <label for="cedula">Cédula *</label>
                    <input type="text" id="cedula" name="cedula" required 
                           value="${cliente.cedula || ''}" placeholder="000-0000000-0">
                  </div>
                  <div class="form-group">
                    <label for="sexo">Sexo *</label>
                    <select id="sexo" name="sexo" required>
                      <option value="">Seleccionar</option>
                      <option value="masculino" ${cliente.sexo === 'masculino' ? 'selected' : ''}>Masculino</option>
                      <option value="femenino" ${cliente.sexo === 'femenino' ? 'selected' : ''}>Femenino</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Información de Contacto -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-phone"></i>
                  Información de Contacto
                </h3>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" 
                           value="${cliente.telefono || ''}" placeholder="809-000-0000">
                  </div>
                  <div class="form-group">
                    <label for="celular">Celular *</label>
                    <input type="tel" id="celular" name="celular" required 
                           value="${cliente.celular || ''}" placeholder="829-000-0000">
                  </div>
                  <div class="form-group form-group-full">
                    <label for="direccion">Dirección *</label>
                    <textarea id="direccion" name="direccion" required 
                              placeholder="Dirección completa del cliente">${cliente.direccion || ''}</textarea>
                  </div>
                </div>
              </div>

              <!-- Información del Garante -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-user-shield"></i>
                  Información del Garante (Opcional)
                  <label class="checkbox-toggle">
                    <input type="checkbox" id="tiene-garante" 
                           ${cliente.garante ? 'checked' : ''} 
                           onchange="window.clientesPage.toggleGarante()">
                    <span class="toggle-text">¿Tiene garante?</span>
                  </label>
                </h3>
                <div class="garante-fields" id="garante-fields" 
                     style="display: ${cliente.garante ? 'block' : 'none'};">
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="garante-nombre">Nombre del Garante</label>
                      <input type="text" id="garante-nombre" name="garanteNombre" 
                             value="${cliente.garante?.nombre || ''}" placeholder="Nombre del garante">
                    </div>
                    <div class="form-group">
                      <label for="garante-apellido">Apellido del Garante</label>
                      <input type="text" id="garante-apellido" name="garanteApellido" 
                             value="${cliente.garante?.apellido || ''}" placeholder="Apellido del garante">
                    </div>
                    <div class="form-group">
                      <label for="garante-cedula">Cédula del Garante</label>
                      <input type="text" id="garante-cedula" name="garanteCedula" 
                             value="${cliente.garante?.cedula || ''}" placeholder="000-0000000-0">
                    </div>
                    <div class="form-group">
                      <label for="garante-telefono">Teléfono del Garante</label>
                      <input type="tel" id="garante-telefono" name="garanteTelefono" 
                             value="${cliente.garante?.telefono || ''}" placeholder="809-000-0000">
                    </div>
                    <div class="form-group form-group-full">
                      <label for="garante-direccion">Dirección del Garante</label>
                      <textarea id="garante-direccion" name="garanteDireccion" 
                                placeholder="Dirección completa del garante">${cliente.garante?.direccion || ''}</textarea>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones -->
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.clientesPage.volverALista()">
                  <i class="fas fa-times"></i>
                  Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i>
                  ${botonTexto}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    `;
  }

  // 👁️ Vista Detalles
  renderDetalles() {
    const cliente = this.clienteSeleccionado;
    if (!cliente) {
      this.volverALista();
      return this.renderLista();
    }

    return `
      <div class="clientes-container animate-fadeInUp">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-user ${cliente.sexo === 'femenino' ? 'text-pink' : 'text-blue'}"></i>
              ${cliente.nombre} ${cliente.apellido}
            </h2>
            <div class="header-actions">
              <button class="btn btn-warning" onclick="window.clientesPage.editarCliente('${cliente.id}')">
                <i class="fas fa-edit"></i>
                Editar
              </button>
              <button class="btn btn-ghost" onclick="window.clientesPage.volverALista()">
                <i class="fas fa-arrow-left"></i>
                Volver
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="info-grid">
              <!-- Información Personal -->
              <div class="info-card">
                <h3><i class="fas fa-id-card"></i> Datos Personales</h3>
                <div class="info-list">
                  <div class="info-item">
                    <span class="label">Nombre Completo:</span>
                    <span class="value">${cliente.nombre} ${cliente.apellido}</span>
                  </div>
                  <div class="info-item">
                    <span class="label">Cédula:</span>
                    <span class="value"><code>${cliente.cedula}</code></span>
                  </div>
                  <div class="info-item">
                    <span class="label">Sexo:</span>
                    <span class="value">${cliente.sexo === 'masculino' ? '♂ Masculino' : '♀ Femenino'}</span>
                  </div>
                  <div class="info-item">
                    <span class="label">Celular:</span>
                    <span class="value">${cliente.celular}</span>
                  </div>
                  ${cliente.telefono ? `
                    <div class="info-item">
                      <span class="label">Teléfono:</span>
                      <span class="value">${cliente.telefono}</span>
                    </div>
                  ` : ''}
                  <div class="info-item">
                    <span class="label">Dirección:</span>
                    <span class="value">${cliente.direccion}</span>
                  </div>
                </div>
              </div>

              <!-- Información del Garante -->
              ${cliente.garante ? `
                <div class="info-card">
                  <h3><i class="fas fa-user-shield"></i> Información del Garante</h3>
                  <div class="info-list">
                    <div class="info-item">
                      <span class="label">Nombre:</span>
                      <span class="value">${cliente.garante.nombre} ${cliente.garante.apellido}</span>
                    </div>
                    <div class="info-item">
                      <span class="label">Cédula:</span>
                      <span class="value"><code>${cliente.garante.cedula}</code></span>
                    </div>
                    ${cliente.garante.telefono ? `
                      <div class="info-item">
                        <span class="label">Teléfono:</span>
                        <span class="value">${cliente.garante.telefono}</span>
                      </div>
                    ` : ''}
                    <div class="info-item">
                      <span class="label">Dirección:</span>
                      <span class="value">${cliente.garante.direccion || 'No especificada'}</span>
                    </div>
                  </div>
                </div>
              ` : ''}
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // 📋 Tabla de Clientes
  renderTablaClientes() {
    return `
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-list"></i>
            Lista de Clientes (${this.clientes.length})
          </h2>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="search-clientes" placeholder="Buscar clientes..." 
                   oninput="window.clientesPage.filtrarClientes(this.value)">
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-modern">
              <thead>
                <tr>
                  <th>Cliente</th>
                  <th>Cédula</th>
                  <th>Contacto</th>
                  <th>Garante</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tabla-clientes-body">
                ${this.clientes.map(cliente => this.crearFilaCliente(cliente)).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  // 👤 Fila de Cliente
  crearFilaCliente(cliente) {
    return `
      <tr class="cliente-row" data-cliente-id="${cliente.id}">
        <td>
          <div class="cliente-info">
            <div class="cliente-avatar">
              <i class="fas fa-user ${cliente.sexo === 'femenino' ? 'text-pink' : 'text-blue'}"></i>
            </div>
            <div class="cliente-datos">
              <strong>${cliente.nombre} ${cliente.apellido}</strong>
              <small>${cliente.sexo === 'masculino' ? '♂' : '♀'} ${cliente.sexo}</small>
            </div>
          </div>
        </td>
        <td><code>${cliente.cedula}</code></td>
        <td>
          <div class="contacto-info">
            <div><i class="fas fa-mobile-alt"></i> ${cliente.celular}</div>
            ${cliente.telefono ? `<div><i class="fas fa-phone"></i> ${cliente.telefono}</div>` : ''}
          </div>
        </td>
        <td>
          ${cliente.garante ? 
            `<span class="badge badge-success"><i class="fas fa-check"></i> Sí</span>` : 
            `<span class="badge badge-secondary"><i class="fas fa-times"></i> No</span>`
          }
        </td>
        <td>
          <div class="acciones-grupo">
            <button class="btn btn-sm btn-primary" onclick="window.clientesPage.verDetalles('${cliente.id}')" title="Ver Detalles">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-warning" onclick="window.clientesPage.editarCliente('${cliente.id}')" title="Editar">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="window.clientesPage.eliminarCliente('${cliente.id}')" title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }

  // 🏠 Estado Vacío
  renderEstadoVacio() {
    return `
      <div class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-users"></i>
        </div>
        <h3>No hay clientes registrados</h3>
        <p>Comienza agregando tu primer cliente al sistema</p>
        <button class="btn btn-primary" onclick="window.clientesPage.irAFormulario()">
          <i class="fas fa-user-plus"></i>
          Agregar Primer Cliente
        </button>
      </div>
    `;
  }

  // 📊 Estadísticas
  obtenerEstadisticas() {
    return {
      total: this.clientes.length,
      conGarante: this.clientes.filter(c => c.garante).length,
      masculinos: this.clientes.filter(c => c.sexo === 'masculino').length,
      femeninos: this.clientes.filter(c => c.sexo === 'femenino').length
    };
  }

  // 🚀 Métodos de Navegación
  irAFormulario() {
    this.vista = 'formulario';
    this.clienteSeleccionado = null;
    this.esEdicion = false;
    this.app.loadPage('clientes');
  }

  editarCliente(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.vista = 'formulario';
      this.clienteSeleccionado = cliente;
      this.esEdicion = true;
      this.app.loadPage('clientes');
    }
  }

  verDetalles(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.vista = 'detalles';
      this.clienteSeleccionado = cliente;
      this.app.loadPage('clientes');
    }
  }

  volverALista() {
    this.vista = 'lista';
    this.clienteSeleccionado = null;
    this.esEdicion = false;
    this.app.loadPage('clientes');
  }

  // 🎛️ Funciones de Formulario
  toggleGarante() {
    const checkbox = document.getElementById('tiene-garante');
    const fields = document.getElementById('garante-fields');
    if (checkbox && fields) {
      fields.style.display = checkbox.checked ? 'block' : 'none';
    }
  }

  procesarFormulario(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const cliente = {
      id: this.esEdicion ? this.clienteSeleccionado.id : this.generarId(),
      nombre: formData.get('nombre').trim(),
      apellido: formData.get('apellido').trim(),
      cedula: formData.get('cedula').trim(),
      sexo: formData.get('sexo'),
      telefono: formData.get('telefono').trim(),
      celular: formData.get('celular').trim(),
      direccion: formData.get('direccion').trim(),
      fechaRegistro: this.esEdicion ? this.clienteSeleccionado.fechaRegistro : new Date().toISOString(),
      fechaActualizacion: new Date().toISOString()
    };

    // Garante
    const tieneGarante = document.getElementById('tiene-garante').checked;
    if (tieneGarante) {
      cliente.garante = {
        nombre: formData.get('garanteNombre').trim(),
        apellido: formData.get('garanteApellido').trim(),
        cedula: formData.get('garanteCedula').trim(),
        telefono: formData.get('garanteTelefono').trim(),
        direccion: formData.get('garanteDireccion').trim()
      };
    }

    // Validar
    if (!this.validarCliente(cliente)) return;

    // Guardar
    if (this.esEdicion) {
      const index = this.clientes.findIndex(c => c.id === cliente.id);
      this.clientes[index] = cliente;
      this.mostrarMensaje('Cliente actualizado exitosamente', 'success');
    } else {
      this.clientes.push(cliente);
      this.mostrarMensaje('Cliente registrado exitosamente', 'success');
    }

    this.guardarClientes();
    this.volverALista();
  }

  validarCliente(cliente) {
    // Verificar cédula duplicada
    const existe = this.clientes.some(c => 
      c.cedula === cliente.cedula && c.id !== cliente.id
    );

    if (existe) {
      this.mostrarMensaje('Ya existe un cliente con esta cédula', 'error');
      return false;
    }

    return true;
  }

  eliminarCliente(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente && confirm(`¿Eliminar a ${cliente.nombre} ${cliente.apellido}?`)) {
      this.clientes = this.clientes.filter(c => c.id !== clienteId);
      this.guardarClientes();
      this.mostrarMensaje('Cliente eliminado exitosamente', 'success');
      this.app.loadPage('clientes');
    }
  }

  filtrarClientes(termino) {
    const filas = document.querySelectorAll('.cliente-row');
    const terminoLower = termino.toLowerCase();

    filas.forEach(fila => {
      const texto = fila.textContent.toLowerCase();
      fila.style.display = texto.includes(terminoLower) ? '' : 'none';
    });
  }

  // 🔧 Utilidades
  generarId() {
    return 'cliente_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  mostrarMensaje(mensaje, tipo = 'info') {
    console.log(`${tipo.toUpperCase()}: ${mensaje}`);
    
    // Toast visual
    const notification = document.createElement('div');
    notification.className = `notification notification-${tipo}`;
    notification.innerHTML = `
      <i class="fas fa-${tipo === 'success' ? 'check' : tipo === 'error' ? 'times' : 'info'}"></i>
      ${mensaje}
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
  }
}
