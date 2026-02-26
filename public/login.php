<?php
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Acceso</title>
</head>
<body>

<div class="login-container">
    <h2>AleMarket</h2>

    <?php if (isset($_GET['error'])): ?>
        <p class="error-msg">Usuario o contraseña incorrectos</p>
    <?php endif; ?>

    <form method="POST" action="../controller.php?action=login">

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit">Ingresar</button>

    </form>

</div>

</body>
</html>