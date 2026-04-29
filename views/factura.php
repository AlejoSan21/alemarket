<?php
// views/factura.php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../controllers/VentaController.php';

// Validar que recibimos un ID de venta
if (!isset($_GET['id'])) {
    die("Error: No se proporcionó un ID de factura.");
}

$vController = new VentaController();
$id_venta = $_GET['id'];

// Obtener datos de la venta y sus detalles
$infoVenta = $vController->obtenerVentaPorId($id_venta);
$detalles  = $vController->obtenerDetalleVenta($id_venta);

if (!$infoVenta) {
    die("Error: La factura no existe.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AleMarket | Factura #<?php echo $id_venta; ?></title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
    <style>
        /* Estilo específico para simular un ticket en pantalla */
        .ticket-preview {
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Courier New', Courier, monospace; /* Fuente de ticket */
        }
        @media print {
            .no-print { display: none; }
            .ticket-preview { border: none; box-shadow: none; margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body class="dashboard-body">

<div class="main-panel">
    <div class="ticket-preview">
        <div style="text-align:center;">
            <h2 style="margin:0;">AleMarket</h2>
            <p style="font-size:0.8rem;">Nit: 123456789-0<br>La Dorada, Caldas</p>
            <hr>
            <p><strong>FACTURA #<?php echo $id_venta; ?></strong></p>
        </div>

        <p style="font-size:0.9rem;">
            <strong>Fecha:</strong> <?php echo $infoVenta['fecha']; ?><br>
            <strong>Vendedor:</strong> <?php echo $infoVenta['empleado']; ?><br>
            <strong>Pago:</strong> <?php echo $infoVenta['metodo_pago']; ?>
        </p>

        <table class="compact-table" style="font-size:0.9rem;">
            <thead>
                <tr>
                    <th>Prod.</th>
                    <th style="text-align:center;">Cant.</th>
                    <th style="text-align:right;">Subt.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $d): ?>
                <tr>
                    <td><?php echo $d['nombre']; ?></td>
                    <td align="center"><?php echo $d['cantidad']; ?></td>
                    <td align="right">$<?php echo number_format($d['cantidad'] * $d['precio_unitario'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top:20px; text-align:right; font-size:1.2rem; font-weight:bold; border-top:1px solid #eee; padding-top:10px;">
            TOTAL: $<?php echo number_format($infoVenta['total'], 0, ',', '.'); ?>
        </div>

        <p style="text-align:center; font-size:0.8rem; margin-top:20px;">¡Gracias por su compra!</p>
    </div>

    <div class="text-center no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()" class="btn-panel primary">🖨️ Imprimir Ticket</button>
        <a href="../controllers/ReporteController.php?tipo=factura_individual&id=<?php echo $id_venta; ?>" class="btn-panel" style="background:#10b981; color:white; border:none;">📥 Descargar PDF</a>
        <br><br>
        <a href="dashboard.php" style="color:var(--primary-color);">Volver al Dashboard</a>
    </div>
</div>

</body>
</html>
</body>
</html>