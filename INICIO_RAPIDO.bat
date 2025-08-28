@echo off
REM INICIO RAPIDO DEL PANEL DE PRESTAMOS
title Panel Prestamos - Inicio Rapido

REM Cambiar a directorio del panel
cd /d "c:\Users\¿peluche _\Desktop\visual\panel_prestamo_modular"

REM Iniciar servidor y abrir navegador
echo Iniciando Panel de Prestamos...
start http://localhost:8082
php -S localhost:8082
