<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido");
}

$model = new Model($pdo);

$venta = $model->obtenerVentaPorId($id);

if (!$venta) {
    die("Venta no encontrada");
}

$detalles = $model->obtenerDetalleVenta($id);

$dompdf = new Dompdf();

$html = "
<h2>Factura - AleMarket</h2>
<p><strong>Venta #:</strong> {$venta['id']}</p>
<p><strong>Fecha:</strong> {$venta['fecha']}</p>

<table border='1' width='100%' cellpadding='5'>
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>
";

$total = 0;

foreach ($detalles as $d) {

    $subtotal = $d['cantidad'] * $d['precio_unitario'];
    $total += $subtotal;

    $html .= "
    <tr>
        <td>{$d['nombre']}</td>
        <td>{$d['cantidad']}</td>
        <td>{$d['precio_unitario']}</td>
        <td>$subtotal</td>
    </tr>
    ";
}

$html .= "
</table>
<h3>Total: $total</h3>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("factura_{$venta['id']}.pdf", ["Attachment" => false]);