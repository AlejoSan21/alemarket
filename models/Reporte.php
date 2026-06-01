<?php
// models/Reporte.php

class Reporte {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerResumenDia() {
        $fecha = date('Y-m-d');
        
        // Ventas y Dinero
        $q1 = "SELECT COUNT(id) as total_ventas, SUM(total) as dinero FROM ventas WHERE DATE(fecha) = ?";
        $stmt1 = $this->conn->prepare($q1);
        $stmt1->execute([$fecha]);
        $res = $stmt1->fetch(PDO::FETCH_ASSOC);

        // Stock Crítico (menos de 5 unidades)
        $q2 = "SELECT COUNT(id) as critico FROM productos WHERE stock <= 5";
        $stmt2 = $this->conn->prepare($q2);
        $stmt2->execute();
        $crit = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Producto más vendido hoy
        $q3 = "SELECT p.nombre, SUM(dv.cantidad) as cantidad 
               FROM detalle_ventas dv 
               JOIN productos p ON dv.producto_id = p.id 
               JOIN ventas v ON dv.venta_id = v.id
               WHERE DATE(v.fecha) = ?
               GROUP BY p.id ORDER BY cantidad DESC LIMIT 1";
        $stmt3 = $this->conn->prepare($q3);
        $stmt3->execute([$fecha]);
        $topProd = $stmt3->fetch(PDO::FETCH_ASSOC);

        return [
            'ventas_hoy'     => $res['total_ventas'] ?? 0,
            'dinero_hoy'     => $res['dinero'] ?? 0,
            'stock_critico'  => $crit['critico'] ?? 0,
            'top_producto'   => $topProd['nombre'] ?? 'Ninguno aún',
            'top_cantidad'   => $topProd['cantidad'] ?? 0
        ];
    }

    /**
     * Top 5 productos más vendidos 
     * Útil para ver qué es lo que más rota en AleMarket
     */
    public function topProductos() {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido 
                FROM detalle_ventas dv 
                JOIN productos p ON dv.producto_id = p.id 
                GROUP BY dv.producto_id 
                ORDER BY total_vendido DESC 
                LIMIT 5";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}