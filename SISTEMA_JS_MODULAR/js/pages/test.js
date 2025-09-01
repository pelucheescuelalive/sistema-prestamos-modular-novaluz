/* ===================================================================
   🎯 PÁGINA TEST
   Página de prueba simple para verificar el sistema
   =================================================================== */

export class TestPage {
  constructor(app) {
    this.app = app;
  }

  async render() {
    console.log('🧪 Renderizando página de test...');
    
    return `
      <div class="test-page" style="padding: 2rem;">
        <h1>🧪 Página de Test</h1>
        <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; border: 1px solid #0ea5e9;">
          <h2>✅ Sistema Funcionando</h2>
          <p>Si puedes ver esta página, el sistema de navegación está funcionando correctamente.</p>
          <p><strong>Hora:</strong> ${new Date().toLocaleString()}</p>
          
          <div style="margin-top: 1rem;">
            <button onclick="window.app.navigateTo('dashboard')" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">
              Ir al Dashboard
            </button>
          </div>
        </div>
        
        <div style="margin-top: 1rem; background: #fef3c7; padding: 1rem; border-radius: 8px; border: 1px solid #f59e0b;">
          <h3>🔍 Información de Debug</h3>
          <p><strong>Página actual:</strong> ${this.app.currentPage || 'Ninguna'}</p>
          <p><strong>Páginas cargadas:</strong> ${this.app.pages.size}</p>
          <p><strong>Componentes cargados:</strong> ${this.app.components.size}</p>
        </div>
      </div>
    `;
  }

  async init() {
    console.log('🧪 Test page initialized');
  }
}
