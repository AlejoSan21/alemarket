<?php
session_start();
// Solo Superadmin o Administrador pueden crear categorías
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'superadmin' && $_SESSION['rol'] != 'administrador')) {
    die("Acceso denegado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - AleMarket1</title>
</head>
<body>
    <h1>Gestión de Inventario</h1>
    <h2>Nueva Categoría</h2>
    
    <form action="../index.php?action=registrar_categoria" method="POST">
        <label>Nombre de la Categoría (Ej: Aseo, Lácteos, Granos):</label><br>
        <input type="text" name="nombre_categoria" required><br><br>
        
        <button type="submit">Guardar Categoría</button>
    </form>
    <br>
    <a href="../index.php?action=inicio">Volver al Inicio</a>
</body>
</html>