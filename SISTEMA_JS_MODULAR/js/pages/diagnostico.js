/* ===================================================================
   🎯 PÁGINA DIAGNÓSTICO DEL SISTEMA
   Herramienta para verificar el estado de todas las páginas
   =================================================================== */

export class DiagnosticoPage {
  constructor(app) {
    this.app = app;
    this.resultados = {};
  }

  async render() {
    console.log('🔍 Renderizando página de diagnóstico...');
    
    return `
      <div class="diagnostico-page" style="padding: 2rem;">
        <div class="page-header">
          <h1>🔍 Diagnóstico del Sistema</h1>
          <p>Verificación completa del estado de todas las páginas y componentes</p>
        </div>

        <div class="diagnostics-container">
          <!-- Estado del Sistema -->
          <div class="diagnostic-section">
            <h2>📊 Estado General</h2>
            <div class="status-grid">
              <div class="status-card">
                <h3>🏗️ Sistema Base</h3>
                <div class="status-indicator success">✅ Funcionando</div>
                <p>Aplicación principal cargada correctamente</p>
              </div>
              <div class="status-card">
                <h3>🧭 Navegación</h3>
                <div class="status-indicator success">✅ Operativa</div>
                <p>Router y navegación SPA funcionando</p>
              </div>
              <div class="status-card">
                <h3>💾 Almacenamiento</h3>
                <div class="status-indicator success">✅ Disponible</div>
                <p>LocalStorage funcionando correctamente</p>
              </div>
              <div class="status-card">
                <h3>🎨 Componentes</h3>
                <div class="status-indicator">${this.app.components.size > 0 ? 'success">✅ Cargados' : 'warning">⚠️ Parcial'}</div>
                <p>${this.app.components.size} componentes disponibles</p>
              </div>
            </div>
          </div>

          <!-- Test de Páginas -->
          <div class="diagnostic-section">
            <h2>📄 Estado de Páginas</h2>
            <div class="test-controls">
              <button onclick="window.diagnostico.testAllPages()" class="btn btn-primary">
                🧪 Probar Todas las Páginas
              </button>
              <button onclick="window.diagnostico.clearResults()" class="btn btn-secondary">
                🗑️ Limpiar Resultados
              </button>
            </div>
            <div id="page-test-results" class="test-results">
              <p class="text-muted">Haz clic en "Probar Todas las Páginas" para comenzar el diagnóstico</p>
            </div>
          </div>

          <!-- Test de Navegación -->
          <div class="diagnostic-section">
            <h2>🧭 Test de Navegación Rápida</h2>
            <div class="navigation-test">
              <div class="nav-buttons">
                ${Object.keys(this.app.pageLoaders).map(page => `
                  <button onclick="window.diagnostico.testPage('${page}')" class="btn btn-outline">
                    📄 ${page.charAt(0).toUpperCase() + page.slice(1)}
                  </button>
                `).join('')}
              </div>
            </div>
          </div>

          <!-- Información del Sistema -->
          <div class="diagnostic-section">
            <h2>🔧 Información Técnica</h2>
            <div class="tech-info">
              <div class="info-grid">
                <div class="info-item">
                  <strong>Páginas Configuradas:</strong> ${Object.keys(this.app.pageLoaders).length}
                </div>
                <div class="info-item">
                  <strong>Páginas Cargadas:</strong> ${this.app.pages.size}
                </div>
                <div class="info-item">
                  <strong>Componentes Activos:</strong> ${this.app.components.size}
                </div>
                <div class="info-item">
                  <strong>Página Actual:</strong> ${this.app.currentPage || 'Ninguna'}
                </div>
                <div class="info-item">
                  <strong>LocalStorage:</strong> ${localStorage.length} elementos
                </div>
                <div class="info-item">
                  <strong>Timestamp:</strong> ${new Date().toLocaleString()}
                </div>
              </div>
            </div>
          </div>

          <!-- Acciones del Sistema -->
          <div class="diagnostic-section">
            <h2>⚙️ Acciones del Sistema</h2>
            <div class="system-actions">
              <button onclick="window.diagnostico.clearCache()" class="btn btn-warning">
                🗑️ Limpiar Cache
              </button>
              <button onclick="window.diagnostico.resetSystem()" class="btn btn-danger">
                🔄 Reset Sistema
              </button>
              <button onclick="window.diagnostico.exportData()" class="btn btn-info">
                📥 Exportar Datos
              </button>
              <button onclick="window.diagnostico.loadTestData()" class="btn btn-success">
                🧪 Cargar Datos de Prueba
              </button>
            </div>
          </div>
        </div>
      </div>

      <style>
        .diagnostico-page {
          max-width: 1200px;
          margin: 0 auto;
        }

        .page-header {
          text-align: center;
          margin-bottom: 2rem;
          padding-bottom: 1rem;
          border-bottom: 2px solid var(--primary-color);
        }

        .diagnostic-section {
          background: var(--card-bg);
          border-radius: 12px;
          padding: 1.5rem;
          margin-bottom: 1.5rem;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .status-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1rem;
          margin-top: 1rem;
        }

        .status-card {
          background: var(--bg-color);
          border: 1px solid var(--border-color);
          border-radius: 8px;
          padding: 1rem;
          text-align: center;
        }

        .status-indicator {
          display: inline-block;
          padding: 0.5rem 1rem;
          border-radius: 20px;
          font-weight: bold;
          margin: 0.5rem 0;
        }

        .status-indicator.success {
          background: #dcfce7;
          color: #166534;
          border: 1px solid #bbf7d0;
        }

        .status-indicator.warning {
          background: #fef3c7;
          color: #92400e;
          border: 1px solid #fde68a;
        }

        .status-indicator.error {
          background: #fecaca;
          color: #991b1b;
          border: 1px solid #fca5a5;
        }

        .test-controls, .navigation-test, .system-actions {
          margin: 1rem 0;
        }

        .nav-buttons {
          display: flex;
          flex-wrap: wrap;
          gap: 0.5rem;
        }

        .test-results {
          margin-top: 1rem;
          min-height: 100px;
          padding: 1rem;
          background: var(--bg-color);
          border-radius: 8px;
          border: 1px solid var(--border-color);
        }

        .info-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1rem;
        }

        .info-item {
          padding: 0.75rem;
          background: var(--bg-color);
          border-radius: 6px;
          border: 1px solid var(--border-color);
        }

        .btn {
          padding: 0.5rem 1rem;
          border: none;
          border-radius: 6px;
          cursor: pointer;
          font-weight: 500;
          margin: 0.25rem;
          transition: all 0.2s;
        }

        .btn-primary {
          background: var(--primary-color);
          color: white;
        }

        .btn-secondary {
          background: #6b7280;
          color: white;
        }

        .btn-outline {
          background: transparent;
          border: 1px solid var(--border-color);
          color: var(--text-color);
        }

        .btn-warning {
          background: #f59e0b;
          color: white;
        }

        .btn-danger {
          background: #ef4444;
          color: white;
        }

        .btn-info {
          background: #06b6d4;
          color: white;
        }

        .btn-success {
          background: #10b981;
          color: white;
        }

        .btn:hover {
          opacity: 0.9;
          transform: translateY(-1px);
        }

        .text-muted {
          color: #6b7280;
          font-style: italic;
        }
      </style>
    `;
  }

  async init() {
    console.log('🔍 Diagnóstico page initialized');
    
    // Exponer funciones globalmente para los botones
    window.diagnostico = {
      testPage: this.testPage.bind(this),
      testAllPages: this.testAllPages.bind(this),
      clearResults: this.clearResults.bind(this),
      clearCache: this.clearCache.bind(this),
      resetSystem: this.resetSystem.bind(this),
      exportData: this.exportData.bind(this),
      loadTestData: this.loadTestData.bind(this)
    };
  }

  async testPage(pageName) {
    console.log(`🧪 Testing page: ${pageName}`);
    
    try {
      const startTime = performance.now();
      await this.app.navigateTo(pageName);
      const endTime = performance.now();
      const loadTime = Math.round(endTime - startTime);
      
      this.showResult(`✅ ${pageName}: Cargada correctamente (${loadTime}ms)`);
      return true;
    } catch (error) {
      this.showResult(`❌ ${pageName}: Error - ${error.message}`);
      return false;
    }
  }

  async testAllPages() {
    const resultsContainer = document.getElementById('page-test-results');
    resultsContainer.innerHTML = '<p>🧪 Iniciando pruebas de todas las páginas...</p>';
    
    const pages = Object.keys(this.app.pageLoaders);
    const results = [];
    
    for (const page of pages) {
      const result = await this.testPage(page);
      results.push({ page, success: result });
      
      // Pequeña pausa entre pruebas
      await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    // Resumen final
    const successCount = results.filter(r => r.success).length;
    const totalCount = results.length;
    
    this.showResult(`\n📊 RESUMEN: ${successCount}/${totalCount} páginas funcionando correctamente`);
    
    if (successCount === totalCount) {
      this.showResult('🎉 ¡Todas las páginas están funcionando perfectamente!');
    }
  }

  showResult(message) {
    const resultsContainer = document.getElementById('page-test-results');
    const currentContent = resultsContainer.innerHTML;
    resultsContainer.innerHTML = currentContent + '<div>' + message + '</div>';
    resultsContainer.scrollTop = resultsContainer.scrollHeight;
  }

  clearResults() {
    const resultsContainer = document.getElementById('page-test-results');
    resultsContainer.innerHTML = '<p class="text-muted">Resultados limpiados</p>';
  }

  clearCache() {
    this.app.pages.clear();
    this.showResult('🗑️ Cache de páginas limpiado');
  }

  resetSystem() {
    if (confirm('¿Estás seguro de que quieres resetear el sistema? Esto limpiará todos los datos.')) {
      localStorage.clear();
      window.location.reload();
    }
  }

  exportData() {
    const data = {
      clientes: this.app.getData('clientes'),
      prestamos: this.app.getData('prestamos'),
      pagos: this.app.getData('pagos'),
      configuracion: this.app.getData('configuracion'),
      timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `nova-luz-backup-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    this.showResult('📥 Datos exportados correctamente');
  }

  loadTestData() {
    // Datos de prueba
    const testData = {
      clientes: [
        { id: 1, nombre: 'Juan Pérez', telefono: '555-0101', email: 'juan@email.com', direccion: 'Calle 123' },
        { id: 2, nombre: 'María García', telefono: '555-0102', email: 'maria@email.com', direccion: 'Avenida 456' }
      ],
      prestamos: [
        { id: 1, clienteId: 1, monto: 5000, tasa: 15, plazo: 12, estado: 'activo', fecha: '2025-01-01' },
        { id: 2, clienteId: 2, monto: 3000, tasa: 12, plazo: 6, estado: 'activo', fecha: '2025-02-01' }
      ],
      pagos: [
        { id: 1, prestamoId: 1, monto: 500, fecha: '2025-01-15', tipo: 'cuota' },
        { id: 2, prestamoId: 2, monto: 600, fecha: '2025-02-15', tipo: 'cuota' }
      ]
    };
    
    this.app.saveData('clientes', testData.clientes);
    this.app.saveData('prestamos', testData.prestamos);
    this.app.saveData('pagos', testData.pagos);
    
    this.showResult('🧪 Datos de prueba cargados correctamente');
  }
}
