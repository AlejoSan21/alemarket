<?php
// controllers/FacturaController.php
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Venta.php';

use Dompdf\Dompdf;

if (isset($_GET['id'])) {
    $db = (new Database())->getConnection();
    $ventaModel = new Venta($db);

    // 1. Obtener cabecera de la venta (vendedor, total, fecha, metodo pago)
    $venta = $ventaModel->obtenerVentaPorId($_GET['id']);
    
    // 2. Obtener los productos de esa venta
    $detalles = $ventaModel->obtenerDetalleVenta($_GET['id']);

    if (!$venta) {
        die("La factura solicitada no existe.");
    }

    // 3. Crear el HTML de la Factura
    $html = "
    <div style='font-family: sans-serif; font-size: 12px;'>
        <h2 style='text-align: center; margin: 0;'>ALEMARKET1</h2>
        <p style='text-align: center; margin: 0;'>Nit: 123456789-0</p>
        <p style='text-align: center;'>La Dorada, Caldas</p>
        <hr>
        <p><strong>Factura N°:</strong> {$venta['id']}</p>
        <p><strong>Fecha:</strong> {$venta['fecha']}</p>
        <p><strong>Cajero:</strong> {$venta['empleado']}</p>
        <p><strong>Pago:</strong> {$venta['metodo_pago']}</p>
        <hr>
        <table width='100%' cellspacing='0' cellpadding='5'>
            <thead>
                <tr style='background-color: #f2f2f2;'>
                    <th align='left'>Cant.</th>
                    <th align='left'>Producto</th>
                    <th align='right'>P. Unit</th>
                    <th align='right'>Subtotal</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($detalles as $item) {
        $sub = $item['cantidad'] * $item['precio_unitario'];
        $html .= "
            <tr>
                <td>{$item['cantidad']}</td>
                <td>{$item['nombre']}</td>
                <td align='right'>$" . number_format($item['precio_unitario'], 0) . "</td>
                <td align='right'>$" . number_format($sub, 0) . "</td>
            </tr>";
    }

    $html .= "
            </tbody>
        </table>
        <hr>
        <h3 align='right' style='font-size: 16px;'>TOTAL A PAGAR: $" . number_format($venta['total'], 0) . "</h3>
        <p style='text-align: center; margin-top: 30px;'>*** Gracias por su compra ***</p>
    </div>";

    // 4. Generar el PDF con Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    
    // Formato de "Ticket" (80mm de ancho aprox, altura variable)
    // O puedes usar 'A5' o 'Letter'
    $dompdf->setPaper([0, 0, 226, 500], 'portrait'); 
    
    $dompdf->render();
    
    // Attachment 0 para que se abra en el navegador y puedas imprimir
    $dompdf->stream("Factura_AleMarket_{$venta['id']}.pdf", ["Attachment" => 0]);
}