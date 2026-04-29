<?php
// Solo permitiremos ver esto si hay una sesión activa de Superadmin
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    die("Acceso denegado. Solo el Superadmin puede crear usuarios.");
}

require_once '../config/db.php';
require_once '../models/Rol.php';

$database = new Database();
$db = $database->getConnection();
$rolModel = new Rol($db);
$stmt = $rolModel->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuarios - AleMarket</title>
</head>
<body>
    <h1>Panel de Superadmin</h1>
    <h2>Registrar Nuevo Usuario</h2>

    <form action="../index.php?action=registrar_usuario" method="POST">
        <label>Nombre Completo (Sin números):</label><br>
        <input type="text" name="nombre_completo" required><br><br>

        <label>Nombre de Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>

        <label>Contraseña (Min. 8 caracteres, debe incluir . o @):</label><br>
        <input type="password" name="password" required><br><br>

        <label>Asignar Rol:</label><br>
        <select name="rol_id" required>
            <option value="">Seleccione un rol</option>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Guardar Usuario</button>
    </form>
    <br>
    <a href="../index.php?action=salir">Cerrar Sesión</a>
</body>
</html>