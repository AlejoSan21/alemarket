<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../controllers/ProductoController.php';

if (!isset($_GET['id'])) { header("Location: productos.php"); exit; }

$pController = new ProductoController();
$producto = $pController->obtenerPorId($_GET['id']);

if (!$producto) {
    die("Error: El producto no existe en el sistema. <a href='productos.php'>Volver</a>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Editar Producto</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel container-small">
    <header class="panel-header">
        <div>
            <h1>Editar Producto</h1>
            <small>Actualizando: <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></small>
        </div>
        <nav>
            <a href="productos.php" style="color: var(--danger-color);"> Cancelar</a>
        </nav>
    </header>

    <section class="panel-block">
        <form method="POST" action="../controllers/ProductoController.php?action=actualizar">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

            <div style="display: grid; gap: 15px;">
                <div>
                    <label>Código de Barras</label>
                    <input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($producto['codigo_barras']); ?>" class="input-pro" required>
                </div>

                <div>
                    <label>Nombre del Producto</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" class="input-pro" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label>Precio de Venta</label>
                        <input type="number" name="precio" value="<?php echo $producto['precio']; ?>" step="0.01" class="input-pro" required>
                    </div>
                    <div>
                        <label>Stock en Bodega</label>
                        <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" class="input-pro" required>
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <button type="submit" class="btn-panel primary" style="width: 100%; padding: 15px; font-size: 1rem;">
                        GUARDAR CAMBIOS
                    </button>
                </div>
            </div>
        </form>
    </section>
</div>

</body>
</html>