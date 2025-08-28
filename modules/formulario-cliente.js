/**
 * MÓDULO FORMULARIO CLIENTE - GESTIÓN DE FORMULARIOS
 * Sistema Modular de Préstamos Nova Luz
 */

/**
 * Inicializar formulario de cliente
 */
function inicializarFormularioCliente() {
    const contenedor = document.getElementById('form-nuevo-cliente');
    if (!contenedor) return;
    
    const formularioHtml = `
        <form id="form-cliente" class="formulario-cliente">
            <input type="hidden" id="cliente-id-edit" value="">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="cliente-nombre">
                        <span class="label-icono">👤</span>
                        Nombre Completo *
                    </label>
                    <input type="text" id="cliente-nombre" name="nombre" required
                           placeholder="Ej: María Elena Rodríguez">
                </div>
                
                <div class="form-group">
                    <label for="cliente-documento">
                        <span class="label-icono">🆔</span>
                        Cédula/Documento *
                    </label>
                    <input type="text" id="cliente-documento" name="documento" required
                           placeholder="Ej: 001-1234567-8">
                </div>
                
                <div class="form-group">
                    <label for="cliente-telefono">
                        <span class="label-icono">📞</span>
                        Teléfono *
                    </label>
                    <input type="tel" id="cliente-telefono" name="telefono" required
                           placeholder="Ej: 809-555-1234">
                </div>
                
                <div class="form-group">
                    <label for="cliente-email">
                        <span class="label-icono">📧</span>
                        Email
                    </label>
                    <input type="email" id="cliente-email" name="email"
                           placeholder="Ej: maria@email.com">
                </div>
                
                <div class="form-group full-width">
                    <label for="cliente-direccion">
                        <span class="label-icono">🏠</span>
                        Dirección
                    </label>
                    <textarea id="cliente-direccion" name="direccion" rows="3"
                              placeholder="Dirección completa del cliente"></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <span class="label-icono">⭐</span>
                        Calificación Inicial
                    </label>
                    <div id="rating-input" data-rating="5">
                        <span class="star active" onclick="setRating(1)">⭐</span>
                        <span class="star active" onclick="setRating(2)">⭐</span>
                        <span class="star active" onclick="setRating(3)">⭐</span>
                        <span class="star active" onclick="setRating(4)">⭐</span>
                        <span class="star active" onclick="setRating(5)">⭐</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cliente-foto">
                        <span class="label-icono">📷</span>
                        Foto de Perfil
                    </label>
                    <div class="foto-upload">
                        <input type="file" id="cliente-foto" accept="image/*" 
                               onchange="previsualizarFoto(this)">
                        <div id="preview-foto" class="foto-preview">
                            <div class="foto-placeholder">📷</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="cancelarFormulario()">
                    ❌ Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    💾 Guardar Cliente
                </button>
            </div>
        </form>
    `;
    
    contenedor.innerHTML = formularioHtml;
    
    // Configurar eventos del formulario
    configurarEventosFormulario();
}

/**
 * Configurar eventos del formulario
 */
function configurarEventosFormulario() {
    const form = document.getElementById('form-cliente');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarCliente();
    });
}

/**
 * Guardar cliente (nuevo o editado)
 */
async function guardarCliente() {
    const form = document.getElementById('form-cliente');
    if (!form) return;
    
    const formData = new FormData();
    const clienteId = document.getElementById('cliente-id-edit').value;
    const isEdit = !!clienteId;
    
    // Datos del cliente
    formData.append('nombre', document.getElementById('cliente-nombre').value);
    formData.append('documento', document.getElementById('cliente-documento').value);
    formData.append('telefono', document.getElementById('cliente-telefono').value);
    formData.append('email', document.getElementById('cliente-email').value);
    formData.append('direccion', document.getElementById('cliente-direccion').value);
    formData.append('calificacion', getRating());
    
    if (isEdit) {
        formData.append('id', clienteId);
    }
    
    // Foto si existe
    const fotoInput = document.getElementById('cliente-foto');
    if (fotoInput.files[0]) {
        formData.append('foto', fotoInput.files[0]);
    }
    
    try {
        const action = isEdit ? 'cliente_actualizar' : 'cliente_crear';
        const response = await fetch(`api_simple.php?action=${action}`, {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            alert(`✅ Cliente ${isEdit ? 'actualizado' : 'creado'} correctamente`);
            
            // Limpiar formulario
            limpiarFormulario();
            
            // Regresar a lista de clientes
            showTab('clientes');
            
            // Recargar lista
            cargarClientes();
            
        } else {
            alert(`❌ Error al ${isEdit ? 'actualizar' : 'crear'} cliente: ` + resultado.error);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error de conexión');
    }
}

/**
 * Previsualizar foto seleccionada
 */
function previsualizarFoto(input) {
    const preview = document.getElementById('preview-foto');
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" 
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Limpiar formulario
 */
function limpiarFormulario() {
    const form = document.getElementById('form-cliente');
    if (!form) return;
    
    form.reset();
    
    // Limpiar campos específicos
    document.getElementById('cliente-id-edit').value = '';
    
    // Resetear rating
    setRating(5);
    
    // Limpiar preview de foto
    const preview = document.getElementById('preview-foto');
    if (preview) {
        preview.innerHTML = '<div class="foto-placeholder">📷</div>';
    }
    
    // Cambiar título y botón a modo "nuevo"
    const titulo = document.querySelector('#vista-nuevo-cliente h2');
    const boton = document.querySelector('#form-cliente button[type="submit"]');
    
    if (titulo) titulo.textContent = '➕ Agregar Nuevo Cliente';
    if (boton) boton.innerHTML = '💾 Guardar Cliente';
}

/**
 * Cancelar formulario
 */
function cancelarFormulario() {
    if (confirm('¿Está seguro de cancelar? Se perderán los datos ingresados.')) {
        limpiarFormulario();
        showTab('clientes');
    }
}

/**
 * Validar formulario antes de enviar
 */
function validarFormulario() {
    const nombre = document.getElementById('cliente-nombre').value.trim();
    const documento = document.getElementById('cliente-documento').value.trim();
    const telefono = document.getElementById('cliente-telefono').value.trim();
    
    if (!nombre) {
        alert('❌ El nombre es obligatorio');
        document.getElementById('cliente-nombre').focus();
        return false;
    }
    
    if (!documento) {
        alert('❌ El documento es obligatorio');
        document.getElementById('cliente-documento').focus();
        return false;
    }
    
    if (!telefono) {
        alert('❌ El teléfono es obligatorio');
        document.getElementById('cliente-telefono').focus();
        return false;
    }
    
    return true;
}

// Inicializar cuando se carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('📝 Módulo Formulario Cliente inicializado');
});
