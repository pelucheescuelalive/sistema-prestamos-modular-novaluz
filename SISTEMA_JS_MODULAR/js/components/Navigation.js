/* ===================================================================
   🎯 COMPONENTE NAVEGACIÓN ULTRA-PROFESIONAL
   Sistema de navegación modular con diseño oscuro premium
   =================================================================== */

export class NavigationComponent {
  constructor(app) {
    this.app = app;
    this.isCollapsed = false;
  }

  render() {
    const nav = document.getElementById('app-navigation');
    if (!nav) return;

    nav.innerHTML = `
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="#dashboard" class="nav-link" data-page="dashboard">
            <div class="nav-icon">
              <i class="fas fa-tachometer-alt"></i>
            </div>
            <span>Dashboard</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#clientes" class="nav-link" data-page="clientes">
            <div class="nav-icon">
              <i class="fas fa-users"></i>
            </div>
            <span>Clientes</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#prestamos" class="nav-link" data-page="prestamos">
            <div class="nav-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <span>Préstamos</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#pagos" class="nav-link" data-page="pagos">
            <div class="nav-icon">
              <i class="fas fa-credit-card"></i>
            </div>
            <span>Pagos</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#mora" class="nav-link" data-page="mora">
            <div class="nav-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <span>Mora</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#calculadora" class="nav-link" data-page="calculadora">
            <div class="nav-icon">
              <i class="fas fa-calculator"></i>
            </div>
            <span>Calculadora</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#reportes" class="nav-link" data-page="reportes">
            <div class="nav-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <span>Reportes</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#configuracion" class="nav-link" data-page="configuracion">
            <div class="nav-icon">
              <i class="fas fa-cog"></i>
            </div>
            <span>Configuración</span>
          </a>
        </li>
      </ul>
    `;

    this.setupEvents();
    this.setActiveLink('dashboard');
  }

  setupEvents() {
    // Eventos de navegación
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const page = link.dataset.page;
        this.setActiveLink(page);
        this.app.navigate(page);
      });
    });
  }

  setActiveLink(page) {
    // Remover clase activa de todos los enlaces
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Agregar clase activa al enlace actual
    const activeLink = document.querySelector(`[data-page="${page}"]`);
    if (activeLink) {
      activeLink.classList.add('active');
    }

    // Actualizar título de página
    const pageTitle = document.getElementById('page-title-text');
    if (pageTitle) {
      const titles = {
        dashboard: 'Dashboard',
        clientes: 'Gestión de Clientes',
        prestamos: 'Gestión de Préstamos',
        pagos: 'Registro de Pagos',
        mora: 'Control de Mora',
        calculadora: 'Calculadora Financiera',
        reportes: 'Reportes y Análisis',
        configuracion: 'Configuración del Sistema'
      };
      pageTitle.textContent = titles[page] || 'Dashboard';
    }

    // Actualizar icono de página
    const pageIcon = document.querySelector('.page-title-icon i');
    if (pageIcon) {
      const icons = {
        dashboard: 'fas fa-tachometer-alt',
        clientes: 'fas fa-users',
        prestamos: 'fas fa-money-bill-wave',
        pagos: 'fas fa-credit-card',
        mora: 'fas fa-exclamation-triangle',
        calculadora: 'fas fa-calculator',
        reportes: 'fas fa-chart-bar',
        configuracion: 'fas fa-cog'
      };
      pageIcon.className = icons[page] || 'fas fa-tachometer-alt';
    }
  }

  getCurrentPage() {
    const activeLink = document.querySelector('.nav-link.active');
    return activeLink ? activeLink.dataset.page : 'dashboard';
  }
}
