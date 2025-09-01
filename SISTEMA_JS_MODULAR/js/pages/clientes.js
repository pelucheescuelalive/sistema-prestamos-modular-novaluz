/* ===================================================================
   👥 PÁGINA DE GESTIÓN DE CLIENT  renderFormulario() {
    const cliente = this.clienteSeleccionado || {};
    const titulo = this.esEdicion ? 'Editar Cliente' : 'Nuevo Cliente';
    const botonTexto = this.esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente';
    const iconoBoton = this.esEdicion ? 'fas fa-save' : 'fas fa-plus';
    
    return `
      <div class="clientes-container animate-fadeInUp">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-user-edit"></i>
              ${titulo}
            </h2>
            <div class="header-actions">
              <button type="submit" form="form-cliente" class="btn btn-primary">
                <i class="${iconoBoton}"></i>
                ${botonTexto}
              </button>
              <button class="btn btn-ghost" onclick="window.clientesPage.volverALista()">
                <i class="fas fa-arrow-left"></i>
                Volver
              </button>
            </div>
          </div>
          <div class="card-body">
            <form id="form-cliente" onsubmit="window.clientesPage.procesarFormulario(event)">`TA FUNCIONAL
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

  // 🔍 Obtener cliente por ID
  obtenerClientePorId(id) {
    return this.clientes.find(cliente => cliente.id === id);
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
              
              <!-- 🖼️ Foto de Perfil -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-portrait"></i>
                  Foto de Perfil del Cliente
                </h3>
                <div class="perfil-photo-container">
                  <div class="perfil-preview">
                    <div id="perfil-preview-img" class="perfil-avatar ${cliente.fotoPerfil ? 'has-photo' : ''}">
                      ${cliente.fotoPerfil 
                        ? `<img src="${cliente.fotoPerfil}" alt="Foto de perfil" class="perfil-img">`
                        : `<div class="perfil-initials">${this.generarIniciales(cliente.nombre, cliente.apellido)}</div>`
                      }
                    </div>
                    <div class="perfil-actions">
                      <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('foto-perfil-input').click()">
                        <i class="fas fa-camera"></i>
                        ${cliente.fotoPerfil ? 'Cambiar Foto' : 'Subir Foto'}
                      </button>
                      ${cliente.fotoPerfil ? `
                        <button type="button" class="btn btn-danger btn-sm" onclick="window.clientesPage.eliminarFotoPerfil()">
                          <i class="fas fa-trash"></i>
                          Eliminar
                        </button>
                      ` : ''}
                    </div>
                    <input type="file" id="foto-perfil-input" accept="image/*" style="display: none" 
                           onchange="window.clientesPage.procesarFotoPerfil(event)">
                  </div>
                  
                  <!-- Editor de Foto -->
                  <div id="photo-editor" class="photo-editor" style="display: none;">
                    <div class="editor-header">
                      <h4>Ajustar Foto de Perfil</h4>
                      <button type="button" class="btn-close" onclick="window.clientesPage.cerrarEditor()">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                    <div class="editor-canvas-container">
                      <canvas id="photo-canvas" class="photo-canvas"></canvas>
                      <div class="crop-overlay"></div>
                    </div>
                    <div class="editor-controls">
                      <div class="zoom-controls">
                        <label>Zoom:</label>
                        <input type="range" id="zoom-slider" min="1" max="3" step="0.1" value="1" 
                               onchange="window.clientesPage.aplicarZoom(this.value)">
                      </div>
                      <div class="position-controls">
                        <button type="button" class="btn btn-sm" onclick="window.clientesPage.moverImagen('up')">
                          <i class="fas fa-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm" onclick="window.clientesPage.moverImagen('down')">
                          <i class="fas fa-arrow-down"></i>
                        </button>
                        <button type="button" class="btn btn-sm" onclick="window.clientesPage.moverImagen('left')">
                          <i class="fas fa-arrow-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm" onclick="window.clientesPage.moverImagen('right')">
                          <i class="fas fa-arrow-right"></i>
                        </button>
                      </div>
                      <div class="editor-actions">
                        <button type="button" class="btn btn-success" onclick="window.clientesPage.guardarFotoEditada()">
                          <i class="fas fa-check"></i>
                          Usar Esta Foto
                        </button>
                        <button type="button" class="btn btn-ghost" onclick="window.clientesPage.cerrarEditor()">
                          <i class="fas fa-times"></i>
                          Cancelar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

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

              <!-- 📸 Multimedia del Cliente -->
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fas fa-images"></i>
                  Multimedia del Cliente (Máximo 5 archivos)
                </h3>
                <div class="multimedia-container">
                  <div class="upload-area" onclick="document.getElementById('multimedia-cliente').click()">
                    <div class="upload-content">
                      <i class="fas fa-cloud-upload-alt"></i>
                      <p>Subir archivos del cliente</p>
                      <small>JPG, PNG, WebP, PDF - Máximo 2MB</small>
                    </div>
                    <input type="file" id="multimedia-cliente" multiple accept="image/*,.pdf" 
                           style="display: none" onchange="window.clientesPage.manejarArchivos(event, 'cliente')">
                  </div>
                  <div class="categoria-selector">
                    <label for="categoria-cliente">Categoría:</label>
                    <select id="categoria-cliente">
                      <option value="cedula-frente">📄 Cédula (Frente)</option>
                      <option value="cedula-reverso">📄 Cédula (Reverso)</option>
                      <option value="foto-personal">📷 Foto Personal</option>
                      <option value="vivienda">🏠 Vivienda</option>
                      <option value="negocio">🏪 Negocio</option>
                      <option value="contrato">📋 Contrato</option>
                      <option value="otros">📁 Otros</option>
                    </select>
                  </div>
                  <div id="preview-cliente" class="multimedia-preview"></div>
                </div>
              </div>

              <!-- 📸 Multimedia del Garante -->
              <div class="form-section" id="multimedia-garante-section" 
                   style="display: ${cliente.garante ? 'block' : 'none'};">
                <h3 class="section-title">
                  <i class="fas fa-images"></i>
                  Multimedia del Garante (Máximo 5 archivos)
                </h3>
                <div class="multimedia-container">
                  <div class="upload-area" onclick="document.getElementById('multimedia-garante').click()">
                    <div class="upload-content">
                      <i class="fas fa-cloud-upload-alt"></i>
                      <p>Subir archivos del garante</p>
                      <small>JPG, PNG, WebP, PDF - Máximo 2MB</small>
                    </div>
                    <input type="file" id="multimedia-garante" multiple accept="image/*,.pdf" 
                           style="display: none" onchange="window.clientesPage.manejarArchivos(event, 'garante')">
                  </div>
                  <div class="categoria-selector">
                    <label for="categoria-garante">Categoría:</label>
                    <select id="categoria-garante">
                      <option value="cedula-frente">📄 Cédula (Frente)</option>
                      <option value="cedula-reverso">📄 Cédula (Reverso)</option>
                      <option value="foto-personal">📷 Foto Personal</option>
                      <option value="vivienda">🏠 Vivienda</option>
                      <option value="negocio">🏪 Negocio</option>
                      <option value="contrato">📋 Contrato</option>
                      <option value="otros">📁 Otros</option>
                    </select>
                  </div>
                  <div id="preview-garante" class="multimedia-preview"></div>
                </div>
              </div>
              
              <!-- 🎯 Botones de Acción -->
              <div class="form-actions">
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="fas fa-save"></i>
                  ${this.esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente'}
                </button>
                <button type="button" class="btn btn-secondary btn-lg" onclick="window.clientesPage.volverALista()">
                  <i class="fas fa-times"></i>
                  Cancelar
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
          
          <!-- 📑 Pestañas de Navegación -->
          <div class="tabs-container">
            <div class="tabs-nav">
              <button class="tab-btn active" onclick="window.clientesPage.cambiarTab(event, 'info')">
                <i class="fas fa-user"></i>
                Información
              </button>
              <button class="tab-btn" onclick="window.clientesPage.cambiarTab(event, 'multimedia-cliente')">
                <i class="fas fa-images"></i>
                Multimedia Cliente
                ${cliente.multimedia?.cliente?.length ? `<span class="badge">${cliente.multimedia.cliente.length}</span>` : ''}
              </button>
              ${cliente.garante ? `
                <button class="tab-btn" onclick="window.clientesPage.cambiarTab(event, 'multimedia-garante')">
                  <i class="fas fa-user-shield"></i>
                  Multimedia Garante
                  ${cliente.multimedia?.garante?.length ? `<span class="badge">${cliente.multimedia.garante.length}</span>` : ''}
                </button>
              ` : ''}
            </div>

            <!-- 📄 Pestaña Información -->
            <div class="tab-content active" id="tab-info">
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

            <!-- 📸 Pestaña Multimedia Cliente -->
            <div class="tab-content" id="tab-multimedia-cliente">
              <div class="card-body">
                ${this.renderMultimediaSeccion(cliente, 'cliente')}
              </div>
            </div>

            <!-- 📸 Pestaña Multimedia Garante -->
            ${cliente.garante ? `
              <div class="tab-content" id="tab-multimedia-garante">
                <div class="card-body">
                  ${this.renderMultimediaSeccion(cliente, 'garante')}
                </div>
              </div>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  }

  // 📋 Grid de Tarjetas de Clientes
  renderTablaClientes() {
    return `
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-users"></i>
            Lista de Clientes (${this.clientes.length})
          </h2>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="search-clientes" placeholder="Buscar clientes..." 
                   oninput="window.clientesPage.filtrarClientes(this.value)">
          </div>
        </div>
        <div class="card-body">
          <div class="clientes-grid" id="clientes-grid">
            ${this.clientes.map(cliente => this.crearTarjetaCliente(cliente)).join('')}
          </div>
        </div>
      </div>
    `;
  }

  // 🃏 Tarjeta de Cliente
  crearTarjetaCliente(cliente) {
    return `
      <div class="cliente-card" data-cliente-id="${cliente.id}" onclick="window.clientesPage.abrirModalExpediente(window.clientesPage.obtenerClientePorId('${cliente.id}'))">
        <div class="cliente-card-header">
          <div class="cliente-avatar-card">
            ${cliente.fotoPerfil 
              ? `<img src="${cliente.fotoPerfil}" alt="${cliente.nombre}" class="avatar-img">`
              : `<div class="avatar-initials">${this.generarIniciales(cliente.nombre, cliente.apellido)}</div>`
            }
            <div class="estado-indicator ${cliente.estado || 'activo'}"></div>
          </div>
          <div class="cliente-info-card">
            <h3 class="cliente-nombre">${cliente.nombre} ${cliente.apellido}</h3>
            <p class="cliente-cedula">
              <i class="fas fa-id-card"></i>
              ${cliente.cedula}
            </p>
            <div class="cliente-badges">
              <span class="badge badge-${cliente.sexo}">${cliente.sexo === 'masculino' ? '♂' : '♀'}</span>
              ${cliente.garante ? '<span class="badge badge-garante"><i class="fas fa-user-shield"></i> Con Garante</span>' : ''}
              ${cliente.multimedia?.cliente?.length ? `<span class="badge badge-multimedia"><i class="fas fa-images"></i> ${cliente.multimedia.cliente.length}</span>` : ''}
            </div>
          </div>
        </div>
        
        <div class="cliente-card-body">
          <div class="contacto-info">
            <div class="contacto-item">
              <i class="fas fa-mobile-alt"></i>
              <span>${cliente.celular}</span>
              <a href="tel:${cliente.celular}" class="btn-quick-action" title="Llamar">
                <i class="fas fa-phone"></i>
              </a>
              <a href="https://wa.me/1${cliente.celular.replace(/\D/g, '')}" class="btn-quick-action whatsapp" title="WhatsApp" target="_blank">
                <i class="fab fa-whatsapp"></i>
              </a>
            </div>
            ${cliente.telefono ? `
              <div class="contacto-item">
                <i class="fas fa-phone"></i>
                <span>${cliente.telefono}</span>
              </div>
            ` : ''}
          </div>
          
          <div class="direccion-info">
            <i class="fas fa-map-marker-alt"></i>
            <span class="direccion-text">${cliente.direccion}</span>
          </div>
        </div>
        
        <div class="cliente-card-footer">
          <div class="fecha-registro">
            <i class="fas fa-calendar"></i>
            <span>Registrado: ${new Date(cliente.fechaRegistro).toLocaleDateString('es-DO')}</span>
          </div>
          <div class="acciones-card">
            <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); window.clientesPage.editarCliente('${cliente.id}')" title="Editar">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); window.clientesPage.eliminarCliente('${cliente.id}')" title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
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
    
    // Inicializar multimedia temporal vacía
    window.multimediaTemp = { cliente: [], garante: [] };
    
    // Inicializar foto de perfil temporal
    window.fotoPerfilTemp = null;
    
    this.recargarVista();
  }

  editarCliente(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.vista = 'formulario';
      this.clienteSeleccionado = cliente;
      this.esEdicion = true;
      
      // Inicializar multimedia temporal vacía para edición
      window.multimediaTemp = { cliente: [], garante: [] };
      
      // Inicializar foto de perfil temporal con la existente
      window.fotoPerfilTemp = cliente.fotoPerfil || null;
      
      this.recargarVista();
    }
  }

  verDetalles(clienteId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (cliente) {
      this.abrirModalExpediente(cliente);
    }
  }

  volverALista() {
    this.vista = 'lista';
    this.clienteSeleccionado = null;
    this.esEdicion = false;
    this.recargarVista();
  }

  // 🔄 Método para recargar la vista actual
  async recargarVista() {
    const mainContent = document.getElementById('main-content');
    if (mainContent) {
      const content = await this.render();
      mainContent.innerHTML = content;
      console.log(`✅ Vista '${this.vista}' recargada correctamente`);
    }
  }

  // 🎛️ Funciones de Formulario
  toggleGarante() {
    const checkbox = document.getElementById('tiene-garante');
    const fields = document.getElementById('garante-fields');
    const multimediaSection = document.getElementById('multimedia-garante-section');
    
    if (checkbox && fields) {
      const mostrar = checkbox.checked;
      fields.style.display = mostrar ? 'block' : 'none';
      if (multimediaSection) {
        multimediaSection.style.display = mostrar ? 'block' : 'none';
      }
    }
  }

  // 📸 Manejar archivos subidos en el formulario
  async manejarArchivos(event, seccion) {
    const archivos = Array.from(event.target.files);
    const categoriaSelect = document.getElementById(`categoria-${seccion}`);
    const categoria = categoriaSelect.value;
    const previewContainer = document.getElementById(`preview-${seccion}`);
    
    // 🚫 Validar límite de 5 archivos por cliente
    const archivosExistentes = previewContainer.children.length;
    const archivosNuevos = archivos.length;
    const totalArchivos = archivosExistentes + archivosNuevos;
    
    if (totalArchivos > 5) {
      this.mostrarMensaje(`❌ Máximo 5 archivos por cliente. Actualmente tienes ${archivosExistentes}, intentas agregar ${archivosNuevos}`, 'error');
      event.target.value = ''; // Limpiar input
      return;
    }

    for (const archivo of archivos) {
      // Validar tamaño
      if (archivo.size > 2 * 1024 * 1024) {
        this.mostrarMensaje(`${archivo.name}: Archivo muy grande (máximo 2MB)`, 'error');
        continue;
      }

      // Validar tipo
      const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
      if (!tiposPermitidos.includes(archivo.type)) {
        this.mostrarMensaje(`${archivo.name}: Tipo de archivo no permitido`, 'error');
        continue;
      }

      try {
        // Convertir a Base64
        const base64 = await this.convertirABase64(archivo);
        
        // Comprimir si es imagen
        const base64Final = archivo.type.startsWith('image/') 
          ? await this.comprimirImagen(base64, 0.8) 
          : base64;

        // Crear preview
        const preview = this.crearPreviewArchivo(archivo, categoria, base64Final, seccion);
        previewContainer.appendChild(preview);

        // Agregar a array temporal (se guardará al enviar formulario)
        if (!window.multimediaTemp) window.multimediaTemp = { cliente: [], garante: [] };
        
        window.multimediaTemp[seccion].push({
          id: this.generarId(),
          nombre: archivo.name,
          tipo: archivo.type,
          categoria: categoria,
          tamaño: archivo.size,
          fechaSubida: new Date().toISOString(),
          datos: base64Final
        });

        this.mostrarMensaje(`${archivo.name} listo para guardar`, 'success');

      } catch (error) {
        console.error('Error procesando archivo:', error);
        this.mostrarMensaje(`Error con ${archivo.name}`, 'error');
      }
    }

    // Limpiar input
    event.target.value = '';
  }

  // Crear preview visual del archivo
  crearPreviewArchivo(archivo, categoria, base64, seccion) {
    const div = document.createElement('div');
    div.className = 'archivo-preview';
    
    const categoriaInfo = this.obtenerCategorias().find(c => c.value === categoria);
    const icono = categoriaInfo ? categoriaInfo.icon : 'fas fa-file';
    
    div.innerHTML = `
      <div class="archivo-info">
        <div class="archivo-icono">
          ${archivo.type.startsWith('image/') 
            ? `<img src="${base64}" alt="${archivo.name}" class="preview-img">` 
            : `<i class="${icono}"></i>`}
        </div>
        <div class="archivo-detalles">
          <div class="archivo-nombre">${archivo.name}</div>
          <div class="archivo-meta">
            <span class="categoria">${categoriaInfo?.label || categoria}</span>
            <span class="tamaño">${this.formatearTamaño(archivo.size)}</span>
          </div>
        </div>
        <button type="button" class="btn-eliminar-preview" 
                onclick="this.parentElement.parentElement.remove()">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `;
    
    return div;
  }

  // Formatear tamaño de archivo
  formatearTamaño(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
  }

  // 📑 Cambiar pestaña en vista de detalles
  cambiarTab(event, tabId) {
    // Remover clase active de todos los botones y contenidos
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Agregar clase active al botón clickeado y su contenido
    event.target.classList.add('active');
    document.getElementById(`tab-${tabId}`).classList.add('active');
  }

  // 📸 Renderizar sección de multimedia
  renderMultimediaSeccion(cliente, seccion) {
    const multimedia = cliente.multimedia?.[seccion] || [];
    const titulo = seccion === 'cliente' ? 'del Cliente' : 'del Garante';
    
    if (multimedia.length === 0) {
      return `
        <div class="multimedia-empty">
          <div class="empty-state">
            <i class="fas fa-images"></i>
            <h3>Sin archivos multimedia</h3>
            <p>No hay archivos ${titulo.toLowerCase()} guardados</p>
            <button class="btn btn-primary" onclick="window.clientesPage.editarCliente('${cliente.id}')">
              <i class="fas fa-plus"></i>
              Agregar Archivos
            </button>
          </div>
        </div>
      `;
    }

    return `
      <div class="multimedia-galeria">
        <div class="galeria-header">
          <h3>
            <i class="fas fa-images"></i>
            Archivos Multimedia ${titulo}
            <span class="contador">(${multimedia.length}/5)</span>
          </h3>
          <button class="btn btn-primary" onclick="window.clientesPage.editarCliente('${cliente.id}')">
            <i class="fas fa-plus"></i>
            Agregar Más
          </button>
        </div>
        
        <div class="archivos-grid">
          ${multimedia.map(archivo => this.renderArchivoMultimedia(archivo, cliente.id, seccion)).join('')}
        </div>
      </div>
    `;
  }

  // 📄 Renderizar archivo individual de multimedia
  renderArchivoMultimedia(archivo, clienteId, seccion) {
    const categoriaInfo = this.obtenerCategorias().find(c => c.value === archivo.categoria);
    const icono = categoriaInfo ? categoriaInfo.icon : 'fas fa-file';
    const fechaFormateada = new Date(archivo.fechaSubida).toLocaleDateString('es-DO');
    
    return `
      <div class="archivo-card">
        <div class="archivo-preview-container">
          ${archivo.tipo.startsWith('image/') 
            ? `<img src="${archivo.datos}" alt="${archivo.nombre}" class="archivo-preview-img" 
                   onclick="window.clientesPage.abrirVisorImagen('${archivo.datos}', '${archivo.nombre}')">`
            : `<div class="archivo-preview-icon">
                 <i class="${icono}"></i>
               </div>`}
          <div class="archivo-overlay">
            <button class="btn-accion btn-ver" 
                    onclick="window.clientesPage.abrirVisorImagen('${archivo.datos}', '${archivo.nombre}')">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn-accion btn-eliminar" 
                    onclick="window.clientesPage.confirmarEliminarMultimedia('${clienteId}', '${seccion}', '${archivo.id}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        
        <div class="archivo-info-card">
          <div class="archivo-titulo">${archivo.nombre}</div>
          <div class="archivo-categoria">
            <i class="${icono}"></i>
            ${categoriaInfo?.label || archivo.categoria}
          </div>
          <div class="archivo-meta-info">
            <span class="archivo-fecha">${fechaFormateada}</span>
            <span class="archivo-tamaño">${this.formatearTamaño(archivo.tamaño)}</span>
          </div>
        </div>
      </div>
    `;
  }

  // 🖼️ Abrir visor de imagen
  abrirVisorImagen(datosBase64, nombre) {
    const modal = document.createElement('div');
    modal.className = 'modal-visor-imagen';
    modal.innerHTML = `
      <div class="visor-overlay" onclick="this.parentElement.remove()">
        <div class="visor-contenido" onclick="event.stopPropagation()">
          <div class="visor-header">
            <h3>${nombre}</h3>
            <button class="btn-cerrar" onclick="this.closest('.modal-visor-imagen').remove()">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="visor-imagen">
            <img src="${datosBase64}" alt="${nombre}">
          </div>
          <div class="visor-acciones">
            <a href="${datosBase64}" download="${nombre}" class="btn btn-primary">
              <i class="fas fa-download"></i>
              Descargar
            </a>
          </div>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
  }

  // 🗑️ Confirmar eliminación de multimedia
  confirmarEliminarMultimedia(clienteId, seccion, multimediaId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    const archivo = cliente.multimedia[seccion].find(m => m.id === multimediaId);
    
    if (confirm(`¿Eliminar "${archivo.nombre}"?`)) {
      this.eliminarMultimedia(clienteId, seccion, multimediaId);
      this.recargarVista(); // Recargar para actualizar la vista
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
      fechaActualizacion: new Date().toISOString(),
      // 📸 Sistema de Multimedia
      multimedia: this.esEdicion ? this.clienteSeleccionado.multimedia || { cliente: [], garante: [] } : { cliente: [], garante: [] },
      // 🖼️ Foto de Perfil
      fotoPerfil: this.esEdicion ? this.clienteSeleccionado.fotoPerfil || null : null
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

    // 📸 Agregar multimedia temporal al cliente
    if (window.multimediaTemp) {
      if (window.multimediaTemp.cliente.length > 0) {
        cliente.multimedia.cliente.push(...window.multimediaTemp.cliente);
      }
      if (window.multimediaTemp.garante.length > 0) {
        cliente.multimedia.garante.push(...window.multimediaTemp.garante);
      }
      // Limpiar multimedia temporal
      window.multimediaTemp = { cliente: [], garante: [] };
    }

    // 🖼️ Agregar foto de perfil temporal
    if (window.fotoPerfilTemp) {
      cliente.fotoPerfil = window.fotoPerfilTemp;
      window.fotoPerfilTemp = null;
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
      this.recargarVista();
    }
  }

  filtrarClientes(termino) {
    const tarjetas = document.querySelectorAll('.cliente-card');
    const terminoLower = termino.toLowerCase();

    tarjetas.forEach(tarjeta => {
      const texto = tarjeta.textContent.toLowerCase();
      tarjeta.style.display = texto.includes(terminoLower) ? '' : 'none';
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

  // 📸 ============ SISTEMA DE MULTIMEDIA ============

  // Agregar archivo multimedia
  async agregarMultimedia(clienteId, seccion, archivo, categoria = 'documento') {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (!cliente) return false;

    // Inicializar multimedia si no existe
    if (!cliente.multimedia) {
      cliente.multimedia = { cliente: [], garante: [] };
    }

    // Validar límite de archivos (máximo 5 por sección)
    if (cliente.multimedia[seccion].length >= 5) {
      this.mostrarMensaje(`Máximo 5 archivos por ${seccion}`, 'error');
      return false;
    }

    // Validar tamaño (máximo 2MB)
    if (archivo.size > 2 * 1024 * 1024) {
      this.mostrarMensaje('El archivo no puede superar 2MB', 'error');
      return false;
    }

    // Validar tipo de archivo
    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
    if (!tiposPermitidos.includes(archivo.type)) {
      this.mostrarMensaje('Solo se permiten imágenes (JPG, PNG, WebP) y PDF', 'error');
      return false;
    }

    try {
      // Convertir a Base64
      const base64 = await this.convertirABase64(archivo);
      
      // Comprimir imagen si es necesario
      const base64Comprimido = archivo.type.startsWith('image/') 
        ? await this.comprimirImagen(base64, 0.8) 
        : base64;

      // Crear objeto multimedia
      const multimedia = {
        id: this.generarId(),
        nombre: archivo.name,
        tipo: archivo.type,
        categoria: categoria,
        tamaño: archivo.size,
        fechaSubida: new Date().toISOString(),
        datos: base64Comprimido
      };

      // Agregar al cliente
      cliente.multimedia[seccion].push(multimedia);
      this.guardarClientes();
      
      this.mostrarMensaje(`Archivo "${archivo.name}" agregado exitosamente`, 'success');
      return true;

    } catch (error) {
      console.error('Error al procesar archivo:', error);
      this.mostrarMensaje('Error al procesar el archivo', 'error');
      return false;
    }
  }

  // Eliminar archivo multimedia
  eliminarMultimedia(clienteId, seccion, multimediaId) {
    const cliente = this.clientes.find(c => c.id === clienteId);
    if (!cliente || !cliente.multimedia) return false;

    const index = cliente.multimedia[seccion].findIndex(m => m.id === multimediaId);
    if (index === -1) return false;

    const archivo = cliente.multimedia[seccion][index];
    if (confirm(`¿Eliminar "${archivo.nombre}"?`)) {
      cliente.multimedia[seccion].splice(index, 1);
      this.guardarClientes();
      this.mostrarMensaje('Archivo eliminado', 'success');
      return true;
    }
    return false;
  }

  // Convertir archivo a Base64
  convertirABase64(archivo) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.onerror = reject;
      reader.readAsDataURL(archivo);
    });
  }

  // Comprimir imagen
  async comprimirImagen(base64, calidad = 0.8) {
    return new Promise((resolve) => {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();

      img.onload = () => {
        // Calcular dimensiones (máximo 1200px)
        const maxWidth = 1200;
        const maxHeight = 1200;
        let { width, height } = img;

        if (width > height) {
          if (width > maxWidth) {
            height = (height * maxWidth) / width;
            width = maxWidth;
          }
        } else {
          if (height > maxHeight) {
            width = (width * maxHeight) / height;
            height = maxHeight;
          }
        }

        canvas.width = width;
        canvas.height = height;

        // Dibujar y comprimir
        ctx.drawImage(img, 0, 0, width, height);
        const resultado = canvas.toDataURL('image/jpeg', calidad);
        resolve(resultado);
      };

      img.src = base64;
    });
  }

  // Obtener categorías de archivos
  obtenerCategorias() {
    return [
      { value: 'cedula-frente', label: 'Cédula (Frente)', icon: 'fas fa-id-card' },
      { value: 'cedula-reverso', label: 'Cédula (Reverso)', icon: 'fas fa-id-card' },
      { value: 'contrato', label: 'Contrato', icon: 'fas fa-file-contract' },
      { value: 'foto-personal', label: 'Foto Personal', icon: 'fas fa-portrait' },
      { value: 'vivienda', label: 'Vivienda', icon: 'fas fa-home' },
      { value: 'negocio', label: 'Negocio', icon: 'fas fa-store' },
      { value: 'otros', label: 'Otros', icon: 'fas fa-file' }
    ];
  }

  // 🖼️ ============ SISTEMA DE FOTO DE PERFIL ============

  // Generar iniciales del nombre
  generarIniciales(nombre = '', apellido = '') {
    const inicial1 = nombre.charAt(0).toUpperCase() || '';
    const inicial2 = apellido.charAt(0).toUpperCase() || '';
    return inicial1 + inicial2 || '?';
  }

  // Variables para el editor de fotos
  photoEditorState = {
    originalImage: null,
    canvas: null,
    ctx: null,
    zoom: 1,
    offsetX: 0,
    offsetY: 0,
    isDragging: false
  };

  // Procesar archivo de foto de perfil
  async procesarFotoPerfil(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    // Validar que sea imagen
    if (!archivo.type.startsWith('image/')) {
      this.mostrarMensaje('Solo se permiten archivos de imagen', 'error');
      return;
    }

    // Validar tamaño (máximo 5MB para foto de perfil)
    if (archivo.size > 5 * 1024 * 1024) {
      this.mostrarMensaje('La imagen no puede superar 5MB', 'error');
      return;
    }

    try {
      // Convertir a Base64
      const base64 = await this.convertirABase64(archivo);
      
      // Abrir editor
      this.abrirEditorFoto(base64);
      
    } catch (error) {
      console.error('Error al procesar foto:', error);
      this.mostrarMensaje('Error al procesar la imagen', 'error');
    }
  }

  // Abrir editor de foto
  abrirEditorFoto(imagenBase64) {
    const editor = document.getElementById('photo-editor');
    const canvas = document.getElementById('photo-canvas');
    const ctx = canvas.getContext('2d');
    
    // Configurar canvas
    canvas.width = 300;
    canvas.height = 300;
    
    // Crear imagen
    const img = new Image();
    img.onload = () => {
      // Resetear estado
      this.photoEditorState = {
        originalImage: img,
        canvas: canvas,
        ctx: ctx,
        zoom: 1,
        offsetX: 0,
        offsetY: 0,
        isDragging: false
      };
      
      // Dibujar imagen inicial
      this.dibujarImagenEnCanvas();
      
      // Mostrar editor
      editor.style.display = 'block';
      
      // Agregar eventos de arrastre
      this.agregarEventosEditor();
    };
    
    img.src = imagenBase64;
  }

  // Dibujar imagen en canvas
  dibujarImagenEnCanvas() {
    const { canvas, ctx, originalImage, zoom, offsetX, offsetY } = this.photoEditorState;
    
    // Limpiar canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Calcular dimensiones
    const imgAspect = originalImage.width / originalImage.height;
    const canvasAspect = canvas.width / canvas.height;
    
    let drawWidth, drawHeight;
    
    if (imgAspect > canvasAspect) {
      // Imagen más ancha - ajustar por altura
      drawHeight = canvas.height * zoom;
      drawWidth = drawHeight * imgAspect;
    } else {
      // Imagen más alta - ajustar por ancho
      drawWidth = canvas.width * zoom;
      drawHeight = drawWidth / imgAspect;
    }
    
    // Centrar imagen con offset
    const x = (canvas.width - drawWidth) / 2 + offsetX;
    const y = (canvas.height - drawHeight) / 2 + offsetY;
    
    // Dibujar imagen
    ctx.drawImage(originalImage, x, y, drawWidth, drawHeight);
    
    // Dibujar círculo de recorte
    this.dibujarCirculoRecorte();
  }

  // Dibujar círculo de recorte
  dibujarCirculoRecorte() {
    const { canvas, ctx } = this.photoEditorState;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(canvas.width, canvas.height) / 2 - 20;
    
    // Crear máscara
    ctx.save();
    ctx.globalCompositeOperation = 'destination-in';
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
    ctx.fill();
    ctx.restore();
    
    // Dibujar borde del círculo
    ctx.strokeStyle = '#3b82f6';
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
    ctx.stroke();
  }

  // Agregar eventos del editor
  agregarEventosEditor() {
    const canvas = this.photoEditorState.canvas;
    
    // Mouse events
    canvas.addEventListener('mousedown', (e) => this.iniciarArrastre(e));
    canvas.addEventListener('mousemove', (e) => this.moverArrastre(e));
    canvas.addEventListener('mouseup', () => this.terminarArrastre());
    canvas.addEventListener('mouseleave', () => this.terminarArrastre());
    
    // Touch events para móvil
    canvas.addEventListener('touchstart', (e) => this.iniciarArrastre(e.touches[0]));
    canvas.addEventListener('touchmove', (e) => {
      e.preventDefault();
      this.moverArrastre(e.touches[0]);
    });
    canvas.addEventListener('touchend', () => this.terminarArrastre());
  }

  // Manejar arrastre
  iniciarArrastre(e) {
    this.photoEditorState.isDragging = true;
    const rect = this.photoEditorState.canvas.getBoundingClientRect();
    this.photoEditorState.lastX = e.clientX - rect.left;
    this.photoEditorState.lastY = e.clientY - rect.top;
  }

  moverArrastre(e) {
    if (!this.photoEditorState.isDragging) return;
    
    const rect = this.photoEditorState.canvas.getBoundingClientRect();
    const currentX = e.clientX - rect.left;
    const currentY = e.clientY - rect.top;
    
    const deltaX = currentX - this.photoEditorState.lastX;
    const deltaY = currentY - this.photoEditorState.lastY;
    
    this.photoEditorState.offsetX += deltaX;
    this.photoEditorState.offsetY += deltaY;
    
    this.photoEditorState.lastX = currentX;
    this.photoEditorState.lastY = currentY;
    
    this.dibujarImagenEnCanvas();
  }

  terminarArrastre() {
    this.photoEditorState.isDragging = false;
  }

  // Aplicar zoom
  aplicarZoom(valor) {
    this.photoEditorState.zoom = parseFloat(valor);
    this.dibujarImagenEnCanvas();
  }

  // Mover imagen con botones
  moverImagen(direccion) {
    const movimiento = 10;
    
    switch (direccion) {
      case 'up':
        this.photoEditorState.offsetY += movimiento;
        break;
      case 'down':
        this.photoEditorState.offsetY -= movimiento;
        break;
      case 'left':
        this.photoEditorState.offsetX += movimiento;
        break;
      case 'right':
        this.photoEditorState.offsetX -= movimiento;
        break;
    }
    
    this.dibujarImagenEnCanvas();
  }

  // Guardar foto editada
  guardarFotoEditada() {
    const canvas = this.photoEditorState.canvas;
    
    // Crear canvas circular final
    const finalCanvas = document.createElement('canvas');
    finalCanvas.width = 200;
    finalCanvas.height = 200;
    const finalCtx = finalCanvas.getContext('2d');
    
    // Crear círculo de recorte
    finalCtx.beginPath();
    finalCtx.arc(100, 100, 100, 0, 2 * Math.PI);
    finalCtx.clip();
    
    // Redimensionar y dibujar imagen final
    finalCtx.drawImage(canvas, 0, 0, 200, 200);
    
    // Convertir a Base64
    const fotoPerfilBase64 = finalCanvas.toDataURL('image/jpeg', 0.8);
    
    // Actualizar preview
    this.actualizarPreviewPerfil(fotoPerfilBase64);
    
    // Cerrar editor
    this.cerrarEditor();
    
    this.mostrarMensaje('Foto de perfil actualizada', 'success');
  }

  // Actualizar preview de perfil
  actualizarPreviewPerfil(fotoBase64) {
    const preview = document.getElementById('perfil-preview-img');
    preview.className = 'perfil-avatar has-photo';
    preview.innerHTML = `<img src="${fotoBase64}" alt="Foto de perfil" class="perfil-img">`;
    
    // Guardar en variable temporal
    window.fotoPerfilTemp = fotoBase64;
  }

  // Eliminar foto de perfil
  eliminarFotoPerfil() {
    if (confirm('¿Eliminar foto de perfil?')) {
      const preview = document.getElementById('perfil-preview-img');
      preview.className = 'perfil-avatar';
      
      // Mostrar iniciales
      const nombre = document.getElementById('nombre').value || '';
      const apellido = document.getElementById('apellido').value || '';
      preview.innerHTML = `<div class="perfil-initials">${this.generarIniciales(nombre, apellido)}</div>`;
      
      // Limpiar variable temporal
      window.fotoPerfilTemp = null;
      
      this.mostrarMensaje('Foto de perfil eliminada', 'success');
    }
  }

  // Cerrar editor
  cerrarEditor() {
    const editor = document.getElementById('photo-editor');
    editor.style.display = 'none';
    
    // Limpiar input
    document.getElementById('foto-perfil-input').value = '';
  }

  // 🔍 ============ MODAL DE EXPEDIENTE ============

  // Abrir modal de expediente
  abrirModalExpediente(cliente) {
    // Crear modal
    const modal = document.createElement('div');
    modal.className = 'modal-expediente';
    modal.innerHTML = this.renderModalExpediente(cliente);
    
    // Agregar al DOM
    document.body.appendChild(modal);
    
    // Animar entrada
    setTimeout(() => modal.classList.add('active'), 10);
    
    // Agregar listener para cerrar con ESC
    const handleEscape = (e) => {
      if (e.key === 'Escape') {
        this.cerrarModalExpediente();
      }
    };
    
    document.addEventListener('keydown', handleEscape);
    modal.escapeHandler = handleEscape;
  }

  // Cerrar modal de expediente
  cerrarModalExpediente() {
    const modal = document.querySelector('.modal-expediente');
    if (modal) {
      // Remover listener de ESC
      if (modal.escapeHandler) {
        document.removeEventListener('keydown', modal.escapeHandler);
      }
      
      // Animar salida
      modal.classList.remove('active');
      
      // Remover del DOM después de la animación
      setTimeout(() => {
        modal.remove();
      }, 300);
    }
  }

  // Renderizar modal de expediente
  renderModalExpediente(cliente) {
    return `
      <div class="modal-overlay" onclick="cerrarModalExpediente()">
        <div class="modal-content" onclick="event.stopPropagation()">
          <div class="modal-header">
            <div class="cliente-header-info">
              <div class="cliente-avatar-modal">
                ${cliente.fotoPerfil 
                  ? `<img src="${cliente.fotoPerfil}" alt="${cliente.nombre}" class="avatar-modal-img">`
                  : `<div class="avatar-modal-initials">${this.generarIniciales(cliente.nombre, cliente.apellido)}</div>`
                }
              </div>
              <div class="cliente-title-info">
                <h2 class="cliente-modal-nombre">${cliente.nombre} ${cliente.apellido}</h2>
                <div class="cliente-modal-badges">
                  <span class="modal-badge badge-${cliente.sexo}">${cliente.sexo === 'masculino' ? '♂ Masculino' : '♀ Femenino'}</span>
                  ${cliente.garante ? '<span class="modal-badge badge-garante">Con Garante</span>' : ''}
                </div>
              </div>
            </div>
            <div class="modal-actions">
              <button class="btn btn-warning" onclick="window.clientesPage.editarCliente('${cliente.id}'); window.clientesPage.cerrarModalExpediente();">
                <i class="fas fa-edit"></i>
                Editar
              </button>
              <button class="btn btn-ghost" onclick="cerrarModalExpediente()">
                <i class="fas fa-times"></i>
                Cerrar
              </button>
            </div>
          </div>

          <!-- Pestañas del modal -->
          <div class="modal-tabs">
            <div class="modal-tabs-nav">
              <button class="modal-tab-btn active" onclick="cambiarTabModal(event, 'datos')">
                <i class="fas fa-user"></i>
                DATOS
              </button>
              <button class="modal-tab-btn" onclick="cambiarTabModal(event, 'garante')">
                <i class="fas fa-user-shield"></i>
                GARANTE
              </button>
              <button class="modal-tab-btn" onclick="cambiarTabModal(event, 'prestamos')">
                <i class="fas fa-money-bill-wave"></i>
                PRÉSTAMOS
              </button>
              <button class="modal-tab-btn" onclick="cambiarTabModal(event, 'multimedia')">
                <i class="fas fa-images"></i>
                MULTIMEDIA
                ${cliente.multimedia?.cliente?.length || cliente.multimedia?.garante?.length ? 
                  `<span class="tab-counter">${(cliente.multimedia?.cliente?.length || 0) + (cliente.multimedia?.garante?.length || 0)}</span>` 
                  : ''}
              </button>
            </div>

            <!-- Contenido de pestañas -->
            <div class="modal-tabs-content">
              <!-- Pestaña DATOS -->
              <div class="modal-tab-content active" id="modal-tab-datos">
                ${this.renderTabDatos(cliente)}
              </div>

              <!-- Pestaña GARANTE -->
              <div class="modal-tab-content" id="modal-tab-garante">
                ${this.renderTabGarante(cliente)}
              </div>

              <!-- Pestaña PRÉSTAMOS -->
              <div class="modal-tab-content" id="modal-tab-prestamos">
                ${this.renderTabPrestamos(cliente)}
              </div>

              <!-- Pestaña MULTIMEDIA -->
              <div class="modal-tab-content" id="modal-tab-multimedia">
                ${this.renderTabMultimediaModal(cliente)}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Renderizar pestaña de datos
  renderTabDatos(cliente) {
    return `
      <div class="datos-grid">
        <div class="dato-item">
          <i class="fas fa-id-card"></i>
          <div class="dato-info">
            <span class="dato-label">Cédula de Identidad</span>
            <span class="dato-value">${cliente.cedula}</span>
          </div>
        </div>

        <div class="dato-item">
          <i class="fas fa-venus-mars"></i>
          <div class="dato-info">
            <span class="dato-label">Género</span>
            <span class="dato-value">${cliente.sexo === 'masculino' ? 'Masculino' : 'Femenino'}</span>
          </div>
        </div>

        <div class="dato-item">
          <i class="fas fa-mobile-alt"></i>
          <div class="dato-info">
            <span class="dato-label">Celular</span>
            <span class="dato-value">${cliente.celular}</span>
          </div>
          <div class="dato-actions">
            <a href="tel:${cliente.celular}" class="btn-action" title="Llamar">
              <i class="fas fa-phone"></i>
            </a>
            <a href="https://wa.me/1${cliente.celular.replace(/\D/g, '')}" class="btn-action whatsapp" title="WhatsApp" target="_blank">
              <i class="fab fa-whatsapp"></i>
            </a>
          </div>
        </div>

        ${cliente.telefono ? `
          <div class="dato-item">
            <i class="fas fa-phone"></i>
            <div class="dato-info">
              <span class="dato-label">Teléfono</span>
              <span class="dato-value">${cliente.telefono}</span>
            </div>
            <div class="dato-actions">
              <a href="tel:${cliente.telefono}" class="btn-action" title="Llamar">
                <i class="fas fa-phone"></i>
              </a>
            </div>
          </div>
        ` : ''}

        <div class="dato-item direccion-item">
          <i class="fas fa-map-marker-alt"></i>
          <div class="dato-info">
            <span class="dato-label">Dirección</span>
            <span class="dato-value">${cliente.direccion}</span>
          </div>
        </div>

        <div class="dato-item">
          <i class="fas fa-calendar-plus"></i>
          <div class="dato-info">
            <span class="dato-label">Fecha de Registro</span>
            <span class="dato-value">${new Date(cliente.fechaRegistro).toLocaleDateString('es-DO', {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            })}</span>
          </div>
        </div>

        ${cliente.fechaActualizacion !== cliente.fechaRegistro ? `
          <div class="dato-item">
            <i class="fas fa-calendar-check"></i>
            <div class="dato-info">
              <span class="dato-label">Última Actualización</span>
              <span class="dato-value">${new Date(cliente.fechaActualizacion).toLocaleDateString('es-DO')}</span>
            </div>
          </div>
        ` : ''}
      </div>
    `;
  }

  // Renderizar pestaña de garante
  renderTabGarante(cliente) {
    if (!cliente.garante) {
      return `
        <div class="empty-tab">
          <i class="fas fa-user-shield"></i>
          <h3>Sin Garante</h3>
          <p>Este cliente no tiene información de garante registrada</p>
          <button class="btn btn-primary" onclick="window.clientesPage.editarCliente('${cliente.id}'); this.closest('.modal-expediente').remove();">
            <i class="fas fa-plus"></i>
            Agregar Garante
          </button>
        </div>
      `;
    }

    return `
      <div class="garante-info">
        <div class="garante-header">
          <div class="garante-avatar">
            <i class="fas fa-user-shield"></i>
          </div>
          <div class="garante-title">
            <h3>${cliente.garante.nombre} ${cliente.garante.apellido}</h3>
            <span class="garante-role">Garante del Cliente</span>
          </div>
        </div>

        <div class="datos-grid">
          <div class="dato-item">
            <i class="fas fa-id-card"></i>
            <div class="dato-info">
              <span class="dato-label">Cédula</span>
              <span class="dato-value">${cliente.garante.cedula}</span>
            </div>
          </div>

          <div class="dato-item">
            <i class="fas fa-phone"></i>
            <div class="dato-info">
              <span class="dato-label">Teléfono</span>
              <span class="dato-value">${cliente.garante.telefono || 'No especificado'}</span>
            </div>
            ${cliente.garante.telefono ? `
              <div class="dato-actions">
                <a href="tel:${cliente.garante.telefono}" class="btn-action" title="Llamar">
                  <i class="fas fa-phone"></i>
                </a>
              </div>
            ` : ''}
          </div>

          <div class="dato-item direccion-item">
            <i class="fas fa-map-marker-alt"></i>
            <div class="dato-info">
              <span class="dato-label">Dirección</span>
              <span class="dato-value">${cliente.garante.direccion || 'No especificada'}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Renderizar pestaña de préstamos
  renderTabPrestamos(cliente) {
    // TODO: Conectar con módulo de préstamos cuando esté disponible
    return `
      <div class="empty-tab">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Préstamos</h3>
        <p>Información de préstamos del cliente</p>
        <small class="text-muted">Funcionalidad en desarrollo - Se conectará con el módulo de préstamos</small>
      </div>
    `;
  }

  // Renderizar pestaña de multimedia en modal
  renderTabMultimediaModal(cliente) {
    const multimediaCliente = cliente.multimedia?.cliente || [];
    const multimediaGarante = cliente.multimedia?.garante || [];
    
    if (multimediaCliente.length === 0 && multimediaGarante.length === 0) {
      return `
        <div class="empty-tab">
          <i class="fas fa-images"></i>
          <h3>Sin Archivos Multimedia</h3>
          <p>No hay archivos multimedia registrados</p>
          <button class="btn btn-primary" onclick="window.clientesPage.editarCliente('${cliente.id}'); this.closest('.modal-expediente').remove();">
            <i class="fas fa-plus"></i>
            Agregar Archivos
          </button>
        </div>
      `;
    }

    return `
      <div class="multimedia-modal">
        ${multimediaCliente.length > 0 ? `
          <div class="multimedia-section">
            <h4><i class="fas fa-user"></i> Archivos del Cliente (${multimediaCliente.length})</h4>
            <div class="multimedia-grid-modal">
              ${multimediaCliente.map(archivo => this.renderArchivoModal(archivo)).join('')}
            </div>
          </div>
        ` : ''}

        ${multimediaGarante.length > 0 ? `
          <div class="multimedia-section">
            <h4><i class="fas fa-user-shield"></i> Archivos del Garante (${multimediaGarante.length})</h4>
            <div class="multimedia-grid-modal">
              ${multimediaGarante.map(archivo => this.renderArchivoModal(archivo)).join('')}
            </div>
          </div>
        ` : ''}
      </div>
    `;
  }

  // Renderizar archivo en modal
  renderArchivoModal(archivo) {
    const categoriaInfo = this.obtenerCategorias().find(c => c.value === archivo.categoria);
    const icono = categoriaInfo ? categoriaInfo.icon : 'fas fa-file';
    
    return `
      <div class="archivo-modal" onclick="window.clientesPage.abrirVisorImagen('${archivo.datos}', '${archivo.nombre}')">
        <div class="archivo-thumb">
          ${archivo.tipo.startsWith('image/') 
            ? `<img src="${archivo.datos}" alt="${archivo.nombre}" class="thumb-img">`
            : `<i class="${icono}"></i>`}
        </div>
        <div class="archivo-info-modal">
          <div class="archivo-nombre-modal">${archivo.nombre}</div>
          <div class="archivo-categoria-modal">${categoriaInfo?.label || archivo.categoria}</div>
        </div>
      </div>
    `;
  }

  // Cambiar pestaña en modal
  cambiarTabModal(event, tabId) {
    // Remover active de todos los botones y contenidos
    const modal = event.target.closest('.modal-expediente');
    modal.querySelectorAll('.modal-tab-btn').forEach(btn => btn.classList.remove('active'));
    modal.querySelectorAll('.modal-tab-content').forEach(content => content.classList.remove('active'));
    
    // Agregar active al clickeado
    event.target.classList.add('active');
    modal.querySelector(`#modal-tab-${tabId}`).classList.add('active');
  }
}

// Función global para cerrar modal
window.cerrarModalExpediente = function() {
  if (window.clientesPage) {
    window.clientesPage.cerrarModalExpediente();
  }
};

// Función global para cambiar pestañas
window.cambiarTabModal = function(event, tabId) {
  if (window.clientesPage) {
    window.clientesPage.cambiarTabModal(event, tabId);
  }
};
