@echo off
title NOVA LUZ - Backup y Sync
echo ===========================================
echo   💾 NOVA LUZ PRO - Backup & Sync Manager
echo ===========================================
echo.

cd /d "%~dp0"

:: Obtener fecha y hora para el backup
for /f "tokens=2 delims==" %%I in ('wmic OS Get localdatetime /value') do set datetime=%%I
set fecha=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%
set hora=%datetime:~8,2%-%datetime:~10,2%
set timestamp=%fecha%_%hora%

echo 📦 Creando backup local...
set backup_name=BACKUP_NOVA_LUZ_%timestamp%.zip

:: Crear backup usando PowerShell
powershell -Command "Compress-Archive -Path '.\*' -DestinationPath '..\%backup_name%' -Force"

if %ERRORLEVEL% equ 0 (
    echo ✅ Backup creado: %backup_name%
) else (
    echo ❌ Error al crear backup
)

echo.
echo 📝 Creando commit automático...
git add .
git commit -m "💾 Auto-backup y sincronización - %fecha% %hora%

✅ Cambios incluidos:
- Backup local creado: %backup_name%  
- Sincronización automática
- Estado del proyecto preservado

🕐 Timestamp: %datetime:~0,4%/%datetime:~4,2%/%datetime:~6,2% %datetime:~8,2%:%datetime:~10,2%:%datetime:~12,2%"

echo.
echo 🚀 Subiendo a GitHub...
git push

if %ERRORLEVEL% equ 0 (
    echo ✅ ¡Proyecto sincronizado exitosamente!
    echo 📁 Backup local: ..\%backup_name%
    echo 🌐 Código actualizado en GitHub
) else (
    echo ❌ Error en la sincronización con GitHub
    echo 💾 Backup local creado correctamente
)

echo.
echo 📊 Estado actual:
git status --short

echo.
echo ⏸️  Presiona cualquier tecla para cerrar...
pause >nul
