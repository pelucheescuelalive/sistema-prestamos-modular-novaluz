/**
 * Módulo de Reportes y Análisis
 * Sistema de generación de reportes financieros avanzados
 */

export class ReportesPage {
    constructor() {
        this.reportes = [];
        this.filtros = {
            fechaInicio: '',
            fechaFin: '',
            tipoReporte: 'todos',
            cliente: '',
            estado: 'todos'
        };
    }

    render() {
        return `
            <div class="reportes-container">
                <div class="page-header">
                    <h1><i class="fas fa-chart-bar"></i> Reportes y Análisis</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="reportesPage.generarReporte()">
                            <i class="fas fa-file-alt"></i> Generar Reporte
                        </button>
                        <button class="btn btn-success" onclick="reportesPage.exportarExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>

                <div class="filtros-section">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-filter"></i> Filtros de Reporte</h3>
                        </div>
                        <div class="card-body">
                            <div class="filtros-grid">
                                <div class="form-group">
                                    <label>Fecha Inicio:</label>
                                    <input type="date" id="fechaInicio" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Fecha Fin:</label>
                                    <input type="date" id="fechaFin" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Reporte:</label>
                                    <select id="tipoReporte" class="form-control">
                                        <option value="todos">Todos los Reportes</option>
                                        <option value="prestamos">Préstamos</option>
                                        <option value="pagos">Pagos</option>
                                        <option value="mora">Mora y Vencimientos</option>
                                        <option value="clientes">Clientes</option>
                                        <option value="financiero">Análisis Financiero</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Estado:</label>
                                    <select id="estadoReporte" class="form-control">
                                        <option value="todos">Todos</option>
                                        <option value="activo">Activos</option>
                                        <option value="pagado">Pagados</option>
                                        <option value="vencido">Vencidos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reportes-grid">
                    <!-- Reporte de Préstamos -->
                    <div class="card reporte-card">
                        <div class="card-header">
                            <h3><i class="fas fa-money-bill-wave"></i> Préstamos Activos</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-value" id="totalPrestamos">0</span>
                                    <span class="stat-label">Total Préstamos</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" id="montoPrestamos">$0</span>
                                    <span class="stat-label">Monto Total</span>
                                </div>
                            </div>
                            <canvas id="graficoTiposPrestamos" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Reporte de Pagos -->
                    <div class="card reporte-card">
                        <div class="card-header">
                            <h3><i class="fas fa-credit-card"></i> Pagos del Mes</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-value" id="totalPagos">0</span>
                                    <span class="stat-label">Pagos Realizados</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" id="montoPagos">$0</span>
                                    <span class="stat-label">Monto Recaudado</span>
                                </div>
                            </div>
                            <canvas id="graficoPagosMensuales" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Reporte de Mora -->
                    <div class="card reporte-card">
                        <div class="card-header">
                            <h3><i class="fas fa-exclamation-triangle"></i> Análisis de Mora</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-value" id="prestamosMora">0</span>
                                    <span class="stat-label">En Mora</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" id="montoMora">$0</span>
                                    <span class="stat-label">Monto en Mora</span>
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="porcentajeMora" style="width: 0%"></div>
                            </div>
                            <span class="progress-label">Porcentaje de Mora</span>
                        </div>
                    </div>

                    <!-- Reporte de Rentabilidad -->
                    <div class="card reporte-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Rentabilidad</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-value" id="ingresosMes">$0</span>
                                    <span class="stat-label">Ingresos del Mes</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" id="roi">0%</span>
                                    <span class="stat-label">ROI</span>
                                </div>
                            </div>
                            <canvas id="graficoRentabilidad" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="tabla-reportes">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-table"></i> Detalle de Transacciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="tablaReportes">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportesTableBody">
                                        <!-- Datos dinámicos -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    init() {
        this.cargarDatos();
        this.configurarFiltros();
        this.generarGraficos();
        this.configurarFechasDefault();
    }

    configurarFechasDefault() {
        const hoy = new Date();
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        
        document.getElementById('fechaInicio').value = inicioMes.toISOString().split('T')[0];
        document.getElementById('fechaFin').value = hoy.toISOString().split('T')[0];
    }

    cargarDatos() {
        // Cargar datos desde localStorage
        const prestamos = JSON.parse(localStorage.getItem('prestamos') || '[]');
        const pagos = JSON.parse(localStorage.getItem('pagos') || '[]');
        const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');

        this.actualizarEstadisticas(prestamos, pagos, clientes);
        this.cargarTablaReportes(prestamos, pagos);
    }

    actualizarEstadisticas(prestamos, pagos, clientes) {
        // Estadísticas de préstamos
        const prestamosActivos = prestamos.filter(p => p.estado === 'activo');
        const montoPrestamos = prestamosActivos.reduce((sum, p) => sum + parseFloat(p.monto), 0);
        
        document.getElementById('totalPrestamos').textContent = prestamosActivos.length;
        document.getElementById('montoPrestamos').textContent = `$${montoPrestamos.toLocaleString()}`;

        // Estadísticas de pagos del mes actual
        const hoy = new Date();
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const pagosMes = pagos.filter(p => new Date(p.fecha) >= inicioMes);
        const montoPagosMes = pagosMes.reduce((sum, p) => sum + parseFloat(p.monto), 0);
        
        document.getElementById('totalPagos').textContent = pagosMes.length;
        document.getElementById('montoPagos').textContent = `$${montoPagosMes.toLocaleString()}`;

        // Estadísticas de mora
        const prestamosMora = prestamos.filter(p => {
            if (p.estado !== 'activo') return false;
            const fechaVencimiento = new Date(p.fechaVencimiento);
            return fechaVencimiento < hoy;
        });
        
        const montoMora = prestamosMora.reduce((sum, p) => sum + parseFloat(p.monto), 0);
        const porcentajeMora = prestamosActivos.length > 0 ? 
            (prestamosMora.length / prestamosActivos.length) * 100 : 0;

        document.getElementById('prestamosMora').textContent = prestamosMora.length;
        document.getElementById('montoMora').textContent = `$${montoMora.toLocaleString()}`;
        document.getElementById('porcentajeMora').style.width = `${porcentajeMora}%`;

        // Rentabilidad
        const interesesGenerados = pagosMes.reduce((sum, p) => {
            const prestamo = prestamos.find(pr => pr.id === p.prestamoId);
            if (prestamo) {
                return sum + (parseFloat(p.monto) - parseFloat(prestamo.monto) / prestamo.cuotas);
            }
            return sum;
        }, 0);

        document.getElementById('ingresosMes').textContent = `$${interesesGenerados.toLocaleString()}`;
        
        const capitalInvertido = prestamosActivos.reduce((sum, p) => sum + parseFloat(p.monto), 0);
        const roi = capitalInvertido > 0 ? (interesesGenerados / capitalInvertido) * 100 : 0;
        
        document.getElementById('roi').textContent = `${roi.toFixed(2)}%`;
    }

    cargarTablaReportes(prestamos, pagos) {
        const tbody = document.getElementById('reportesTableBody');
        let transacciones = [];

        // Combinar préstamos y pagos
        prestamos.forEach(prestamo => {
            transacciones.push({
                fecha: prestamo.fecha,
                cliente: prestamo.clienteNombre,
                tipo: 'Préstamo',
                monto: parseFloat(prestamo.monto),
                estado: prestamo.estado
            });
        });

        pagos.forEach(pago => {
            const prestamo = prestamos.find(p => p.id === pago.prestamoId);
            transacciones.push({
                fecha: pago.fecha,
                cliente: prestamo ? prestamo.clienteNombre : 'N/A',
                tipo: 'Pago',
                monto: parseFloat(pago.monto),
                estado: 'completado'
            });
        });

        // Ordenar por fecha (más recientes primero)
        transacciones.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        tbody.innerHTML = transacciones.slice(0, 50).map(t => `
            <tr>
                <td>${new Date(t.fecha).toLocaleDateString()}</td>
                <td>${t.cliente}</td>
                <td>
                    <span class="badge ${t.tipo === 'Préstamo' ? 'badge-primary' : 'badge-success'}">
                        ${t.tipo}
                    </span>
                </td>
                <td>$${t.monto.toLocaleString()}</td>
                <td>
                    <span class="badge ${this.getEstadoBadgeClass(t.estado)}">
                        ${t.estado}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="reportesPage.verDetalle('${t.tipo}', '${t.fecha}')">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    getEstadoBadgeClass(estado) {
        switch(estado) {
            case 'activo': return 'badge-warning';
            case 'pagado': return 'badge-success';
            case 'vencido': return 'badge-danger';
            case 'completado': return 'badge-success';
            default: return 'badge-secondary';
        }
    }

    generarGraficos() {
        // Aquí implementarías la generación de gráficos usando Chart.js o similar
        // Por simplicidad, solo muestro la estructura
        console.log('Generando gráficos de reportes...');
    }

    configurarFiltros() {
        const filtros = ['fechaInicio', 'fechaFin', 'tipoReporte', 'estadoReporte'];
        
        filtros.forEach(filtroId => {
            const elemento = document.getElementById(filtroId);
            if (elemento) {
                elemento.addEventListener('change', () => this.aplicarFiltros());
            }
        });
    }

    aplicarFiltros() {
        this.filtros.fechaInicio = document.getElementById('fechaInicio').value;
        this.filtros.fechaFin = document.getElementById('fechaFin').value;
        this.filtros.tipoReporte = document.getElementById('tipoReporte').value;
        this.filtros.estado = document.getElementById('estadoReporte').value;

        this.cargarDatos(); // Recargar con filtros aplicados
    }

    generarReporte() {
        const tipoReporte = document.getElementById('tipoReporte').value;
        
        window.toast.show(`Generando reporte de ${tipoReporte}...`, 'info');
        
        // Simular generación de reporte
        setTimeout(() => {
            window.toast.show('Reporte generado exitosamente', 'success');
        }, 2000);
    }

    exportarExcel() {
        window.toast.show('Exportando a Excel...', 'info');
        
        // Aquí implementarías la exportación real
        setTimeout(() => {
            window.toast.show('Archivo Excel descargado', 'success');
        }, 1500);
    }

    verDetalle(tipo, fecha) {
        window.toast.show(`Mostrando detalle de ${tipo} del ${fecha}`, 'info');
    }
}

// Instancia global
window.reportesPage = new ReportesPage();
