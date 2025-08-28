/**
 * MÓDULO RATING - SISTEMA DE CALIFICACIONES
 * Sistema Modular de Préstamos Nova Luz
 */

// Variable global para el cliente siendo calificado
let clienteCalificandoId = null;

/**
 * Mostrar modal de calificación
 */
function mostrarRatingModal(clienteId) {
    clienteCalificandoId = clienteId;
    
    const cliente = clientes.find(c => c.id == clienteId);
    if (!cliente) {
        alert('❌ Cliente no encontrado');
        return;
    }
    
    const ratingActual = parseInt(cliente.calificacion || 0);
    
    const modalHtml = `
        <div id="modal-rating" class="modal-overlay">
            <div class="modal-content rating-modal">
                <div class="modal-header">
                    <h3>⭐ Calificar Cliente</h3>
                    <button class="modal-close" onclick="cerrarModalRating()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="cliente-rating-info">
                        <h4>${cliente.nombre}</h4>
                        <p>Calificación actual: ${'⭐'.repeat(ratingActual)}${'☆'.repeat(5 - ratingActual)}</p>
                    </div>
                    <div class="rating-selector">
                        <div class="estrellas-grandes">
                            ${[1, 2, 3, 4, 5].map(num => `
                                <span class="estrella-rating ${num <= ratingActual ? 'activa' : ''}" 
                                      data-rating="${num}" 
                                      onclick="seleccionarRating(${num})">
                                    ⭐
                                </span>
                            `).join('')}
                        </div>
                        <p class="rating-descripcion" id="rating-descripcion">
                            ${obtenerDescripcionRating(ratingActual)}
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="cerrarModalRating()">Cancelar</button>
                    <button class="btn btn-primary" onclick="aplicarRating()">💾 Guardar Calificación</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Configurar hover effects
    configurarHoverRating();
}

/**
 * Cerrar modal de rating
 */
function cerrarModalRating() {
    const modal = document.getElementById('modal-rating');
    if (modal) {
        modal.remove();
    }
    clienteCalificandoId = null;
}

/**
 * Seleccionar rating
 */
function seleccionarRating(rating) {
    // Actualizar estrellas visuales
    document.querySelectorAll('.estrella-rating').forEach((estrella, index) => {
        if (index < rating) {
            estrella.classList.add('activa');
        } else {
            estrella.classList.remove('activa');
        }
    });
    
    // Actualizar descripción
    const descripcion = document.getElementById('rating-descripcion');
    if (descripcion) {
        descripcion.textContent = obtenerDescripcionRating(rating);
    }
}

/**
 * Aplicar rating seleccionado
 */
async function aplicarRating() {
    const estrellas = document.querySelectorAll('.estrella-rating.activa');
    const rating = estrellas.length;
    
    if (rating === 0) {
        alert('❌ Por favor selecciona una calificación');
        return;
    }
    
    try {
        const response = await fetch('api_simple.php?action=cliente_actualizar_rating', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: clienteCalificandoId,
                calificacion: rating
            })
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            // Actualizar en el array local
            const cliente = clientes.find(c => c.id == clienteCalificandoId);
            if (cliente) {
                cliente.calificacion = rating;
            }
            
            alert(`✅ Calificación de ${rating} estrella${rating > 1 ? 's' : ''} aplicada correctamente`);
            
            // Actualizar vista
            mostrarClientesEnGaleria(clientes);
            cerrarModalRating();
            
        } else {
            alert('❌ Error al guardar calificación: ' + resultado.error);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error de conexión al guardar calificación');
    }
}

/**
 * Obtener descripción del rating
 */
function obtenerDescripcionRating(rating) {
    const descripciones = {
        0: 'Sin calificación',
        1: '⭐ Muy malo - Cliente problemático',
        2: '⭐⭐ Malo - Pagos irregulares',
        3: '⭐⭐⭐ Regular - Cliente promedio',
        4: '⭐⭐⭐⭐ Bueno - Buen cliente',
        5: '⭐⭐⭐⭐⭐ Excelente - Cliente ideal'
    };
    
    return descripciones[rating] || 'Sin calificación';
}

/**
 * Configurar efectos hover para rating
 */
function configurarHoverRating() {
    const estrellas = document.querySelectorAll('.estrella-rating');
    
    estrellas.forEach((estrella, index) => {
        estrella.addEventListener('mouseenter', function() {
            // Destacar estrellas hasta la actual
            estrellas.forEach((e, i) => {
                if (i <= index) {
                    e.classList.add('hover');
                } else {
                    e.classList.remove('hover');
                }
            });
            
            // Mostrar descripción temporal
            const descripcion = document.getElementById('rating-descripcion');
            if (descripcion) {
                descripcion.textContent = obtenerDescripcionRating(index + 1);
            }
        });
        
        estrella.addEventListener('mouseleave', function() {
            // Remover hover
            estrellas.forEach(e => e.classList.remove('hover'));
            
            // Restaurar descripción basada en selección actual
            const activas = document.querySelectorAll('.estrella-rating.activa');
            const rating = activas.length;
            const descripcion = document.getElementById('rating-descripcion');
            if (descripcion) {
                descripcion.textContent = obtenerDescripcionRating(rating);
            }
        });
    });
}

/**
 * Actualizar rating input en formularios
 */
function actualizarRatingInput(rating) {
    const ratingInput = document.getElementById('rating-input');
    if (!ratingInput) return;
    
    // Crear estrellas si no existen
    if (ratingInput.children.length === 0) {
        for (let i = 1; i <= 5; i++) {
            const estrella = document.createElement('span');
            estrella.className = 'star';
            estrella.textContent = '⭐';
            estrella.onclick = () => setRating(i);
            ratingInput.appendChild(estrella);
        }
    }
    
    // Actualizar estrellas
    const estrellas = ratingInput.querySelectorAll('.star');
    estrellas.forEach((estrella, index) => {
        if (index < rating) {
            estrella.classList.add('active');
        } else {
            estrella.classList.remove('active');
        }
    });
}

/**
 * Establecer rating en formulario
 */
function setRating(rating) {
    const ratingInput = document.getElementById('rating-input');
    if (!ratingInput) return;
    
    ratingInput.dataset.rating = rating;
    
    const estrellas = ratingInput.querySelectorAll('.star');
    estrellas.forEach((estrella, index) => {
        if (index < rating) {
            estrella.classList.add('active');
        } else {
            estrella.classList.remove('active');
        }
    });
}

/**
 * Obtener rating actual del formulario
 */
function getRating() {
    const ratingInput = document.getElementById('rating-input');
    return ratingInput ? parseInt(ratingInput.dataset.rating || 0) : 0;
}

// Inicializar módulo rating
document.addEventListener('DOMContentLoaded', function() {
    console.log('⭐ Módulo Rating inicializado');
});

// Exportar funciones al ámbito global
window.setRating = setRating;
window.getRating = getRating;
window.mostrarRatingModal = mostrarRatingModal;
window.aplicarRating = aplicarRating;
window.cerrarRatingModal = cerrarRatingModal;
