<?php
// Controllers/DashboardController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/DashboardModel.php';

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        // Usamos el modelo único para el dashboard
        $this->dashboardModel = new DashboardModel($db);
    }

    public function obtenerDatosParaVista() {
        return [
            'resumen'        => $this->dashboardModel->getResumenVentas(),
            'totalProds'     => $this->dashboardModel->getTotalProductos(),
            'stockBajo'      => $this->dashboardModel->getStockBajo(5),
            'topVendidos'    => $this->dashboardModel->getTopProductos(5),
            'ultimasVentas'  => $this->dashboardModel->obtenerUltimasVentas(5),
            'ventasMetodo'   => $this->dashboardModel->obtenerVentasPorMetodo(),
            'ventasSemana'   => $this->dashboardModel->getVentasSemana() // <-- NUEVO
        ];
    }
}