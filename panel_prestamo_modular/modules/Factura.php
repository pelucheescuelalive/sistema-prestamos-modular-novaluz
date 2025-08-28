<?php
/**
 * MÓDULO DE FACTURA - GENERACIÓN DE FACTURAS/RECIBOS
 */

class Factura {
    private $ancho = 480;
    private $alto = 600;
    
    /**
     * Generar factura de pago
     */
    public function generar($prestamo, $cliente, $pago, $resultadoPago, $mora = null) {
        $numeroRecibo = $this->generarNumeroRecibo();
        $fecha = date('d/m/Y H:i');
        
        return [
            'html' => $this->generarHTML($prestamo, $cliente, $pago, $resultadoPago, $mora, $numeroRecibo, $fecha),
            'canvas' => $this->generarCanvas($prestamo, $cliente, $pago, $resultadoPago, $mora, $numeroRecibo, $fecha),
            'datos' => [
                'numero' => $numeroRecibo,
                'fecha' => $fecha,
                'cliente' => $cliente,
                'monto' => $pago['monto'],
                'desglose' => $resultadoPago
            ]
        ];
    }
    
    /**
     * Generar JavaScript para Canvas
     */
    private function generarCanvas($prestamo, $cliente, $pago, $resultadoPago, $mora, $numeroRecibo, $fecha) {
        $desglose = Pago::generarDesglose($resultadoPago);
        
        $js = "
        function generarFacturaCanvas() {
            const canvas = document.createElement('canvas');
            canvas.width = {$this->ancho};
            canvas.height = {$this->alto};
            const ctx = canvas.getContext('2d');
            
            // Fondo blanco
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, {$this->ancho}, {$this->alto});
            
            // Encabezado empresa
            ctx.fillStyle = '#1565c0';
            ctx.fillRect(40, 40, 400, 80);
            
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 18px Arial';
            ctx.textAlign = 'left';
            ctx.fillText('💰 Prestamos Inversiones Taveras', 60, 65);
            
            ctx.font = '12px Arial';
            ctx.fillText('Tel: +1849-372-3471 / +1849-372-3471', 60, 85);
            ctx.fillText('23000, La Altagracia, República Dominicana', 60, 100);
            ctx.fillText('PrestamistApp.com', 60, 115);
            
            // Título recibo
            ctx.fillStyle = '#333333';
            ctx.font = 'bold 24px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('RECIBO DE PAGO', {$this->ancho}/2, 160);
            
            // Información del recibo
            ctx.font = '14px Arial';
            ctx.textAlign = 'left';
            ctx.fillText('Recibo No.', 50, 190);
            ctx.fillText('$numeroRecibo', 200, 190);
            
            ctx.textAlign = 'right';
            ctx.fillText('Fecha:', 350, 190);
            ctx.fillText('$fecha', 430, 190);
            
            // Cliente
            ctx.textAlign = 'left';
            ctx.font = 'bold 14px Arial';
            ctx.fillText('RECIBIDO DE:', 50, 220);
            ctx.font = '14px Arial';
            ctx.fillText('{$cliente['nombre']}', 50, 240);
            
            // Monto total
            ctx.fillStyle = '#e3f2fd';
            ctx.fillRect(40, 260, 400, 35);
            ctx.strokeStyle = '#1565c0';
            ctx.lineWidth = 1;
            ctx.strokeRect(40, 260, 400, 35);
            
            ctx.fillStyle = '#1565c0';
            ctx.font = 'bold 16px Arial';
            ctx.textAlign = 'left';
            ctx.fillText('MONTO TOTAL:', 50, 280);
            ctx.textAlign = 'right';
            ctx.fillText('RD$' + Number({$pago['monto']}).toLocaleString('es-ES', {minimumFractionDigits: 2}), 430, 280);
            
            // Concepto
            ctx.fillStyle = '#333333';
            ctx.font = 'bold 14px Arial';
            ctx.textAlign = 'left';
            ctx.fillText('CONCEPTO:', 50, 315);
            ctx.font = '12px Arial';
            ctx.fillText('PAGO CUOTA ABONO', 50, 335);
            
            // Desglose
            let posY = 360;
            if (" . count($desglose) . " > 0) {
                ctx.fillStyle = '#e3f2fd';
                const alturaDesglose = " . (count($desglose) * 20 + 40) . ";
                ctx.fillRect(40, posY, 400, alturaDesglose);
                ctx.strokeStyle = '#1565c0';
                ctx.strokeRect(40, posY, 400, alturaDesglose);
                
                ctx.fillStyle = '#1565c0';
                ctx.font = 'bold 12px Arial';
                ctx.fillText('DESGLOSE DEL PAGO:', 50, posY + 20);
                
                ctx.font = '11px Arial';
                let lineaY = posY + 35;
        ";
        
        foreach ($desglose as $item) {
            $js .= "
                ctx.fillStyle = '{$item['color']}';
                ctx.fillText('• {$item['concepto']}: RD$' + Number({$item['monto']}).toFixed(2), 50, lineaY);
                lineaY += 15;
            ";
        }
        
        $js .= "
                posY += alturaDesglose + 20;
            }
            
            // Mensaje de agradecimiento
            ctx.fillStyle = '#666666';
            ctx.font = 'italic 12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('¡Gracias por su pago puntual!', {$this->ancho}/2, posY + 40);
            ctx.fillText('Sistema generado por PrestamistApp.com', {$this->ancho}/2, posY + 60);
            
            return canvas;
        }
        ";
        
        return $js;
    }
    
    /**
     * Generar HTML de la factura
     */
    private function generarHTML($prestamo, $cliente, $pago, $resultadoPago, $mora, $numeroRecibo, $fecha) {
        $desglose = Pago::generarDesglose($resultadoPago);
        
        $html = "
        <div class='factura-container' style='max-width: 500px; margin: 0 auto; font-family: Arial, sans-serif;'>
            <div class='factura-header' style='background: #1565c0; color: white; padding: 20px; text-align: center;'>
                <h2>💰 Prestamos Inversiones Taveras</h2>
                <p>Tel: +1849-372-3471 / +1849-372-3471<br>
                23000, La Altagracia, República Dominicana<br>
                PrestamistApp.com</p>
            </div>
            
            <div class='factura-body' style='padding: 20px; background: white;'>
                <h1 style='text-align: center; color: #333;'>RECIBO DE PAGO</h1>
                
                <div style='display: flex; justify-content: space-between; margin: 20px 0;'>
                    <div><strong>Recibo No.</strong> $numeroRecibo</div>
                    <div><strong>Fecha:</strong> $fecha</div>
                </div>
                
                <div style='margin: 20px 0;'>
                    <strong>RECIBIDO DE:</strong><br>
                    {$cliente['nombre']}
                </div>
                
                <div style='background: #e3f2fd; padding: 15px; border: 1px solid #1565c0; margin: 20px 0;'>
                    <div style='display: flex; justify-content: space-between;'>
                        <strong>MONTO TOTAL:</strong>
                        <strong style='color: #1565c0;'>RD$" . number_format($pago['monto'], 2) . "</strong>
                    </div>
                </div>
                
                <div style='margin: 20px 0;'>
                    <strong>CONCEPTO:</strong><br>
                    PAGO CUOTA ABONO
                </div>
        ";
        
        if (!empty($desglose)) {
            $html .= "
                <div style='background: #e3f2fd; padding: 15px; border: 1px solid #1565c0; margin: 20px 0;'>
                    <strong>DESGLOSE DEL PAGO:</strong><br><br>
            ";
            
            foreach ($desglose as $item) {
                $color = $item['color'];
                $html .= "<div style='color: $color; margin: 5px 0;'>• {$item['concepto']}: RD$" . number_format($item['monto'], 2) . "</div>";
            }
            
            $html .= "</div>";
        }
        
        $html .= "
                <div style='text-align: center; margin-top: 40px; color: #666; font-style: italic;'>
                    <p>¡Gracias por su pago puntual!</p>
                    <p>Sistema generado por PrestamistApp.com</p>
                </div>
            </div>
        </div>
        ";
        
        return $html;
    }
    
    /**
     * Generar número de recibo único
     */
    private function generarNumeroRecibo() {
        return time() . rand(1000, 9999);
    }
    
    /**
     * Convertir canvas a imagen base64
     */
    public static function canvasToBase64JS() {
        return "
        function descargarFactura() {
            const canvas = generarFacturaCanvas();
            const link = document.createElement('a');
            link.download = 'recibo_pago_' + Date.now() + '.png';
            link.href = canvas.toDataURL();
            link.click();
        }
        
        function copiarImagen() {
            const canvas = generarFacturaCanvas();
            canvas.toBlob(function(blob) {
                navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]).then(() => {
                    alert('Imagen copiada al portapapeles');
                }).catch(err => {
                    console.error('Error al copiar:', err);
                });
            });
        }
        ";
    }
}
?>
