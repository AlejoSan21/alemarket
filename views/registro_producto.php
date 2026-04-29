<?php
session_start();
// Seguridad: Solo Superadmin o Administrador
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'superadmin' && $_SESSION['rol'] != 'administrador')) {
    die("Acceso denegado. No tienes permisos para gestionar inventario.");
}

require_once '../config/db.php';
require_once '../models/Categoria.php';

$database = new Database();
$db = $database->getConnection();
$catModel = new Categoria($db);
$stmt = $catModel->listar(); // Listamos las categorías creadas anteriormente
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - AleMarket</title>
</head>
<body>
    <h1>AleMarket - Gestión de Inventario</h1>
    <hr>
    <h3>Registrar Nuevo Producto</h3>

    <form action="../index.php?action=registrar_producto" method="POST">
        <label>Código de Barras:</label><br>
        <input type="text" name="codigo_barras" placeholder="Escanea o escribe el código" autofocus required><br><br>

        <label>Nombre del Producto:</label><br>
        <input type="text" name="nombre" placeholder="Ej: Arroz 1kg" required><br><br>

        <label>Cantidad Inicial (Stock):</label><br>
        <input type="number" name="stock" value="0" min="0" required><br><br>

        <label>Precio de Venta (Unitario):</label><br>
        <input type="number" step="0.01" name="precio" placeholder="0.00" required><br><br>

        <label>Categoría:</label><br>
        <select name="categoria_id" required>
            <option value="">Seleccione una categoría</option>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Guardar en Base de Datos</button>
    </form>
    <br>
    <a href="../index.php?action=inicio">Volver al Menú Principal</a>
</body>
</html>