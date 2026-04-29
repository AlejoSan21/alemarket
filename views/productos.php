<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../controllers/ProductoController.php';
$pController = new ProductoController();
$productos = $pController->listarProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Inventario</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header class="panel-header">
        <div>
            <h1>Gestión de Inventario</h1>
            <small>AleMarket - Control de existencias</small>
        </div>
        <nav>
            <a href="dashboard.php"> Dashboard</a>
            <a href="ventas.php"> Nueva Venta</a>
            <a href="../index.php?action=logout" class="btn-logout">Salir</a>
        </nav>
    </header>

    <?php if(isset($_GET['ok'])): ?>
        <div class="badge" style="background: #dcfce7; color: #166534; display: block; padding: 10px; margin-bottom: 10px;">
             Operación realizada con éxito.
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="badge badge-danger" style="display: block; padding: 10px; margin-bottom: 10px;">
             <?php echo ($_GET['error'] === 'producto_con_ventas') ? "No se puede eliminar: El producto tiene historial." : "Error: " . htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 1fr 350px;">
        
        <section class="panel-block">
            <h3>Lista de Productos</h3>
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th style="text-align: center;">Stock</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($productos)): ?>
                        <tr><td colspan="5" align="center">No hay productos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach($productos as $p): ?>
                        <tr>
                            <td><code><?php echo $p['codigo_barras']; ?></code></td>
                            <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                            <td>$ <?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                            <td align="center">
                                <span class="badge <?php echo ($p['stock'] <= 5) ? 'badge-danger' : ''; ?>" style="background: <?php echo ($p['stock'] > 5) ? '#f1f5f9' : ''; ?>">
                                    <?php echo $p['stock']; ?>
                                </span>
                            </td>
                            <td align="center">
                                <a href="editar_producto.php?id=<?php echo $p['id']; ?>" class="btn-panel primary">Editar</a> 
                                
                                <?php if ($_SESSION['rol_id'] != 2): ?>
                                    <a href="../controllers/ProductoController.php?action=eliminar&id=<?php echo $p['id']; ?>" 
                                       class="btn-panel danger"
                                       onclick="return confirm('¿Eliminar este producto?')">Borrar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="panel-block">
            <h3> Agregar Producto</h3>
            <form method="POST" action="../controllers/ProductoController.php?action=guardar" style="display: grid; gap: 10px;">
                <label>Código de Barras</label>
                <input type="text" name="codigo_barras" placeholder="Ej: 770..." required class="input-pro">
                
                <label>Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre del producto" required class="input-pro">
                
                <label>Precio de Venta</label>
                <input type="number" name="precio" placeholder="0.00" step="0.01" required class="input-pro">
                
                <label>Stock Inicial</label>
                <input type="number" name="stock" placeholder="Cantidad" required class="input-pro">
                
                <button type="submit" class="btn-panel primary" style="padding: 12px; margin-top: 10px;">Guardar Producto</button>
            </form>
        </section>
    </div>
</div>

</body>
</html>