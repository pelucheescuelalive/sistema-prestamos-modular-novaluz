/**
 * INICIALIZACIÓN DEL SISTEMA MODULAR
 * Se ejecuta después de cargar todos los módulos
 */

// Función de inicialización global
function inicializarSistema() {
    console.log('🚀 Inicializando Sistema Modular Nova Luz...');
    
    // Verificar que todos los módulos estén cargados
    const modulosRequeridos = {
        'showTab': 'Módulo Navegación',
        'cargarClientes': 'Módulo Cliente', 
        'setRating': 'Módulo Rating',
        'mostrarFormularioCliente': 'Módulo Formulario Cliente',
        'cargarPrestamos': 'Módulo Préstamos'
    };
    
    let modulosFaltantes = [];
    
    for (const [funcion, modulo] of Object.entries(modulosRequeridos)) {
        if (typeof window[funcion] !== 'function') {
            modulosFaltantes.push(`${modulo} (${funcion})`);
            console.warn(`⚠️ Función faltante: ${funcion} del ${modulo}`);
        } else {
            console.log(`✅ ${modulo} cargado correctamente`);
        }
    }
    
    if (modulosFaltantes.length === 0) {
        console.log('✅ Todos los módulos cargados correctamente');
        
        // Inicializar la vista por defecto
        if (typeof window.showTab === 'function') {
            window.showTab('dashboard');
        }
        
        // Cargar datos iniciales
        if (typeof window.cargarClientes === 'function') {
            setTimeout(() => {
                window.cargarClientes();
            }, 500);
        }
        
    } else {
        console.error('❌ Módulos faltantes:', modulosFaltantes);
        
        // Mostrar error en la interfaz
        const dashboard = document.getElementById('dashboard');
        if (dashboard) {
            dashboard.innerHTML = `
                <div class="alert alert-danger">
                    <h3>❌ Error de Carga del Sistema</h3>
                    <p>Los siguientes módulos no se cargaron correctamente:</p>
                    <ul>
                        ${modulosFaltantes.map(m => `<li>${m}</li>`).join('')}
                    </ul>
                    <p>Por favor, recarga la página.</p>
                </div>
            `;
        }
    }
}

// Función para verificar funciones específicas
function verificarFuncion(nombreFuncion, modulo) {
    if (typeof window[nombreFuncion] === 'function') {
        console.log(`✅ ${nombreFuncion} disponible desde ${modulo}`);
        return true;
    } else {
        console.error(`❌ ${nombreFuncion} NO disponible desde ${modulo}`);
        return false;
    }
}

// Inicializar cuando el DOM esté listo y todos los scripts cargados
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para que todos los módulos se carguen
    setTimeout(inicializarSistema, 100);
});

// Exportar funciones de verificación
window.inicializarSistema = inicializarSistema;
window.verificarFuncion = verificarFuncion;
