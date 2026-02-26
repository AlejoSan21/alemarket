<?php
require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$productos = $model->obtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Venta</title>

<script>
let items = [];

function agregarProducto() {

    let productoSelect = document.getElementById("producto");
    let cantidad = parseInt(document.getElementById("cantidad").value);

    if (cantidad <= 0 || isNaN(cantidad)) {
        alert("Cantidad inválida");
        return;
    }

    let stock = parseInt(productoSelect.selectedOptions[0].dataset.stock);

    if (cantidad > stock) {
        alert("No hay suficiente stock");
        return;
    }

    let productoId = productoSelect.value;
    let productoNombre = productoSelect.options[productoSelect.selectedIndex].text;
    let precio = parseFloat(productoSelect.selectedOptions[0].dataset.precio);

    let subtotal = precio * cantidad;

    items.push({
        producto_id: productoId,
        cantidad: cantidad,
        precio: precio,
        subtotal: subtotal
    });

    renderTabla();
}

function renderTabla() {

    let tbody = document.getElementById("tablaItems");
    tbody.innerHTML = "";

    items.forEach(item => {

        let row = `<tr>
            <td>${item.producto_id}</td>
            <td>${item.cantidad}</td>
            <td>${item.precio}</td>
            <td>${item.subtotal}</td>
        </tr>`;

        tbody.innerHTML += row;
    });

    document.getElementById("itemsInput").value = JSON.stringify(items);
}
</script>

</head>
<body>

<h2>Registrar Venta</h2>

<a href="dashboard.php">← Volver</a>

<hr>

<label>Producto:</label>
<select id="producto">

<?php foreach ($productos as $p): ?>
<option value="<?= $p['id'] ?>"
data-precio="<?= $p['precio'] ?>"
        data-stock="<?= $p['stock'] ?>">
<?= htmlspecialchars($p['nombre']) ?> — $<?= $p['precio'] ?> (Stock: <?= $p['stock'] ?>)
</option>
<?php endforeach; ?>

</select>

<label>Cantidad:</label>
<input type="number" id="cantidad" value="1">

<button type="button" onclick="agregarProducto()">Agregar</button>

<hr>

<table border="1">
<thead>
<tr>
<th>ID Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody id="tablaItems"></tbody>
</table>

<hr>

<form method="POST" action="../controller.php?action=registrar_venta">

<input type="hidden" name="items" id="itemsInput">

<label>Método de pago:</label>
<select name="metodo_pago">
<option value="1">Efectivo</option>
<option value="2">Transferencia</option>
</select>

<br><br>

<button type="submit">Confirmar venta</button>

</form>

</body>
</html>