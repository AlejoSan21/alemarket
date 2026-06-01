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
            <a href="ventas.php">Nueva Venta</a>
            <a href="historial.php">Historial</a>
            <a href="productos.php">Inventario</a>
            <a href="../index.php?action=logout" style="color:red">Salir</a>
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
            <h3>Tendencia de Ventas</h3>
            <div class="chart-wrapper">
                <canvas id="graficaVentas"></canvas>
            </div>
        </section>

        <div style="display: grid; gap: 10px;">
            <section class="panel-block">
                <h3>Últimas Ventas</h3>
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
                <h3>Alerta Inventario</h3>
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
        <strong>Acciones disponibles:</strong>
        <a href="ventas.php"><button class="btn-primary">Registrar Venta</button></a>
        
        <?php if ($rol == 1 || $rol == 3): ?>
            <a href="../controllers/ReporteController.php?tipo=dia"><button class="btn-primary" style="background:#64748b">Reporte Diario PDF</button></a>
        <?php endif; ?>

        <?php if ($rol == 3): ?>
            <a href="usuarios.php"><button class="btn-primary" style="background:#1e293b">Gestionar Usuarios</button></a>
        <?php endif; ?>
    </div>
</div>

<div id="chatbot-container">
    <div id="chat-circle">🤖</div>
    <div id="chat-box">
        <div id="chat-header">
            <span>Asistente AleMarket</span>
            <span id="close-chat" style="cursor:pointer; font-size: 20px;">&times;</span>
        </div>
        <div id="chat-messages">
            <div class="msg msg-bot">¡Hola <?php echo htmlspecialchars($_SESSION['nombre']); ?>! Soy tu asistente. ¿En qué puedo ayudarte hoy?</div>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Pregunta por stock o ventas">
            <button id="send-btn">Enviar</button>
        </div>
    </div>
</div>

<script>
    // Lógica de la Gráfica
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
                backgroundColor: 'rgba(30, 58, 138, 0.15)',
                borderColor: '#1e3a8a',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#1e3a8a',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    grace: '10%'
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const chatCircle = document.getElementById('chat-circle');
    const chatBox = document.getElementById('chat-box');
    const closeChat = document.getElementById('close-chat');
    const sendBtn = document.getElementById('send-btn');

    chatCircle.onclick = () => { chatBox.style.display = 'flex'; };
    closeChat.onclick = () => { chatBox.style.display = 'none'; };

    async function enviarMensaje() {
        const msg = chatInput.value.trim();
        if (!msg) return;

        chatMessages.innerHTML += `<div class="msg msg-user">${msg}</div>`;
        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const formData = new FormData();
            formData.append('mensaje', msg);

            const response = await fetch('../index.php?action=consulta_chatbot', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            chatMessages.innerHTML += `<div class="msg msg-bot">${data.respuesta}</div>`;
        } catch (error) {
            chatMessages.innerHTML += `<div class="msg msg-bot">Error de conexión.</div>`;
        }
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    sendBtn.onclick = enviarMensaje;
    chatInput.onkeypress = (e) => { if(e.key === 'Enter') enviarMensaje(); };
</script>
</body>
</html>