# 🧮 COPIA DE SEGURIDAD DE FÓRMULAS MATEMÁTICAS
## Sistema de Préstamos - Fórmulas Correctas

📅 **Fecha de Backup:** 28 de agosto de 2025  
🎯 **Versión:** Sistema Modular v1.0  
⚡ **Estado:** Probado y Funcionando  

---

## 📊 TASAS AUTOMÁTICAS CONFIGURADAS

### 🔴 SOLO INTERÉS
| Frecuencia | Tasa |
|------------|------|
| Semanal    | 5%   |
| Quincenal  | 10%  |
| 15 y 30    | 10%  |
| Mensual    | 20%  |

### 📊 INTERÉS FIJO (CUOTA FIJA)
| Frecuencia | Tasa |
|------------|------|
| Semanal    | 5%   |
| Quincenal  | 10%  |
| 15 y 30    | 10%  |
| Mensual    | 20%  |

---

## 🧮 FÓRMULAS MATEMÁTICAS

### 1️⃣ FÓRMULA SOLO INTERÉS
```
Pago por Período = Capital × Tasa

Donde:
- Capital = Monto del préstamo
- Tasa = Tasa de interés (en decimal)

Ejemplo:
- Capital: RD$50,000
- Tasa: 10% (0.10)
- Pago por período: 50,000 × 0.10 = RD$5,000
```

**📝 Código JavaScript:**
```javascript
// CÁLCULO SOLO INTERÉS
const interesPorPeriodo = monto * (tasa / 100);
const totalIntereses = interesPorPeriodo * cuotas;
const totalAPagar = monto + totalIntereses;
```

### 2️⃣ FÓRMULA INTERÉS FIJO (CUOTA FIJA)
```
M = P/n + (P·r)

Donde:
- M = Cuota mensual (lo que el cliente paga cada período)
- P = Monto total del préstamo
- n = Número total de cuotas
- r = Tasa de interés por período (en decimal)

Componentes:
- P/n = Parte del capital que se paga cada cuota
- P·r = Interés fijo que se aplica sobre el monto total cada cuota
```

**📝 Código JavaScript:**
```javascript
// CÁLCULO CUOTA FIJA - FÓRMULA CORRECTA: M = P/n + (P · r)
const tasaDecimal = tasa / 100;
const capitalPorCuota = monto / cuotas; // P/n - Parte del capital por cuota
const interesFijoPorCuota = monto * tasaDecimal; // P · r - Interés fijo por cuota
const cuotaFija = capitalPorCuota + interesFijoPorCuota; // M - Cuota total
const totalIntereses = interesFijoPorCuota * cuotas;
const montoTotal = monto + totalIntereses;
```

---

## � DIFERENCIAS ENTRE FRECUENCIAS DE PAGO

### 🔄 QUINCENAL vs 📊 15 y 30

**QUINCENAL:**
- ✅ Pago cada 15 días desde la fecha de creación del préstamo
- ✅ Fechas variables según el día de inicio
- ✅ Ejemplo: Si se crea el 10 de enero, los pagos serían: 25 enero, 9 febrero, 24 febrero, etc.
- ✅ Ideal para préstamos con fechas flexibles

**15 y 30:**
- ✅ Pagos siempre los días 15 y 30 de cada mes
- ✅ Fechas fijas independiente de cuándo se creó el préstamo
- ✅ Ejemplo: Sin importar cuándo se cree, los pagos serían: 15 enero, 30 enero, 15 febrero, 30 febrero, etc.
- ✅ Ideal para préstamos con fechas estándar de pago

**COMPARACIÓN:**
| Aspecto | Quincenal | 15 y 30 |
|---------|-----------|---------|
| Intervalo | Cada 15 días exactos | Días 15 y 30 de cada mes |
| Fechas | Variables | Fijas |
| Tasa | 10% | 10% |
| Uso recomendado | Préstamos flexibles | Préstamos estándar |

---

## �📋 EJEMPLOS PRÁCTICOS

### Ejemplo 1: Solo Interés
```
Capital: RD$50,000
Tasa: 10% quincenal
Cuotas: 8

Cálculo:
- Pago por período: 50,000 × 0.10 = RD$5,000
- Total intereses: 5,000 × 8 = RD$40,000
- Total a pagar: 50,000 + 40,000 = RD$90,000
```

### Ejemplo 2: Interés Fijo (Cuota Fija)
```
Capital: RD$38,850
Tasa: 5.1% mensual
Cuotas: 37

Cálculo usando M = P/n + (P·r):
- Capital por cuota: 38,850 ÷ 37 = RD$1,050.00
- Interés por cuota: 38,850 × 0.051 = RD$1,981.35
- Cuota fija: 1,050.00 + 1,981.35 = RD$3,031.35

Total a pagar: 3,031.35 × 37 = RD$112,159.95
Total intereses: 112,159.95 - 38,850 = RD$73,309.95
```

---

## 🔧 CONFIGURACIÓN TÉCNICA

### Eventos JavaScript para Autocompletado
```javascript
// Eventos que disparan actualización automática
- onchange="actualizarTasaAutomatica()" // En select de frecuencia y tipo
- oninput="actualizarCalculadoraEnTiempoReal()" // En campos numéricos
```

### Validaciones
```javascript
// Validar campos antes de calcular
if (monto <= 0 || tasa <= 0 || cuotas <= 0) {
    // Mostrar mensaje de espera
    return;
}
```

---

## 📝 NOTAS IMPORTANTES

1. **Solo Interés**: Cada pago cubre únicamente intereses. Pagos adicionales reducen el capital.

2. **Interés Fijo**: Cuota fija que incluye parte del capital + interés fijo sobre el monto total.

3. **Diferencia Clave**: 
   - Solo Interés: Capital permanece igual hasta pagos extra
   - Interés Fijo: Capital se reduce automáticamente con cada cuota

4. **Tasas Automáticas**: Se actualizan según frecuencia y tipo seleccionado.

---

## 🎯 ARCHIVOS AFECTADOS

### Archivo Principal
- `index.php` - Contiene toda la lógica de cálculo

### Funciones Clave
- `actualizarTasaAutomatica()` - Autocompletado de tasas
- `actualizarCalculadoraEnTiempoReal()` - Cálculos en tiempo real
- `calcularEnTiempoReal()` - Función de calculadora alternativa

---

## 🚨 INSTRUCCIONES DE RECUPERACIÓN

Si las fórmulas se dañan, buscar y reemplazar en `index.php`:

1. **Para Solo Interés**, buscar: `// CÁLCULO SOLO INTERÉS`
2. **Para Interés Fijo**, buscar: `// CÁLCULO CUOTA FIJA`
3. **Para Autocompletado**, buscar: `function actualizarTasaAutomatica()`

Copiar el código JavaScript de este archivo de backup directamente.

---

## ✅ VERIFICACIÓN DE FUNCIONAMIENTO

Para probar que las fórmulas funcionan:

1. Ir a sección "💼 Préstamos"
2. Completar: Monto 38850, Tasa (automática), Cuotas 37
3. Seleccionar "Interés Fijo" + "Mensual" 
4. Verificar que la cuota fija sea aproximadamente RD$3,031.35

---

**📞 Contacto:** GitHub Copilot  
**📁 Ubicación:** `panel_prestamo_modular/FORMULAS_BACKUP.md`  
**🔄 Última Actualización:** 28/08/2025 - 13:50 hrs
