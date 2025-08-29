/*
 * =====================================================================
 * BACKUP CÓDIGO JAVASCRIPT - FÓRMULAS DE PRÉSTAMOS
 * =====================================================================
 * Fecha: 28 de agosto de 2025
 * Sistema: Préstamos Modular v1.0
 * Autor: GitHub Copilot
 * =====================================================================
 */

// ============================================================================
// FUNCIÓN 1: AUTOCOMPLETADO DE TASAS AUTOMÁTICAS
// ============================================================================

function actualizarTasaAutomatica() {
    const frecuencia = document.getElementById('prestamo-frecuencia').value;
    const tipo = document.getElementById('prestamo-tipo').value;
    const tasaInput = document.getElementById('prestamo-tasa');
    
    if (!frecuencia || !tipo || !tasaInput) {
        console.log('Campos no encontrados para autocompletado');
        return;
    }
    
    let tasaAutomatica = 0;
    
    // Configuración de tasas predeterminadas
    const tasasPredeterminadas = {
        interes: {
            semanal: 5,
            quincenal: 10,
            '15y30': 10,
            mensual: 20
        },
        cuota: {
            semanal: 5,
            quincenal: 10,
            '15y30': 10,
            mensual: 20
        }
    };
    
    // Obtener configuración desde campos o usar predeterminadas
    const configuracionTasas = {
        interes: {
            semanal: parseFloat(document.getElementById('config-interes-semanal')?.value) || tasasPredeterminadas.interes.semanal,
            quincenal: parseFloat(document.getElementById('config-interes-quincenal')?.value) || tasasPredeterminadas.interes.quincenal,
            '15y30': parseFloat(document.getElementById('config-interes-15y30')?.value) || tasasPredeterminadas.interes['15y30'],
            mensual: parseFloat(document.getElementById('config-interes-mensual')?.value) || tasasPredeterminadas.interes.mensual
        },
        cuota: {
            semanal: parseFloat(document.getElementById('config-cuota-semanal')?.value) || tasasPredeterminadas.cuota.semanal,
            quincenal: parseFloat(document.getElementById('config-cuota-quincenal')?.value) || tasasPredeterminadas.cuota.quincenal,
            '15y30': parseFloat(document.getElementById('config-cuota-15y30')?.value) || tasasPredeterminadas.cuota['15y30'],
            mensual: parseFloat(document.getElementById('config-cuota-mensual')?.value) || tasasPredeterminadas.cuota.mensual
        }
    };
    
    // Seleccionar la tasa automática
    if (tipo === 'interes') {
        tasaAutomatica = configuracionTasas.interes[frecuencia];
    } else if (tipo === 'cuota') {
        tasaAutomatica = configuracionTasas.cuota[frecuencia];
    }
    
    // Actualizar el campo de tasa con efectos visuales
    if (tasaAutomatica && tasaAutomatica > 0) {
        const tasaAnterior = tasaInput.value;
        tasaInput.value = tasaAutomatica;
        
        // Efectos visuales solo si cambió
        if (tasaAnterior != tasaAutomatica) {
            tasaInput.classList.add('auto-updated');
            tasaInput.style.background = 'linear-gradient(135deg, #e8f5e8, #f1f8e9)';
            tasaInput.style.border = '2px solid #4caf50';
            tasaInput.style.boxShadow = '0 0 10px rgba(76, 175, 80, 0.3)';
            
            const indicador = document.getElementById('indicador-tasa-auto');
            if (indicador) {
                indicador.style.display = 'inline';
                indicador.className = 'indicador-auto';
            }
            
            setTimeout(() => {
                tasaInput.classList.remove('auto-updated');
                tasaInput.style.background = '';
                tasaInput.style.border = '';
                tasaInput.style.boxShadow = '';
                
                if (indicador) {
                    indicador.style.display = 'none';
                    indicador.className = '';
                }
            }, 4000);
            
            console.log(`🎯 Tasa actualizada automáticamente: ${frecuencia} + ${tipo} = ${tasaAutomatica}%`);
        }
    }
    
    // Actualizar calculadora en tiempo real
    actualizarCalculadoraEnTiempoReal();
}

// ============================================================================
// FUNCIÓN 2: CALCULADORA EN TIEMPO REAL
// ============================================================================

function actualizarCalculadoraEnTiempoReal() {
    const monto = parseFloat(document.getElementById('prestamo-monto').value) || 0;
    const tasa = parseFloat(document.getElementById('prestamo-tasa').value) || 0;
    const cuotas = parseInt(document.getElementById('prestamo-cuotas').value) || 0;
    const tipo = document.getElementById('prestamo-tipo').value || 'interes';
    const frecuencia = document.getElementById('prestamo-frecuencia').value || 'quincenal';
    
    const resultadoDiv = document.getElementById('resultado-calculo');
    
    // Validar datos
    if (monto <= 0 || tasa <= 0 || cuotas <= 0) {
        resultadoDiv.innerHTML = `
            <div style="text-align: center; color: #666; font-style: italic; padding: 20px;">
                <div style="font-size: 48px; margin-bottom: 10px;">🧮</div>
                <p>Completa los campos del préstamo para ver los cálculos automáticos...</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    if (tipo === 'interes') {
        // ========================================================================
        // FÓRMULA SOLO INTERÉS
        // ========================================================================
        const interesPorPeriodo = monto * (tasa / 100);
        const totalIntereses = interesPorPeriodo * cuotas;
        const totalAPagar = monto + totalIntereses;
        
        html = `
            <div style="border-left: 4px solid #e53935; padding-left: 15px;">
                <h4 style="color: #c62828; margin-bottom: 15px;">
                    🔴 Solo Interés - Frecuencia ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 1.1em;">
                    <div style="background: #e8f5e8; padding: 12px; border-radius: 6px;">
                        <strong>💰 Capital:</strong><br>
                        RD$${number_format(monto, 2)}
                    </div>
                    <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                        <strong>📈 Interés total:</strong><br>
                        RD$${number_format(totalIntereses, 2)}
                    </div>
                    <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                        <strong>⭐ Pago por período:</strong><br>
                        RD$${number_format(interesPorPeriodo, 2)}
                    </div>
                    <div style="background: #e3f2fd; padding: 12px; border-radius: 6px;">
                        <strong>💵 Total a pagar:</strong><br>
                        RD$${number_format(totalAPagar, 2)}
                    </div>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #fff8e1; border-radius: 6px; border-left: 3px solid #ffc107;">
                    <small><strong>📝 Nota:</strong> Cada pago cubre solo intereses. Pagos adicionales reducen el capital.</small>
                </div>
            </div>
        `;
    } else {
        // ========================================================================
        // FÓRMULA INTERÉS FIJO (CUOTA FIJA): M = P/n + (P·r)
        // ========================================================================
        const tasaDecimal = tasa / 100;
        const capitalPorCuota = monto / cuotas; // P/n - Parte del capital por cuota
        const interesFijoPorCuota = monto * tasaDecimal; // P · r - Interés fijo por cuota
        const cuotaFija = capitalPorCuota + interesFijoPorCuota; // M - Cuota total
        const totalIntereses = interesFijoPorCuota * cuotas;
        const montoTotal = monto + totalIntereses;
        
        html = `
            <div style="border-left: 4px solid #2196f3; padding-left: 15px;">
                <h4 style="color: #1976d2; margin-bottom: 15px;">
                    📊 Cuota Fija - Frecuencia ${frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1)}
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 1.1em;">
                    <div style="background: #e8f5e8; padding: 12px; border-radius: 6px;">
                        <strong>💰 Capital:</strong><br>
                        RD$${number_format(monto, 2)}
                    </div>
                    <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                        <strong>📈 Interés total:</strong><br>
                        RD$${number_format(totalIntereses, 2)}
                    </div>
                    <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                        <strong>⭐ Cuota fija:</strong><br>
                        RD$${number_format(cuotaFija, 2)}
                    </div>
                    <div style="background: #e3f2fd; padding: 12px; border-radius: 6px;">
                        <strong>💵 Total a pagar:</strong><br>
                        RD$${number_format(montoTotal, 2)}
                    </div>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #e8f5e8; border-radius: 6px; border-left: 3px solid #4caf50;">
                    <small><strong>📝 Fórmula:</strong> M = P/n + (P·r) = RD$${number_format(capitalPorCuota, 2)} + RD$${number_format(interesFijoPorCuota, 2)} = RD$${number_format(cuotaFija, 2)}</small>
                </div>
            </div>
        `;
    }
    
    resultadoDiv.innerHTML = html;
}

// ============================================================================
// FUNCIÓN 3: CALCULADORA ALTERNATIVA (calcularEnTiempoReal)
// ============================================================================

function calcularEnTiempoReal() {
    const monto = parseFloat(document.getElementById('prestamo-monto').value) || 0;
    const tasa = parseFloat(document.getElementById('prestamo-tasa').value) || 0;
    const cuotas = parseInt(document.getElementById('prestamo-cuotas').value) || 1;
    const tipo = document.getElementById('prestamo-tipo').value || 'interes';
    const frecuencia = document.getElementById('prestamo-frecuencia').value || 'quincenal';
    
    const resultadoDiv = document.getElementById('resultado-calculo');
    
    if (monto <= 0 || tasa <= 0) {
        resultadoDiv.innerHTML = `
            <div style="text-align: center; color: #666; font-style: italic;">
                Ingresa el monto y la tasa para ver los cálculos automáticos...
            </div>
        `;
        return;
    }
    
    let html = '';
    
    if (tipo === 'interes') {
        // CÁLCULO SOLO INTERÉS
        const interesPorPeriodo = monto * (tasa / 100);
        
        html = `
            <div style="border-left: 4px solid #2196f3; padding-left: 15px;">
                <h4 style="color: #1976d2; margin-bottom: 10px;">💰 Solo Interés</h4>
                <div style="font-size: 1.1em; line-height: 1.8;">
                    <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                        <strong>Capital:</strong> RD$${number_format(monto, 2)}
                    </div>
                    <div style="background: #fff3e0; padding: 12px; border-radius: 6px;">
                        <strong>Interés por período:</strong> RD$${number_format(interesPorPeriodo, 2)}
                    </div>
                </div>
            </div>
        `;
    } else {
        // CÁLCULO CUOTA FIJA - FÓRMULA CORRECTA: M = P/n + (P·r)
        const tasaDecimal = tasa / 100;
        const capitalPorCuota = monto / cuotas; // P/n
        const interesFijoPorCuota = monto * tasaDecimal; // P · r
        const cuotaFija = capitalPorCuota + interesFijoPorCuota; // M
        const totalIntereses = interesFijoPorCuota * cuotas;
        const montoTotal = monto + totalIntereses;
        
        html = `
            <div style="border-left: 4px solid #4caf50; padding-left: 15px;">
                <h4 style="color: #388e3c; margin-bottom: 10px;">📈 Cuota Fija</h4>
                <div style="font-size: 1.1em; line-height: 1.8;">
                    <div style="background: #e8f5e8; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                        <strong>Capital:</strong> RD$${number_format(monto, 2)}
                    </div>
                    <div style="background: #fff3e0; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                        <strong>Interés total:</strong> RD$${number_format(totalIntereses, 2)}
                    </div>
                    <div style="background: #f3e5f5; padding: 12px; border-radius: 6px;">
                        <strong>Cuota fija:</strong> RD$${number_format(cuotaFija, 2)} (${cuotas} cuotas)
                    </div>
                </div>
            </div>
        `;
    }
    
    resultadoDiv.innerHTML = html;
}

// ============================================================================
// FUNCIÓN 4: CONFIGURACIÓN DE TASAS
// ============================================================================

function obtenerConfiguracionTasas() {
    // Valores predeterminados
    const predeterminados = {
        interes: { semanal: 5, quincenal: 10, mensual: 20 },
        cuota: { semanal: 5, quincenal: 10, mensual: 20 }
    };
    
    return {
        interes: {
            semanal: parseFloat(document.getElementById('config-interes-semanal')?.value) || predeterminados.interes.semanal,
            quincenal: parseFloat(document.getElementById('config-interes-quincenal')?.value) || predeterminados.interes.quincenal,
            mensual: parseFloat(document.getElementById('config-interes-mensual')?.value) || predeterminados.interes.mensual
        },
        cuota: {
            semanal: parseFloat(document.getElementById('config-cuota-semanal')?.value) || predeterminados.cuota.semanal,
            quincenal: parseFloat(document.getElementById('config-cuota-quincenal')?.value) || predeterminados.cuota.quincenal,
            mensual: parseFloat(document.getElementById('config-cuota-mensual')?.value) || predeterminados.cuota.mensual
        }
    };
}

// ============================================================================
// FUNCIÓN 5: FORMATEO DE NÚMEROS
// ============================================================================

function number_format(number, decimals) {
    return new Intl.NumberFormat('es-DO', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

// ============================================================================
// EVENTOS DE INICIALIZACIÓN
// ============================================================================

// Configurar eventos en los campos del formulario
function configurarCalculadoraInteligente() {
    const campos = ['prestamo-monto', 'prestamo-tasa', 'prestamo-cuotas', 'prestamo-tipo', 'prestamo-frecuencia'];
    
    campos.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('input', calcularEnTiempoReal);
            campo.addEventListener('change', calcularEnTiempoReal);
        }
    });
    
    calcularEnTiempoReal(); // Cálculo inicial
}

/*
 * =====================================================================
 * INSTRUCCIONES DE USO DE ESTE BACKUP
 * =====================================================================
 * 
 * 1. Si las fórmulas se dañan, copiar las funciones de este archivo
 * 2. Reemplazar en index.php las funciones correspondientes
 * 3. Verificar que los IDs de los elementos HTML coincidan
 * 4. Probar con el ejemplo: 38,850 pesos, 5.1%, 37 cuotas = 3,031.35
 * 
 * =====================================================================
 */
