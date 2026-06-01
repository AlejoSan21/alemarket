<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Acceso</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="body-login">

    <div class="login-container">
        <h2>AleMarket</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg">
                Usuario o contraseña incorrectos
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'password_cambiada'): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem; text-align: center; justify-content: center;">
                ¡Contraseña actualizada con éxito! Ya puedes ingresar.
            </div>
        <?php endif; ?>

        <form method="POST" action="../index.php?action=procesar_login">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar al Sistema</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="recuperar.php" style="color: var(--primary-color); font-size: 0.85rem; text-decoration: none; font-weight: 600; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
    </div>

</body>
</html>