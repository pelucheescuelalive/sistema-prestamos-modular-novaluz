/* ===================================================================
   🎯 PÁGINA CALCULADORA
   Herramientas de cálculo para préstamos e intereses
   =================================================================== */

export class CalculadoraPage {
  constructor(app) {
    this.app = app;
    this.calculadoraActiva = 'prestamo';
  }

  async render() {
    return `
      <div class="calculadora-page">
        <!-- Encabezado -->
        <div class="page-header">
          <div class="header-left">
            <h1 class="page-title">
              <i class="fas fa-calculator"></i>
              Calculadora Financiera
            </h1>
            <p class="page-subtitle">
              Herramientas de cálculo para préstamos, intereses y rentabilidad
            </p>
          </div>
          <div class="header-right">
            <button class="btn btn-outline-secondary" onclick="calculadoraPage.limpiarTodo()">
              <i class="fas fa-eraser"></i>
              Limpiar Todo
            </button>
            <button class="btn btn-primary" onclick="calculadoraPage.guardarCalculo()">
              <i class="fas fa-save"></i>
              Guardar Cálculo
            </button>
          </div>
        </div>

        <!-- Pestañas de Calculadoras -->
        <div class="calculadora-tabs">
          <nav class="nav nav-tabs">
            <a class="nav-link ${this.calculadoraActiva === 'prestamo' ? 'active' : ''}" 
               onclick="calculadoraPage.cambiarCalculadora('prestamo')">
              <i class="fas fa-money-bill-wave"></i>
              Préstamos
            </a>
            <a class="nav-link ${this.calculadoraActiva === 'interes' ? 'active' : ''}" 
               onclick="calculadoraPage.cambiarCalculadora('interes')">
              <i class="fas fa-percentage"></i>
              Intereses
            </a>
            <a class="nav-link ${this.calculadoraActiva === 'amortizacion' ? 'active' : ''}" 
               onclick="calculadoraPage.cambiarCalculadora('amortizacion')">
              <i class="fas fa-table"></i>
              Amortización
            </a>
            <a class="nav-link ${this.calculadoraActiva === 'rentabilidad' ? 'active' : ''}" 
               onclick="calculadoraPage.cambiarCalculadora('rentabilidad')">
              <i class="fas fa-chart-line"></i>
              Rentabilidad
            </a>
            <a class="nav-link ${this.calculadoraActiva === 'comparacion' ? 'active' : ''}" 
               onclick="calculadoraPage.cambiarCalculadora('comparacion')">
              <i class="fas fa-balance-scale"></i>
              Comparación
            </a>
          </nav>
        </div>

        <!-- Contenido de Calculadoras -->
        <div class="calculadora-content">
          ${this.renderCalculadoraActiva()}
        </div>
      </div>
    `;
  }

  renderCalculadoraActiva() {
    switch (this.calculadoraActiva) {
      case 'prestamo':
        return this.renderCalculadoraPrestamo();
      case 'interes':
        return this.renderCalculadoraInteres();
      case 'amortizacion':
        return this.renderCalculadoraAmortizacion();
      case 'rentabilidad':
        return this.renderCalculadoraRentabilidad();
      case 'comparacion':
        return this.renderCalculadoraComparacion();
      default:
        return this.renderCalculadoraPrestamo();
    }
  }

  renderCalculadoraPrestamo() {
    return `
      <div class="calculadora-prestamo">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-money-bill-wave"></i> Calculadora de Préstamos</h5>
                <p class="card-subtitle">Calcula montos, intereses y pagos de préstamos</p>
              </div>
              <div class="card-body">
                <form id="calc-prestamo-form">
                  <div class="form-group">
                    <label for="calc-monto">Monto del Préstamo (RD$)</label>
                    <input type="number" id="calc-monto" class="form-control" 
                           placeholder="10,000.00" step="0.01" min="100"
                           onchange="calculadoraPage.calcularPrestamo()">
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="calc-tasa">Tasa de Interés (%)</label>
                      <input type="number" id="calc-tasa" class="form-control" 
                             placeholder="10.00" step="0.01" min="0" max="100"
                             onchange="calculadoraPage.calcularPrestamo()">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="calc-periodo-tasa">Período de la Tasa</label>
                      <select id="calc-periodo-tasa" class="form-control" onchange="calculadoraPage.calcularPrestamo()">
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                        <option value="semanal">Semanal</option>
                        <option value="diario">Diario</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="calc-plazo">Plazo</label>
                      <input type="number" id="calc-plazo" class="form-control" 
                             placeholder="12" min="1"
                             onchange="calculadoraPage.calcularPrestamo()">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="calc-periodo-plazo">Período del Plazo</label>
                      <select id="calc-periodo-plazo" class="form-control" onchange="calculadoraPage.calcularPrestamo()">
                        <option value="meses">Meses</option>
                        <option value="semanas">Semanas</option>
                        <option value="dias">Días</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="calc-tipo-interes">Tipo de Interés</label>
                    <select id="calc-tipo-interes" class="form-control" onchange="calculadoraPage.calcularPrestamo()">
                      <option value="simple">Interés Simple</option>
                      <option value="compuesto">Interés Compuesto</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="calc-frecuencia-pago">Frecuencia de Pago</label>
                    <select id="calc-frecuencia-pago" class="form-control" onchange="calculadoraPage.calcularPrestamo()">
                      <option value="unico">Pago Único al Final</option>
                      <option value="mensual">Pagos Mensuales</option>
                      <option value="quincenal">Pagos Quincenales</option>
                      <option value="semanal">Pagos Semanales</option>
                    </select>
                  </div>
                  
                  <button type="button" class="btn btn-primary btn-block" onclick="calculadoraPage.calcularPrestamo()">
                    <i class="fas fa-calculator"></i>
                    Calcular
                  </button>
                </form>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Resultados del Cálculo</h5>
              </div>
              <div class="card-body" id="calc-prestamo-resultados">
                <div class="resultado-placeholder">
                  <i class="fas fa-calculator"></i>
                  <p>Complete los datos para ver los resultados</p>
                </div>
              </div>
            </div>
            
            <!-- Gráfico de Distribución -->
            <div class="card mt-3">
              <div class="card-header">
                <h6><i class="fas fa-pie-chart"></i> Distribución del Pago</h6>
              </div>
              <div class="card-body" id="calc-prestamo-grafico">
                <div class="grafico-placeholder">
                  <p class="text-muted">Gráfico aparecerá después del cálculo</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  renderCalculadoraInteres() {
    return `
      <div class="calculadora-interes">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-percentage"></i> Calcular Interés</h6>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Capital Inicial (RD$)</label>
                  <input type="number" id="int-capital" class="form-control" 
                         placeholder="10,000.00" onchange="calculadoraPage.calcularInteres()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés (%)</label>
                  <input type="number" id="int-tasa" class="form-control" 
                         placeholder="10.00" onchange="calculadoraPage.calcularInteres()">
                </div>
                <div class="form-group">
                  <label>Tiempo</label>
                  <input type="number" id="int-tiempo" class="form-control" 
                         placeholder="12" onchange="calculadoraPage.calcularInteres()">
                </div>
                <div class="form-group">
                  <label>Período</label>
                  <select id="int-periodo" class="form-control" onchange="calculadoraPage.calcularInteres()">
                    <option value="meses">Meses</option>
                    <option value="años">Años</option>
                    <option value="dias">Días</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-search-dollar"></i> Calcular Capital</h6>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Monto Final Deseado (RD$)</label>
                  <input type="number" id="cap-monto-final" class="form-control" 
                         placeholder="15,000.00" onchange="calculadoraPage.calcularCapital()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés (%)</label>
                  <input type="number" id="cap-tasa" class="form-control" 
                         placeholder="10.00" onchange="calculadoraPage.calcularCapital()">
                </div>
                <div class="form-group">
                  <label>Tiempo</label>
                  <input type="number" id="cap-tiempo" class="form-control" 
                         placeholder="12" onchange="calculadoraPage.calcularCapital()">
                </div>
                <div class="form-group">
                  <label>Período</label>
                  <select id="cap-periodo" class="form-control" onchange="calculadoraPage.calcularCapital()">
                    <option value="meses">Meses</option>
                    <option value="años">Años</option>
                    <option value="dias">Días</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-clock"></i> Calcular Tiempo</h6>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Capital Inicial (RD$)</label>
                  <input type="number" id="time-capital" class="form-control" 
                         placeholder="10,000.00" onchange="calculadoraPage.calcularTiempo()">
                </div>
                <div class="form-group">
                  <label>Monto Final (RD$)</label>
                  <input type="number" id="time-monto-final" class="form-control" 
                         placeholder="15,000.00" onchange="calculadoraPage.calcularTiempo()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés (%)</label>
                  <input type="number" id="time-tasa" class="form-control" 
                         placeholder="10.00" onchange="calculadoraPage.calcularTiempo()">
                </div>
                <div class="form-group">
                  <label>Período de la Tasa</label>
                  <select id="time-periodo" class="form-control" onchange="calculadoraPage.calcularTiempo()">
                    <option value="meses">Mensual</option>
                    <option value="años">Anual</option>
                    <option value="dias">Diario</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Resultados de Intereses -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Resultados de Cálculos de Interés</h5>
              </div>
              <div class="card-body" id="calc-interes-resultados">
                <div class="resultado-placeholder">
                  <i class="fas fa-percentage"></i>
                  <p>Complete los datos en cualquier calculadora para ver los resultados</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  renderCalculadoraAmortizacion() {
    return `
      <div class="calculadora-amortizacion">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-table"></i> Tabla de Amortización</h5>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Monto del Préstamo (RD$)</label>
                  <input type="number" id="amort-monto" class="form-control" 
                         placeholder="100,000.00" onchange="calculadoraPage.calcularAmortizacion()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés Anual (%)</label>
                  <input type="number" id="amort-tasa" class="form-control" 
                         placeholder="12.00" onchange="calculadoraPage.calcularAmortizacion()">
                </div>
                <div class="form-group">
                  <label>Plazo en Meses</label>
                  <input type="number" id="amort-plazo" class="form-control" 
                         placeholder="12" onchange="calculadoraPage.calcularAmortizacion()">
                </div>
                <div class="form-group">
                  <label>Fecha de Inicio</label>
                  <input type="date" id="amort-fecha" class="form-control" 
                         value="${new Date().toISOString().split('T')[0]}"
                         onchange="calculadoraPage.calcularAmortizacion()">
                </div>
                <div class="form-group">
                  <label>Tipo de Cuota</label>
                  <select id="amort-tipo" class="form-control" onchange="calculadoraPage.calcularAmortizacion()">
                    <option value="francesa">Cuota Francesa (Fija)</option>
                    <option value="alemana">Cuota Alemana (Decreciente)</option>
                    <option value="americana">Cuota Americana (Solo Intereses)</option>
                  </select>
                </div>
                <button type="button" class="btn btn-primary btn-block" onclick="calculadoraPage.calcularAmortizacion()">
                  <i class="fas fa-table"></i>
                  Generar Tabla
                </button>
              </div>
            </div>
          </div>
          
          <div class="col-md-8">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-list"></i> Tabla de Amortización</h6>
                <div class="table-actions">
                  <button class="btn btn-sm btn-outline-primary" onclick="calculadoraPage.exportarAmortizacion()">
                    <i class="fas fa-download"></i> Exportar
                  </button>
                  <button class="btn btn-sm btn-outline-success" onclick="calculadoraPage.imprimirAmortizacion()">
                    <i class="fas fa-print"></i> Imprimir
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px;">
                  <table class="table table-sm table-striped mb-0" id="tabla-amortizacion">
                    <thead class="thead-dark sticky-top">
                      <tr>
                        <th>Cuota</th>
                        <th>Fecha</th>
                        <th>Cuota Total</th>
                        <th>Capital</th>
                        <th>Interés</th>
                        <th>Saldo</th>
                      </tr>
                    </thead>
                    <tbody id="amortizacion-body">
                      <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                          <i class="fas fa-table"></i><br>
                          Complete los datos para generar la tabla
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  renderCalculadoraRentabilidad() {
    return `
      <div class="calculadora-rentabilidad">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Análisis de Rentabilidad</h5>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Cartera Total Actual (RD$)</label>
                  <input type="number" id="rent-cartera" class="form-control" 
                         placeholder="1,000,000.00" onchange="calculadoraPage.calcularRentabilidad()">
                </div>
                <div class="form-group">
                  <label>Tasa Promedio de Interés (%)</label>
                  <input type="number" id="rent-tasa-promedio" class="form-control" 
                         placeholder="15.00" onchange="calculadoraPage.calcularRentabilidad()">
                </div>
                <div class="form-group">
                  <label>Gastos Operacionales Mensuales (RD$)</label>
                  <input type="number" id="rent-gastos" class="form-control" 
                         placeholder="50,000.00" onchange="calculadoraPage.calcularRentabilidad()">
                </div>
                <div class="form-group">
                  <label>Tasa de Mora Estimada (%)</label>
                  <input type="number" id="rent-mora" class="form-control" 
                         placeholder="5.00" onchange="calculadoraPage.calcularRentabilidad()">
                </div>
                <div class="form-group">
                  <label>Período de Análisis</label>
                  <select id="rent-periodo" class="form-control" onchange="calculadoraPage.calcularRentabilidad()">
                    <option value="1">1 Mes</option>
                    <option value="3">3 Meses</option>
                    <option value="6">6 Meses</option>
                    <option value="12" selected>12 Meses</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-chart-bar"></i> Indicadores de Rentabilidad</h6>
              </div>
              <div class="card-body" id="rent-resultados">
                <div class="resultado-placeholder">
                  <i class="fas fa-chart-line"></i>
                  <p>Complete los datos para ver el análisis</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  renderCalculadoraComparacion() {
    return `
      <div class="calculadora-comparacion">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-balance-scale"></i> Opción A</h6>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre de la Opción</label>
                  <input type="text" id="comp-a-nombre" class="form-control" 
                         placeholder="Préstamo Tradicional" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Monto (RD$)</label>
                  <input type="number" id="comp-a-monto" class="form-control" 
                         placeholder="50,000.00" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés (%)</label>
                  <input type="number" id="comp-a-tasa" class="form-control" 
                         placeholder="12.00" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Plazo (meses)</label>
                  <input type="number" id="comp-a-plazo" class="form-control" 
                         placeholder="12" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Comisiones/Gastos (RD$)</label>
                  <input type="number" id="comp-a-gastos" class="form-control" 
                         placeholder="1,000.00" onchange="calculadoraPage.compararOpciones()">
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h6><i class="fas fa-balance-scale"></i> Opción B</h6>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre de la Opción</label>
                  <input type="text" id="comp-b-nombre" class="form-control" 
                         placeholder="Préstamo Alternativo" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Monto (RD$)</label>
                  <input type="number" id="comp-b-monto" class="form-control" 
                         placeholder="50,000.00" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Tasa de Interés (%)</label>
                  <input type="number" id="comp-b-tasa" class="form-control" 
                         placeholder="15.00" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Plazo (meses)</label>
                  <input type="number" id="comp-b-plazo" class="form-control" 
                         placeholder="8" onchange="calculadoraPage.compararOpciones()">
                </div>
                <div class="form-group">
                  <label>Comisiones/Gastos (RD$)</label>
                  <input type="number" id="comp-b-gastos" class="form-control" 
                         placeholder="500.00" onchange="calculadoraPage.compararOpciones()">
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tabla de Comparación -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5><i class="fas fa-table"></i> Comparación Detallada</h5>
              </div>
              <div class="card-body" id="comparacion-resultados">
                <div class="resultado-placeholder">
                  <i class="fas fa-balance-scale"></i>
                  <p>Complete los datos de ambas opciones para ver la comparación</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Métodos de cálculo

  calcularPrestamo() {
    const monto = parseFloat(document.getElementById('calc-monto')?.value) || 0;
    const tasa = parseFloat(document.getElementById('calc-tasa')?.value) || 0;
    const plazo = parseInt(document.getElementById('calc-plazo')?.value) || 0;
    const tipoInteres = document.getElementById('calc-tipo-interes')?.value || 'simple';
    const frecuenciaPago = document.getElementById('calc-frecuencia-pago')?.value || 'unico';

    if (!monto || !tasa || !plazo) {
      return;
    }

    let interes, montoTotal, cuotaMensual;

    if (tipoInteres === 'simple') {
      interes = (monto * tasa * plazo) / 100;
      montoTotal = monto + interes;
    } else {
      // Interés compuesto
      montoTotal = monto * Math.pow(1 + (tasa / 100), plazo);
      interes = montoTotal - monto;
    }

    // Calcular cuota según frecuencia
    let numeroPagos = 1;
    if (frecuenciaPago === 'mensual') numeroPagos = plazo;
    else if (frecuenciaPago === 'quincenal') numeroPagos = plazo * 2;
    else if (frecuenciaPago === 'semanal') numeroPagos = plazo * 4;

    cuotaMensual = numeroPagos > 1 ? montoTotal / numeroPagos : montoTotal;

    const tasaEfectiva = (interes / monto) * 100;
    const rentabilidadAnual = ((montoTotal - monto) / monto) * (12 / plazo) * 100;

    const resultadosHTML = `
      <div class="resultados-grid">
        <div class="resultado-item">
          <div class="resultado-label">Monto del Préstamo</div>
          <div class="resultado-valor text-primary">$${monto.toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Interés Total</div>
          <div class="resultado-valor text-info">$${interes.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Total a Pagar</div>
          <div class="resultado-valor text-success">$${montoTotal.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">${numeroPagos > 1 ? 'Cuota' : 'Pago Único'}</div>
          <div class="resultado-valor text-warning">$${cuotaMensual.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Tasa Efectiva</div>
          <div class="resultado-valor text-info">${tasaEfectiva.toFixed(2)}%</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Rentabilidad Anual</div>
          <div class="resultado-valor text-success">${rentabilidadAnual.toFixed(2)}%</div>
        </div>
        ${numeroPagos > 1 ? `
          <div class="resultado-item">
            <div class="resultado-label">Número de Pagos</div>
            <div class="resultado-valor">${numeroPagos}</div>
          </div>
        ` : ''}
      </div>
      
      <div class="mt-3">
        <button class="btn btn-outline-primary btn-sm" onclick="calculadoraPage.crearPrestamoDesdeCalculo()">
          <i class="fas fa-plus"></i> Crear Préstamo
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="calculadoraPage.exportarCalculo()">
          <i class="fas fa-download"></i> Exportar
        </button>
      </div>
    `;

    const resultadosDiv = document.getElementById('calc-prestamo-resultados');
    if (resultadosDiv) {
      resultadosDiv.innerHTML = resultadosHTML;
    }

    // Generar gráfico
    this.generarGraficoPrestamo(monto, interes);
  }

  generarGraficoPrestamo(capital, interes) {
    const total = capital + interes;
    const porcentajeCapital = (capital / total) * 100;
    const porcentajeInteres = (interes / total) * 100;

    const graficoHTML = `
      <div class="distribucion-pago">
        <div class="distribucion-item">
          <div class="distribucion-label">
            <span class="color-indicator bg-primary"></span>
            Capital (${porcentajeCapital.toFixed(1)}%)
          </div>
          <div class="distribucion-bar">
            <div class="bar-fill bg-primary" style="width: ${porcentajeCapital}%"></div>
          </div>
          <div class="distribucion-valor">$${capital.toLocaleString()}</div>
        </div>
        <div class="distribucion-item">
          <div class="distribucion-label">
            <span class="color-indicator bg-info"></span>
            Interés (${porcentajeInteres.toFixed(1)}%)
          </div>
          <div class="distribucion-bar">
            <div class="bar-fill bg-info" style="width: ${porcentajeInteres}%"></div>
          </div>
          <div class="distribucion-valor">$${interes.toFixed(2).toLocaleString()}</div>
        </div>
      </div>
    `;

    const graficoDiv = document.getElementById('calc-prestamo-grafico');
    if (graficoDiv) {
      graficoDiv.innerHTML = graficoHTML;
    }
  }

  calcularInteres() {
    const capital = parseFloat(document.getElementById('int-capital')?.value) || 0;
    const tasa = parseFloat(document.getElementById('int-tasa')?.value) || 0;
    const tiempo = parseFloat(document.getElementById('int-tiempo')?.value) || 0;

    if (!capital || !tasa || !tiempo) return;

    const interes = (capital * tasa * tiempo) / 100;
    const montoFinal = capital + interes;

    this.mostrarResultadosInteres('Cálculo de Interés', {
      'Capital Inicial': `$${capital.toLocaleString()}`,
      'Tasa de Interés': `${tasa}%`,
      'Tiempo': `${tiempo} ${document.getElementById('int-periodo')?.value || 'meses'}`,
      'Interés Generado': `$${interes.toFixed(2).toLocaleString()}`,
      'Monto Final': `$${montoFinal.toFixed(2).toLocaleString()}`
    });
  }

  calcularCapital() {
    const montoFinal = parseFloat(document.getElementById('cap-monto-final')?.value) || 0;
    const tasa = parseFloat(document.getElementById('cap-tasa')?.value) || 0;
    const tiempo = parseFloat(document.getElementById('cap-tiempo')?.value) || 0;

    if (!montoFinal || !tasa || !tiempo) return;

    const capital = montoFinal / (1 + (tasa * tiempo) / 100);
    const interes = montoFinal - capital;

    this.mostrarResultadosInteres('Cálculo de Capital', {
      'Monto Final Deseado': `$${montoFinal.toLocaleString()}`,
      'Tasa de Interés': `${tasa}%`,
      'Tiempo': `${tiempo} ${document.getElementById('cap-periodo')?.value || 'meses'}`,
      'Capital Necesario': `$${capital.toFixed(2).toLocaleString()}`,
      'Interés a Generar': `$${interes.toFixed(2).toLocaleString()}`
    });
  }

  calcularTiempo() {
    const capital = parseFloat(document.getElementById('time-capital')?.value) || 0;
    const montoFinal = parseFloat(document.getElementById('time-monto-final')?.value) || 0;
    const tasa = parseFloat(document.getElementById('time-tasa')?.value) || 0;

    if (!capital || !montoFinal || !tasa) return;

    const tiempo = ((montoFinal - capital) / capital) * (100 / tasa);
    const interes = montoFinal - capital;

    this.mostrarResultadosInteres('Cálculo de Tiempo', {
      'Capital Inicial': `$${capital.toLocaleString()}`,
      'Monto Final': `$${montoFinal.toLocaleString()}`,
      'Tasa de Interés': `${tasa}%`,
      'Tiempo Necesario': `${tiempo.toFixed(2)} ${document.getElementById('time-periodo')?.value || 'meses'}`,
      'Interés Total': `$${interes.toFixed(2).toLocaleString()}`
    });
  }

  mostrarResultadosInteres(titulo, resultados) {
    const resultadosHTML = `
      <h6 class="mb-3">${titulo}</h6>
      <div class="resultados-interes">
        ${Object.entries(resultados).map(([label, valor]) => `
          <div class="resultado-item">
            <span class="resultado-label">${label}:</span>
            <span class="resultado-valor">${valor}</span>
          </div>
        `).join('')}
      </div>
    `;

    const resultadosDiv = document.getElementById('calc-interes-resultados');
    if (resultadosDiv) {
      resultadosDiv.innerHTML = resultadosHTML;
    }
  }

  calcularAmortizacion() {
    const monto = parseFloat(document.getElementById('amort-monto')?.value) || 0;
    const tasaAnual = parseFloat(document.getElementById('amort-tasa')?.value) || 0;
    const plazoMeses = parseInt(document.getElementById('amort-plazo')?.value) || 0;
    const fechaInicio = new Date(document.getElementById('amort-fecha')?.value);
    const tipo = document.getElementById('amort-tipo')?.value || 'francesa';

    if (!monto || !tasaAnual || !plazoMeses) return;

    const tasaMensual = tasaAnual / 100 / 12;
    let tabla = [];

    if (tipo === 'francesa') {
      // Cuota francesa (fija)
      const cuotaFija = monto * (tasaMensual * Math.pow(1 + tasaMensual, plazoMeses)) / 
                       (Math.pow(1 + tasaMensual, plazoMeses) - 1);
      
      let saldo = monto;
      for (let i = 1; i <= plazoMeses; i++) {
        const interes = saldo * tasaMensual;
        const capital = cuotaFija - interes;
        saldo -= capital;
        
        const fecha = new Date(fechaInicio);
        fecha.setMonth(fecha.getMonth() + i - 1);
        
        tabla.push({
          cuota: i,
          fecha: fecha.toLocaleDateString('es-DO'),
          cuotaTotal: cuotaFija,
          capital: capital,
          interes: interes,
          saldo: Math.max(0, saldo)
        });
      }
    }
    // Agregar más tipos según necesidad

    this.renderTablaAmortizacion(tabla);
  }

  renderTablaAmortizacion(tabla) {
    const tbody = document.getElementById('amortizacion-body');
    if (!tbody || !tabla.length) return;

    const filas = tabla.map(fila => `
      <tr>
        <td>${fila.cuota}</td>
        <td>${fila.fecha}</td>
        <td class="text-right">$${fila.cuotaTotal.toFixed(2).toLocaleString()}</td>
        <td class="text-right">$${fila.capital.toFixed(2).toLocaleString()}</td>
        <td class="text-right">$${fila.interes.toFixed(2).toLocaleString()}</td>
        <td class="text-right">$${fila.saldo.toFixed(2).toLocaleString()}</td>
      </tr>
    `).join('');

    tbody.innerHTML = filas;
  }

  calcularRentabilidad() {
    const cartera = parseFloat(document.getElementById('rent-cartera')?.value) || 0;
    const tasaPromedio = parseFloat(document.getElementById('rent-tasa-promedio')?.value) || 0;
    const gastos = parseFloat(document.getElementById('rent-gastos')?.value) || 0;
    const tasaMora = parseFloat(document.getElementById('rent-mora')?.value) || 0;
    const periodo = parseInt(document.getElementById('rent-periodo')?.value) || 12;

    if (!cartera || !tasaPromedio) return;

    const ingresosBrutos = (cartera * tasaPromedio / 100) * (periodo / 12);
    const perdidaeMora = ingresosBrutos * (tasaMora / 100);
    const gastosTotal = gastos * periodo;
    const ingresosNetos = ingresosBrutos - perdidaeMora - gastosTotal;
    const rentabilidad = (ingresosNetos / cartera) * 100;
    const roi = ((ingresosNetos - cartera) / cartera) * 100;

    const resultadosHTML = `
      <div class="rentabilidad-resultados">
        <div class="resultado-item">
          <div class="resultado-label">Ingresos Brutos</div>
          <div class="resultado-valor text-success">$${ingresosBrutos.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Pérdidas por Mora</div>
          <div class="resultado-valor text-danger">-$${perdidaeMora.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Gastos Operacionales</div>
          <div class="resultado-valor text-warning">-$${gastosTotal.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">Ingresos Netos</div>
          <div class="resultado-valor text-primary">$${ingresosNetos.toFixed(2).toLocaleString()}</div>
        </div>
        <div class="resultado-item destacado">
          <div class="resultado-label">Rentabilidad</div>
          <div class="resultado-valor ${rentabilidad > 0 ? 'text-success' : 'text-danger'}">
            ${rentabilidad.toFixed(2)}%
          </div>
        </div>
        <div class="resultado-item">
          <div class="resultado-label">ROI</div>
          <div class="resultado-valor ${roi > 0 ? 'text-success' : 'text-danger'}">
            ${roi.toFixed(2)}%
          </div>
        </div>
      </div>
    `;

    const resultadosDiv = document.getElementById('rent-resultados');
    if (resultadosDiv) {
      resultadosDiv.innerHTML = resultadosHTML;
    }
  }

  compararOpciones() {
    // Obtener datos de ambas opciones
    const opcionA = this.obtenerDatosOpcion('a');
    const opcionB = this.obtenerDatosOpcion('b');

    if (!opcionA.monto || !opcionB.monto) return;

    const comparacionHTML = `
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Concepto</th>
              <th class="text-center">${opcionA.nombre}</th>
              <th class="text-center">${opcionB.nombre}</th>
              <th class="text-center">Diferencia</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Monto del Préstamo</strong></td>
              <td class="text-right">$${opcionA.monto.toLocaleString()}</td>
              <td class="text-right">$${opcionB.monto.toLocaleString()}</td>
              <td class="text-right">$${Math.abs(opcionA.monto - opcionB.monto).toLocaleString()}</td>
            </tr>
            <tr>
              <td><strong>Tasa de Interés</strong></td>
              <td class="text-right">${opcionA.tasa}%</td>
              <td class="text-right">${opcionB.tasa}%</td>
              <td class="text-right">${Math.abs(opcionA.tasa - opcionB.tasa).toFixed(2)}%</td>
            </tr>
            <tr>
              <td><strong>Plazo</strong></td>
              <td class="text-right">${opcionA.plazo} meses</td>
              <td class="text-right">${opcionB.plazo} meses</td>
              <td class="text-right">${Math.abs(opcionA.plazo - opcionB.plazo)} meses</td>
            </tr>
            <tr>
              <td><strong>Interés Total</strong></td>
              <td class="text-right">$${opcionA.interesTotal.toFixed(2).toLocaleString()}</td>
              <td class="text-right">$${opcionB.interesTotal.toFixed(2).toLocaleString()}</td>
              <td class="text-right ${opcionA.interesTotal > opcionB.interesTotal ? 'text-danger' : 'text-success'}">
                $${Math.abs(opcionA.interesTotal - opcionB.interesTotal).toFixed(2).toLocaleString()}
              </td>
            </tr>
            <tr>
              <td><strong>Total a Pagar</strong></td>
              <td class="text-right">$${opcionA.totalPagar.toFixed(2).toLocaleString()}</td>
              <td class="text-right">$${opcionB.totalPagar.toFixed(2).toLocaleString()}</td>
              <td class="text-right ${opcionA.totalPagar > opcionB.totalPagar ? 'text-danger' : 'text-success'}">
                $${Math.abs(opcionA.totalPagar - opcionB.totalPagar).toFixed(2).toLocaleString()}
              </td>
            </tr>
            <tr class="table-info">
              <td><strong>Recomendación</strong></td>
              <td colspan="3" class="text-center">
                <strong class="text-primary">
                  ${opcionA.totalPagar < opcionB.totalPagar ? opcionA.nombre : opcionB.nombre}
                </strong>
                es la opción más económica
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    `;

    const resultadosDiv = document.getElementById('comparacion-resultados');
    if (resultadosDiv) {
      resultadosDiv.innerHTML = comparacionHTML;
    }
  }

  obtenerDatosOpcion(opcion) {
    const monto = parseFloat(document.getElementById(`comp-${opcion}-monto`)?.value) || 0;
    const tasa = parseFloat(document.getElementById(`comp-${opcion}-tasa`)?.value) || 0;
    const plazo = parseInt(document.getElementById(`comp-${opcion}-plazo`)?.value) || 0;
    const gastos = parseFloat(document.getElementById(`comp-${opcion}-gastos`)?.value) || 0;
    const nombre = document.getElementById(`comp-${opcion}-nombre`)?.value || `Opción ${opcion.toUpperCase()}`;

    const interesTotal = (monto * tasa * plazo) / 100;
    const totalPagar = monto + interesTotal + gastos;

    return {
      nombre,
      monto,
      tasa,
      plazo,
      gastos,
      interesTotal,
      totalPagar
    };
  }

  // Métodos de acciones
  cambiarCalculadora(tipo) {
    this.calculadoraActiva = tipo;
    this.app.renderCurrentPage();
  }

  limpiarTodo() {
    // Limpiar todos los campos de la calculadora activa
    const inputs = document.querySelectorAll('.calculadora-content input, .calculadora-content select');
    inputs.forEach(input => {
      if (input.type === 'date') {
        input.value = new Date().toISOString().split('T')[0];
      } else {
        input.value = '';
      }
    });

    // Limpiar resultados
    const resultados = document.querySelectorAll('[id$="-resultados"]');
    resultados.forEach(resultado => {
      resultado.innerHTML = '<div class="resultado-placeholder"><i class="fas fa-calculator"></i><p>Complete los datos para ver los resultados</p></div>';
    });

    window.toastManager?.info('Calculadora limpiada');
  }

  guardarCalculo() {
    window.toastManager?.info('Funcionalidad de guardado en desarrollo');
  }

  crearPrestamoDesdeCalculo() {
    this.app.navigate('prestamos');
  }

  exportarCalculo() {
    window.toastManager?.info('Exportando cálculo...');
  }

  exportarAmortizacion() {
    window.toastManager?.info('Exportando tabla de amortización...');
  }

  imprimirAmortizacion() {
    window.print();
  }
}

// Instancia global
if (typeof window !== 'undefined') {
  window.calculadoraPage = new CalculadoraPage();
}
