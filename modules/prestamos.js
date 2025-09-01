/**
 * MÓDULO PRÉSTAMOS - FUNCIONES FRONTEND
 * Sistema Modular de Préstamos Nova Luz
 */

// Variables globales del módulo préstamos
let prestamos = [];
let prestamoEditandoId = null;

/**
 * Cargar datos de préstamos desde la API
 */
async function cargarPrestamos() {
    try {
        console.log('🔄 Cargando préstamos...');
        const response = await fetch('api_simple.php?action=prestamo_listar');
        const resultado = await response.json();
        
        console.log('📡 Respuesta API préstamos:', resultado);
        
        if (resultado.success) {
            prestamos = resultado.data || [];
            console.log('✅ Préstamos cargados:', prestamos);
            
            mostrarPrestamosEnTabla(prestamos);
            actualizarContadorPrestamos();
            return prestamos;
        } else {
            console.error('❌ Error cargando préstamos:', resultado.error);
            prestamos = [];
            mostrarPrestamosEnTabla([]);
        }
    } catch (error) {
        console.error('❌ Error cargando préstamos:', error);
        prestamos = [];
        mostrarPrestamosEnTabla([]);
    }
}

/**
 * Mostrar préstamos en tabla
 */
function mostrarPrestamosEnTabla(prestamosData) {
    const tabla = document.getElementById('prestamos-tabla');
    if (!tabla) return;
    
    if (!prestamosData || prestamosData.length === 0) {
        tabla.innerHTML = `
            <div class="mensaje-vacio">
                <div class="icono-vacio">💼</div>
                <h3>No hay préstamos registrados</h3>
                <p>Crea tu primer préstamo haciendo clic en "Nuevo Préstamo"</p>
            </div>
        `;
        return;
    }
    
    const tablaHtml = `
        <table class="tabla-prestamos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Cuotas</th>
                    <th>Tasa</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${prestamosData.map(prestamo => `
                    <tr data-prestamo-id="${prestamo.id}">
                        <td class="prestamo-id">#${prestamo.id}</td>
                        <td class="cliente-nombre">${prestamo.cliente_nombre || 'Cliente no encontrado'}</td>
                        <td class="monto">RD$${parseFloat(prestamo.monto || 0).toFixed(2)}</td>
                        <td class="cuotas">${prestamo.plazo || 0}</td>
                        <td class="tasa">${parseFloat(prestamo.tasa || 0).toFixed(1)}%</td>
                        <td class="estado">
                            <span class="estado-badge ${prestamo.estado?.toLowerCase()}">${prestamo.estado || 'Activo'}</span>
                        </td>
                        <td class="fecha">${formatearFecha(prestamo.fecha_inicio)}</td>
                        <td class="acciones">
                            <button class="btn-accion ver" onclick="verPrestamo('${prestamo.id}')" title="Ver detalles">
                                👁️
                            </button>
                            <button class="btn-accion editar" onclick="editarPrestamo('${prestamo.id}')" title="Editar">
                                ✏️
                            </button>
                            <button class="btn-accion eliminar" onclick="eliminarPrestamo('${prestamo.id}')" title="Eliminar">
                                🗑️
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    tabla.innerHTML = tablaHtml;
}

/**
 * Actualizar contador de préstamos
 */
function actualizarContadorPrestamos() {
    const contador = document.getElementById('total-prestamos');
    if (contador) {
        contador.textContent = prestamos.length;
    }
}

/**
 * Ver detalles de un préstamo
 */
function verPrestamo(prestamoId) {
    const prestamo = prestamos.find(p => p.id == prestamoId);
    if (!prestamo) {
        alert('❌ Préstamo no encontrado');
        return;
    }
    
    const modalHtml = `
        <div id="modal-prestamo-detalle" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>💼 Detalles del Préstamo #${prestamo.id}</h3>
                    <button class="modal-close" onclick="cerrarModalPrestamo()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="prestamo-detalle">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Cliente:</label>
                                <span>${prestamo.cliente_nombre}</span>
                            </div>
                            <div class="info-item">
                                <label>Monto:</label>
                                <span>RD$${parseFloat(prestamo.monto).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Plazo:</label>
                                <span>${prestamo.plazo} cuotas</span>
                            </div>
                            <div class="info-item">
                                <label>Tasa:</label>
                                <span>${parseFloat(prestamo.tasa).toFixed(1)}% mensual</span>
                            </div>
                            <div class="info-item">
                                <label>Estado:</label>
                                <span class="estado-badge ${prestamo.estado?.toLowerCase()}">${prestamo.estado}</span>
                            </div>
                            <div class="info-item">
                                <label>Fecha Inicio:</label>
                                <span>${formatearFecha(prestamo.fecha_inicio)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="cerrarModalPrestamo()">Cerrar</button>
                    <button class="btn btn-primary" onclick="editarPrestamo('${prestamo.id}')">✏️ Editar</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

/**
 * Cerrar modal de préstamo
 */
function cerrarModalPrestamo() {
    const modal = document.getElementById('modal-prestamo-detalle');
    if (modal) modal.remove();
}

/**
 * Editar préstamo
 */
function editarPrestamo(prestamoId) {
    const prestamo = prestamos.find(p => p.id == prestamoId);
    if (!prestamo) {
        alert('❌ Préstamo no encontrado');
        return;
    }
    
    prestamoEditandoId = prestamoId;
    
    // Cambiar a vista de nuevo préstamo
    showTab('nuevo-prestamo');
    
    // Rellenar formulario
    setTimeout(() => {
        document.getElementById('prestamo-id-edit').value = prestamoId;
        document.getElementById('prestamo-cliente').value = prestamo.cliente_id;
        document.getElementById('prestamo-monto').value = prestamo.monto;
        document.getElementById('prestamo-plazo').value = prestamo.plazo;
        document.getElementById('prestamo-tasa').value = prestamo.tasa;
        
        // Cambiar título
        const titulo = document.querySelector('#vista-nuevo-prestamo h2');
        const boton = document.querySelector('#form-prestamo button[type="submit"]');
        
        if (titulo) titulo.textContent = '✏️ Editar Préstamo';
        if (boton) boton.innerHTML = '💾 Actualizar Préstamo';
    }, 100);
}

/**
 * Eliminar préstamo
 */
async function eliminarPrestamo(prestamoId) {
    const prestamo = prestamos.find(p => p.id == prestamoId);
    if (!prestamo) {
        alert('❌ Préstamo no encontrado');
        return;
    }
    
    if (confirm(`⚠️ ¿Está seguro de eliminar el préstamo #${prestamoId}?\n\nCliente: ${prestamo.cliente_nombre}\nMonto: RD$${parseFloat(prestamo.monto).toFixed(2)}\n\nEsta acción no se puede deshacer.`)) {
        try {
            const response = await fetch('api_simple.php?action=prestamo_eliminar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: prestamoId })
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                alert('✅ Préstamo eliminado correctamente');
                cargarPrestamos(); // Recargar lista
                cerrarModalPrestamo(); // Cerrar modal si está abierto
            } else {
                alert('❌ Error eliminando préstamo: ' + resultado.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error de conexión');
        }
    }
}

/**
 * Filtrar préstamos por cliente
 */
function filtrarPrestamosPorCliente(clienteId) {
    const prestamosFiltrados = prestamos.filter(p => p.cliente_id == clienteId);
    mostrarPrestamosEnTabla(prestamosFiltrados);
    
    // Mensaje informativo
    const mensaje = document.getElementById('filtro-mensaje');
    if (mensaje) {
        mensaje.textContent = `Mostrando préstamos del cliente ${clienteId}`;
        mensaje.style.display = 'block';
    }
}

/**
 * Formatear fecha
 */
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    
    try {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-DO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (error) {
        return fecha;
    }
}

/**
 * Inicializar formulario de nuevo préstamo
 */
function inicializarFormularioPrestamo() {
    console.log('🔄 Inicializando formulario de préstamo...');
    
    const contenedor = document.getElementById('form-nuevo-prestamo');
    if (!contenedor) return;
    
    // Generar formulario HTML
    const formularioHtml = `
        <form id="form-prestamo" class="formulario-prestamo">
            <input type="hidden" id="prestamo-id-edit" value="">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="prestamo-cliente">
                        <span class="label-icono">👤</span>
                        Cliente *
                    </label>
                    <select id="prestamo-cliente" name="cliente_id" required>
                        <option value="">Selecciona un cliente</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="prestamo-monto">
                        <span class="label-icono">💰</span>
                        Monto *
                    </label>
                    <input type="number" id="prestamo-monto" name="monto" required
                           placeholder="Ej: 50000" step="0.01" min="0">
                </div>
                
                <div class="form-group">
                    <label for="prestamo-tasa">
                        <span class="label-icono">📈</span>
                        Tasa (%) *
                    </label>
                    <input type="number" id="prestamo-tasa" name="tasa" required
                           placeholder="Ej: 10" step="0.1" min="0" value="10">
                </div>
                
                <div class="form-group">
                    <label for="prestamo-plazo">
                        <span class="label-icono">📅</span>
                        Plazo (días)
                    </label>
                    <input type="number" id="prestamo-plazo" name="plazo"
                           placeholder="Ej: 30" min="1">
                </div>
                
                <div class="form-group">
                    <label for="prestamo-tipo">
                        <span class="label-icono">📋</span>
                        Tipo de Préstamo *
                    </label>
                    <select id="prestamo-tipo" name="tipo_prestamo" required>
                        <option value="">Selecciona tipo</option>
                        <option value="solo_interes">Solo Interés</option>
                        <option value="cuotas_fijas">Cuotas Fijas</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="prestamo-frecuencia">
                        <span class="label-icono">🔄</span>
                        Frecuencia de Pago *
                    </label>
                    <select id="prestamo-frecuencia" name="frecuencia" required>
                        <option value="">Selecciona frecuencia</option>
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="prestamo-fecha">
                        <span class="label-icono">📅</span>
                        Fecha de Inicio *
                    </label>
                    <input type="date" id="prestamo-fecha" name="fecha_inicio" required>
                </div>
                
                <div class="form-group">
                    <label for="prestamo-cuotas">
                        <span class="label-icono">🔢</span>
                        Número de Cuotas
                    </label>
                    <input type="number" id="prestamo-cuotas" name="numero_cuotas"
                           placeholder="Ej: 4" min="1">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="showTab('prestamos')">
                    ← Volver a Préstamos
                </button>
                <button type="submit" class="btn btn-primary">
                    💾 Crear Préstamo
                </button>
            </div>
        </form>
    `;
    
    contenedor.innerHTML = formularioHtml;
    
    // Configurar eventos del formulario
    configurarEventosFormularioPrestamo();
    
    // Cargar clientes en el select
    cargarClientesEnSelect();
    
    // Establecer fecha actual por defecto
    const fechaHoy = new Date().toISOString().split('T')[0];
    document.getElementById('prestamo-fecha').value = fechaHoy;
}

/**
 * Configurar eventos del formulario de préstamo
 */
function configurarEventosFormularioPrestamo() {
    const form = document.getElementById('form-prestamo');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarPrestamo();
    });
}

/**
 * Cargar clientes en el select
 */
async function cargarClientesEnSelect() {
    try {
        const select = document.getElementById('prestamo-cliente');
        if (!select) return;
        
        // Si ya hay clientes cargados globalmente, usarlos
        if (window.clientes && window.clientes.length > 0) {
            select.innerHTML = '<option value="">Selecciona un cliente</option>';
            window.clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = `${cliente.nombre} - ${cliente.documento || cliente.cedula}`;
                select.appendChild(option);
            });
        } else {
            // Cargar clientes desde la API
            const response = await fetch('api_simple.php?action=cliente_listar');
            const resultado = await response.json();
            
            if (resultado.success && resultado.data) {
                select.innerHTML = '<option value="">Selecciona un cliente</option>';
                resultado.data.forEach(cliente => {
                    const option = document.createElement('option');
                    option.value = cliente.id;
                    option.textContent = `${cliente.nombre} - ${cliente.documento || cliente.cedula}`;
                    select.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error cargando clientes:', error);
    }
}

/**
 * Guardar préstamo
 */
async function guardarPrestamo() {
    const form = document.getElementById('form-prestamo');
    if (!form) return;
    
    const prestamoId = document.getElementById('prestamo-id-edit').value;
    const isEdit = !!prestamoId;
    
    const formData = new FormData(form);
    const datos = {};
    
    for (let [key, value] of formData.entries()) {
        datos[key] = value;
    }
    
    console.log('Datos del préstamo a guardar:', datos);
    
    // Validar campos requeridos
    if (!datos.cliente_id || !datos.monto || !datos.tasa || !datos.tipo_prestamo || !datos.frecuencia || !datos.fecha_inicio) {
        alert('⚠️ Por favor completa todos los campos obligatorios');
        return;
    }
    
    try {
        const action = isEdit ? 'prestamo_actualizar' : 'prestamo_crear';
        const response = await fetch(`api_simple.php?action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        // Obtener texto y verificar que sea JSON válido
        const textoRespuesta = await response.text();
        console.log('📡 Respuesta guardar préstamo:', textoRespuesta);
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
        } catch (jsonError) {
            console.error('❌ Error parsing JSON al guardar préstamo:', jsonError);
            alert('❌ Error en la respuesta del servidor. Por favor, intenta de nuevo.');
            return;
        }
        
        if (resultado.success) {
            alert(`✅ Préstamo ${isEdit ? 'actualizado' : 'creado'} correctamente`);
            
            // Limpiar formulario
            form.reset();
            
            // Regresar a lista de préstamos
            showTab('prestamos');
            
            // Recargar lista
            cargarPrestamos();
            
        } else {
            console.error('❌ Error del servidor:', resultado.error);
            alert(`❌ Error: ${resultado.error || 'Error desconocido'}`);
        }
        
    } catch (error) {
        console.error('❌ Error guardando préstamo:', error);
        alert('❌ Error conectando con el servidor. Por favor, intenta de nuevo.');
    }
}

// Inicializar módulo préstamos
document.addEventListener('DOMContentLoaded', function() {
    console.log('💼 Módulo Préstamos inicializado');
});

// Exportar funciones al ámbito global
window.cargarPrestamos = cargarPrestamos;
window.mostrarFormularioPrestamo = mostrarFormularioPrestamo;
window.inicializarFormularioPrestamo = inicializarFormularioPrestamo;
window.configurarEventosFormularioPrestamo = configurarEventosFormularioPrestamo;
window.cargarClientesEnSelect = cargarClientesEnSelect;
window.guardarPrestamo = guardarPrestamo;
