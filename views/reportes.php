<?php
// views/reportes.php
session_start();

// 1. Seguridad: Solo usuarios autenticados
if (!isset($_SESSION['usuario_id'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once __DIR__ . '/../controllers/VentaController.php';
$vController = new VentaController();

// 2. Lógica de carga: Solo busca si hay fechas en la URL
$ventas = [];
$total_periodo = 0;
$fecha_inicio = $_GET['inicio'] ?? null;
$fecha_fin = $_GET['fin'] ?? null;

if ($fecha_inicio && $fecha_fin) {
    $ventas = $vController->generarReporte();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AleMarket1 | Reportes de Ventas</title>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a> | 
        <a href="historial.php">Ver Historial</a> |
        <a href="ventas.php">Nueva Venta</a>
    </nav>

    <h1>Reporte de Ventas por Fechas</h1>

    <form method="GET" action="reportes.php">
        <label>Desde:</label>
        <input type="date" name="inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
        
        <label>Hasta:</label>
        <input type="date" name="fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
        
        <button type="submit">Consultar Reporte</button>
    </form>

    <hr>

    <?php if($fecha_inicio && $fecha_fin): ?>
        <h3>Resultados del <?php echo date('d/m/Y', strtotime($fecha_inicio)); ?> al <?php echo date('d/m/Y', strtotime($fecha_fin)); ?></h3>
        
        <table border="1">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Fecha y Hora</th>
                    <th>Vendedor</th>
                    <th>Método de Pago</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($ventas)): ?>
                    <tr><td colspan="5">No se encontraron ventas en este rango de fechas.</td></tr>
                <?php else: ?>
                    <?php foreach($ventas as $v): 
                        $total_periodo += $v['total']; 
                    ?>
                        <tr>
                            <td>#<?php echo $v['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></td>
                            <td><?php echo htmlspecialchars($v['nombre_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($v['metodo_pago']); ?></td>
                            <td>$ <?php echo number_format($v['total'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr style="font-weight: bold; background-color: #f9f9f9;">
                        <td colspan="4" style="text-align: right;">TOTAL RECAUDADO EN EL PERIODO:</td>
                        <td>$ <?php echo number_format($total_periodo, 0, ',', '.'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Selecciona un rango de fechas para generar el reporte de ventas de AleMarket1.</p>
    <?php endif; ?>

</body>
</html>