<?php
require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$ventasHoy = $model->ventasHoy();
$dineroHoy = $model->dineroHoy();
$productos = $model->totalProductos();

$stockBajo = $model->productosStockBajo();
$topProductos = $model->topProductos();

$nombre = $_SESSION['nombre'];
$rol_id = $_SESSION['rol_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | AleMarket</title>
</head>

<body>

<h3>📊 Resumen del Día</h3>

<div style="display:flex; gap:20px; margin-bottom:30px;">

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>🧾 Ventas Hoy</h4>
<p style="font-size:24px;">
<?= $ventasHoy['total'] ?? 0 ?>
</p>
</div>

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>💰 Total Hoy</h4>
<p style="font-size:24px;">
<?= formatoPeso($dineroHoy['total'] ?? 0) ?>
</p>
</div>

<div style="border:1px solid #ccc; padding:15px; width:200px;">
<h4>📦 Productos</h4>
<p style="font-size:24px;">
<?= $productos['total'] ?? 0 ?>
</p>
</div>

</div>
<h3>⚠ Productos con poco stock</h3>

<?php if ($stockBajo): ?>

<ul>

<?php foreach ($stockBajo as $p): ?>

<li>
<?= htmlspecialchars($p['nombre']) ?> 
- Stock: <?= $p['stock'] ?>
</li>

<?php endforeach; ?>

</ul>

<?php else: ?>

<p>Todos los productos tienen stock suficiente.</p>

<?php endif; ?>
<div class="container">
<h3>🏆 Top 5 Productos Más Vendidos</h3>

<?php if ($topProductos): ?>

<ol>

<?php foreach ($topProductos as $p): ?>

<li>
<?= htmlspecialchars($p['nombre']) ?> 
— <?= $p['total_vendido'] ?> vendidos
</li>

<?php endforeach; ?>

</ol>

<?php else: ?>

<p>No hay ventas registradas.</p>

<?php endif; ?>
<header>
<div>
<h2>Hola, <?= htmlspecialchars($nombre); ?> 👋</h2>

<span class="badge">
<?= ($rol_id == 1) ? "Administrador" : "Empleado"; ?>
</span>

</div>
</header>

<h3>Panel de Control</h3>

<ul class="menu-grid">

<li>
<a href="ventas.php">
Registrar Venta
</a>
</li>

<li>
<a href="productos.php">
Gestión de Productos
</a>
</li>

<li>
<a href="historial.php">
Historial Ventas
</a>
</li>

<?php if ($rol_id == 1): ?>
<li>
<a href="usuarios.php">
Gestionar Empleados
</a>
</li>
<?php endif; ?>

<li>
<a href="reporte_dia.php">
Ver Reporte del Día
</a>
</li>

<li>
<a href="../controller.php?action=logout">
Cerrar sesión
</a>
</li>

</ul>

</div>

</body>
</html>