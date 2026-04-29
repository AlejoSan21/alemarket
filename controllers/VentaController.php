<?php
// controllers/VentaController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Venta.php';

class VentaController {
    private $db;
    private $ventaModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ventaModel = new Venta($this->db);
    }

    public function confirmarVentaFinal() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (empty($_SESSION['carrito'])) {
            header("Location: ../views/ventas.php?error=carrito_vacio");
            exit;
        }

        $resultado = $this->ventaModel->registrar($_SESSION['usuario_id'], $_POST['metodo_pago_id'], $_SESSION['carrito']);

        if (is_numeric($resultado)) {
            unset($_SESSION['carrito']);
            header("Location: ../views/factura.php?id=" . $resultado . "&ok=venta_realizada");
        } else {
            header("Location: ../views/ventas.php?error=" . urlencode($resultado));
        }
        exit;
    }

    // --- FUNCIÓN ÚNICA DE MOSTRAR HISTORIAL ---
    public function mostrarHistorial() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Capturamos fechas si existen
        $inicio = $_GET['desde'] ?? null;
        $fin = $_GET['hasta'] ?? null;
        
        return $this->ventaModel->listarHistorial($_SESSION['rol_id'], $_SESSION['usuario_id'], $inicio, $fin);
    }

    public function obtenerVentaPorId($id) {
        return $this->ventaModel->obtenerVentaPorId($id);
    }

    public function obtenerDetalleVenta($id) {
        return $this->ventaModel->obtenerDetalleVenta($id);
    }
    public function anularVenta($id) {
        if ($_SESSION['rol_id'] != 2) { // Solo Admin/Superadmin
            $res = $this->ventaModel->anular($id);
            header("Location: ../views/historial.php?ok=" . ($res ? "anulada" : "error_anular"));
        } else {
            header("Location: ../views/historial.php?error=no_permiso");
        }
        exit;
    }
}

// ROUTING
if (isset($_GET['action'])) {
    $controller = new VentaController();
    if ($_GET['action'] === 'crear_venta_final') {
        $controller->confirmarVentaFinal();
    }
    if (isset($_GET['action']) && $_GET['action'] === 'anular' && isset($_GET['id'])) {
    $controller->anularVenta($_GET['id']);
}
}