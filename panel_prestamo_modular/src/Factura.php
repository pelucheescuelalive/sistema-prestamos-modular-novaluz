<?php
/**
 * Módulo Factura - Sistema de Préstamos Modular
 * Genera facturas y reportes profesionales
 */

require_once __DIR__ . '/DatabaseConnection.php';

class Factura {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    /**
     * Inicializar la base de datos SQLite
     */
    private function initDatabase() {
        try {
            $dbPath = __DIR__ . '/../data/prestamos.db';
            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (Exception $e) {
            throw new Exception("Error conectando a la base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Generar factura de préstamo
     */
    public function generarFacturaPrestamo($prestamo_id) {
        try {
            // Obtener datos del préstamo y cliente
            $sql = "SELECT p.*, c.nombre, c.telefono, c.direccion, c.cedula 
                    FROM prestamos p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.id = :prestamo_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$datos) {
                return ['success' => false, 'error' => 'Préstamo no encontrado'];
            }
            
            // Generar HTML de la factura
            $html = $this->generarHTMLFacturaPrestamo($datos);
            
            return [
                'success' => true,
                'html' => $html,
                'numero_factura' => 'PRES-' . str_pad($prestamo_id, 6, '0', STR_PAD_LEFT)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error generando factura: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generar recibo de pago
     */
    public function generarReciboPago($pago_id) {
        try {
            // Obtener datos del pago, préstamo y cliente
            $sql = "SELECT pg.*, p.monto as prestamo_monto, c.nombre, c.telefono, c.cedula 
                    FROM pagos pg 
                    LEFT JOIN prestamos p ON pg.prestamo_id = p.id 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE pg.id = :pago_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pago_id', $pago_id);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$datos) {
                return ['success' => false, 'error' => 'Pago no encontrado'];
            }
            
            // Generar HTML del recibo
            $html = $this->generarHTMLReciboPago($datos);
            
            return [
                'success' => true,
                'html' => $html,
                'numero_recibo' => 'REC-' . str_pad($pago_id, 6, '0', STR_PAD_LEFT)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error generando recibo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generar HTML de factura de préstamo
     */
    private function generarHTMLFacturaPrestamo($datos) {
        $fecha_actual = date('d/m/Y');
        $numero_factura = 'PRES-' . str_pad($datos['id'], 6, '0', STR_PAD_LEFT);
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Factura de Préstamo</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #333;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #007bff;
                    padding-bottom: 20px;
                }
                .company-name { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #007bff; 
                    margin-bottom: 5px;
                }
                .invoice-title { 
                    font-size: 18px; 
                    color: #666; 
                }
                .invoice-info { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-bottom: 30px;
                }
                .client-info, .invoice-details { 
                    width: 45%; 
                }
                .section-title { 
                    font-weight: bold; 
                    color: #007bff; 
                    margin-bottom: 10px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                .details-table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-bottom: 30px;
                }
                .details-table th, .details-table td { 
                    border: 1px solid #ddd; 
                    padding: 12px; 
                    text-align: left;
                }
                .details-table th { 
                    background-color: #f8f9fa; 
                    font-weight: bold;
                }
                .total-section { 
                    text-align: right; 
                    margin-top: 20px;
                }
                .total-amount { 
                    font-size: 18px; 
                    font-weight: bold; 
                    color: #007bff;
                }
                .footer { 
                    margin-top: 40px; 
                    text-align: center; 
                    font-size: 12px; 
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .print-button {
                    background: #007bff;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-bottom: 20px;
                }
                @media print {
                    .print-button { display: none; }
                }
            </style>
        </head>
        <body>
            <button class='print-button' onclick='window.print()'>🖨️ Imprimir Factura</button>
            
            <div class='header'>
                <div class='company-name'>SISTEMA DE PRÉSTAMOS</div>
                <div class='invoice-title'>FACTURA DE PRÉSTAMO</div>
            </div>
            
            <div class='invoice-info'>
                <div class='client-info'>
                    <div class='section-title'>Información del Cliente</div>
                    <p><strong>Nombre:</strong> {$datos['nombre']}</p>
                    <p><strong>Cédula:</strong> {$datos['cedula']}</p>
                    <p><strong>Teléfono:</strong> {$datos['telefono']}</p>
                    <p><strong>Dirección:</strong> {$datos['direccion']}</p>
                </div>
                
                <div class='invoice-details'>
                    <div class='section-title'>Detalles de la Factura</div>
                    <p><strong>Número:</strong> {$numero_factura}</p>
                    <p><strong>Fecha:</strong> {$fecha_actual}</p>
                    <p><strong>Fecha del Préstamo:</strong> " . date('d/m/Y', strtotime($datos['fecha'])) . "</p>
                    <p><strong>Vencimiento:</strong> " . date('d/m/Y', strtotime($datos['fecha_vencimiento'])) . "</p>
                </div>
            </div>
            
            <table class='details-table'>
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Cantidad</th>
                        <th>Monto</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Préstamo de Capital</td>
                        <td>1</td>
                        <td>$" . number_format($datos['monto'], 2) . "</td>
                        <td>$" . number_format($datos['monto'], 2) . "</td>
                    </tr>
                    <tr>
                        <td>Tipo de Interés</td>
                        <td colspan='3'>{$datos['tipo_interes']}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class='total-section'>
                <div class='total-amount'>
                    Total del Préstamo: $" . number_format($datos['monto'], 2) . "
                </div>
            </div>
            
            <div class='footer'>
                <p>Esta factura fue generada automáticamente el {$fecha_actual}</p>
                <p>Sistema de Préstamos - Todos los derechos reservados</p>
            </div>
        </body>
        </html>";
        
        return $html;
    }
    
    /**
     * Generar HTML de recibo de pago
     */
    private function generarHTMLReciboPago($datos) {
        $fecha_actual = date('d/m/Y');
        $numero_recibo = 'REC-' . str_pad($datos['id'], 6, '0', STR_PAD_LEFT);
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Recibo de Pago</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #333;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #28a745;
                    padding-bottom: 20px;
                }
                .company-name { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #28a745; 
                    margin-bottom: 5px;
                }
                .receipt-title { 
                    font-size: 18px; 
                    color: #666; 
                }
                .receipt-info { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-bottom: 30px;
                }
                .client-info, .receipt-details { 
                    width: 45%; 
                }
                .section-title { 
                    font-weight: bold; 
                    color: #28a745; 
                    margin-bottom: 10px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                .payment-details { 
                    background: #f8f9fa; 
                    padding: 20px; 
                    border-radius: 5px; 
                    margin-bottom: 20px;
                }
                .amount-paid { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #28a745; 
                    text-align: center; 
                    margin: 20px 0;
                    padding: 15px;
                    border: 2px solid #28a745;
                    border-radius: 5px;
                }
                .footer { 
                    margin-top: 40px; 
                    text-align: center; 
                    font-size: 12px; 
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .print-button {
                    background: #28a745;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-bottom: 20px;
                }
                @media print {
                    .print-button { display: none; }
                }
            </style>
        </head>
        <body>
            <button class='print-button' onclick='window.print()'>🖨️ Imprimir Recibo</button>
            
            <div class='header'>
                <div class='company-name'>SISTEMA DE PRÉSTAMOS</div>
                <div class='receipt-title'>RECIBO DE PAGO</div>
            </div>
            
            <div class='receipt-info'>
                <div class='client-info'>
                    <div class='section-title'>Información del Cliente</div>
                    <p><strong>Nombre:</strong> {$datos['nombre']}</p>
                    <p><strong>Cédula:</strong> {$datos['cedula']}</p>
                    <p><strong>Teléfono:</strong> {$datos['telefono']}</p>
                </div>
                
                <div class='receipt-details'>
                    <div class='section-title'>Detalles del Recibo</div>
                    <p><strong>Número:</strong> {$numero_recibo}</p>
                    <p><strong>Fecha:</strong> {$fecha_actual}</p>
                    <p><strong>Fecha del Pago:</strong> " . date('d/m/Y', strtotime($datos['fecha'])) . "</p>
                    <p><strong>Tipo:</strong> {$datos['tipo']}</p>
                </div>
            </div>
            
            <div class='amount-paid'>
                MONTO PAGADO: $" . number_format($datos['monto'], 2) . "
            </div>
            
            <div class='payment-details'>
                <div class='section-title'>Desglose del Pago</div>";
        
        if ($datos['monto_interes'] > 0) {
            $html .= "<p><strong>Intereses:</strong> $" . number_format($datos['monto_interes'], 2) . "</p>";
        }
        if ($datos['monto_capital'] > 0) {
            $html .= "<p><strong>Capital:</strong> $" . number_format($datos['monto_capital'], 2) . "</p>";
        }
        if ($datos['monto_mora'] > 0) {
            $html .= "<p><strong>Mora:</strong> $" . number_format($datos['monto_mora'], 2) . "</p>";
        }
        if (!empty($datos['observaciones'])) {
            $html .= "<p><strong>Observaciones:</strong> {$datos['observaciones']}</p>";
        }
        
        $html .= "
            </div>
            
            <div class='footer'>
                <p>Recibo generado automáticamente el {$fecha_actual}</p>
                <p>Gracias por su pago puntual</p>
                <p>Sistema de Préstamos - Todos los derechos reservados</p>
            </div>
        </body>
        </html>";
        
        return $html;
    }
    
    /**
     * Generar reporte de estado de cuenta
     */
    public function generarEstadoCuenta($prestamo_id) {
        try {
            // Obtener datos del préstamo
            $sql = "SELECT p.*, c.nombre, c.telefono, c.cedula 
                    FROM prestamos p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.id = :prestamo_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                return ['success' => false, 'error' => 'Préstamo no encontrado'];
            }
            
            // Obtener historial de pagos
            $sql = "SELECT * FROM pagos WHERE prestamo_id = :prestamo_id ORDER BY fecha ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Generar HTML del estado de cuenta
            $html = $this->generarHTMLEstadoCuenta($prestamo, $pagos);
            
            return [
                'success' => true,
                'html' => $html
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error generando estado de cuenta: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generar HTML de estado de cuenta
     */
    private function generarHTMLEstadoCuenta($prestamo, $pagos) {
        $fecha_actual = date('d/m/Y');
        $total_pagado = array_sum(array_column($pagos, 'monto'));
        $saldo_pendiente = $prestamo['monto'] - array_sum(array_column($pagos, 'monto_capital'));
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Estado de Cuenta</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #6f42c1; padding-bottom: 20px; }
                .company-name { font-size: 24px; font-weight: bold; color: #6f42c1; margin-bottom: 5px; }
                .report-title { font-size: 18px; color: #666; }
                .summary { display: flex; justify-content: space-between; margin-bottom: 30px; }
                .summary-item { background: #f8f9fa; padding: 15px; border-radius: 5px; width: 30%; text-align: center; }
                .summary-amount { font-size: 20px; font-weight: bold; color: #6f42c1; }
                .payments-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                .payments-table th, .payments-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                .payments-table th { background-color: #f8f9fa; font-weight: bold; }
                .print-button { background: #6f42c1; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
                @media print { .print-button { display: none; } }
            </style>
        </head>
        <body>
            <button class='print-button' onclick='window.print()'>🖨️ Imprimir Estado de Cuenta</button>
            
            <div class='header'>
                <div class='company-name'>SISTEMA DE PRÉSTAMOS</div>
                <div class='report-title'>ESTADO DE CUENTA</div>
                <p>Cliente: {$prestamo['nombre']} | Fecha: {$fecha_actual}</p>
            </div>
            
            <div class='summary'>
                <div class='summary-item'>
                    <div>Monto Original</div>
                    <div class='summary-amount'>$" . number_format($prestamo['monto'], 2) . "</div>
                </div>
                <div class='summary-item'>
                    <div>Total Pagado</div>
                    <div class='summary-amount'>$" . number_format($total_pagado, 2) . "</div>
                </div>
                <div class='summary-item'>
                    <div>Saldo Pendiente</div>
                    <div class='summary-amount'>$" . number_format($saldo_pendiente, 2) . "</div>
                </div>
            </div>
            
            <h3>Historial de Pagos</h3>
            <table class='payments-table'>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Interés</th>
                        <th>Capital</th>
                        <th>Mora</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>";
        
        foreach ($pagos as $pago) {
            $html .= "
                    <tr>
                        <td>" . date('d/m/Y', strtotime($pago['fecha'])) . "</td>
                        <td>$" . number_format($pago['monto'], 2) . "</td>
                        <td>$" . number_format($pago['monto_interes'], 2) . "</td>
                        <td>$" . number_format($pago['monto_capital'], 2) . "</td>
                        <td>$" . number_format($pago['monto_mora'], 2) . "</td>
                        <td>{$pago['tipo']}</td>
                    </tr>";
        }
        
        $html .= "
                </tbody>
            </table>
        </body>
        </html>";
        
        return $html;
    }
}

?>
