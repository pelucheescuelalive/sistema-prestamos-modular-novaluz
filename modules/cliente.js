/**
 * MÓDULO CLIENTE - FUNCIONES FRONTEND
 * Sistema Modular de Préstamos Nova Luz
 */

// Variables globales del módulo cliente
let clientes = [];

/**
 * Cargar datos de clientes desde la API
 */
async function cargarClientes() {
    try {
        console.log('🔄 Cargando clientes...');
        const response = await fetch('api_simple.php?action=cliente_listar');
        const resultado = await response.json();
        
        console.log('📡 Respuesta API clientes:', resultado);
        
        if (resultado.success) {
            clientes = resultado.data || [];
            console.log('✅ Clientes cargados:', clientes);
            
            // Si no hay clientes, crear datos de prueba
            if (clientes.length === 0) {
                console.log('📝 No hay clientes, creando datos de prueba...');
                clientes = crearClientesPrueba();
            }
            
            mostrarClientesEnGaleria(clientes);
            actualizarContadorClientes();
            return clientes;
        } else {
            console.error('❌ Error cargando clientes:', resultado.error);
            console.log('📝 Creando datos de prueba...');
            clientes = crearClientesPrueba();
            mostrarClientesEnGaleria(clientes);
        }
    } catch (error) {
        console.error('❌ Error cargando clientes:', error);
        console.log('📝 Creando datos de prueba por error...');
        clientes = crearClientesPrueba();
        mostrarClientesEnGaleria(clientes);
    }
}

/**
 * Crear clientes de prueba para testing
 */
function crearClientesPrueba() {
    return [
        {
            id: 'CLI001',
            cliente_id: 'CLI001',
            nombre: 'María Elena Rodríguez',
            telefono: '809-555-1234',
            documento: '001-0123456-7',
            email: 'maria.rodriguez@email.com',
            direccion: 'Calle Principal #123, Santo Domingo',
            calificacion: 5.0,
            activo: true,
            foto_perfil: null
        },
        {
            id: 'CLI002',
            cliente_id: 'CLI002',
            nombre: 'Juan Carlos Méndez',
            telefono: '809-555-5678',
            documento: '001-9876543-2',
            email: 'juan.mendez@email.com',
            direccion: 'Av. Independencia #456, Santiago',
            calificacion: 4.5,
            activo: true,
            foto_perfil: null
        },
        {
            id: 'CLI003',
            cliente_id: 'CLI003',
            nombre: 'Ana Patricia Jiménez',
            telefono: '809-555-9012',
            documento: '001-5555555-5',
            email: 'ana.jimenez@email.com',
            direccion: 'Calle Duarte #789, La Vega',
            calificacion: 3.8,
            activo: true,
            foto_perfil: null
        }
    ];
}

/**
 * Actualizar contador de clientes
 */
function actualizarContadorClientes() {
    const contador = document.getElementById('total-clientes');
    if (contador) {
        contador.textContent = clientes.length;
    }
}

/**
 * Filtrar clientes según criterios de búsqueda
 */
function filtrarClientes() {
    const busqueda = document.getElementById('buscar-clientes')?.value.toLowerCase() || '';
    const filtroCalificacion = document.getElementById('filtro-calificacion')?.value || '';
    const filtroFoto = document.getElementById('filtro-con-foto')?.value || '';
    
    let clientesFiltrados = clientes.filter(cliente => {
        // Filtro de búsqueda
        const coincideBusqueda = !busqueda || 
            cliente.nombre.toLowerCase().includes(busqueda) ||
            cliente.telefono.includes(busqueda) ||
            cliente.documento.includes(busqueda);
        
        // Filtro de calificación
        let coincideCalificacion = true;
        if (filtroCalificacion) {
            const calificacion = parseFloat(cliente.calificacion || 0);
            coincideCalificacion = calificacion >= parseFloat(filtroCalificacion);
        }
        
        // Filtro de foto
        let coincideFoto = true;
        if (filtroFoto === 'con-foto') {
            coincideFoto = !!cliente.foto_perfil;
        } else if (filtroFoto === 'sin-foto') {
            coincideFoto = !cliente.foto_perfil;
        }
        
        return coincideBusqueda && coincideCalificacion && coincideFoto;
    });
    
    console.log(`🔍 Filtro aplicado: ${clientesFiltrados.length}/${clientes.length} clientes`);
    mostrarClientesEnGaleria(clientesFiltrados);
}

/**
 * Subir foto de cliente
 */
async function subirFoto(clienteId, archivo) {
    try {
        const formData = new FormData();
        formData.append('foto', archivo);
        formData.append('cliente_id', clienteId);
        
        const response = await fetch('upload_foto.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            // Actualizar foto en la base de datos
            await fetch('api_simple.php?action=cliente_actualizar_foto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: clienteId,
                    foto_perfil: resultado.url
                })
            });
            
            // Actualizar cliente en array local
            const cliente = clientes.find(c => c.id === clienteId);
            if (cliente) {
                cliente.foto_perfil = resultado.url;
            }
            
            return resultado.url;
        } else {
            throw new Error(resultado.error || 'Error subiendo foto');
        }
        
    } catch (error) {
        console.error('Error subiendo foto:', error);
        throw error;
    }
}

/**
 * Mostrar clientes en la galería
 */
function mostrarClientesEnGaleria(clientesData) {
    const galeria = document.getElementById('clientes-gallery');
    if (!galeria) return;
    
    if (!clientesData || clientesData.length === 0) {
        galeria.innerHTML = `
            <div class="mensaje-vacio">
                <div class="icono-vacio">👥</div>
                <h3>No hay clientes registrados</h3>
                <p>Agrega tu primer cliente haciendo clic en "Nuevo Cliente"</p>
            </div>
        `;
        return;
    }
    
    galeria.innerHTML = clientesData.map(cliente => {
        const rating = parseInt(cliente.calificacion || 0);
        const estrellas = '⭐'.repeat(rating) + '☆'.repeat(5 - rating);
        
        return `
            <div class="cliente-card" data-cliente-id="${cliente.id}">
                <div class="cliente-foto">
                    ${cliente.foto_perfil ? 
                        `<img src="${cliente.foto_perfil}" alt="Foto ${cliente.nombre}">` : 
                        '<div class="foto-placeholder">👤</div>'
                    }
                </div>
                <div class="cliente-info">
                    <h3>${cliente.nombre}</h3>
                    <p class="telefono">📞 ${cliente.telefono}</p>
                    <p class="documento">🆔 ${cliente.documento || cliente.cedula}</p>
                    <div class="rating-display" onclick="mostrarRatingModal('${cliente.id}')" 
                         title="Calificación: ${rating}/5 estrellas">
                        ${estrellas}
                    </div>
                </div>
                <div class="cliente-acciones">
                    <button class="btn-ver" onclick="abrirModalCliente('${cliente.id}')" 
                            title="Ver detalles">
                        👁️ Ver
                    </button>
                    <button class="btn-opciones" onclick="mostrarOpcionesCliente('${cliente.id}', event)" 
                            title="Más opciones">
                        ⋮ OPCIONES
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Mostrar modal de detalles del cliente
 */
function abrirModalCliente(clienteId) {
    const cliente = clientes.find(c => c.id == clienteId);
    if (!cliente) {
        alert('Cliente no encontrado');
        return;
    }
    
    const modalHtml = `
        <div id="modal-cliente-detalle" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>👤 Detalles del Cliente</h3>
                    <button class="modal-close" onclick="cerrarModalCliente()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="cliente-detalle">
                        <div class="foto-detalle">
                            ${cliente.foto_perfil ? 
                                `<img src="${cliente.foto_perfil}" alt="Foto ${cliente.nombre}">` : 
                                '<div class="foto-placeholder-grande">👤</div>'
                            }
                        </div>
                        <div class="info-detalle">
                            <p><strong>Nombre:</strong> ${cliente.nombre}</p>
                            <p><strong>Teléfono:</strong> ${cliente.telefono}</p>
                            <p><strong>Documento:</strong> ${cliente.documento || cliente.cedula}</p>
                            <p><strong>Email:</strong> ${cliente.email || 'No registrado'}</p>
                            <p><strong>Dirección:</strong> ${cliente.direccion || 'No registrada'}</p>
                            <p><strong>Calificación:</strong> ${'⭐'.repeat(parseInt(cliente.calificacion || 0))}</p>
                            <p><strong>Registro:</strong> ${cliente.fecha_registro || 'No disponible'}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="cerrarModalCliente()">Cerrar</button>
                    <button class="btn btn-primary" onclick="editarCliente('${clienteId}')">✏️ Editar</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

/**
 * Cerrar modal de cliente
 */
function cerrarModalCliente() {
    const modal = document.getElementById('modal-cliente-detalle');
    if (modal) modal.remove();
}

/**
 * Mostrar menú de opciones del cliente
 */
function mostrarOpcionesCliente(clienteId, event) {
    const menu = document.getElementById('opciones-menu-cliente');
    if (!menu) {
        crearMenuOpciones();
        return mostrarOpcionesCliente(clienteId, event);
    }
    
    // Configurar datos del cliente
    menu.dataset.clienteId = clienteId;
    
    // Posicionar menú
    const rect = event.target.getBoundingClientRect();
    menu.style.top = (rect.bottom + window.scrollY + 5) + 'px';
    menu.style.left = (rect.left + window.scrollX) + 'px';
    menu.classList.add('active');
    
    // Cerrar menú al hacer clic fuera
    setTimeout(() => {
        document.addEventListener('click', function closeMenu(e) {
            if (!menu.contains(e.target) && !e.target.classList.contains('btn-opciones')) {
                menu.classList.remove('active');
                document.removeEventListener('click', closeMenu);
            }
        });
    }, 100);
}

/**
 * Crear menú de opciones si no existe
 */
function crearMenuOpciones() {
    const menuHtml = `
        <div id="opciones-menu-cliente" class="opciones-menu">
            <div class="opcion-item" onclick="editarCliente()">
                <span class="opcion-icono">✏️</span>
                <span class="opcion-texto">Editar Cliente</span>
            </div>
            <div class="opcion-item" onclick="verPrestamoCliente()">
                <span class="opcion-icono">💼</span>
                <span class="opcion-texto">Ver Préstamos</span>
            </div>
            <div class="opcion-item" onclick="crearPrestamoCliente()">
                <span class="opcion-icono">➕</span>
                <span class="opcion-texto">Crear Préstamo</span>
            </div>
            <div class="opcion-item peligroso" onclick="eliminarCliente()">
                <span class="opcion-icono">🗑️</span>
                <span class="opcion-texto">Eliminar Cliente</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', menuHtml);
}

/**
 * Editar cliente
 */
function editarCliente(clienteId = null) {
    const id = clienteId || document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
    const menu = document.getElementById('opciones-menu-cliente');
    if (menu) menu.classList.remove('active');
    
    const cliente = clientes.find(c => c.id == id);
    if (!cliente) {
        alert('❌ Cliente no encontrado');
        return;
    }
    
    // Cambiar a vista de nuevo cliente
    showTab('nuevo-cliente');
    
    // Rellenar formulario con datos del cliente
    setTimeout(() => {
        document.getElementById('cliente-id-edit').value = id;
        document.getElementById('cliente-nombre').value = cliente.nombre;
        document.getElementById('cliente-documento').value = cliente.documento || cliente.cedula;
        document.getElementById('cliente-telefono').value = cliente.telefono;
        
        const emailField = document.getElementById('cliente-email');
        const direccionField = document.getElementById('cliente-direccion');
        
        if (emailField) emailField.value = cliente.email || '';
        if (direccionField) direccionField.value = cliente.direccion || '';
        
        // Calificación
        const rating = parseInt(cliente.calificacion || 0);
        actualizarRatingInput(rating);
        
        // Foto
        if (cliente.foto_perfil) {
            const preview = document.getElementById('preview-foto');
            if (preview) {
                preview.innerHTML = `<img src="${cliente.foto_perfil}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
            }
        }
        
        // Cambiar título y botón
        const titulo = document.querySelector('#vista-nuevo-cliente h3');
        const boton = document.querySelector('#form-cliente button[type="submit"]');
        
        if (titulo) titulo.textContent = 'Editar Cliente';
        if (boton) boton.innerHTML = '💾 Actualizar Cliente';
    }, 100);
}

/**
 * Eliminar cliente
 */
async function eliminarCliente() {
    const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
    const menu = document.getElementById('opciones-menu-cliente');
    if (menu) menu.classList.remove('active');
    
    const cliente = clientes.find(c => c.id == clienteId);
    if (!cliente) {
        alert('❌ Cliente no encontrado');
        return;
    }
    
    if (confirm(`⚠️ ¿Está seguro de eliminar al cliente "${cliente.nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        try {
            const response = await fetch('api_simple.php?action=cliente_eliminar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: clienteId })
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                alert('✅ Cliente eliminado correctamente');
                cargarClientes(); // Recargar lista
                cerrarModalCliente(); // Cerrar modal si está abierto
            } else {
                alert('❌ Error eliminando cliente: ' + resultado.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error de conexión');
        }
    }
}

/**
 * Ver préstamos del cliente
 */
function verPrestamoCliente() {
    const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
    const menu = document.getElementById('opciones-menu-cliente');
    if (menu) menu.classList.remove('active');
    
    console.log('💼 Viendo préstamos del cliente:', clienteId);
    
    // Cambiar a tab de préstamos
    showTab('prestamos');
    
    // Filtrar préstamos por cliente
    setTimeout(() => {
        if (typeof filtrarPrestamosPorCliente === 'function') {
            filtrarPrestamosPorCliente(clienteId);
        } else {
            alert(`🔍 Mostrando préstamos del cliente ${clienteId}`);
        }
    }, 500);
}

/**
 * Crear préstamo para cliente
 */
function crearPrestamoCliente() {
    const clienteId = document.getElementById('opciones-menu-cliente')?.dataset.clienteId;
    const menu = document.getElementById('opciones-menu-cliente');
    if (menu) menu.classList.remove('active');
    
    console.log('➕ Creando préstamo para cliente:', clienteId);
    
    // Cambiar a pestaña de préstamos
    showTab('prestamos');
    
    // Pre-seleccionar cliente en formulario
    setTimeout(() => {
        const clienteSelect = document.getElementById('prestamo-cliente');
        if (clienteSelect) {
            clienteSelect.value = clienteId;
        }
    }, 500);
}

// Inicializar módulo cliente cuando se carga
document.addEventListener('DOMContentLoaded', function() {
    console.log('📦 Módulo Cliente inicializado');
});
