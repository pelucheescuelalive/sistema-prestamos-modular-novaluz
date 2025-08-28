@echo off
title NOVA LUZ - Commit Rápido
echo ===========================================
echo   ⚡ NOVA LUZ PRO - Commit Rápido
echo ===========================================
echo.

cd /d "%~dp0"

:: Verificar si hay cambios
git diff --quiet
if %ERRORLEVEL% equ 0 (
    git diff --cached --quiet
    if %ERRORLEVEL% equ 0 (
        echo ℹ️  No hay cambios para commitear
        timeout /t 3
        exit /b 0
    )
)

echo 📋 Cambios detectados:
git status --short
echo.

:: Mensaje rápido basado en el tipo de cambios
for /f %%i in ('git diff --name-only --cached ^| find /c "."') do set staged=%%i
for /f %%i in ('git diff --name-only ^| find /c "."') do set unstaged=%%i

if %staged% gtr 0 if %unstaged% gtr 0 (
    set tipo=📝 Actualización mixta
) else if %staged% gtr 0 (
    set tipo=✅ Cambios preparados
) else (
    set tipo=🔄 Cambios rápidos
)

echo %tipo% - Agregando archivos...
git add .

for /f "tokens=2 delims==" %%I in ('wmic OS Get localdatetime /value') do set datetime=%%I
set fecha=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%
set hora=%datetime:~8,2%:%datetime:~10,2%

set mensaje=%tipo% - %fecha% %hora%

echo.
echo 💾 Commit: %mensaje%
git commit -m "%mensaje%"

echo.
echo 🚀 Subiendo a GitHub...
git push

if %ERRORLEVEL% equ 0 (
    echo ✅ ¡Sincronizado exitosamente!
) else (
    echo ❌ Error en push - pero commit guardado localmente
)

timeout /t 2
