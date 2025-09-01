/* ===================================================================
   🎯 COMPONENTE MODAL
   Sistema modular de ventanas modales
   =================================================================== */

export class ModalComponent {
  constructor(app = null) {
    this.app = app;
    this.currentModal = null;
    this.callbacks = {};
  }

  render() {
    // El Modal se renderiza dinámicamente cuando se llama show()
    // Aquí solo preparamos el sistema
    console.log('✅ Modal component ready');
  }

  show(options = {}) {
    const {
      title = 'Modal',
      content = '',
      size = 'medium', // small, medium, large, xlarge
      type = 'default', // default, confirm, alert, form
      buttons = [],
      closable = true,
      backdrop = true,
      onShow = null,
      onHide = null,
      onConfirm = null,
      onCancel = null
    } = options;

    // Remover modal existente si hay uno
    this.hide();

    // Crear estructura del modal
    const modalHTML = `
      <div class="modal-overlay ${backdrop ? 'with-backdrop' : ''}" data-modal-overlay>
        <div class="modal modal-${size} modal-${type}" data-modal>
          ${closable ? `
            <button class="modal-close" data-modal-close>
              <i class="fas fa-times"></i>
            </button>
          ` : ''}
          
          <div class="modal-header">
            <h3 class="modal-title">${title}</h3>
          </div>
          
          <div class="modal-body">
            ${content}
          </div>
          
          ${buttons.length > 0 ? `
            <div class="modal-footer">
              ${this.renderButtons(buttons)}
            </div>
          ` : ''}
        </div>
      </div>
    `;

    // Insertar en el DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    this.currentModal = document.querySelector('[data-modal-overlay]:last-child');

    // Guardar callbacks
    this.callbacks = { onShow, onHide, onConfirm, onCancel };

    // Configurar eventos
    this.setupEvents();

    // Mostrar modal con animación
    requestAnimationFrame(() => {
      this.currentModal.classList.add('show');
      if (onShow) onShow();
    });

    // Focus automático en el primer input si es un formulario
    if (type === 'form') {
      setTimeout(() => {
        const firstInput = this.currentModal.querySelector('input, textarea, select');
        if (firstInput) firstInput.focus();
      }, 150);
    }

    return this;
  }

  hide() {
    if (!this.currentModal) return;

    this.currentModal.classList.remove('show');
    
    setTimeout(() => {
      if (this.currentModal && this.currentModal.parentNode) {
        this.currentModal.parentNode.removeChild(this.currentModal);
      }
      this.currentModal = null;
      if (this.callbacks.onHide) this.callbacks.onHide();
    }, 300);

    return this;
  }

  renderButtons(buttons) {
    return buttons.map(btn => {
      const {
        text = 'Button',
        type = 'default', // primary, secondary, success, warning, danger
        action = 'close',
        disabled = false,
        callback = null
      } = btn;

      return `
        <button class="btn btn-${type}" 
                data-modal-action="${action}" 
                ${disabled ? 'disabled' : ''}
                ${callback ? `data-callback="${callback.name}"` : ''}>
          ${text}
        </button>
      `;
    }).join('');
  }

  setupEvents() {
    if (!this.currentModal) return;

    // Cerrar con X
    const closeBtn = this.currentModal.querySelector('[data-modal-close]');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.hide());
    }

    // Cerrar con backdrop
    const overlay = this.currentModal.querySelector('[data-modal-overlay]');
    if (overlay) {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) this.hide();
      });
    }

    // Cerrar con ESC
    const escHandler = (e) => {
      if (e.key === 'Escape') {
        this.hide();
        document.removeEventListener('keydown', escHandler);
      }
    };
    document.addEventListener('keydown', escHandler);

    // Botones de acción
    const actionButtons = this.currentModal.querySelectorAll('[data-modal-action]');
    actionButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const action = btn.dataset.modalAction;
        
        switch (action) {
          case 'close':
            this.hide();
            break;
          case 'confirm':
            if (this.callbacks.onConfirm) {
              const result = this.callbacks.onConfirm();
              if (result !== false) this.hide();
            } else {
              this.hide();
            }
            break;
          case 'cancel':
            if (this.callbacks.onCancel) this.callbacks.onCancel();
            this.hide();
            break;
        }
      });
    });
  }

  // Métodos de conveniencia
  static alert(message, title = 'Alerta') {
    const modal = new ModalComponent();
    return modal.show({
      title,
      content: `<p class="modal-alert-message">${message}</p>`,
      type: 'alert',
      size: 'small',
      buttons: [
        { text: 'Aceptar', type: 'primary', action: 'close' }
      ]
    });
  }

  static confirm(message, title = 'Confirmar') {
    return new Promise((resolve) => {
      const modal = new ModalComponent();
      modal.show({
        title,
        content: `<p class="modal-confirm-message">${message}</p>`,
        type: 'confirm',
        size: 'small',
        buttons: [
          { text: 'Cancelar', type: 'secondary', action: 'cancel' },
          { text: 'Confirmar', type: 'primary', action: 'confirm' }
        ],
        onConfirm: () => resolve(true),
        onCancel: () => resolve(false)
      });
    });
  }

  static prompt(message, defaultValue = '', title = 'Ingreso de datos') {
    return new Promise((resolve) => {
      const modal = new ModalComponent();
      const inputId = 'modal-prompt-input';
      
      modal.show({
        title,
        content: `
          <p class="modal-prompt-message">${message}</p>
          <input type="text" id="${inputId}" class="form-control" value="${defaultValue}" placeholder="Ingrese el valor...">
        `,
        type: 'form',
        size: 'medium',
        buttons: [
          { text: 'Cancelar', type: 'secondary', action: 'cancel' },
          { text: 'Aceptar', type: 'primary', action: 'confirm' }
        ],
        onConfirm: () => {
          const input = document.getElementById(inputId);
          const value = input ? input.value.trim() : '';
          resolve(value || null);
        },
        onCancel: () => resolve(null)
      });
    });
  }

  static form(formHTML, title = 'Formulario', onSubmit = null) {
    const modal = new ModalComponent();
    return modal.show({
      title,
      content: formHTML,
      type: 'form',
      size: 'large',
      buttons: [
        { text: 'Cancelar', type: 'secondary', action: 'cancel' },
        { text: 'Guardar', type: 'primary', action: 'confirm' }
      ],
      onConfirm: onSubmit
    });
  }
}
