@echo off
title Panel de Prestamos - Servidor PHP
color 0A
echo.
echo ========================================
echo    PANEL DE PRESTAMOS - AUTOSTART
echo ========================================
echo.
echo [INFO] Iniciando servidor PHP...
echo [INFO] Ubicacion: %CD%
echo [INFO] Puerto: 8082
echo.

REM Cambiar a la carpeta del panel
cd /d "c:\Users\¿peluche _\Desktop\visual\panel_prestamo_modular"

REM Verificar si PHP está disponible
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP no está instalado o no está en PATH
    echo [INFO] Por favor instala PHP desde: https://www.php.net/downloads
    pause
    exit /b 1
)

REM Mostrar información del sistema
echo [OK] PHP encontrado - Version:
php --version | findstr "PHP"
echo.

REM Verificar si el puerto 8082 está libre
netstat -an | findstr ":8082" >nul
if %errorlevel% equ 0 (
    echo [WARNING] El puerto 8082 ya está en uso
    echo [INFO] Intentando detener procesos existentes...
    taskkill /f /im php.exe >nul 2>&1
    timeout /t 2 >nul
)

REM Abrir navegador después de 3 segundos
echo [INFO] Abriendo navegador en 3 segundos...
start "" /min cmd /c "timeout /t 3 >nul && start http://localhost:8082"

REM Iniciar servidor PHP
echo [INFO] Iniciando servidor PHP en localhost:8082...
echo [INFO] Presiona Ctrl+C para detener el servidor
echo.
echo ========================================
echo    SERVIDOR ACTIVO EN:
echo    http://localhost:8082
echo ========================================
echo.

php -S localhost:8082

REM Si el servidor se detiene
echo.
echo [INFO] Servidor detenido
pause
