/* ===================================================================
   🎯 COMPONENTE TOAST NOTIFICATIONS
   Sistema de notificaciones emergentes
   =================================================================== */

export class ToastComponent {
  constructor(app = null) {
    this.app = app;
    this.toasts = [];
    this.container = null;
    this.init();
  }

  init() {
    // Crear contenedor de toasts si no existe
    if (!document.getElementById('toast-container')) {
      const container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    this.container = document.getElementById('toast-container');
  }

  render() {
    // El Toast se renderiza dinámicamente cuando se llama show()
    // Solo aseguramos que el contenedor existe
    this.init();
    console.log('✅ Toast component ready');
  }

  show(message, options = {}) {
    const {
      type = 'info', // success, error, warning, info
      duration = 5000,
      closable = true,
      icon = null,
      title = null,
      position = 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
      onClick = null,
      onClose = null
    } = options;

    const toastId = 'toast-' + Date.now() + Math.random().toString(36).substr(2, 9);
    
    const toast = {
      id: toastId,
      type,
      message,
      title,
      duration,
      closable,
      icon: icon || this.getDefaultIcon(type),
      onClick,
      onClose
    };

    this.toasts.push(toast);
    this.render(toast);

    // Auto-cerrar si tiene duración
    if (duration > 0) {
      setTimeout(() => {
        this.hide(toastId);
      }, duration);
    }

    return toastId;
  }

  hide(toastId) {
    const toastElement = document.getElementById(toastId);
    if (!toastElement) return;

    // Animación de salida
    toastElement.classList.add('toast-hiding');
    
    setTimeout(() => {
      if (toastElement.parentNode) {
        toastElement.parentNode.removeChild(toastElement);
      }
      
      // Remover del array
      this.toasts = this.toasts.filter(t => t.id !== toastId);
      
      // Callback de cierre
      const toast = this.toasts.find(t => t.id === toastId);
      if (toast && toast.onClose) {
        toast.onClose();
      }
    }, 300);
  }

  render(toast) {
    const toastHTML = `
      <div id="${toast.id}" class="toast toast-${toast.type}">
        <div class="toast-content">
          ${toast.icon ? `<div class="toast-icon">${toast.icon}</div>` : ''}
          <div class="toast-body">
            ${toast.title ? `<div class="toast-title">${toast.title}</div>` : ''}
            <div class="toast-message">${toast.message}</div>
          </div>
          ${toast.closable ? `
            <button class="toast-close" onclick="window.toastManager.hide('${toast.id}')">
              <i class="fas fa-times"></i>
            </button>
          ` : ''}
        </div>
        <div class="toast-progress"></div>
      </div>
    `;

    this.container.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toast.id);
    
    // Configurar evento de click
    if (toast.onClick) {
      toastElement.addEventListener('click', toast.onClick);
    }

    // Animación de entrada
    requestAnimationFrame(() => {
      toastElement.classList.add('toast-show');
    });

    // Animación de progreso si tiene duración
    if (toast.duration > 0) {
      const progressBar = toastElement.querySelector('.toast-progress');
      if (progressBar) {
        progressBar.style.animationDuration = toast.duration + 'ms';
        progressBar.classList.add('toast-progress-active');
      }
    }
  }

  getDefaultIcon(type) {
    const icons = {
      success: '<i class="fas fa-check-circle"></i>',
      error: '<i class="fas fa-exclamation-circle"></i>',
      warning: '<i class="fas fa-exclamation-triangle"></i>',
      info: '<i class="fas fa-info-circle"></i>'
    };
    return icons[type] || icons.info;
  }

  clear() {
    this.toasts.forEach(toast => {
      this.hide(toast.id);
    });
  }

  // Métodos de conveniencia
  success(message, options = {}) {
    return this.show(message, { ...options, type: 'success' });
  }

  error(message, options = {}) {
    return this.show(message, { ...options, type: 'error', duration: 8000 });
  }

  warning(message, options = {}) {
    return this.show(message, { ...options, type: 'warning', duration: 6000 });
  }

  info(message, options = {}) {
    return this.show(message, { ...options, type: 'info' });
  }

  // Toast para operaciones específicas del sistema de préstamos
  clienteCreado(nombre) {
    return this.success(`Cliente "${nombre}" creado exitosamente`, {
      title: 'Cliente Registrado',
      icon: '<i class="fas fa-user-plus"></i>'
    });
  }

  prestamoCreado(cliente, monto) {
    return this.success(`Préstamo de $${monto.toLocaleString()} otorgado a ${cliente}`, {
      title: 'Préstamo Aprobado',
      icon: '<i class="fas fa-money-bill-wave"></i>'
    });
  }

  pagoRegistrado(monto) {
    return this.success(`Pago de $${monto.toLocaleString()} registrado correctamente`, {
      title: 'Pago Recibido',
      icon: '<i class="fas fa-credit-card"></i>'
    });
  }

  errorOperacion(operacion, detalle = '') {
    return this.error(`Error en ${operacion}. ${detalle}`, {
      title: 'Error de Operación',
      icon: '<i class="fas fa-exclamation-circle"></i>'
    });
  }

  advertenciaMora(cliente, dias) {
    return this.warning(`${cliente} tiene ${dias} días de atraso en sus pagos`, {
      title: 'Alerta de Mora',
      icon: '<i class="fas fa-clock"></i>',
      duration: 8000
    });
  }
}

// Instancia global para fácil acceso
if (typeof window !== 'undefined') {
  window.toastManager = new ToastComponent();
}

export default ToastComponent;
