<?php
require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$ventas = $model->historialVentas(
    $_SESSION['usuario_id'],
    $_SESSION['rol_id'],
    $desde,
    $hasta
);

$resumen = $model->resumenHistorial(
    $_SESSION['usuario_id'],
    $_SESSION['rol_id'],
    $desde,
    $hasta
);

$resumenMetodo = $model->ventasPorMetodoPago(
    $_SESSION['usuario_id'],
    $_SESSION['rol_id'],
    $desde,
    $hasta
);

$ventasPorDia = $model->ventasPorDia(
    $_SESSION['usuario_id'],
    $_SESSION['rol_id'],
    $desde,
    $hasta
);

$productoTop = $model->productoMasVendido(
    $_SESSION['usuario_id'],
    $_SESSION['rol_id']
);

$ventasEmpleado = $model->VentasPorEmpleado();

$labelsEmpleado = [];
$dataEmpleado = [];

foreach ($ventasEmpleado as $v) {
    $labelsEmpleado[] = $v['nombre'];
    $dataEmpleado[] = $v['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Ventas</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<h2>Historial de Ventas</h2>

<a href="dashboard.php">← Volver</a>

<hr>

<h3>📊 Resumen General</h3>

<div style="display:flex; gap:20px; margin-bottom:20px;">

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>🧾 Total Ventas</h4>
<p style="font-size:22px;">
<?= $resumen['cantidad'] ?? 0 ?>
</p>
</div>

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>💰 Total Generado</h4>
<p style="font-size:22px;">
<?= formatoPeso($resumen['total'] ?? 0) ?>
</p>
</div>

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>🏆 Producto Top</h4>
<p style="font-size:18px;">
<?= htmlspecialchars($productoTop['nombre'] ?? 'Ninguno') ?>
</p>
</div>

</div>

<hr>

<h3>💳 Ventas por Método de Pago</h3>

<?php if ($resumenMetodo): ?>
<table border="1" cellpadding="8">
<tr>
<th>Método</th>
<th>Total</th>
</tr>

<?php foreach ($resumenMetodo as $metodo): ?>
<tr>
<td><?= htmlspecialchars($metodo['metodo_pago']) ?></td>
<td><?= formatoPeso($metodo['total']) ?></td>
</tr>
<?php endforeach; ?>

</table>
<?php else: ?>
<p>No hay ventas registradas.</p>
<?php endif; ?>

<hr>

<h3>📅 Filtrar por fecha</h3>

<form method="GET">
Desde:
<input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>">

Hasta:
<input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>">

<button type="submit">Filtrar</button>
</form>

<hr>

<h3>📋 Lista de Ventas</h3>

<table border="1" cellpadding="5">
<tr>
<th>ID Venta</th>
<th>Empleado</th>
<th>Método Pago</th>
<th>Total</th>
<th>Fecha</th>
<th>Detalle</th>
</tr>

<?php foreach ($ventas as $v): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= htmlspecialchars($v['empleado']) ?></td>
<td><?= htmlspecialchars($v['metodo_pago']) ?></td>
<td><?= formatoPeso($v['total']) ?></td>
<td><?= $v['fecha'] ?></td>
<td>
<a href="detalle_venta.php?id=<?= $v['id'] ?>">Ver</a> |
<a href="factura.php?id=<?= $v['id'] ?>" target="_blank">Factura</a>
</td>
</tr>
<?php endforeach; ?>

</table>

<hr>
<hr>



</body>
</html>