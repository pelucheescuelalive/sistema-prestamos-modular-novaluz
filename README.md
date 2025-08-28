# 🚀 Sistema de Préstamos Modular - Nova Luz Pro

## 📋 Descripción
Sistema web modular para la gestión integral de préstamos, desarrollado en PHP con base de datos SQLite. Incluye gestión de clientes, cálculo automático de cuotas, diferentes modalidades de préstamo y panel administrativo completo.

## ✨ Características Principales

### 💰 Gestión de Préstamos
- **Modalidades**: Solo interés y cuotas fijas
- **Frecuencias**: Diaria, semanal, quincenal, mensual
- **Cálculo automático** de cuotas e intereses
- **Seguimiento en tiempo real** del estado de cada préstamo

### 👥 Gestión de Clientes
- Registro completo de información personal
- Historial de préstamos por cliente
- Búsqueda y filtrado avanzado

### 📊 Panel Administrativo
- Dashboard con resumen ejecutivo
- Reportes y estadísticas
- Interfaz intuitiva y responsive

## 🛠️ Tecnologías Utilizadas
- **Backend**: PHP 7.4+
- **Base de datos**: SQLite
- **Frontend**: HTML5, CSS3, JavaScript ES6
- **Interfaz**: Diseño moderno y responsive

## 🚀 Instalación Rápida

### Prerequisitos
- PHP 7.4 o superior
- Servidor web (Apache/Nginx) o PHP Built-in Server
- Extensión SQLite habilitada

### Pasos de instalación
1. **Clonar o descargar** el proyecto
2. **Ejecutar el servidor**:
   ```bash
   php -S localhost:8082
   ```
3. **Acceder al sistema**: http://localhost:8082

## 📁 Estructura del Proyecto
```
panel_prestamo_modular/
├── src/                    # Clases principales
├── api/                    # API endpoints
├── controllers/            # Controladores
├── modules/               # Módulos del sistema
├── views/                 # Vistas
├── data/                  # Datos y configuración
├── index.php              # Archivo principal
├── api_simple.php         # API simplificada
└── prestamos.db          # Base de datos SQLite
```

## 🔧 Configuración

### Base de datos
La base de datos SQLite se crea automáticamente en la primera ejecución. No requiere configuración adicional.

### Personalización
- Modifica `src/` para personalizar la lógica de negocio
- Ajusta estilos en `index.php` (CSS integrado)
- Configura nuevos módulos en `modules/`

## 📖 Uso del Sistema

### Creación de Cliente
1. Acceder a la pestaña "Clientes"
2. Completar el formulario con la información requerida
3. Guardar el cliente

### Creación de Préstamo
1. Seleccionar cliente existente
2. Configurar monto, tasa e interés
3. Elegir modalidad (Solo interés/Cuotas)
4. Definir frecuencia y plazo
5. Crear préstamo

### Gestión de Pagos
- El sistema calcula automáticamente las cuotas
- Seguimiento del progreso de pagos
- Alertas de cuotas vencidas

## 🧪 Testing y Desarrollo

### Archivos de prueba incluidos:
- `test_http.php` - Test directo de la API
- `test_frontend.html` - Test del frontend
- `test_modal.html` - Test de componentes UI

### Ejecutar tests:
```bash
php test_http.php
```

## 📝 Changelog

### Versión 1.1.0 (28 de agosto de 2025)
- ✅ **Solucionado**: Error "desconocido" al crear préstamos
- ✅ **Mejorado**: Función `mostrarResultadoEnModal()` híbrida
- ✅ **Agregado**: Modal de loading para mejor UX
- ✅ **Optimizado**: Manejo de errores más robusto

### Versión 1.0.0
- 🎉 Lanzamiento inicial del sistema
- 💰 Gestión completa de préstamos
- 👥 Módulo de clientes
- 📊 Panel administrativo

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para nueva característica (`git checkout -b feature/AmazingFeature`)
3. Commit los cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📞 Soporte

Para soporte técnico o consultas:
- 📧 **Email**: [Tu email de contacto]
- 📱 **Teléfono**: [Tu número de contacto]
- 🌐 **Web**: [Tu sitio web]

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

---

**🚀 Nova Luz Pro** - Sistema de Préstamos Modular  
*Desarrollado con ❤️ para la gestión eficiente de préstamos*