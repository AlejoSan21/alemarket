<?php
require_once __DIR__ . '/../model.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['rol_id'] != 1) {
    die("Acceso solo para administrador");
}

$usuarios = $pdo->query("
    SELECT u.id, u.nombre, u.usuario, r.nombre AS rol
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>
</head>
<body>

<h2>Gestión de Empleados</h2>

<a href="dashboard.php">← Volver</a>

<hr>

<h3>Crear empleado</h3>

<form method="POST" action="../controller.php?action=crear_empleado">

<label>Nombre:</label><br>
<input type="text" name="nombre" required><br><br>

<label>Usuario:</label><br>
<input type="text" name="usuario" required><br><br>

<label>Password:</label><br>
<input type="password" name="password" required><br><br>

<button type="submit">Crear</button>

</form>

<hr>

<h3>Lista usuarios</h3>

<table border="1" cellpadding="5">
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Usuario</th>
<th>Rol</th>
</tr>

<?php foreach ($usuarios as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['nombre']) ?></td>
<td><?= htmlspecialchars($u['usuario']) ?></td>
<td><?= $u['rol'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>