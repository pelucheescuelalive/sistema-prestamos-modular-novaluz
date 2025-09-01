/* ===================================================================
   🎯 COMPONENTE HEADER
   Header responsive con navegación y controles
   =================================================================== */

export class HeaderComponent {
  constructor(app) {
    this.app = app;
  }

  render() {
    const header = document.getElementById('app-header');
    if (!header) return;

    header.innerHTML = `
      <div class="header-content">
        <div class="header-left">
          <button id="menu-toggle" class="menu-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <div class="logo">
            <span class="logo-icon">🏦</span>
            <span class="logo-text">SistemaPrestamosPro</span>
            <span class="logo-badge">JS</span>
          </div>
        </div>
        
        <div class="header-center">
          <h1 id="page-title" class="page-title">Dashboard</h1>
        </div>
        
        <div class="header-right">
          <button id="theme-toggle" class="theme-toggle" title="Cambiar tema">
            <i class="fas fa-moon"></i>
          </button>
          <button id="notification-toggle" class="notification-toggle" title="Notificaciones">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" style="display: none;">0</span>
          </button>
          <div class="user-info">
            <span class="user-icon">👤</span>
            <span class="user-name">Administrador</span>
            <span class="user-flag">🇩🇴</span>
          </div>
        </div>
      </div>
    `;

    this.setupEvents();
  }

  setupEvents() {
    // El evento del menu-toggle se maneja en app.js
    // Aquí podemos agregar otros eventos específicos del header
  }

  updateTitle(title) {
    const titleElement = document.getElementById('page-title');
    if (titleElement) {
      titleElement.textContent = title;
    }
  }

  updateNotifications(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
      badge.textContent = count;
      badge.style.display = count > 0 ? 'inline' : 'none';
    }
  }
}
