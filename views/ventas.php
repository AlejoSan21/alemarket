<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../controllers/ProductoController.php';
$pController = new ProductoController();
$productos = $pController->listarProductos();

if (!isset($_SESSION['carrito'])) { $_SESSION['carrito'] = []; }

$error_msg = null;

// --- LÓGICA DE AGREGADO CON AGRUPACIÓN Y VALIDACIÓN DE STOCK ---
if (isset($_POST['agregar_prod']) || isset($_POST['barcode_scan'])) {
    $id_buscado = $_POST['producto_id'] ?? null;
    $barcode = $_POST['codigo_barras'] ?? null;
    $cant_a_sumar = (int)($_POST['cantidad'] ?? 1);

    foreach($productos as $p) {
        if(($id_buscado && $p['id'] == $id_buscado) || ($barcode && $p['codigo_barras'] == $barcode)) {
            
            $stock_disponible = $p['stock'];
            
            // Verificar cuánto hay ya en el carrito
            $cant_en_carrito = 0;
            $posicion = -1;
            foreach ($_SESSION['carrito'] as $idx => $item) {
                if ($item['id'] == $p['id']) {
                    $cant_en_carrito = $item['cantidad'];
                    $posicion = $idx;
                    break;
                }
            }

            // Validar stock total (carrito + lo nuevo)
            if (($cant_en_carrito + $cant_a_sumar) > $stock_disponible) {
                $error_msg = "Stock insuficiente. Solo quedan " . $stock_disponible . " unidades de " . $p['nombre'];
            } else {
                if ($posicion !== -1) {
                    $_SESSION['carrito'][$posicion]['cantidad'] += $cant_a_sumar;
                    $_SESSION['carrito'][$posicion]['subtotal'] = $_SESSION['carrito'][$posicion]['cantidad'] * $_SESSION['carrito'][$posicion]['precio'];
                } else {
                    $_SESSION['carrito'][] = [
                        'id' => $p['id'],
                        'nombre' => $p['nombre'],
                        'precio' => $p['precio'],
                        'cantidad' => $cant_a_sumar,
                        'subtotal' => $p['precio'] * $cant_a_sumar
                    ];
                }
            }
            break;
        }
    }
    if (!$error_msg) { header("Location: ventas.php"); exit; }
}

if (isset($_GET['borrar_item'])) {
    unset($_SESSION['carrito'][$_GET['borrar_item']]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']); 
    header("Location: ventas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AleMarket | Terminal de Ventas</title>
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body class="dashboard-body">

<div class="main-panel">
    <header class="panel-header">
        <div>
            <h1> Terminal de Ventas</h1>
            <small>AleMarket1 - Registro de transacciones</small>
        </div>
        <nav>
            <a href="dashboard.php"> Menú Principal</a>
            <a href="historial.php"> Historial</a>
            <a href="../index.php?action=logout" class="btn-logout">Salir</a>
        </nav>
    </header>

    <?php if($error_msg): ?>
        <div class="alert alert-danger">
            <strong> Atención:</strong> <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 350px 1fr;">
        
        <div style="display: grid; gap: 10px; align-content: start;">
            <section class="panel-block">
                <h3> Escáner de Barras</h3>
                <form method="POST">
                    <input type="text" name="codigo_barras" autofocus placeholder="Pistolear aquí..." onfocus="this.value=''" class="input-pro">
                    <input type="hidden" name="cantidad" value="1">
                    <button type="submit" name="barcode_scan" style="display:none;">Escanear</button>
                </form>
            </section>

            <section class="panel-block">
                <h3> Selección Manual</h3>
                <form method="POST" style="display: grid; gap: 10px;">
                    <select name="producto_id" class="input-pro">
                        <option value="">-- Seleccione Producto --</option>
                        <?php foreach($productos as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['nombre']); ?> ($<?php echo number_format($p['precio'], 0); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div style="display: flex; gap: 5px;">
                        <input type="number" name="cantidad" value="1" min="1" class="input-pro" style="width: 80px;">
                        <button type="submit" name="agregar_prod" class="btn-panel primary" style="flex:1;">Agregar</button>
                    </div>
                </form>
            </section>

            <?php if(!empty($_SESSION['carrito'])): ?>
            <section class="panel-block" style="border-top: 4px solid var(--success-color);">
                <h3> Confirmar Pago</h3>
                <form method="POST" action="../controllers/VentaController.php?action=crear_venta_final">
                    <label>Método de Pago:</label>
                    <select name="metodo_pago_id" class="input-pro" required>
                        <option value="1"> Efectivo</option>
                        <option value="2"> Nequi / Transferencia</option>
                    </select>
                    <button type="submit" class="btn-panel primary" style="width:100%; margin-top:15px; padding: 15px; font-size: 1.1rem;" onclick="return confirm('¿Finalizar venta?')">
                        REGISTRAR VENTA
                    </button>
                </form>
            </section>
            <?php endif; ?>
        </div>

        <section class="panel-block">
            <h3> Detalle de la Venta Actual</h3>
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align: center;">Cant.</th>
                        <th style="text-align: right;">Subtotal</th>
                        <th style="text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; if(empty($_SESSION['carrito'])): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--text-muted);">No hay productos en la venta</td></tr>
                    <?php else: ?>
                        <?php foreach($_SESSION['carrito'] as $index => $item): $total += $item['subtotal']; ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td>
                            <td align="center">
                                <span class="badge" style="background:#e0f2fe; color:#0369a1;"><?php echo $item['cantidad']; ?></span>
                            </td>
                            <td align="right">$<?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                            <td align="center">
                                <a href="ventas.php?borrar_item=<?php echo $index; ?>" class="btn-panel danger">✕</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; padding: 20px; background: var(--primary-color); border-radius: 8px; display: flex; justify-content: space-between; align-items: center; color: white;">
                <span style="font-size: 1.2rem; font-weight: bold; opacity: 0.9;">TOTAL A COBRAR:</span>
                <span style="font-size: 2.5rem; font-weight: 800;">$<?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
        </section>
    </div>
</div>
</body>
</html>