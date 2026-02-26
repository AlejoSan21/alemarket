<?php

require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$productos = $model->obtenerProductos();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Productos</title>
</head>
<body>

<h2>Gestión de productos</h2>
<?php if (isset($_GET['error']) && $_GET['error'] == 'vendido'): ?>
<p style="color:red;">
No se puede eliminar el producto porque tiene ventas registradas.
</p>
<?php endif; ?>

<?php if (isset($_GET['ok']) && $_GET['ok'] == 'eliminado'): ?>
<p style="color:green;">
Producto eliminado correctamente.
</p>
<?php endif; ?>
<a href="dashboard.php">← Volver</a>

<hr>

<h3>Agregar producto</h3>

<form method="POST" action="../controller.php?action=agregar_producto">

Código:
<input type="text" name="codigo" required><br><br>

Nombre:
<input type="text" name="nombre" required><br><br>

Precio:
<input type="number" step="0.01" name="precio" required><br><br>

Stock:
<input type="number" min="0" name="stock" required><br><br>

<button>Guardar</button>

</form>

<hr>

<h3>Lista</h3>

<table border="1" cellpadding="5">

<tr>
<th>ID</th>
<th>Código</th>
<th>Nombre</th>
<th>Precio</th>
<th>Stock</th>
<th>Acciones</th>
</tr>

<?php foreach ($productos as $p): ?>

<tr>

<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['codigo_barras']) ?></td>
<td><?= htmlspecialchars($p['nombre']) ?></td>
<td>$<?= number_format($p['precio'],0,',','.') ?></td>
<td><?= $p['stock'] ?></td>

<td>
<a href="editar_producto.php?id=<?= $p['id'] ?>">Editar</a> |
<a href="../controller.php?action=eliminar_producto&id=<?= $p['id'] ?>"
onclick="return confirm('Eliminar?')">Eliminar</a>
</td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>