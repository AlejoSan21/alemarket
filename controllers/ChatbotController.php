<?php
require_once 'models/Producto.php';
require_once 'models/DashboardModel.php';

class ChatbotController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function responder() {
        // Recibimos la pregunta del usuario
        $mensaje = strtolower($_POST['mensaje'] ?? '');
        $respuesta = "Lo siento, no entiendo tu pregunta. Prueba consultando por 'productos', 'stock' o 'ventas hoy'.";

        if (strpos($mensaje, 'hola') !== false) {
            $respuesta = "¡Hola! Soy el asistente de AleMarket. Puedes preguntarme por stock o por las ventas de hoy.";
        }
        // Consultas de stock/productos
        elseif (strpos($mensaje, 'producto') !== false || strpos($mensaje, 'lista') !== false || strpos($mensaje, 'stock') !== false || strpos($mensaje, 'bajo') !== false) {
            $productoModel = new Producto($this->db);
            
            
            $productos = $productoModel->listar(); 
            
            if (empty($productos)) {
                $respuesta = "Actualmente no hay productos activos en el sistema.";
            } else {
                $respuesta = "Actualmente tenemos estos productos en inventario:";
                foreach ($productos as $p) {
                    $respuesta .= "<br>• " . htmlspecialchars($p['nombre']) . " - Stock: " . $p['stock'] . " ($" . number_format($p['precio'], 0, ',', '.') . ")";
                }
            }
        }
        // Consultas sobre ventas de hoy
        elseif (strpos($mensaje, 'venta') !== false || strpos($mensaje, 'ventas') !== false || strpos($mensaje, 'hoy') !== false || strpos($mensaje, 'dinero') !== false || strpos($mensaje, 'recaud') !== false || strpos($mensaje, 'cobrar') !== false) {
            $dashboardModel = new DashboardModel($this->db);
            $resumenVentas = $dashboardModel->getResumenVentas();
            $totalVentas = $resumenVentas['total_ventas'] ?? 0;
            $dineroTotal = $resumenVentas['dinero_total'] ?? 0;

            if ($totalVentas == 0) {
                $respuesta = "Hoy no se ha registrado ninguna venta todavía.";
            } else {
                $respuesta = "Hoy se han registrado " . $totalVentas . " venta" . ($totalVentas == 1 ? '' : 's') . ". Total facturado: $" . number_format($dineroTotal, 0, ',', '.') . ".";
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['respuesta' => $respuesta]);
    }
}