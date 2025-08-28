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
                    <label for="cliente-genero">
                        <span class="label-icono">👤</span>
                        Género *
                    </label>
                    <select id="cliente-genero" name="genero" required>
                        <option value="">Seleccionar género</option>
                        <option value="masculino">👨 Masculino</option>
                        <option value="femenino">👩 Femenino</option>
                    </select>
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
                    <div class="foto-perfil-container">
                        <div id="preview-foto" class="foto-preview">
                            <div class="foto-placeholder">📷</div>
                        </div>
                        <div class="foto-upload-info">
                            <h4>Subir Foto de Perfil</h4>
                            <p>Selecciona una imagen para la cédula o perfil del cliente</p>
                            <div class="foto-actions">
                                <div class="file-input-custom">
                                    <input type="file" id="cliente-foto" accept="image/*" 
                                           onchange="previsualizarFoto(this)">
                                    <label for="cliente-foto" class="file-input-label">
                                        📤 Subir Foto
                                    </label>
                                </div>
                                <button type="button" class="btn-foto-defecto" onclick="aplicarFotoDefecto()">
                                    🎭 Usar Foto por Defecto
                                </button>
                            </div>
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
    
    // Evento para cambio de género
    const generoSelect = document.getElementById('cliente-genero');
    if (generoSelect) {
        generoSelect.addEventListener('change', function() {
            const preview = document.getElementById('preview-foto');
            const fotoInput = document.getElementById('cliente-foto');
            
            // Solo aplicar foto por defecto si no hay foto seleccionada
            if (preview && fotoInput && !fotoInput.files.length) {
                const placeholder = preview.querySelector('.foto-placeholder');
                if (placeholder) {
                    aplicarFotoDefecto();
                }
            }
        });
    }
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
    formData.append('genero', document.getElementById('cliente-genero').value);
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
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        // Obtener texto y verificar que sea JSON válido
        const textoRespuesta = await response.text();
        console.log('📡 Respuesta guardar cliente:', textoRespuesta);
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
        } catch (jsonError) {
            console.error('❌ Error parsing JSON al guardar cliente:', jsonError);
            alert('❌ Error en la respuesta del servidor. Por favor, intenta de nuevo.');
            return;
        }
        
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
 * Previsualizar foto seleccionada con editor
 */
function previsualizarFoto(input) {
    const preview = document.getElementById('preview-foto');
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const archivo = input.files[0];
        
        // Validar tipo de archivo
        if (!archivo.type.match('image.*')) {
            alert('❌ Por favor selecciona un archivo de imagen');
            input.value = '';
            return;
        }
        
        // Mostrar editor de imagen
        mostrarEditorImagen(archivo, preview);
    }
}

/**
 * Mostrar editor de imagen para ajustar foto
 */
function mostrarEditorImagen(archivo, preview) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const modalHtml = `
            <div id="editor-foto-modal" class="modal-overlay">
                <div class="modal-content editor-foto">
                    <div class="modal-header">
                        <h3>📷 Ajustar Foto de Perfil</h3>
                        <button class="modal-close" onclick="cerrarEditorFoto()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="editor-container">
                            <div class="imagen-original">
                                <img id="imagen-crop" src="${e.target.result}" alt="Imagen a editar">
                                <div class="crop-overlay">
                                    <div class="crop-area" id="crop-area">
                                        <div class="crop-handle top-left"></div>
                                        <div class="crop-handle top-right"></div>
                                        <div class="crop-handle bottom-left"></div>
                                        <div class="crop-handle bottom-right"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-resultado">
                                <h4>Vista previa:</h4>
                                <div class="foto-preview-circular">
                                    <canvas id="preview-canvas" width="120" height="120"></canvas>
                                </div>
                                <p class="instrucciones">
                                    Arrastra el área de selección para ajustar la foto
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" onclick="cerrarEditorFoto()">Cancelar</button>
                        <button class="btn btn-primary" onclick="aplicarCropFoto()">✂️ Usar Esta Foto</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Inicializar editor
        setTimeout(() => {
            inicializarEditorCrop();
        }, 100);
    };
    
    reader.readAsDataURL(archivo);
}

/**
 * Inicializar editor de crop
 */
function inicializarEditorCrop() {
    const imagen = document.getElementById('imagen-crop');
    const cropArea = document.getElementById('crop-area');
    
    if (!imagen || !cropArea) return;
    
    // Posicionar área de crop inicial (centrada)
    imagen.onload = function() {
        const imgRect = imagen.getBoundingClientRect();
        const size = Math.min(imgRect.width, imgRect.height) * 0.7;
        
        cropArea.style.width = size + 'px';
        cropArea.style.height = size + 'px';
        cropArea.style.left = (imgRect.width - size) / 2 + 'px';
        cropArea.style.top = (imgRect.height - size) / 2 + 'px';
        
        // Configurar arrastre
        hacerArrastrable(cropArea);
        
        // Actualizar preview inicial
        actualizarPreviewCrop();
    };
}

/**
 * Hacer el área de crop arrastrable
 */
function hacerArrastrable(elemento) {
    let isDragging = false;
    let startX, startY, startLeft, startTop;
    
    elemento.addEventListener('mousedown', function(e) {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = parseInt(elemento.style.left) || 0;
        startTop = parseInt(elemento.style.top) || 0;
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        e.preventDefault();
    });
    
    function onMouseMove(e) {
        if (!isDragging) return;
        
        const newLeft = startLeft + (e.clientX - startX);
        const newTop = startTop + (e.clientY - startY);
        
        // Limitar dentro de la imagen
        const imagen = document.getElementById('imagen-crop');
        const imgRect = imagen.getBoundingClientRect();
        const cropRect = elemento.getBoundingClientRect();
        
        const maxLeft = imgRect.width - cropRect.width;
        const maxTop = imgRect.height - cropRect.height;
        
        elemento.style.left = Math.max(0, Math.min(maxLeft, newLeft)) + 'px';
        elemento.style.top = Math.max(0, Math.min(maxTop, newTop)) + 'px';
        
        actualizarPreviewCrop();
    }
    
    function onMouseUp() {
        isDragging = false;
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }
}

/**
 * Actualizar preview del crop
 */
function actualizarPreviewCrop() {
    const imagen = document.getElementById('imagen-crop');
    const cropArea = document.getElementById('crop-area');
    const canvas = document.getElementById('preview-canvas');
    
    if (!imagen || !cropArea || !canvas) return;
    
    const ctx = canvas.getContext('2d');
    const cropRect = cropArea.getBoundingClientRect();
    const imgRect = imagen.getBoundingClientRect();
    
    // Calcular posición relativa
    const x = parseInt(cropArea.style.left) || 0;
    const y = parseInt(cropArea.style.top) || 0;
    const width = cropRect.width;
    const height = cropRect.height;
    
    // Dibujar la parte recortada en el canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(imagen, x, y, width, height, 0, 0, canvas.width, canvas.height);
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
/**
 * Aplicar foto recortada
 */
function aplicarCropFoto() {
    const canvas = document.getElementById('preview-canvas');
    const preview = document.getElementById('preview-foto');
    
    if (!canvas || !preview) return;
    
    // Convertir canvas a blob
    canvas.toBlob(function(blob) {
        // Crear URL para la imagen recortada
        const imageUrl = URL.createObjectURL(blob);
        
        // Mostrar en preview principal
        preview.innerHTML = `
            <img src="${imageUrl}" alt="Foto de perfil" 
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        `;
        
        // Guardar blob para envío posterior
        window.fotoEditada = blob;
        
        // Cerrar editor
        cerrarEditorFoto();
        
        console.log('✅ Foto editada aplicada correctamente');
    }, 'image/jpeg', 0.9);
}

/**
 * Cerrar editor de foto
 */
function cerrarEditorFoto() {
    const modal = document.getElementById('editor-foto-modal');
    if (modal) {
        modal.remove();
    }
}

/**
 * Obtener fotos por defecto según género
 */
function obtenerFotoDefecto(genero) {
    const fotosDefecto = {
        masculino: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiM0Mjg1RjQiLz4KPHBhdGggZD0iTTYwIDM1QzY3LjE3OTcgMzUgNzMgNDAuODIwMyA3MyA0OEM3MyA1NS4xNzk3IDY3LjE3OTcgNjEgNjAgNjFDNTIuODIwMyA2MSA0NyA1NS4xNzk3IDQ3IDQ4QzQ3IDQwLjgyMDMgNTIuODIwMyAzNSA2MCAzNVoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik02MCA2OEM3My4yNTQ4IDY4IDg0IDc4Ljc0NTIgODQgOTJWMTIwSDM2VjkyQzM2IDc4Ljc0NTIgNDYuNzQ1MiA2OCA2MCA2OFoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPg==',
        femenino: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNGRjQ0ODEiLz4KPHBhdGggZD0iTTYwIDM1QzY3LjE3OTcgMzUgNzMgNDAuODIwMyA3MyA0OEM3MyA1NS4xNzk3IDY3LjE3OTcgNjEgNjAgNjFDNTIuODIwMyA2MSA0NyA1NS4xNzk3IDQ3IDQ4QzQ3IDQwLjgyMDMgNTIuODIwMyAzNSA2MCAzNVoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik02MCA2OEM3My4yNTQ4IDY4IDg0IDc4Ljc0NTIgODQgOTJWMTIwSDM2VjkyQzM2IDc4Ljc0NTIgNDYuNzQ1MiA2OCA2MCA2OFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik00NSAyNUg3NUw3MCAzMEg1MEw0NSAyNVoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPg=='
    };
    
    return fotosDefecto[genero] || fotosDefecto.masculino;
}

/**
 * Aplicar foto por defecto según género seleccionado
 */
function aplicarFotoDefecto() {
    const generoSelect = document.getElementById('genero-cliente');
    const preview = document.getElementById('preview-foto');
    
    if (!generoSelect || !preview) return;
    
    const genero = generoSelect.value;
    if (genero) {
        const fotoDefecto = obtenerFotoDefecto(genero);
        preview.innerHTML = `
            <img src="${fotoDefecto}" alt="Foto por defecto ${genero}" 
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        `;
        
        // Limpiar archivo seleccionado
        const inputFoto = document.getElementById('foto-cliente');
        if (inputFoto) inputFoto.value = '';
        
        console.log(`📷 Foto por defecto aplicada: ${genero}`);
    }
}
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

// Exportar funciones principales al ámbito global
window.mostrarFormularioCliente = mostrarFormularioCliente;
window.guardarCliente = guardarCliente;
window.previsualizarFoto = previsualizarFoto;
window.aplicarCropFoto = aplicarCropFoto;
window.cerrarEditorFoto = cerrarEditorFoto;
window.aplicarFotoDefecto = aplicarFotoDefecto;
