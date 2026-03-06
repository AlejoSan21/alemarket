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

function agregarProducto(){

    let productoSelect = document.getElementById("producto");
    let cantidad = parseInt(document.getElementById("cantidad").value);

    if(isNaN(cantidad) || cantidad <= 0){
        alert("Cantidad inválida");
        return;
    }

    let stock = parseInt(productoSelect.selectedOptions[0].dataset.stock);

    if(cantidad > stock){
        alert("No hay suficiente stock");
        return;
    }

    let productoId = productoSelect.value;
    let productoNombre = productoSelect.options[productoSelect.selectedIndex].text;
    let precio = parseFloat(productoSelect.selectedOptions[0].dataset.precio);

    let subtotal = precio * cantidad;

    items.push({
        producto_id: productoId,
        nombre: productoNombre,
        cantidad: cantidad,
        precio: precio,
        subtotal: subtotal
    });

    renderTabla();
}

function eliminarItem(index){
    items.splice(index,1);
    renderTabla();
}

function renderTabla(){

    let tbody = document.getElementById("tablaItems");
    let total = 0;

    tbody.innerHTML = "";

    items.forEach((item,index)=>{

        total += item.subtotal;

        let fila = `
        <tr>
            <td>${item.nombre}</td>
            <td>${item.cantidad}</td>
            <td>$${item.precio}</td>
            <td>$${item.subtotal}</td>
            <td>
                <button type="button" onclick="eliminarItem(${index})">
                ❌
                </button>
            </td>
        </tr>
        `;

        tbody.innerHTML += fila;

    });

    document.getElementById("totalGeneral").innerText = total.toFixed(2);
    document.getElementById("itemsInput").value = JSON.stringify(items);
}

function validarFormulario(){

    if(items.length === 0){
        alert("Debe agregar al menos un producto");
        return false;
    }

    return true;

}

</script>

</head>

<body>

<h2>Registrar Venta</h2>

<a href="dashboard.php">← Volver</a>

<hr>

<h3>Agregar producto</h3>

<label>Producto:</label>

<select id="producto">

<?php foreach($productos as $p): ?>

<option 
value="<?= $p['id'] ?>"
data-precio="<?= $p['precio'] ?>"
data-stock="<?= $p['stock'] ?>">

<?= htmlspecialchars($p['nombre']) ?> 
— $<?= $p['precio'] ?> 
(Stock: <?= $p['stock'] ?>)

</option>

<?php endforeach; ?>

</select>

<label>Cantidad:</label>

<input
type="number"
id="cantidad"
value="1"
min="1"
>

<button type="button" onclick="agregarProducto()">
Agregar
</button>

<hr>

<h3>Productos en la venta</h3>

<table border="1" width="100%">

<thead>
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
<th>Eliminar</th>
</tr>
</thead>

<tbody id="tablaItems"></tbody>

</table>

<h3>
Total: $<span id="totalGeneral">0.00</span>
</h3>

<hr>

<form
method="POST"
action="../controller.php?action=registrar_venta"
onsubmit="return validarFormulario()"
>

<input
type="hidden"
name="items"
id="itemsInput"
>

<label>Método de pago:</label>

<select name="metodo_pago">

<option value="1">Efectivo</option>
<option value="2">Transferencia</option>

</select>

<br><br>

<button type="submit">
Confirmar venta
</button>

</form>

</body>
</html>