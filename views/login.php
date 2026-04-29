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

        <form method="POST" action="../index.php?action=procesar_login">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar al Sistema</button>
        </form>
    </div>

</body>
</html>