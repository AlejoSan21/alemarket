<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../controllers/VentaController.php';
$vController = new VentaController();

// PASAMOS LAS FECHAS AL CONTROLADOR (si existen)
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;
$ventas = $vController->mostrarHistorial(); 

$rol = $_SESSION['rol_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Historial</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header class="panel-header">
        <div>
            <h1>Historial de Ventas</h1>
            <small>Consulta de facturación y movimientos</small>
        </div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="ventas.php">Nueva Venta</a>
            <a href="productos.php">Inventario</a>
            <a href="../index.php?action=logout" class="btn-logout">Salir</a>
        </nav>
    </header>

    <div class="content-grid" style="grid-template-columns: 1fr 300px;">
        
        <section class="panel-block">
            <h3>Registros de Ventas</h3>
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Vendedor</th>
                        <th>Método</th>
                        <th>Total</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($ventas)): ?>
                        <tr><td colspan="6" style="text-align:center; padding: 20px;">No se encontraron registros.</td></tr>
                    <?php else: ?>
                        <?php foreach($ventas as $v): ?>
                        <tr>
                            <td>#<?php echo $v['id']; ?></td>
                            <td><small><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></small></td>
                            <td><?php echo htmlspecialchars($v['nombre_usuario']); ?></td>
                            <td><span class="badge" style="background:#f1f5f9;"><?php echo $v['metodo_pago']; ?></span></td>
                            <td><strong>$<?php echo number_format($v['total'], 0, ',', '.'); ?></strong></td>
                            <td align="center">
                                <a href="factura.php?id=<?php echo $v['id']; ?>" class="btn-panel" style="padding: 4px 8px;">Ver</a>
                                <a href="../controllers/ReporteController.php?tipo=factura_individual&id=<?php echo $v['id']; ?>" class="btn-panel primary" style="padding: 4px 8px;">PDF</a>
                                
                                <?php if ($rol != 2): ?>
                                    <a href="../controllers/VentaController.php?action=anular&id=<?php echo $v['id']; ?>" 
                                       class="btn-panel" 
                                       style="background: #fee2e2; color: #ef4444; border: none; padding: 4px 8px;"
                                       onclick="return confirm('¿Anular venta? El stock se devolverá.')">Anular</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <div style="display: grid; gap: 10px; align-content: start;">
            
            <?php if ($rol != 2): ?>
            <section class="panel-block">
                <h3>Filtrar Fechas</h3>
                <form method="GET" action="historial.php" style="display: grid; gap: 10px;">
                    <div>
                        <label style="font-size: 0.75rem; color: var(--text-muted);">Desde:</label>
                        <input type="date" name="desde" value="<?php echo $_GET['desde'] ?? ''; ?>" class="input-pro">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: var(--text-muted);">Hasta:</label>
                        <input type="date" name="hasta" value="<?php echo $_GET['hasta'] ?? ''; ?>" class="input-pro">
                    </div>
                    <button type="submit" class="btn-panel primary" style="width: 100%;">Aplicar Filtro</button>
                    <a href="historial.php" style="text-align: center; font-size: 0.8rem; color: var(--secondary-color);">Limpiar Filtros</a>
                </form>
            </section>
            <?php endif; ?>

            <section class="panel-block">
                <h3>Generar Reportes</h3>
                <div style="display: grid; gap: 8px;">
                    <a href="../controllers/ReporteController.php?tipo=dia" class="btn-panel" style="text-align: center; background: #10b981; color: white; border: none;">Cierre del Día</a>
                    
                    <?php if ($rol != 2): ?>
                        <a href="../controllers/ReporteController.php?tipo=mes" class="btn-panel" style="text-align: center; background: #3b82f6; color: white; border: none;">Reporte del Mes</a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

</body>
</html>