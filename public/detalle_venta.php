<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
SELECT d.*, p.nombre
FROM detalle_venta d
JOIN productos p ON d.producto_id = p.id
WHERE d.venta_id = ?
");

$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Venta</title>
</head>
<body>

<h2>Detalle de Venta #<?= $id ?></h2>

<a href="historial.php">← Volver</a>

<hr>

<table border="1" cellpadding="5">
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>

<?php foreach ($items as $i): ?>
<tr>
<td><?= htmlspecialchars($i['nombre']) ?></td>
<td><?= $i['cantidad'] ?></td>
<td>$<?= $i['precio_unitario'] ?></td>
<td>$<?= $i['subtotal'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>