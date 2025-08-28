@echo off
setlocal enabledelayedexpansion
title NOVA LUZ - Git Manager Pro
color 0B

:menu
cls
echo.
echo ╔════════════════════════════════════════════════════════════════════╗
echo ║                    🚀 NOVA LUZ PRO - Git Manager                   ║
echo ║                     Sistema de Préstamos Modular                   ║
echo ╠════════════════════════════════════════════════════════════════════╣
echo ║                                                                    ║
echo ║  [1] ⚡ Commit Rápido           - Guarda cambios automáticamente   ║
echo ║  [2] 📝 Commit Personalizado    - Con mensaje personalizado       ║
echo ║  [3] 💾 Backup y Sync           - Backup local + GitHub           ║
echo ║  [4] 🔧 Configurar GitHub       - Primera vez / Cambiar repo      ║
echo ║  [5] 📊 Estado del Repositorio  - Ver cambios pendientes          ║
echo ║  [6] 🌐 Abrir en GitHub         - Ver repositorio online          ║
echo ║  [7] 📋 Ver Logs                - Historial de commits            ║
echo ║  [8] 🎯 Iniciar Servidor        - Ejecutar PHP server             ║
echo ║  [0] ❌ Salir                                                      ║
echo ║                                                                    ║
echo ╚════════════════════════════════════════════════════════════════════╝

cd /d "%~dp0"

echo.
set /p opcion="🎯 Selecciona una opción (0-8): "

if "%opcion%"=="1" goto commit_rapido
if "%opcion%"=="2" goto commit_personalizado
if "%opcion%"=="3" goto backup_sync
if "%opcion%"=="4" goto configurar_github
if "%opcion%"=="5" goto estado_repo
if "%opcion%"=="6" goto abrir_github
if "%opcion%"=="7" goto ver_logs
if "%opcion%"=="8" goto iniciar_servidor
if "%opcion%"=="0" goto salir

echo ❌ Opción no válida
timeout /t 2
goto menu

:commit_rapido
echo.
echo ⚡ Ejecutando commit rápido...
call COMMIT_RAPIDO.bat
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:commit_personalizado
echo.
echo 📝 Commit personalizado...
call AUTO_COMMIT_PUSH.bat
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:backup_sync
echo.
echo 💾 Ejecutando backup y sincronización...
call BACKUP_Y_SYNC.bat
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:configurar_github
echo.
echo 🔧 Configurando GitHub...
call CONFIGURAR_GITHUB.bat
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:estado_repo
echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                    📊 Estado del Repositorio                 ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo 📁 Directorio actual:
cd
echo.
echo 🔄 Estado de Git:
git status
echo.
echo 📋 Remote configurado:
git remote -v
echo.
echo 📝 Último commit:
git log --oneline -1
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:abrir_github
echo.
echo 🌐 Abriendo GitHub...
if exist GITHUB_CONFIG.txt (
    for /f "tokens=2 delims=:" %%a in ('findstr "URL:" GITHUB_CONFIG.txt') do (
        start "" %%a
        echo ✅ Abriendo repositorio en el navegador...
    )
) else (
    echo ❌ No hay configuración de GitHub. Configura primero con la opción 4.
    timeout /t 3
)
goto menu

:ver_logs
echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                    📋 Historial de Commits                   ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
git log --oneline -10 --graph --decorate
echo.
echo ⏸️  Presiona cualquier tecla para volver al menú...
pause >nul
goto menu

:iniciar_servidor
echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                    🎯 Iniciando Servidor PHP                 ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo 🚀 Iniciando servidor en puerto 8082...
echo 🌐 URL: http://localhost:8082
echo ⚠️  Presiona Ctrl+C para detener el servidor
echo.
timeout /t 3
php -S localhost:8082
goto menu

:salir
echo.
echo ✅ ¡Hasta luego! Gracias por usar Nova Luz Git Manager Pro
timeout /t 2
exit
