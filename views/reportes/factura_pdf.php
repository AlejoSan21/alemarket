<h1>ALEMARKET</h1>
<p>Ticket de Venta #<?= $datos['id'] ?></p>
<hr>
<p>Fecha: <?= $datos['fecha'] ?></p>
<p>Vendedor: <?= $datos['vendedor'] ?></p>
<p>Método: <?= $datos['metodo'] ?></p>

<table border="1" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($datos['productos'] as $p): ?>
        <tr>
            <td><?= $p['nombre'] ?></td>
            <td><?= $p['cantidad'] ?></td>
            <td><?= number_format($p['precio_unitario'], 0) ?></td>
            <td><?= number_format($p['subtotal'], 0) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h3>Total: $<?= number_format($datos['total'], 0) ?></h3>