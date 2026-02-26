<?php
require_once __DIR__.'/../model.php';

$id=$_GET['id'];
$p=$model->obtenerProducto($id);
?>

<h2>Editar producto</h2>

<form method="POST" action="../controller.php?action=editar_producto">

<input type="hidden" name="id" value="<?= $p['id'] ?>">

Código:
<input type="text" name="codigo" value="<?= $p['codigo_barras'] ?>"><br>

Nombre:
<input type="text" name="nombre" value="<?= $p['nombre'] ?>"><br>

Precio:
<input type="number" step="0.01" name="precio" value="<?= $p['precio'] ?>"><br>

Stock:
<input type="number" name="stock" value="<?= $p['stock'] ?>"><br>

<button>Actualizar</button>

</form>