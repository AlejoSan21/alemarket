<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../Controllers/DashboardController.php';
$controller = new DashboardController();
$datos = $controller->obtenerDatosParaVista();
$rol = $_SESSION['rol_id']; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Dashboard</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header>
        <div>
            <h1>AleMarket</h1>
            <small>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></small>
        </div>
        <nav>
            <a href="ventas.php"> Nueva Venta</a> 
            <a href="historial.php"> Historial</a>
            <a href="productos.php"> Inventario</a>
            <?php if ($rol == 1 || $rol == 3): ?>
                <a href="reportes.php" style="font-weight: bold; color: var(--success-color);"> Reportes</a>
            <?php endif; ?>
            <a href="../index.php?action=logout" style="color:red"> Salir</a>
        </nav>
    </header>

    <section class="stats-grid">
        <div class="stat-card" style="border-top: 4px solid var(--primary-color);">
            <h3>Ventas Hoy</h3>
            <p><?php echo $datos['resumen']['total_ventas']; ?></p>
        </div>
        
        <?php if ($rol == 1 || $rol == 3): ?>
        <div class="stat-card" style="border-top: 4px solid var(--success-color);">
            <h3>Dinero en Caja</h3>
            <p>$<?php echo number_format($datos['resumen']['dinero_total'], 0, ',', '.'); ?></p>
        </div>
        <?php endif; ?>

        <div class="stat-card" style="border-top: 4px solid #3b82f6;">
            <h3>Total Productos</h3>
            <p><?php echo $datos['totalProds']['total']; ?></p>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--warning-color);">
            <h3>Stock Bajo</h3>
            <p style="color:var(--danger-color)"><?php echo count($datos['stockBajo']); ?></p>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="panel-block">
            <h3> Tendencia de Ventas</h3>
            <div style="height: 250px;">
                <canvas id="graficaVentas"></canvas>
            </div>
        </section>

        <div style="display: grid; gap: 10px;">
            <section class="panel-block">
                <h3> Últimas Ventas</h3>
                <table>
                    <thead>
                        <tr><th>Vendedor</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos['ultimasVentas'] as $uv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($uv['vendedor']); ?></td>
                            <td><strong>$<?php echo number_format($uv['total'], 0, ',', '.'); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="panel-block">
                <h3> Alerta Inventario</h3>
                <table>
                    <tbody>
                        <?php foreach($datos['stockBajo'] as $sb): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sb['nombre']); ?></td>
                            <td align="right"><span class="badge-danger"><?php echo $sb['stock']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <div class="quick-actions">
        <strong>Acciones rápidas:</strong>
        <a href="ventas.php"><button class="btn-primary"> Registrar Venta</button></a>
        
        <?php if ($rol == 1 || $rol == 3): ?>
            <a href="../controllers/ReporteController.php?tipo=dia"><button class="btn-primary" style="background:#64748b"> Cierre del Día</button></a>
        <?php endif; ?>

        <?php if ($rol == 3): ?>
            <a href="usuarios.php"><button class="btn-primary" style="background:#1e293b"> Gestionar Usuarios</button></a>
        <?php endif; ?>
    </div>
</div>

<script>
    // El script de la gráfica se mantiene igual que antes
    const datosVentas = <?php echo json_encode($datos['ventasSemana']); ?>;
    const etiquetas = datosVentas.map(item => item.dia);
    const montos = datosVentas.map(item => item.total);
    const ctx = document.getElementById('graficaVentas').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Ventas ($)',
                data: montos,
                backgroundColor: 'rgba(30, 58, 138, 0.1)',
                borderColor: '#1e3a8a',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
</body>
</html>