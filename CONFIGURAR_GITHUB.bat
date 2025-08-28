@echo off
title NOVA LUZ - Configurar GitHub
echo ===========================================
echo   🔧 NOVA LUZ PRO - Configurador GitHub
echo ===========================================
echo.

cd /d "%~dp0"

echo 📋 Este script te ayudará a configurar GitHub para tu proyecto
echo.

set /p usuario="👤 Ingresa tu nombre de usuario de GitHub: "
set /p repo="📁 Ingresa el nombre del repositorio (por defecto: sistema-prestamos-modular-novaluz): "

if "%repo%"=="" set repo=sistema-prestamos-modular-novaluz

echo.
echo 🔗 Configurando remote con:
echo    Usuario: %usuario%
echo    Repositorio: %repo%
echo    URL: https://github.com/%usuario%/%repo%.git
echo.

set /p confirmar="¿Es correcto? (S/N): "
if /i not "%confirmar%"=="S" if /i not "%confirmar%"=="s" (
    echo ❌ Configuración cancelada
    pause
    exit /b 0
)

echo.
echo 🔧 Configurando remote...
git remote remove origin 2>nul
git remote add origin https://github.com/%usuario%/%repo%.git

if %ERRORLEVEL% equ 0 (
    echo ✅ Remote configurado exitosamente
) else (
    echo ❌ Error al configurar remote
    pause
    exit /b 1
)

echo.
echo 📤 ¿Quieres hacer el primer push ahora? (S/N): 
set /p push=""

if /i "%push%"=="S" if /i "%push%"=="s" (
    echo.
    echo 🚀 Haciendo primer push...
    git push -u origin master
    
    if %ERRORLEVEL% equ 0 (
        echo ✅ ¡Proyecto subido exitosamente a GitHub!
        echo 🌐 Tu repositorio está en: https://github.com/%usuario%/%repo%
    ) else (
        echo ❌ Error en el push
        echo 💡 Asegúrate de:
        echo    1. Haber creado el repositorio en GitHub
        echo    2. Tener permisos de escritura
        echo    3. Estar autenticado correctamente
    )
) else (
    echo ⏭️  Push omitido. Puedes hacerlo después con: git push -u origin master
)

echo.
echo 💾 Guardando configuración en archivo...
echo # Configuración GitHub > GITHUB_CONFIG.txt
echo Usuario: %usuario% >> GITHUB_CONFIG.txt
echo Repositorio: %repo% >> GITHUB_CONFIG.txt
echo URL: https://github.com/%usuario%/%repo%.git >> GITHUB_CONFIG.txt
echo Configurado: %date% %time% >> GITHUB_CONFIG.txt

echo.
echo ✅ ¡Configuración completada!
echo 🎯 Ahora puedes usar AUTO_COMMIT_PUSH.bat para subir cambios automáticamente
echo.
pause
