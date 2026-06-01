<?php
// Models/DashboardModel.php

class DashboardModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Resumen de ventas del día actual
    public function getResumenVentas() {
        $sql = "SELECT 
                    COUNT(*) as total_ventas, 
                    IFNULL(SUM(total), 0) as dinero_total 
                FROM ventas 
                WHERE DATE(fecha) = CURDATE()";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Conteo total de productos en inventario
    public function getTotalProductos() {
        return $this->db->query("SELECT COUNT(*) as total FROM productos")->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Productos con stock bajo (Configurable)
    public function getStockBajo($limite = 5) {
        $stmt = $this->db->prepare("SELECT nombre, stock FROM productos WHERE stock <= ? ORDER BY stock ASC");
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Ranking de productos más vendidos históricamente
    public function getTopProductos($limite = 5) {
        $stmt = $this->db->prepare("
            SELECT p.nombre, SUM(dv.cantidad) as total_vendido
            FROM detalle_ventas dv
            JOIN productos p ON dv.producto_id = p.id
            GROUP BY dv.producto_id
            ORDER BY total_vendido DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Las últimas ventas realizadas (Para el panel de actividad reciente)
    public function obtenerUltimasVentas($limite = 5) {
        $sql = "SELECT v.*, u.nombre as vendedor 
                FROM ventas v 
                JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.fecha DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Reporte de dinero recaudado por método (Efectivo, Nequi, etc.)
    public function obtenerVentasPorMetodo() {
        $fecha = date('Y-m-d');
        $sql = "SELECT m.nombre, IFNULL(SUM(v.total), 0) as total 
                FROM metodos_pago m
                LEFT JOIN ventas v ON v.metodo_pago_id = m.id AND DATE(v.fecha) = ?
                GROUP BY m.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 7. Datos para la gráfica semanal
    public function getVentasSemana() {
        $sql = "SELECT 
                    DATE(fecha) as fecha,
                    DATE_FORMAT(fecha, '%d/%m') as dia, 
                    SUM(total) as total 
                FROM ventas 
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(fecha) 
                ORDER BY DATE(fecha) ASC";

        $stmt = $this->db->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ventas = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));
            $dia = date('d/m', strtotime($fecha));
            $ventas[$dia] = 0;
        }

        foreach ($resultados as $fila) {
            $ventas[$fila['dia']] = (int)$fila['total'];
        }

        $salida = [];
        foreach ($ventas as $dia => $total) {
            $salida[] = ['dia' => $dia, 'total' => $total];
        }

        return $salida;
    }
}