<?php
require_once __DIR__ . '/../model.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model.php';
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    die("Acceso denegado");
}

use Dompdf\Dompdf;

$model = new Model($pdo);

$ventas = $model->obtenerVentasDelDia();
$resumen = $model->totalVentasDelDia();

$dompdf = new Dompdf();

$html = "
<h2>Reporte General del Día</h2>
<p>Fecha: ".date('Y-m-d')."</p>
<p>Total Ventas: {$resumen['cantidad']}</p>
<p>Total Vendido: {$resumen['total']}</p>

<table border='1' width='100%' cellpadding='5'>
<tr>
<th>ID</th>
<th>Empleado</th>
<th>Total</th>
<th>Fecha</th>
</tr>
";

foreach ($ventas as $v) {
    $html .= "
    <tr>
        <td>{$v['id']}</td>
        <td>{$v['empleado']}</td>
        <td>{$v['total']}</td>
        <td>{$v['fecha']}</td>
    </tr>
    ";
}

$html .= "</table>";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_dia.pdf", ["Attachment" => false]);