<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new Usuario($db);

$token = isset($_GET['token']) ? $_GET['token'] : '';
$usuarioValido = $userModel->validarToken($token);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Restablecer Contraseña</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="body-login">

    <div class="login-container">
        <h2>AleMarket</h2>
        <h3 style="font-size: 1.1rem; color: var(--primary-color); margin-bottom: 1rem;">Restablecer Contraseña</h3>

        <?php if (!$usuarioValido): ?>
            <div class="alert alert-danger" style="font-size: 0.85rem; padding: 12px; margin-bottom: 1.5rem; text-align: left;">
                El enlace de recuperación no es válido o ya ha expirado. Por favor, solicita uno nuevo.
            </div>
            <div style="margin-top: 1rem;">
                <a href="recuperar.php" style="color: var(--primary-color); font-size: 0.85rem; text-decoration: none; font-weight: 600;">
                    Volver a intentar
                </a>
            </div>
        <?php else: ?>
            <p style="font-size: 0.85rem; color: var(--secondary-color); margin-bottom: 1.5rem; line-height: 1.4;">
                Hola <strong><?php echo htmlspecialchars($usuarioValido['nombre']); ?></strong>, ingresa tu nueva contraseña de acceso al sistema.
            </p>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'token_invalido'): ?>
                <div class="alert alert-danger" style="font-size: 0.8rem; padding: 10px; margin-bottom: 1rem; text-align: left;">
                    Hubo un error procesando tu solicitud. Vuelve a intentarlo.
                </div>
            <?php endif; ?>

            <form action="../index.php?action=procesarRestablecer" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <input type="password" name="password" id="password" required minlength="8" placeholder="Nueva Contraseña (Mín. 8 caracteres)" style="margin-bottom: 1.2rem;">

                <button type="submit">Guardar Nueva Contraseña</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>