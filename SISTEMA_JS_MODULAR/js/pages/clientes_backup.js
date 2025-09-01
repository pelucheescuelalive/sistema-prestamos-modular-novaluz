/* ===================================================================
   👥 PÁGINA DE GESTIÓN DE CLIENTES
   Sistema completo de CRUD para clientes con información de garantes
   =================================================================== */

export class ClientesPage {
  constructor(app) {
    this.app = app;
    this.clientes = this.cargarClientes();
    this.clienteSeleccionado = null;
    this.modoFormulario = false;
    this.modoDetalles = false;
    console.log('👥 Módulo de Clientes inicializado');
  }

  async render() {
    console.log('🎨 Renderizando página de Clientes');
    
    if (this.modoDetalles && this.clienteSeleccionado) {
      return this.renderDetallesCliente();
    } else if (this.modoFormulario) {
      return this.renderFormulario();
    } else {
      return this.renderListaClientes();
    }
  }

  // 💾 Gestión de Datos
  cargarClientes() {
    return JSON.parse(localStorage.getItem('novaluz_clientes')) || [];
  }

  guardarClientes() {
    localStorage.setItem('novaluz_clientes', JSON.stringify(this.clientes));
  }

  // 📋 Renderizar Lista Principal
  renderListaClientes() {
    const estadisticas = this.calcularEstadisticas();
    
    return `
      <div class="clientes-container animate-fadeInUp">
        <!-- Header de Clientes -->
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
              <button class="btn btn-primary btn-lg" onclick="window.clientesPage.mostrarFormulario()">
                <i class="fas fa-user-plus"></i>
                Nuevo Cliente
              </button>
            </div>
          </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="stats-mini">
          <div class="stat-mini">
            <i class="fas fa-users"></i>
            <div class="stat-info">
              <span class="stat-number">${estadisticas.total}</span>
              <span class="stat-label">Total Clientes</span>
            </div>
          </div>
          <div class="stat-mini">
            <i class="fas fa-user-shield"></i>
            <div class="stat-info">
              <span class="stat-number">${estadisticas.conGarante}</span>
              <span class="stat-label">Con Garante</span>
            </div>
          </div>
          <div class="stat-mini">
            <i class="fas fa-venus-mars"></i>
            <div class="stat-info">
              <span class="stat-number">${estadisticas.femeninos}F / ${estadisticas.masculinos}M</span>
              <span class="stat-label">Distribución</span>
            </div>
          </div>
        </div>

        <!-- Lista de Clientes -->
        ${this.clientes.length === 0 ? this.renderEstadoVacio() : this.renderTablaClientes()}
      </div>
    `;
  }

  // 📝 Renderizar Formulario
  renderFormulario() {
    const cliente = this.clienteSeleccionado || {};
    const esEdicion = this.clienteSeleccionado !== null;
    
    return `
      <div class="clientes-container animate-fadeInUp">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-user-edit"></i>
              ${esEdicion ? 'Editar Cliente' : 'Nuevo Cliente'}
            </h2>
            <button class="btn btn-ghost" onclick="window.clientesPage.volverALista()">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="card-body">
            <form id="form-cliente" class="form-modern" onsubmit="window.clientesPage.guardarCliente(event)">
              <!-- Datos Personales -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-user"></i>
                  Información Personal
                </h3>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="${cliente.nombre || ''}" 
                           placeholder="Ingresa el nombre">
                  </div>
                  <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" required 
                           value="${cliente.apellido || ''}" 
                           placeholder="Ingresa el apellido">
                  </div>
                  <div class="form-group">
                    <label for="cedula">Cédula *</label>
                    <input type="text" id="cedula" name="cedula" required 
                           value="${cliente.cedula || ''}" 
                           placeholder="000-0000000-0">
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

              <!-- Contacto -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-phone"></i>
                  Información de Contacto
                </h3>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" 
                           value="${cliente.telefono || ''}" 
                           placeholder="809-000-0000">
                  </div>
                  <div class="form-group">
                    <label for="celular">Celular *</label>
                    <input type="tel" id="celular" name="celular" required 
                           value="${cliente.celular || ''}" 
                           placeholder="829-000-0000">
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
                    <input type="checkbox" id="tiene-garante" ${cliente.garante ? 'checked' : ''} 
                           onchange="window.clientesPage.toggleGarante(this)">
                    <span class="toggle-text">¿Tiene garante?</span>
                  </label>
                </h3>
                <div class="garante-fields" id="garante-fields" ${!cliente.garante ? 'style="display: none;"' : ''}>
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="garante-nombre">Nombre del Garante</label>
                      <input type="text" id="garante-nombre" name="garanteNombre" 
                             value="${cliente.garante?.nombre || ''}" 
                             placeholder="Nombre del garante">
                    </div>
                    <div class="form-group">
                      <label for="garante-apellido">Apellido del Garante</label>
                      <input type="text" id="garante-apellido" name="garanteApellido" 
                             value="${cliente.garante?.apellido || ''}" 
                             placeholder="Apellido del garante">
                    </div>
                    <div class="form-group">
                      <label for="garante-cedula">Cédula del Garante</label>
                      <input type="text" id="garante-cedula" name="garanteCedula" 
                             value="${cliente.garante?.cedula || ''}" 
                             placeholder="000-0000000-0">
                    </div>
                    <div class="form-group">
                      <label for="garante-telefono">Teléfono del Garante</label>
                      <input type="tel" id="garante-telefono" name="garanteTelefono" 
                             value="${cliente.garante?.telefono || ''}" 
                             placeholder="809-000-0000">
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
                  ${esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    `;
  }

  // 📋 Renderizar Tabla de Clientes
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
                ${this.clientes.map(cliente => this.renderFilaCliente(cliente)).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  // 👤 Renderizar Fila de Cliente
  renderFilaCliente(cliente) {
    return `
      <tr class="cliente-row" data-id="${cliente.id}">
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
        <td>
          <code>${cliente.cedula}</code>
        </td>
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
            <button class="btn btn-sm btn-danger" onclick="window.clientesPage.confirmarEliminar('${cliente.id}')" title="Eliminar">
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
        <button class="btn btn-primary" onclick="window.clientesPage.mostrarFormulario()">
          <i class="fas fa-user-plus"></i>
          Agregar Primer Cliente
        </button>
      </div>
    `;
  }

  // 📊 Calcular Estadísticas
  calcularEstadisticas() {
    return {
      total: this.clientes.length,
      conGarante: this.clientes.filter(c => c.garante).length,
      femeninos: this.clientes.filter(c => c.sexo === 'femenino').length,
      masculinos: this.clientes.filter(c => c.sexo === 'masculino').length
    };
  }

  // 🔧 Métodos de Interacción
  mostrarFormulario() {
    this.modoFormulario = true;
    this.modoDetalles = false;
    this.clienteSeleccionado = null;
    this.app.loadPage('clientes');
  }

  volverALista() {
    this.modoFormulario = false;
    this.modoDetalles = false;
    this.clienteSeleccionado = null;
    this.app.loadPage('clientes');
  }

  editarCliente(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.clienteSeleccionado = cliente;
      this.modoFormulario = true;
      this.modoDetalles = false;
      this.app.loadPage('clientes');
    }
  }

  verDetalles(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.clienteSeleccionado = cliente;
      this.modoDetalles = true;
      this.modoFormulario = false;
      this.app.loadPage('clientes');
    }
  }

  toggleGarante(checkbox) {
    const garanteFields = document.getElementById('garante-fields');
    garanteFields.style.display = checkbox.checked ? 'block' : 'none';
  }

  guardarCliente(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    const cliente = {
      id: this.clienteSeleccionado?.id || this.generarId(),
      nombre: formData.get('nombre').trim(),
      apellido: formData.get('apellido').trim(),
      cedula: formData.get('cedula').trim(),
      sexo: formData.get('sexo'),
      telefono: formData.get('telefono').trim(),
      celular: formData.get('celular').trim(),
      direccion: formData.get('direccion').trim(),
      fechaRegistro: this.clienteSeleccionado?.fechaRegistro || new Date().toISOString(),
      fechaActualizacion: new Date().toISOString()
    };

    // Información del garante
    if (document.getElementById('tiene-garante').checked) {
      cliente.garante = {
        nombre: formData.get('garanteNombre').trim(),
        apellido: formData.get('garanteApellido').trim(),
        cedula: formData.get('garanteCedula').trim(),
        telefono: formData.get('garanteTelefono').trim(),
        direccion: formData.get('garanteDireccion').trim()
      };
    }

    // Validar cédula duplicada
    const cedulaExiste = this.clientes.some(c => 
      c.cedula === cliente.cedula && c.id !== cliente.id
    );

    if (cedulaExiste) {
      alert('Ya existe un cliente con esta cédula');
      return;
    }

    // Guardar cliente
    if (this.clienteSeleccionado) {
      const index = this.clientes.findIndex(c => c.id === cliente.id);
      this.clientes[index] = cliente;
      this.mostrarNotificacion('Cliente actualizado exitosamente', 'success');
    } else {
      this.clientes.push(cliente);
      this.mostrarNotificacion('Cliente registrado exitosamente', 'success');
    }

    this.guardarClientes();
    this.volverALista();
  }

  confirmarEliminar(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente && confirm(`¿Estás seguro de que deseas eliminar a ${cliente.nombre} ${cliente.apellido}?\n\nEsta acción no se puede deshacer.`)) {
      this.clientes = this.clientes.filter(c => c.id !== clienteId);
      this.guardarClientes();
      this.mostrarNotificacion('Cliente eliminado exitosamente', 'success');
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

  generarId() {
    return 'cliente_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  mostrarNotificacion(mensaje, tipo = 'info') {
    console.log(`${tipo.toUpperCase()}: ${mensaje}`);
    
    // Crear notificación visual
    const notification = document.createElement('div');
    notification.className = `notification notification-${tipo}`;
    notification.innerHTML = `
      <i class="fas fa-${tipo === 'success' ? 'check' : tipo === 'error' ? 'times' : 'info'}"></i>
      ${mensaje}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.remove();
    }, 3000);
  }

  // 👁️ Renderizar Detalles del Cliente (versión simplificada)
  renderDetallesCliente() {
    const cliente = this.clienteSeleccionado;
    
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
}
