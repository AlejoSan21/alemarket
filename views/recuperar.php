<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Recuperar Contraseña</title>
    <link rel="stylesheet" href="http://localhost/alemarket1/views/assets/css/estilos.css">
</head>
<body class="body-login">

    <div class="login-container">
        <h2>AleMarket</h2>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'enviado'): ?>
            <h3 style="font-size: 1.1rem; color: var(--primary-color); margin-bottom: 1rem;">¡Correo Enviado!</h3>
            
            <div class="alert alert-success" style="font-size: 0.85rem; padding: 12px; margin-bottom: 1rem; text-align: center; justify-content: center; display: flex;">
                Se ha enviado un enlace de recuperación a tu correo electrónico.
            </div>
            
            <p style="font-size: 0.8rem; color: var(--secondary-color); line-height: 1.4; margin-bottom: 1.5rem; text-align: center;">
                Revisa tu bandeja de entrada (y la carpeta de spam) para restablecer tu cuenta.
            </p>

        <?php else: ?>
            <h3 style="font-size: 1.1rem; color: var(--secondary-color); margin-bottom: 1rem;">¿Olvidaste tu contraseña?</h3>
            <p style="font-size: 0.85rem; color: var(--secondary-color); margin-bottom: 1.5rem; line-height: 1.4;">
                Introduce el correo electrónico asociado a tu cuenta para restablecer la clave.
            </p>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'no_existe'): ?>
                <div class="alert alert-danger" style="font-size: 0.8rem; padding: 10px; margin-bottom: 1rem; text-align: left; background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 4px;">
                    El correo electrónico no está registrado.
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error_envio'): ?>
                <div class="alert alert-danger" style="font-size: 0.8rem; padding: 10px; margin-bottom: 1rem; text-align: left; background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 4px;">
                    Error al enviar el correo. Verifica tu conexión o configuración SMTP.
                </div>
            <?php endif; ?>

            <form action="http://localhost/alemarket1/index.php?action=solicitarRecuperacion" method="POST">
                <input type="email" name="correo" id="correo" required placeholder="Tu Correo Electrónico" style="margin-bottom: 1.2rem;">
                <button type="submit">Continuar</button>
            </form>
        <?php endif; ?>
        
        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="http://localhost/alemarket1/views/login.php" style="color: var(--primary-color); font-size: 0.85rem; text-decoration: none; font-weight: 600;">
                Volver al Login
            </a>
        </div>
    </div>

</body>
</html>