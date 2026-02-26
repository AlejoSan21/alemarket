<?php
require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$ventas = $model->historialVentas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Ventas</title>
</head>
<body>

<h2>Historial de Ventas</h2>

<a href="dashboard.php">← Volver</a>

<hr>

<table border="1" cellpadding="5">
<tr>
<th>ID Venta</th>
<th>Empleado</th>
<th>Total</th>
<th>Fecha</th>
<th>Detalle</th>
</tr>

<?php foreach ($ventas as $v): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= htmlspecialchars($v['empleado']) ?></td>
<td>$<?= $v['total'] ?></td>
<td><?= $v['fecha'] ?></td>
<td><a href="detalle_venta.php?id=<?= $v['id'] ?>">Ver</a></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>