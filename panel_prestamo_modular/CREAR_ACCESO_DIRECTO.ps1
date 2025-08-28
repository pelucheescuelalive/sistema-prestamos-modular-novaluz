# INSTRUCCIONES PARA CREAR ACCESO DIRECTO EN ESCRITORIO
# 
# Para crear un acceso directo en el escritorio:
# 
# 1. Clic derecho en el escritorio
# 2. Nuevo > Acceso directo
# 3. Ubicacion del elemento:
#    "c:\Users\¿peluche _\Desktop\visual\panel_prestamo_modular\INICIAR_PANEL_PRESTAMOS.bat"
# 4. Nombre: Panel de Prestamos
# 5. Finalizar
# 
# COMANDO POWERSHELL PARA CREAR ACCESO DIRECTO AUTOMATICAMENTE:

$WshShell = New-Object -comObject WScript.Shell
$Shortcut = $WshShell.CreateShortcut("$env:USERPROFILE\Desktop\Panel de Prestamos.lnk")
$Shortcut.TargetPath = "c:\Users\¿peluche _\Desktop\visual\panel_prestamo_modular\INICIAR_PANEL_PRESTAMOS.bat"
$Shortcut.WorkingDirectory = "c:\Users\¿peluche _\Desktop\visual\panel_prestamo_modular"
$Shortcut.Description = "Panel de Prestamos - Sistema Automatico"
$Shortcut.IconLocation = "shell32.dll,21"
$Shortcut.Save()

Write-Host "Acceso directo creado en el escritorio" -ForegroundColor Green
