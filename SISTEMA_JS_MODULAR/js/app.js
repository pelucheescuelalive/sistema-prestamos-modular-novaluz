/**
 * 🚀 Sistema Nova Luz Pro - Aplicación Principal SPA
 * Arquitectura moderna ES6 con componentes modulares
 */

class SistemaNovaLuz {
  constructor() {
    console.log('🎯 Iniciando Sistema Nova Luz Pro...');
    
    this.currentPage = null;
    this.pages = new Map();
    this.components = new Map();
    this.isLoading = false;
    
    // Estado global
    this.appState = {
      clientes: JSON.parse(localStorage.getItem('clientes') || '[]'),
      prestamos: JSON.parse(localStorage.getItem('prestamos') || '[]'),
      pagos: JSON.parse(localStorage.getItem('pagos') || '[]'),
      configuracion: JSON.parse(localStorage.getItem('configuracion') || '{}')
    };
    
    // Configuración de páginas (lazy loading)
    this.pageLoaders = {
      diagnostico: () => import('./pages/diagnostico.js'),
      test: () => import('./pages/test.js'),
      dashboard: () => import('./pages/dashboard.js'),
      clientes: () => import('./pages/clientes.js'),
      prestamos: () => import('./pages/prestamos.js'),
      pagos: () => import('./pages/pagos.js'),
      mora: () => import('./pages/mora.js'),
      calculadora: () => import('./pages/calculadora.js'),
      reportes: () => import('./pages/reportes.js'),
      configuracion: () => import('./pages/configuracion.js')
    };
    
    // Inicializar
    this.init();
  }

  async init() {
    try {
      console.log('🔧 Inicializando componentes...');
      
      // 1. Cargar componentes principales
      await this.loadComponents();
      
      // 2. Configurar interfaz
      this.setupUI();
      
      // 3. Configurar navegación
      this.setupRouter();
      
      // 4. Cargar página inicial
      await this.loadInitialPage();
      
      // 5. Mostrar aplicación
      this.showApp();
      
      console.log('✅ Sistema iniciado correctamente');
      
    } catch (error) {
      console.error('❌ Error crítico:', error);
      this.handleCriticalError(error);
    }
  }

  async loadComponents() {
    try {
      console.log('📦 Cargando componentes...');
      
      // Cargar componentes base con manejo individual de errores
      const loadComponent = async (path, className) => {
        try {
          const module = await import(path);
          return module[className] || null;
        } catch (error) {
          console.warn(`⚠️ No se pudo cargar ${className}:`, error.message);
          return null;
        }
      };
      
      // Cargar cada componente
      const HeaderComponent = await loadComponent('./components/Header.js', 'HeaderComponent');
      const NavigationComponent = await loadComponent('./components/Navigation.js', 'NavigationComponent');
      const ModalComponent = await loadComponent('./components/Modal.js', 'ModalComponent');
      const ToastComponent = await loadComponent('./components/Toast.js', 'ToastComponent');
      
      // Instanciar componentes disponibles
      if (HeaderComponent) {
        this.components.set('header', new HeaderComponent(this));
        console.log('✅ Header cargado');
      }
      
      if (NavigationComponent) {
        this.components.set('navigation', new NavigationComponent(this));
        console.log('✅ Navigation cargado');
      }
      
      if (ModalComponent) {
        this.components.set('modal', new ModalComponent(this));
        window.modal = this.components.get('modal');
        console.log('✅ Modal cargado');
      }
      
      if (ToastComponent) {
        this.components.set('toast', new ToastComponent(this));
        window.toast = this.components.get('toast');
        console.log('✅ Toast cargado');
      }
      
      console.log('✅ Componentes cargados:', this.components.size);
      
    } catch (error) {
      console.error('❌ Error cargando componentes:', error);
      // No lanzar el error para permitir que la app continue funcionando
    }
  }

  setupUI() {
    console.log('🎨 Configurando interfaz...');
    
    // Renderizar componentes principales con manejo seguro
    try {
      const header = this.components.get('header');
      if (header && typeof header.render === 'function') {
        header.render();
        console.log('✅ Header renderizado');
      } else {
        console.warn('⚠️ Header no disponible');
        this.renderFallbackHeader();
      }
      
      // FORZAR SIEMPRE EL MENÚ COMPLETO - Solución temporal
      console.log('🔧 FORZANDO MENÚ COMPLETO - Todas las 8 páginas + herramientas');
      this.renderFallbackNavigation();
      
      
      const modal = this.components.get('modal');
      if (modal && typeof modal.render === 'function') {
        modal.render();
        console.log('✅ Modal renderizado');
      } else {
        console.warn('⚠️ Modal no disponible');
      }
      
      const toast = this.components.get('toast');
      if (toast && typeof toast.render === 'function') {
        toast.render();
        console.log('✅ Toast renderizado');
      } else {
        console.warn('⚠️ Toast no disponible');
      }
    } catch (error) {
      console.error('❌ Error renderizando componentes:', error);
      this.renderFallbackComponents();
    }
    
    // Configurar tema
    this.initializeTheme();
    
    // Eventos globales
    this.setupGlobalEvents();
  }

  renderFallbackHeader() {
    const header = document.getElementById('app-header');
    if (header) {
      header.innerHTML = `
        <div class="header-content">
          <div class="header-left">
            <div class="logo">
              <span class="logo-icon">🏦</span>
              <span class="logo-text">Sistema Nova Luz</span>
            </div>
          </div>
          <div class="header-center">
            <h1 id="page-title" class="page-title">Sistema de Préstamos</h1>
          </div>
        </div>
      `;
    }
  }

  renderCompleteNavigation() {
    const nav = document.getElementById('app-navigation');
    if (nav) {
      nav.innerHTML = `
        <div class="nav-header">
          <div class="nav-brand">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Menú Principal</span>
          </div>
        </div>
        <ul class="nav-menu">
          <li class="nav-item">
            <a href="#dashboard" class="nav-link" data-page="dashboard">
              <i class="fas fa-tachometer-alt"></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#clientes" class="nav-link" data-page="clientes">
              <i class="fas fa-users"></i>
              <span class="nav-text">Clientes</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#prestamos" class="nav-link" data-page="prestamos">
              <i class="fas fa-money-bill-wave"></i>
              <span class="nav-text">Préstamos</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#pagos" class="nav-link" data-page="pagos">
              <i class="fas fa-credit-card"></i>
              <span class="nav-text">Pagos</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#mora" class="nav-link" data-page="mora">
              <i class="fas fa-exclamation-triangle"></i>
              <span class="nav-text">Mora</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#calculadora" class="nav-link" data-page="calculadora">
              <i class="fas fa-calculator"></i>
              <span class="nav-text">Calculadora</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#reportes" class="nav-link" data-page="reportes">
              <i class="fas fa-chart-bar"></i>
              <span class="nav-text">Reportes</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#configuracion" class="nav-link" data-page="configuracion">
              <i class="fas fa-cogs"></i>
              <span class="nav-text">Configuración</span>
            </a>
          </li>
          <li class="nav-divider"></li>
          <li class="nav-item">
            <a href="#diagnostico" class="nav-link" data-page="diagnostico">
              <i class="fas fa-stethoscope"></i>
              <span class="nav-text">Diagnóstico</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#test" class="nav-link" data-page="test">
              <i class="fas fa-flask"></i>
              <span class="nav-text">Test</span>
            </a>
          </li>
        </ul>
      `;
    }
  }

  renderFallbackNavigation() {
    const nav = document.getElementById('app-navigation');
    if (nav) {
      nav.innerHTML = `
        <div class="nav-header">
          <div class="nav-brand">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Menú Principal</span>
          </div>
        </div>
        <ul class="nav-menu">
          <li class="nav-item">
            <a href="#dashboard" class="nav-link" data-page="dashboard">
              <i class="fas fa-tachometer-alt"></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#clientes" class="nav-link" data-page="clientes">
              <i class="fas fa-users"></i>
              <span class="nav-text">Clientes</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#prestamos" class="nav-link" data-page="prestamos">
              <i class="fas fa-money-bill-wave"></i>
              <span class="nav-text">Préstamos</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#pagos" class="nav-link" data-page="pagos">
              <i class="fas fa-credit-card"></i>
              <span class="nav-text">Pagos</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#mora" class="nav-link" data-page="mora">
              <i class="fas fa-exclamation-triangle"></i>
              <span class="nav-text">Mora</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#calculadora" class="nav-link" data-page="calculadora">
              <i class="fas fa-calculator"></i>
              <span class="nav-text">Calculadora</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#reportes" class="nav-link" data-page="reportes">
              <i class="fas fa-chart-bar"></i>
              <span class="nav-text">Reportes</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#configuracion" class="nav-link" data-page="configuracion">
              <i class="fas fa-cogs"></i>
              <span class="nav-text">Configuración</span>
            </a>
          </li>
          <li class="nav-divider"></li>
          <li class="nav-item">
            <a href="#diagnostico" class="nav-link" data-page="diagnostico">
              <i class="fas fa-stethoscope"></i>
              <span class="nav-text">Diagnóstico</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#test" class="nav-link" data-page="test">
              <i class="fas fa-flask"></i>
              <span class="nav-text">Test</span>
            </a>
          </li>
        </ul>
      `;
    }
  }

  renderFallbackComponents() {
    console.log('🆘 Renderizando componentes de emergencia...');
    this.renderFallbackHeader();
    this.renderFallbackNavigation();
  }

  setupRouter() {
    console.log('🧭 Configurando navegación...');
    
    // Escuchar cambios de hash
    window.addEventListener('hashchange', () => {
      this.handleRoute();
    });

    // Interceptar clicks de navegación
    document.addEventListener('click', (e) => {
      const navLink = e.target.closest('[data-page]');
      if (navLink) {
        e.preventDefault();
        const page = navLink.getAttribute('data-page');
        this.navigateTo(page);
        return;
      }

      const hashLink = e.target.closest('a[href^="#"]');
      if (hashLink) {
        e.preventDefault();
        const page = hashLink.getAttribute('href').slice(1);
        if (page && this.pageLoaders[page]) {
          this.navigateTo(page);
        }
        return;
      }
    });

    // Historial del navegador
    window.addEventListener('popstate', (e) => {
      if (e.state?.page) {
        this.navigateTo(e.state.page, false);
      }
    });
  }

  async handleRoute() {
    const hash = window.location.hash.slice(1) || 'dashboard';
    await this.navigateTo(hash, false);
  }

  async navigateTo(page, updateHistory = true) {
    try {
      console.log(`🔄 Navegando a: ${page}`);
      
      // Validar página
      if (!this.pageLoaders[page]) {
        console.warn(`⚠️ Página '${page}' no encontrada`);
        page = 'dashboard';
      }
      
      // Evitar navegación duplicada
      if (this.currentPage === page && !this.isLoading) {
        return;
      }
      
      // Mostrar loading
      this.showLoading();
      
      try {
        // Cargar página si no está en caché
        if (!this.pages.has(page)) {
          console.log(`📥 Cargando módulo: ${page}`);
          const module = await this.pageLoaders[page]();
          console.log(`📦 Módulo cargado para ${page}:`, module);
          console.log(`📝 Propiedades del módulo:`, Object.keys(module));
          
          // Obtener clase de página
          const PageClass = this.getPageClass(module, page);
          if (PageClass) {
            console.log(`🎯 Clase encontrada: ${PageClass.name}`);
            this.pages.set(page, new PageClass(this));
            console.log(`✅ Instancia creada para página: ${page}`);
          } else {
            throw new Error(`Clase de página no encontrada para: ${page}`);
          }
        }
        
        // Renderizar página
        const pageInstance = this.pages.get(page);
        if (pageInstance) {
          console.log(`🎨 Renderizando página: ${page}`);
          const content = await pageInstance.render();
          const mainContent = document.getElementById('main-content');
          
          if (mainContent) {
            if (content) {
              mainContent.innerHTML = content;
              console.log(`✅ Contenido insertado para página: ${page}`);
              
              // Inicializar página
              if (typeof pageInstance.init === 'function') {
                await pageInstance.init();
                console.log(`🔧 Página ${page} inicializada`);
              }
              
              // Configurar instancias globales para eventos onclick
              this.configurarInstanciaGlobal(page, pageInstance);
              
              // Actualizar estado
              this.currentPage = page;
              this.updateActiveNavigation(page);
              
              // Actualizar historial
              if (updateHistory) {
                window.history.pushState({ page }, '', `#${page}`);
              }
              
              // Actualizar título
              this.updatePageTitle(page);
              
              console.log(`✅ Página '${page}' cargada completamente`);
            } else {
              console.error(`❌ El contenido de la página ${page} está vacío`);
              throw new Error(`Contenido vacío para página: ${page}`);
            }
          } else {
            console.error('❌ Elemento main-content no encontrado');
            throw new Error('Elemento main-content no encontrado en el DOM');
          }
        } else {
          console.error(`❌ Instancia de página no encontrada: ${page}`);
          throw new Error(`Instancia de página no encontrada: ${page}`);
        }
        
      } catch (pageError) {
        console.error(`❌ Error cargando página ${page}:`, pageError);
        
        // Fallback al dashboard o mostrar página de error
        if (page !== 'dashboard') {
          await this.navigateTo('dashboard');
        } else {
          this.showErrorPage(pageError);
        }
      }
      
      this.hideLoading();
      
    } catch (error) {
      console.error('❌ Error crítico en navegación:', error);
      this.hideLoading();
      this.handleCriticalError(error);
    }
  }

  getPageClass(module, pageName) {
    console.log(`🔍 Buscando clase para página: ${pageName}`, module);
    
    const classNames = {
      'diagnostico': 'DiagnosticoPage',
      'test': 'TestPage',
      'dashboard': 'DashboardPage',
      'clientes': 'ClientesPage', 
      'prestamos': 'PrestamosPage',
      'pagos': 'PagosPage',
      'mora': 'MoraPage',
      'calculadora': 'CalculadoraPage',
      'reportes': 'ReportesPage',
      'configuracion': 'ConfiguracionPage'
    };
    
    const className = classNames[pageName];
    
    // Intentar múltiples formas de encontrar la clase
    if (className) {
      // 1. Exportación nombrada directa
      if (module[className]) {
        console.log(`✅ Clase encontrada como exportación nombrada: ${className}`);
        return module[className];
      }
      
      // 2. Exportación default
      if (module.default && module.default.name === className) {
        console.log(`✅ Clase encontrada como exportación default: ${className}`);
        return module.default;
      }
      
      // 3. Primer valor si hay solo uno
      const values = Object.values(module);
      if (values.length === 1 && typeof values[0] === 'function') {
        console.log(`✅ Clase encontrada como único valor: ${values[0].name}`);
        return values[0];
      }
    }
    
    console.error(`❌ No se pudo encontrar la clase ${className} en el módulo`, module);
    return null;
  }

  updateActiveNavigation(page) {
    // Remover activas
    document.querySelectorAll('.nav-link').forEach(link => {
      link.classList.remove('active');
    });
    
    // Marcar activa
    const activeLink = document.querySelector(`[data-page="${page}"]`);
    if (activeLink) {
      activeLink.classList.add('active');
    }
  }

  updatePageTitle(page) {
    const titles = {
      dashboard: 'Dashboard',
      clientes: 'Gestión de Clientes', 
      prestamos: 'Gestión de Préstamos',
      pagos: 'Gestión de Pagos',
      mora: 'Control de Mora',
      calculadora: 'Calculadora Financiera',
      reportes: 'Reportes y Análisis',
      configuracion: 'Configuración del Sistema'
    };
    
    document.title = `Nova Luz Pro - ${titles[page] || 'Sistema de Préstamos'}`;
  }

  async loadInitialPage() {
    const initialPage = window.location.hash.slice(1) || 'dashboard';
    await this.navigateTo(initialPage, false);
  }

  setupGlobalEvents() {
    document.addEventListener('click', (e) => {
      // Toggle menú
      if (e.target.closest('#menu-toggle')) {
        this.toggleSidebar();
      }
      
      // Toggle tema
      if (e.target.closest('#theme-toggle')) {
        this.toggleTheme();
      }
    });
  }

  initializeTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
    
    const icon = document.querySelector('#theme-toggle i');
    if (icon) {
      icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
  }

  toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const newTheme = current === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    const icon = document.querySelector('#theme-toggle i');
    if (icon) {
      icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
    
    this.showToast(`Tema ${newTheme === 'dark' ? 'oscuro' : 'claro'} activado`);
  }

  toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main-content');
    
    if (sidebar) sidebar.classList.toggle('collapsed');
    if (main) main.classList.toggle('expanded');
  }

  showLoading() {
    this.isLoading = true;
    const loading = document.getElementById('loading-overlay');
    if (loading) loading.style.display = 'flex';
  }

  hideLoading() {
    this.isLoading = false;
    const loading = document.getElementById('loading-overlay');
    if (loading) loading.style.display = 'none';
  }

  showApp() {
    const app = document.getElementById('app');
    if (app) {
      app.style.opacity = '1';
      app.style.visibility = 'visible';
    }
    
    // Ocultar splash
    const splash = document.getElementById('loading-screen');
    if (splash) {
      setTimeout(() => splash.style.display = 'none', 800);
    }
  }

  showErrorPage(error) {
    const mainContent = document.getElementById('main-content');
    if (mainContent) {
      mainContent.innerHTML = `
        <div class="error-page">
          <div class="error-container">
            <h1>⚠️ Error del Sistema</h1>
            <p>Ha ocurrido un error al cargar la aplicación.</p>
            <details>
              <summary>Detalles técnicos</summary>
              <pre>${error.message || error}</pre>
            </details>
            <button onclick="window.location.reload()" class="btn btn-primary">
              🔄 Recargar Aplicación
            </button>
          </div>
        </div>
      `;
    }
  }

  handleCriticalError(error) {
    console.error('💥 Error crítico del sistema:', error);
    
    this.hideLoading();
    
    // Mostrar error de emergencia
    document.body.innerHTML = `
      <div style="
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh; 
        background: #1a1a2e; 
        color: white; 
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 2rem;
      ">
        <div>
          <h1>🚨 Error Crítico</h1>
          <p>El sistema no puede iniciarse correctamente.</p>
          <button 
            onclick="localStorage.clear(); window.location.reload();" 
            style="
              background: #3b82f6; 
              color: white; 
              border: none; 
              padding: 1rem 2rem; 
              border-radius: 8px; 
              cursor: pointer; 
              margin-top: 1rem;
            "
          >
            🔄 Resetear y Recargar
          </button>
        </div>
      </div>
    `;
  }

  showToast(message, type = 'info') {
    if (window.toast) {
      window.toast.show(message, type);
    } else {
      console.log(`Toast: ${message}`);
    }
  }

  // Métodos de datos
  saveData(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
    this.appState[key] = data;
  }

  getData(key) {
    return this.appState[key] || [];
  }

  updateData(key, id, updatedItem) {
    const data = this.getData(key);
    const index = data.findIndex(item => item.id === id);
    if (index !== -1) {
      data[index] = { ...data[index], ...updatedItem };
      this.saveData(key, data);
    }
  }

  deleteData(key, id) {
    const data = this.getData(key);
    const filtered = data.filter(item => item.id !== id);
    this.saveData(key, filtered);
  }

  configurarInstanciaGlobal(page, pageInstance) {
    // Configurar instancias globales para eventos onclick
    switch(page) {
      case 'clientes':
        window.clientesPage = pageInstance;
        console.log('🔗 Instancia global clientesPage configurada');
        break;
      case 'prestamos':
        window.prestamosPage = pageInstance;
        console.log('🔗 Instancia global prestamosPage configurada');
        break;
      case 'pagos':
        window.pagosPage = pageInstance;
        console.log('🔗 Instancia global pagosPage configurada');
        break;
      case 'mora':
        window.moraPage = pageInstance;
        console.log('🔗 Instancia global moraPage configurada');
        break;
      case 'dashboard':
        window.dashboardPage = pageInstance;
        console.log('🔗 Instancia global dashboardPage configurada');
        break;
      default:
        console.log(`📝 Página ${page} no requiere instancia global`);
        break;
    }
  }
}

// 🚀 INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', () => {
  console.log('🌟 DOM Ready - Iniciando Nova Luz Pro...');
  
  try {
    window.app = new SistemaNovaLuz();
  } catch (error) {
    console.error('💥 Error fatal al inicializar:', error);
    
    document.body.innerHTML = `
      <div style="
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh; 
        background: #ef4444; 
        color: white; 
        font-family: Arial, sans-serif;
        text-align: center;
      ">
        <div>
          <h1>💥 Error Fatal</h1>
          <p>No se puede inicializar el sistema</p>
          <button onclick="window.location.reload()">Recargar</button>
        </div>
      </div>
    `;
  }
});

// Exportar para debugging
window.SistemaNovaLuz = SistemaNovaLuz;

// Exportar la clase para módulos ES6
export { SistemaNovaLuz };
