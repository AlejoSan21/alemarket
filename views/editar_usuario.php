<?php
session_start();

// Seguridad: Solo Superadmin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: dashboard.php"); 
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

if (!isset($_GET['id']) || empty($_GET['id'])) { 
    header("Location: usuarios.php"); 
    exit; 
}

$db = (new Database())->getConnection();
$usuarioModel = new Usuario($db);
$user = $usuarioModel->obtenerPorId($_GET['id']);

if (!$user) {
    die("Usuario no encontrado. <a href='usuarios.php'>Volver</a>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Editar Usuario</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel container-small">
    <header class="panel-header">
        <div>
            <h1>✏️ Editar Colaborador</h1>
            <small>Perfil de: <strong><?php echo htmlspecialchars($user['nombre']); ?></strong></small>
        </div>
        <nav>
            <a href="usuarios.php" class="btn-panel">Cancelar</a>
        </nav>
    </header>

    <section class="panel-block">
        <form method="POST" action="../controllers/UsuarioController.php?action=actualizar">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <label>Nombre Completo</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" class="input-pro" required>

            <label>Nombre de Usuario</label>
            <input type="text" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" class="input-pro" required>

            <label>Correo Electrónico</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($user['correo'] ?? ''); ?>" class="input-pro" required>

            <label>Rol en el Sistema</label>
            <select name="rol_id" class="input-pro" required>
                <option value="1" <?php echo ($user['rol_id'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo ($user['rol_id'] == 2) ? 'selected' : ''; ?>>Empleado</option>
                <option value="3" <?php echo ($user['rol_id'] == 3) ? 'selected' : ''; ?>>Superadmin</option>
            </select>

            <div class="seccion-password" style="margin-top: 15px; margin-bottom: 15px;">
                <label>🔐 Cambiar Contraseña</label>
                <input type="password" name="nueva_password" placeholder="Dejar vacío para no cambiar" class="input-pro">
            </div>

            <button type="submit" class="btn-panel primary" style="width: 100%; padding: 15px; font-size: 1rem;">
                Guardar Cambios
            </button>
        </form>
    </section>
</div>

</body>
</html>