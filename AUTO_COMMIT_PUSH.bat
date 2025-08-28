@echo off
title NOVA LUZ - Auto Commit y Push
echo ===========================================
echo   🚀 NOVA LUZ PRO - Auto Git Manager
echo ===========================================
echo.

cd /d "%~dp0"

echo ⏳ Verificando cambios...
git status --short

if %ERRORLEVEL% neq 0 (
    echo ❌ Error: No es un repositorio Git válido
    pause
    exit /b 1
)

echo.
echo 📝 Agregando todos los archivos...
git add .

echo.
set /p mensaje="💬 Ingresa el mensaje del commit (o presiona Enter para mensaje automático): "

if "%mensaje%"=="" (
    for /f "tokens=2 delims==" %%I in ('wmic OS Get localdatetime /value') do set datetime=%%I
    set fecha=!datetime:~0,4!-!datetime:~4,2!-!datetime:~6,2!
    set hora=!datetime:~8,2!:!datetime:~10,2!
    set mensaje=📦 Auto-commit - !fecha! !hora!
)

echo.
echo 💾 Creando commit: "%mensaje%"
git commit -m "%mensaje%"

if %ERRORLEVEL% neq 0 (
    echo ⚠️  No hay cambios para commitear o error en commit
) else (
    echo ✅ Commit creado exitosamente
)

echo.
echo 🔄 Verificando remote...
git remote -v | findstr origin

if %ERRORLEVEL% neq 0 (
    echo ⚠️  No hay remote configurado. 
    echo 📋 Para configurar GitHub:
    echo    1. Crea un repositorio en GitHub
    echo    2. Ejecuta: git remote add origin https://github.com/TU_USUARIO/NOMBRE_REPO.git
    echo    3. Ejecuta: git push -u origin master
    echo.
    pause
    exit /b 0
)

echo.
echo 🚀 Subiendo cambios a GitHub...
git push

if %ERRORLEVEL% equ 0 (
    echo ✅ ¡Cambios subidos exitosamente a GitHub!
    echo 🌐 Tu proyecto está actualizado en la nube
) else (
    echo ❌ Error al subir a GitHub
    echo 💡 Posibles soluciones:
    echo    - Verifica tu conexión a internet
    echo    - Confirma tus credenciales de GitHub
    echo    - Ejecuta: git pull origin master (si hay conflictos)
)

echo.
echo ⏸️  Presiona cualquier tecla para cerrar...
pause >nul
