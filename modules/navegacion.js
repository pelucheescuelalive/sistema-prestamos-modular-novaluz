/**
 * MÓDULO NAVEGACIÓN - SISTEMA DE TABS
 * Sistema Modular de Préstamos Nova Luz
 */

/**
 * Mostrar tab específico
 */
function showTab(tabName) {
    try {
        console.log('🔄 Navegando a:', tabName);
        
        // Remover clase activa de todos los botones
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Ocultar todas las vistas
        document.querySelectorAll('.vista').forEach(vista => {
            vista.style.display = 'none';
        });
        
        // Activar botón correspondiente
        const botonActivo = document.querySelector(`[onclick*="showTab('${tabName}')"]`);
        if (botonActivo) {
            botonActivo.classList.add('active');
        }
        
        // Mostrar vista correspondiente
        const vista = document.getElementById(`vista-${tabName}`);
        if (vista) {
            vista.style.display = 'block';
            
            // Cargar datos específicos del módulo
            cargarDatosTab(tabName);
            
            console.log('✅ Navegación exitosa a:', tabName);
        } else {
            console.error('❌ Vista no encontrada:', `vista-${tabName}`);
        }
        
    } catch (error) {
        console.error('❌ Error en navegación:', error);
    }
}

/**
 * Cargar datos específicos según el tab
 */
function cargarDatosTab(tabName) {
    switch(tabName) {
        case 'dashboard':
            if (typeof cargarEstadisticas === 'function') {
                cargarEstadisticas();
            }
            break;
            
        case 'clientes':
            if (typeof cargarClientes === 'function') {
                cargarClientes();
            }
            break;
            
        case 'prestamos':
            if (typeof cargarPrestamos === 'function') {
                cargarPrestamos();
            }
            break;
            
        case 'pagos':
            if (typeof cargarPagos === 'function') {
                cargarPagos();
            }
            break;
            
        case 'mora':
            if (typeof cargarMora === 'function') {
                cargarMora();
            }
            break;
            
        case 'calculadora':
            if (typeof inicializarCalculadora === 'function') {
                inicializarCalculadora();
            }
            break;
            
        case 'reportes':
            if (typeof cargarReportes === 'function') {
                cargarReportes();
            }
            break;
            
        case 'configuracion':
            if (typeof cargarConfiguracion === 'function') {
                cargarConfiguracion();
            }
            break;
            
        case 'nuevo-cliente':
            if (typeof inicializarFormularioCliente === 'function') {
                inicializarFormularioCliente();
            }
            break;
    }
}

/**
 * Inicializar navegación al cargar la página
 */
function inicializarNavegacion() {
    console.log('🚀 Inicializando sistema de navegación');
    
    // Mostrar dashboard por defecto
    showTab('dashboard');
    
    // Configurar eventos de teclado para navegación
    document.addEventListener('keydown', function(e) {
        // Alt + número para cambiar tabs
        if (e.altKey && e.key >= '1' && e.key <= '9') {
            e.preventDefault();
            const tabIndex = parseInt(e.key) - 1;
            const botones = document.querySelectorAll('.tab-btn');
            if (botones[tabIndex]) {
                const onclick = botones[tabIndex].getAttribute('onclick');
                if (onclick) {
                    const match = onclick.match(/showTab\('([^']+)'\)/);
                    if (match) {
                        showTab(match[1]);
                    }
                }
            }
        }
    });
}

/**
 * Verificar que todas las vistas requeridas existan
 */
function verificarSistema() {
    const vistasRequeridas = [
        'dashboard',
        'clientes', 
        'prestamos',
        'pagos',
        'mora',
        'calculadora',
        'reportes',
        'configuracion',
        'nuevo-cliente'
    ];
    
    const vistasExistentes = [];
    const vistasFaltantes = [];
    
    vistasRequeridas.forEach(vista => {
        const elemento = document.getElementById(`vista-${vista}`);
        if (elemento) {
            vistasExistentes.push(vista);
        } else {
            vistasFaltantes.push(vista);
        }
    });
    
    console.log('✅ Vistas existentes:', vistasExistentes);
    if (vistasFaltantes.length > 0) {
        console.warn('⚠️ Vistas faltantes:', vistasFaltantes);
    }
    
    return vistasFaltantes.length === 0;
}

// Inicializar cuando se carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    inicializarNavegacion();
    verificarSistema();
});

// Exportar funciones al ámbito global
window.showTab = showTab;
