<?php
session_start();
// Seguridad: Solo Superadmin (ID 3)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/../controllers/UsuarioController.php';
$uController = new UsuarioController();
$usuarios = $uController->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Gestión de Personal</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header class="panel-header">
        <div>
            <h1>👥 Gestión de Personal</h1>
            <small>Panel exclusivo del Superadministrador</small>
        </div>
        <nav>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="../index.php?action=logout" class="btn-logout">Salir</a>
        </nav>
    </header>

    <?php if(isset($_GET['ok'])): ?>
        <div class="alert alert-success">
            <strong>✅ Éxito:</strong> Operación realizada correctamente.
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Error:</strong> 
            <?php 
                if($_GET['error'] == 'usuario_con_ventas') echo "No puedes eliminar este usuario porque tiene ventas registradas. Mejor desactívalo.";
                elseif($_GET['error'] == 'password_debil') echo "La contraseña debe tener al menos 8 caracteres, una mayúscula y un símbolo.";
                else echo htmlspecialchars($_GET['error']); 
            ?>
        </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 1fr 350px;">
        
        <section class="panel-block">
            <h3>Colaboradores Registrados</h3>
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Login</th>
                        <th>Rol</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($u['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                        <td>
                            <span class="badge" style="background: #f1f5f9;">
                                <?php echo htmlspecialchars($u['rol_nombre']); ?>
                            </span>
                        </td>
                        <td align="center">
                            <?php if($u['estado'] == 1): ?>
                                <span class="badge" style="background: #dcfce7; color: #166534;">Activo</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fee2e2; color: #ef4444;">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td align="center">
                            <?php if($u['id'] != $_SESSION['usuario_id']): ?>
                                <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn-panel">✏️</a>
                                
                                <?php if($u['estado'] == 1): ?>
                                    <a href="../controllers/UsuarioController.php?action=desactivar&id=<?php echo $u['id']; ?>" 
                                       class="btn-panel danger" onclick="return confirm('¿Desactivar?')">Off</a>
                                <?php else: ?>
                                    <a href="../controllers/UsuarioController.php?action=activar&id=<?php echo $u['id']; ?>" 
                                       class="btn-panel success">On</a>
                                <?php endif; ?>

                                <a href="../controllers/UsuarioController.php?action=eliminar&id=<?php echo $u['id']; ?>" 
                                   class="btn-panel danger" style="background: #334155;" 
                                   onclick="return confirm('¿Borrar definitivamente?')">🗑️</a>
                            <?php else: ?>
                                <small style="color: var(--secondary-color);">Mi cuenta</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel-block">
            <h3>➕ Nuevo Colaborador</h3>
            <form method="POST" action="../controllers/UsuarioController.php?action=registrar" style="display: grid; gap: 10px;">
                <input type="text" name="nombre" placeholder="Nombre completo" required class="input-pro">
                <input type="text" name="usuario" placeholder="Nombre de usuario" required class="input-pro">
                <input type="password" name="password" placeholder="Contraseña (Min. 8 carac, 1 Mayus, 1 Símbolo)" required class="input-pro">
                
                <select name="rol_id" required class="input-pro">
                    <option value="">-- Seleccionar Rol --</option>
                    <option value="1">Administrador</option>
                    <option value="2">Empleado</option>
                    <option value="3">Superadmin</option>
                </select>
                
                <button type="submit" class="btn-panel primary" style="padding: 12px;">Registrar en Sistema</button>
            </form>
        </section>
    </div>
</div>

</body>
</html>