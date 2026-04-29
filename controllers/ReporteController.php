<?php
// controllers/ReporteController.php
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Venta.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['tipo'])) {
    $db = (new Database())->getConnection();
    $ventaModel = new Venta($db);
    $tipo = $_GET['tipo'];

    // Configuración para que cargue imágenes y estilos mejor
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // ESTILO CSS PARA LOS PDF (Se inyecta en el HTML)
    $style = "
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.4; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #1e3a8a; margin: 0; font-size: 24px; }
        .ticket-title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f1f5f9; color: #1e3a8a; padding: 8px; font-size: 12px; border-bottom: 1px solid #cbd5e1; }
        td { padding: 8px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .total-box { background: #1e3a8a; color: white; padding: 10px; margin-top: 20px; border-radius: 4px; }
        .footer { font-size: 9px; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>";

    $html = "";

    // CASO 1: FACTURA INDIVIDUAL (TICKET)
    if ($tipo === 'factura_individual' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $venta = $ventaModel->obtenerVentaPorId($id);
        $detalles = $ventaModel->obtenerDetalleVenta($id);

        $html = $style . "
        <div class='text-center'>
            <h1>ALEMARKET</h1>
            <p style='font-size:10px;'>NIT: 123456789-0 | La Dorada, Caldas</p>
            <div class='ticket-title'>Ticket de Venta #$id</div>
        </div>
        <p style='font-size:11px;'>
            <strong>Fecha:</strong> {$venta['fecha']}<br>
            <strong>Cajero:</strong> {$venta['empleado']}<br>
            <strong>Método:</strong> {$venta['metodo_pago']}
        </p>
        <table>
            <thead>
                <tr>
                    <th align='left'>Descripción</th>
                    <th align='center'>Cant.</th>
                    <th align='right'>Subtotal</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($detalles as $d) {
            $sub = $d['cantidad'] * $d['precio_unitario'];
            $html .= "<tr>
                        <td>{$d['nombre']}</td>
                        <td align='center'>{$d['cantidad']}</td>
                        <td align='right'>$" . number_format($sub, 0, ',', '.') . "</td>
                      </tr>";
        }
        $html .= "</tbody>
        </table>
        <div class='total-box text-right'>
            <span style='font-size:12px;'>TOTAL A PAGAR:</span><br>
            <span style='font-size:20px; font-weight:800;'>$" . number_format($venta['total'], 0, ',', '.') . "</span>
        </div>
        <p class='text-center footer'>¡Gracias por preferir AleMarket!<br>Conserve este ticket para cualquier reclamo.</p>";
        
        $dompdf->setPaper([0, 0, 280, 500], 'portrait'); 

    } 
    // CASO 2: REPORTES DIARIOS / MENSUALES (A4)
    else {
        $label = ($tipo === 'dia') ? "DIARIO" : "MENSUAL";
        if ($tipo === 'dia') {
            $inicio = date('Y-m-d');
            $fin = date('Y-m-d');
        } else {
            $inicio = date('Y-m-01');
            $fin = date('Y-m-t');
        }

        $ventas = $ventaModel->reportePorFechas($inicio, $fin);
        
        $html = $style . "
        <div class='header'>
            <h1>ALEMARKET - BALANCE $label</h1>
            <p style='margin:0;'>Rango: $inicio hasta $fin</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th align='left'>FACTURA</th>
                    <th align='left'>FECHA</th>
                    <th align='left'>VENDEDOR</th>
                    <th align='right'>VALOR TOTAL</th>
                </tr>
            </thead>
            <tbody>";
        
        $sumaTotal = 0;
        foreach ($ventas as $v) {
            $html .= "<tr>
                        <td>#{$v['id']}</td>
                        <td>{$v['fecha']}</td>
                        <td>{$v['nombre_usuario']}</td>
                        <td align='right'>$" . number_format($v['total'], 0, ',', '.') . "</td>
                      </tr>";
            $sumaTotal += $v['total'];
        }
        $html .= "</tbody>
        </table>
        <div class='text-right' style='margin-top:20px; border-top: 2px solid #1e3a8a; padding-top:10px;'>
            <span style='font-size:14px;'>RECAUDADO TOTAL:</span><br>
            <span style='font-size:24px; font-weight:800; color:#1e3a8a;'>$" . number_format($sumaTotal, 0, ',', '.') . "</span>
        </div>";
        
        $dompdf->setPaper('A4', 'portrait');
    }

    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("AleMarket_Reporte.pdf", array("Attachment" => 1));
}