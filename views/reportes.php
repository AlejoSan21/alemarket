<?php
session_start();

if (!isset($_SESSION['usuario_id'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once __DIR__ . '/../controllers/VentaController.php';
$vController = new VentaController();

// 2. Lógica de carga: Usamos los nombres que el controlador ya reconoce
$ventas = [];
$total_periodo = 0;
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if ($desde && $hasta) {
    // El método mostrarHistorial() ya captura el $_GET['desde'] y $_GET['hasta'] internamente
    $ventas = $vController->mostrarHistorial();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Reportes</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header class="panel-header">
        <div>
            <h1> Reportes de Ventas</h1>
            <small>Análisis de ingresos por periodo</small>
        </div>
        <nav>
            <a href="dashboard.php"> Dashboard</a>
            <a href="historial.php"> Historial</a>
            <a href="ventas.php"> Nueva Venta</a>
        </nav>
    </header>

    <div class="content-grid" style="grid-template-columns: 300px 1fr;">
        
        <aside class="panel-block">
            <h3> Parámetros</h3>
            <form method="GET" action="reportes.php" style="display: grid; gap: 10px;">
                <div>
                    <label>Fecha Inicial:</label>
                    <input type="date" name="desde" value="<?php echo htmlspecialchars($desde); ?>" class="input-pro" required>
                </div>
                <div>
                    <label>Fecha Final:</label>
                    <input type="date" name="hasta" value="<?php echo htmlspecialchars($hasta); ?>" class="input-pro" required>
                </div>
                <button type="submit" class="btn-panel primary" style="width: 100%; padding: 12px;">Generar Reporte</button>
                <a href="reportes.php" style="text-align: center; font-size: 0.8rem; color: var(--secondary-color);">Limpiar</a>
            </form>
        </aside>

        <section class="panel-block">
            <?php if($desde && $hasta): ?>
                <h3>Resultados del <?php echo date('d/m/Y', strtotime($desde)); ?> al <?php echo date('d/m/Y', strtotime($hasta)); ?></h3>
                
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Vendedor</th>
                            <th>Método</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($ventas)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 40px;">No hubo ventas en este rango.</td></tr>
                        <?php else: ?>
                            <?php foreach($ventas as $v): $total_periodo += $v['total']; ?>
                                <tr>
                                    <td>#<?php echo $v['id']; ?></td>
                                    <td><small><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></small></td>
                                    <td><?php echo htmlspecialchars($v['nombre_usuario']); ?></td>
                                    <td><span class="badge" style="background: #f1f5f9;"><?php echo $v['metodo_pago']; ?></span></td>
                                    <td align="right"><strong>$<?php echo number_format($v['total'], 0, ',', '.'); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px; padding: 20px; background: #10b981; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; color: white;">
                    <span style="font-size: 1.1rem; font-weight: bold;">TOTAL RECAUDADO:</span>
                    <span style="font-size: 2rem; font-weight: 800;">$<?php echo number_format($total_periodo, 0, ',', '.'); ?></span>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 60px; color: var(--secondary-color);">
                    <p style="font-size: 3rem; margin-bottom: 10px;">📅</p>
                    <p>Seleccione un rango de fechas a la izquierda para analizar las ventas.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

</body>
</html>