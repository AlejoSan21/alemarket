<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

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

<div class="container">
    <header>
        <div>
            <h2>Hola, <?php echo htmlspecialchars($nombre); ?> 👋</h2>
            <span class="badge">
                <?php echo ($rol_id == 1) ? "Administrador" : "Empleado"; ?>
            </span>
        </div>
        
    </header>

    <h3>Panel de Control</h3>

    <ul class="menu-grid">
        <li>
            <a href="ventas.php">
                <span style="font-size: 2rem;"></span>
                Registrar Venta
            </a>
        </li>
        
        <li>
            <a href="productos.php">
                <span style="font-size: 2rem;"></span>
                Gestión de Productos
            </a>
        </li>

        <li>
            <a href="historial.php">
                <span style="font-size: 2rem;"></span>
                Historial Ventas
            </a>
        </li>

        <?php if ($rol_id == 1): ?>
            <li>
                <a href="usuarios.php">
                    <span style="font-size: 2rem;"></span>
                    Gestionar Empleados
                </a>    
            </li>
        <?php endif; ?>

            <li><a href="../controller.php?action=logout">Cerrar sesión
                <span style="font-size: 2rem;"></span>
            </a>
        </li>
    
    </ul>
</div>

</body>
</html>