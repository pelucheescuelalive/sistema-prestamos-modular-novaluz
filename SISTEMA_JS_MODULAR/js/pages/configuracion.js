/**
 * Módulo de Configuración del Sistema
 * Configuración avanzada y personalización del sistema
 */

export class ConfiguracionPage {
    constructor() {
        this.configuracion = this.cargarConfiguracion();
    }

    render() {
        return `
            <div class="configuracion-container">
                <div class="page-header">
                    <h1><i class="fas fa-cogs"></i> Configuración del Sistema</h1>
                    <div class="header-actions">
                        <button class="btn btn-success" onclick="configuracionPage.guardarConfiguracion()">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <button class="btn btn-warning" onclick="configuracionPage.restaurarDefecto()">
                            <i class="fas fa-undo"></i> Restaurar Defecto
                        </button>
                    </div>
                </div>

                <div class="configuracion-grid">
                    <!-- Configuración General -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-building"></i> Información de la Empresa</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nombre de la Empresa:</label>
                                <input type="text" id="nombreEmpresa" class="form-control" 
                                       value="${this.configuracion.empresa.nombre}">
                            </div>
                            <div class="form-group">
                                <label>RUC/NIT:</label>
                                <input type="text" id="rucEmpresa" class="form-control" 
                                       value="${this.configuracion.empresa.ruc}">
                            </div>
                            <div class="form-group">
                                <label>Dirección:</label>
                                <input type="text" id="direccionEmpresa" class="form-control" 
                                       value="${this.configuracion.empresa.direccion}">
                            </div>
                            <div class="form-group">
                                <label>Teléfono:</label>
                                <input type="tel" id="telefonoEmpresa" class="form-control" 
                                       value="${this.configuracion.empresa.telefono}">
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" id="emailEmpresa" class="form-control" 
                                       value="${this.configuracion.empresa.email}">
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Préstamos -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-percentage"></i> Configuración de Préstamos</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tasa de Interés por Defecto (%):</label>
                                <input type="number" id="tasaInteres" class="form-control" 
                                       value="${this.configuracion.prestamos.tasaDefecto}" 
                                       min="0" max="100" step="0.1">
                            </div>
                            <div class="form-group">
                                <label>Plazo Máximo (meses):</label>
                                <input type="number" id="plazoMaximo" class="form-control" 
                                       value="${this.configuracion.prestamos.plazoMaximo}" 
                                       min="1" max="120">
                            </div>
                            <div class="form-group">
                                <label>Monto Mínimo:</label>
                                <input type="number" id="montoMinimo" class="form-control" 
                                       value="${this.configuracion.prestamos.montoMinimo}" 
                                       min="0">
                            </div>
                            <div class="form-group">
                                <label>Monto Máximo:</label>
                                <input type="number" id="montoMaximo" class="form-control" 
                                       value="${this.configuracion.prestamos.montoMaximo}" 
                                       min="0">
                            </div>
                            <div class="form-group">
                                <label>Mora por Día Vencido (%):</label>
                                <input type="number" id="moraDiaria" class="form-control" 
                                       value="${this.configuracion.prestamos.moraDiaria}" 
                                       min="0" max="10" step="0.1">
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Notificaciones -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-bell"></i> Notificaciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input type="checkbox" id="notifVencimientos" class="form-check-input" 
                                       ${this.configuracion.notificaciones.vencimientos ? 'checked' : ''}>
                                <label for="notifVencimientos" class="form-check-label">
                                    Notificar vencimientos próximos
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="notifPagos" class="form-check-input" 
                                       ${this.configuracion.notificaciones.pagos ? 'checked' : ''}>
                                <label for="notifPagos" class="form-check-label">
                                    Notificar pagos recibidos
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="notifMora" class="form-check-input" 
                                       ${this.configuracion.notificaciones.mora ? 'checked' : ''}>
                                <label for="notifMora" class="form-check-label">
                                    Alertas de mora
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Días de anticipación para recordatorios:</label>
                                <input type="number" id="diasAnticipacion" class="form-control" 
                                       value="${this.configuracion.notificaciones.diasAnticipacion}" 
                                       min="1" max="30">
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Seguridad -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-shield-alt"></i> Seguridad</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input type="checkbox" id="requiereAutenticacion" class="form-check-input" 
                                       ${this.configuracion.seguridad.requiereAuth ? 'checked' : ''}>
                                <label for="requiereAutenticacion" class="form-check-label">
                                    Requerir autenticación
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="backupAutomatico" class="form-check-input" 
                                       ${this.configuracion.seguridad.backupAutomatico ? 'checked' : ''}>
                                <label for="backupAutomatico" class="form-check-label">
                                    Backup automático diario
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Tiempo de sesión (minutos):</label>
                                <input type="number" id="tiempoSesion" class="form-control" 
                                       value="${this.configuracion.seguridad.tiempoSesion}" 
                                       min="5" max="480">
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Interfaz -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-palette"></i> Interfaz de Usuario</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tema:</label>
                                <select id="temaInterfaz" class="form-control">
                                    <option value="light" ${this.configuracion.interfaz.tema === 'light' ? 'selected' : ''}>Claro</option>
                                    <option value="dark" ${this.configuracion.interfaz.tema === 'dark' ? 'selected' : ''}>Oscuro</option>
                                    <option value="auto" ${this.configuracion.interfaz.tema === 'auto' ? 'selected' : ''}>Automático</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Idioma:</label>
                                <select id="idioma" class="form-control">
                                    <option value="es" ${this.configuracion.interfaz.idioma === 'es' ? 'selected' : ''}>Español</option>
                                    <option value="en" ${this.configuracion.interfaz.idioma === 'en' ? 'selected' : ''}>English</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Elementos por página:</label>
                                <select id="elementosPorPagina" class="form-control">
                                    <option value="10" ${this.configuracion.interfaz.elementosPagina === 10 ? 'selected' : ''}>10</option>
                                    <option value="25" ${this.configuracion.interfaz.elementosPagina === 25 ? 'selected' : ''}>25</option>
                                    <option value="50" ${this.configuracion.interfaz.elementosPagina === 50 ? 'selected' : ''}>50</option>
                                    <option value="100" ${this.configuracion.interfaz.elementosPagina === 100 ? 'selected' : ''}>100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Reportes -->
                    <div class="card config-section">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> Reportes</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input type="checkbox" id="reporteAutomatico" class="form-check-input" 
                                       ${this.configuracion.reportes.automatico ? 'checked' : ''}>
                                <label for="reporteAutomatico" class="form-check-label">
                                    Generar reportes automáticos
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Frecuencia de reportes:</label>
                                <select id="frecuenciaReportes" class="form-control">
                                    <option value="diario" ${this.configuracion.reportes.frecuencia === 'diario' ? 'selected' : ''}>Diario</option>
                                    <option value="semanal" ${this.configuracion.reportes.frecuencia === 'semanal' ? 'selected' : ''}>Semanal</option>
                                    <option value="mensual" ${this.configuracion.reportes.frecuencia === 'mensual' ? 'selected' : ''}>Mensual</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Email para reportes:</label>
                                <input type="email" id="emailReportes" class="form-control" 
                                       value="${this.configuracion.reportes.email}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Acciones de Sistema -->
                <div class="card system-actions">
                    <div class="card-header">
                        <h3><i class="fas fa-tools"></i> Herramientas del Sistema</h3>
                    </div>
                    <div class="card-body">
                        <div class="actions-grid">
                            <button class="btn btn-info action-btn" onclick="configuracionPage.exportarDatos()">
                                <i class="fas fa-download"></i>
                                <span>Exportar Datos</span>
                            </button>
                            <button class="btn btn-warning action-btn" onclick="configuracionPage.importarDatos()">
                                <i class="fas fa-upload"></i>
                                <span>Importar Datos</span>
                            </button>
                            <button class="btn btn-success action-btn" onclick="configuracionPage.crearBackup()">
                                <i class="fas fa-save"></i>
                                <span>Crear Backup</span>
                            </button>
                            <button class="btn btn-primary action-btn" onclick="configuracionPage.optimizarBD()">
                                <i class="fas fa-database"></i>
                                <span>Optimizar BD</span>
                            </button>
                            <button class="btn btn-secondary action-btn" onclick="configuracionPage.limpiarCache()">
                                <i class="fas fa-broom"></i>
                                <span>Limpiar Cache</span>
                            </button>
                            <button class="btn btn-danger action-btn" onclick="configuracionPage.resetearSistema()">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Resetear Sistema</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    init() {
        this.aplicarConfiguracionActual();
        this.configurarEventos();
    }

    cargarConfiguracion() {
        const configDefault = {
            empresa: {
                nombre: 'Nova Luz Prestamos',
                ruc: '',
                direccion: '',
                telefono: '',
                email: ''
            },
            prestamos: {
                tasaDefecto: 15.0,
                plazoMaximo: 24,
                montoMinimo: 100,
                montoMaximo: 50000,
                moraDiaria: 0.5
            },
            notificaciones: {
                vencimientos: true,
                pagos: true,
                mora: true,
                diasAnticipacion: 3
            },
            seguridad: {
                requiereAuth: false,
                backupAutomatico: true,
                tiempoSesion: 60
            },
            interfaz: {
                tema: 'light',
                idioma: 'es',
                elementosPagina: 25
            },
            reportes: {
                automatico: true,
                frecuencia: 'mensual',
                email: ''
            }
        };

        const configGuardada = localStorage.getItem('configuracion_sistema');
        return configGuardada ? { ...configDefault, ...JSON.parse(configGuardada) } : configDefault;
    }

    aplicarConfiguracionActual() {
        // Aplicar tema
        const tema = this.configuracion.interfaz.tema;
        if (tema !== 'auto') {
            document.documentElement.setAttribute('data-theme', tema);
        }
    }

    configurarEventos() {
        // Evento para cambio de tema en tiempo real
        document.getElementById('temaInterfaz').addEventListener('change', (e) => {
            const nuevoTema = e.target.value;
            if (nuevoTema !== 'auto') {
                document.documentElement.setAttribute('data-theme', nuevoTema);
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
        });
    }

    guardarConfiguracion() {
        // Recopilar todos los valores del formulario
        const nuevaConfig = {
            empresa: {
                nombre: document.getElementById('nombreEmpresa').value,
                ruc: document.getElementById('rucEmpresa').value,
                direccion: document.getElementById('direccionEmpresa').value,
                telefono: document.getElementById('telefonoEmpresa').value,
                email: document.getElementById('emailEmpresa').value
            },
            prestamos: {
                tasaDefecto: parseFloat(document.getElementById('tasaInteres').value),
                plazoMaximo: parseInt(document.getElementById('plazoMaximo').value),
                montoMinimo: parseFloat(document.getElementById('montoMinimo').value),
                montoMaximo: parseFloat(document.getElementById('montoMaximo').value),
                moraDiaria: parseFloat(document.getElementById('moraDiaria').value)
            },
            notificaciones: {
                vencimientos: document.getElementById('notifVencimientos').checked,
                pagos: document.getElementById('notifPagos').checked,
                mora: document.getElementById('notifMora').checked,
                diasAnticipacion: parseInt(document.getElementById('diasAnticipacion').value)
            },
            seguridad: {
                requiereAuth: document.getElementById('requiereAutenticacion').checked,
                backupAutomatico: document.getElementById('backupAutomatico').checked,
                tiempoSesion: parseInt(document.getElementById('tiempoSesion').value)
            },
            interfaz: {
                tema: document.getElementById('temaInterfaz').value,
                idioma: document.getElementById('idioma').value,
                elementosPagina: parseInt(document.getElementById('elementosPorPagina').value)
            },
            reportes: {
                automatico: document.getElementById('reporteAutomatico').checked,
                frecuencia: document.getElementById('frecuenciaReportes').value,
                email: document.getElementById('emailReportes').value
            }
        };

        // Guardar en localStorage
        localStorage.setItem('configuracion_sistema', JSON.stringify(nuevaConfig));
        this.configuracion = nuevaConfig;

        window.toast.show('Configuración guardada exitosamente', 'success');
    }

    restaurarDefecto() {
        if (confirm('¿Está seguro que desea restaurar la configuración por defecto? Se perderán todos los cambios.')) {
            localStorage.removeItem('configuracion_sistema');
            window.toast.show('Configuración restaurada por defecto', 'info');
            
            // Recargar la página para aplicar cambios
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    exportarDatos() {
        window.toast.show('Preparando exportación de datos...', 'info');
        
        const datos = {
            clientes: JSON.parse(localStorage.getItem('clientes') || '[]'),
            prestamos: JSON.parse(localStorage.getItem('prestamos') || '[]'),
            pagos: JSON.parse(localStorage.getItem('pagos') || '[]'),
            configuracion: this.configuracion,
            fechaExportacion: new Date().toISOString()
        };

        const blob = new Blob([JSON.stringify(datos, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `backup_novaluz_${new Date().toISOString().split('T')[0]}.json`;
        a.click();

        window.toast.show('Datos exportados exitosamente', 'success');
    }

    importarDatos() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json';
        
        input.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const datos = JSON.parse(e.target.result);
                        
                        if (confirm('¿Está seguro que desea importar estos datos? Se sobrescribirán los datos actuales.')) {
                            if (datos.clientes) localStorage.setItem('clientes', JSON.stringify(datos.clientes));
                            if (datos.prestamos) localStorage.setItem('prestamos', JSON.stringify(datos.prestamos));
                            if (datos.pagos) localStorage.setItem('pagos', JSON.stringify(datos.pagos));
                            if (datos.configuracion) localStorage.setItem('configuracion_sistema', JSON.stringify(datos.configuracion));
                            
                            window.toast.show('Datos importados exitosamente', 'success');
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } catch (error) {
                        window.toast.show('Error al importar datos. Archivo inválido.', 'error');
                    }
                };
                reader.readAsText(file);
            }
        };
        
        input.click();
    }

    crearBackup() {
        window.toast.show('Creando backup del sistema...', 'info');
        
        // Simular creación de backup
        setTimeout(() => {
            this.exportarDatos();
            window.toast.show('Backup creado exitosamente', 'success');
        }, 1000);
    }

    optimizarBD() {
        window.toast.show('Optimizando base de datos...', 'info');
        
        // Simular optimización
        setTimeout(() => {
            window.toast.show('Base de datos optimizada', 'success');
        }, 2000);
    }

    limpiarCache() {
        // Limpiar datos de cache del navegador
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    caches.delete(name);
                });
            });
        }
        
        window.toast.show('Cache limpiado exitosamente', 'success');
    }

    resetearSistema() {
        if (confirm('¡ADVERTENCIA! ¿Está seguro que desea resetear completamente el sistema? Se perderán TODOS los datos.')) {
            if (confirm('Esta acción es IRREVERSIBLE. ¿Continuar?')) {
                localStorage.clear();
                window.toast.show('Sistema reseteado. Recargando...', 'warning');
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        }
    }
}

// Instancia global
window.configuracionPage = new ConfiguracionPage();
