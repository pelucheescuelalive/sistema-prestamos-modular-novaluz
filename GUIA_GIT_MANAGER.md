# 🚀 Nova Luz Git Manager Pro

## 📋 Guía de Uso de Scripts Automáticos

### 🎯 Scripts Principales

#### 1. **GIT_MANAGER_PRO.bat** - Menú Principal
- **Descripción**: Interfaz principal con todas las opciones
- **Uso**: Doble clic para abrir el menú interactivo
- **Funciones**: Acceso a todos los scripts desde un solo lugar

#### 2. **COMMIT_RAPIDO.bat** - Guardado Rápido
- **Descripción**: Guarda cambios automáticamente con timestamp
- **Uso**: Doble clic cuando quieras guardar cambios rápidamente
- **Resultado**: Commit automático + Push a GitHub

#### 3. **AUTO_COMMIT_PUSH.bat** - Commit Personalizado
- **Descripción**: Permite escribir mensaje personalizado
- **Uso**: Doble clic y escribe tu mensaje de commit
- **Resultado**: Commit con tu mensaje + Push a GitHub

#### 4. **BACKUP_Y_SYNC.bat** - Backup Completo
- **Descripción**: Crea backup local + sube a GitHub
- **Uso**: Doble clic para backup completo
- **Resultado**: 
  - Archivo ZIP con respaldo local
  - Commit automático con timestamp
  - Push a GitHub

#### 5. **CONFIGURAR_GITHUB.bat** - Configuración Inicial
- **Descripción**: Configura conexión con GitHub (solo primera vez)
- **Uso**: Ejecutar una vez para configurar repositorio
- **Requisitos**: Tener repositorio creado en GitHub

## 🔧 Configuración Inicial (Primera Vez)

### Paso 1: Crear repositorio en GitHub
1. Ve a https://github.com
2. Click "New repository"
3. Nombre: `sistema-prestamos-modular-novaluz`
4. **NO** marcar "Initialize with README"
5. Crear repositorio

### Paso 2: Configurar scripts
1. Ejecutar `CONFIGURAR_GITHUB.bat`
2. Ingresar tu usuario de GitHub
3. Confirmar nombre del repositorio
4. ¡Listo!

## ⚡ Uso Diario

### Para cambios rápidos:
```
Doble clic en: COMMIT_RAPIDO.bat
```
- Guarda y sube automáticamente
- Mensaje automático con fecha/hora

### Para cambios importantes:
```
Doble clic en: AUTO_COMMIT_PUSH.bat
```
- Escribe mensaje descriptivo
- Guarda y sube a GitHub

### Para respaldo completo:
```
Doble clic en: BACKUP_Y_SYNC.bat
```
- Crea backup local (.zip)
- Sube todo a GitHub
- Doble protección

## 🎯 Flujo de Trabajo Recomendado

### Desarrollo Normal:
1. **Trabajar** en el proyecto
2. **Probar** cambios en localhost:8082
3. **Ejecutar** `COMMIT_RAPIDO.bat` para guardar
4. **Repetir** según necesidad

### Cambios Importantes:
1. **Completar** funcionalidad
2. **Probar** exhaustivamente
3. **Ejecutar** `AUTO_COMMIT_PUSH.bat`
4. **Escribir** mensaje descriptivo

### Respaldo Semanal:
1. **Ejecutar** `BACKUP_Y_SYNC.bat`
2. **Verificar** que el backup se creó
3. **Confirmar** subida a GitHub

## 📁 Archivos Creados

- `GIT_MANAGER_PRO.bat` - Menú principal
- `COMMIT_RAPIDO.bat` - Guardado rápido
- `AUTO_COMMIT_PUSH.bat` - Commit personalizado
- `BACKUP_Y_SYNC.bat` - Backup completo
- `CONFIGURAR_GITHUB.bat` - Configuración inicial
- `GITHUB_CONFIG.txt` - Configuración guardada (auto-generado)

## 🛡️ Seguridad y Respaldos

### Protección Triple:
1. **Local**: Archivos en tu computadora
2. **Backup ZIP**: Respaldo local comprimido
3. **GitHub**: Respaldo en la nube + control de versiones

### Recuperación:
- **Git**: `git log` para ver historial
- **Backup**: Archivos ZIP en carpeta superior
- **GitHub**: Historial completo online

## ❓ Resolución de Problemas

### "Error al subir a GitHub":
1. Verificar conexión a internet
2. Confirmar credenciales de GitHub
3. Ejecutar: `git pull origin master`

### "No es un repositorio Git":
1. Verificar que estás en la carpeta correcta
2. Re-ejecutar `CONFIGURAR_GITHUB.bat`

### "Remote no configurado":
1. Ejecutar `CONFIGURAR_GITHUB.bat`
2. Verificar que el repositorio existe en GitHub

## 🎉 ¡Listo para Usar!

Ahora cada vez que hagas cambios, simplemente:
1. **Cambios rápidos**: `COMMIT_RAPIDO.bat`
2. **Cambios importantes**: `AUTO_COMMIT_PUSH.bat`
3. **Respaldo completo**: `BACKUP_Y_SYNC.bat`

**¡Tu proyecto estará siempre protegido y actualizado en GitHub!** 🚀
