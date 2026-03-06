<?php
require_once __DIR__ . '/../model.php';
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    die("Acceso denegado");
}

$model = new Model($pdo);

$ventas = $model->obtenerVentasDelDia();
$resumen = $model->totalVentasDelDia();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporte del Día</title>
    <style>
        body { font-family: Arial; }
        table { border-collapse: collapse; width: 100%; margin-top:20px;}
        th, td { border:1px solid #000; padding:8px; text-align:center; }
        th { background:#eee; }
    </style>
</head>
<body>
<a href="dashboard.php">← Volver</a>
<h2>📊 Reporte General del Día</h2>

<p><strong>Fecha:</strong> <?= date('Y-m-d') ?></p>
<p><strong>Total Ventas:</strong> <?= $resumen['cantidad'] ?? 0 ?></p>
<p><strong>Total Vendido:</strong> $<?= $resumen['total'] ?? 0 ?></p>

<table>
<tr>
    <th>ID Venta</th>
    <th>Empleado</th>
    <th>Método Pago</th>
    <th>Total</th>
    <th>Fecha</th>
</tr>

<?php foreach ($ventas as $v): ?>
<tr>
    <td><?= $v['id'] ?></td>
    <td><?= $v['empleado'] ?></td>
    <td><?= $v['metodo_pago_id'] ?></td>
    <td>$<?= $v['total'] ?></td>
    <td><?= $v['fecha'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<br>
<a href="reporte_dia_pdf.php">🧾 Exportar en PDF</a>

</body>
</html>