<?php
// models/Venta.php

class Venta {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($usuario_id, $metodo_pago_id, $items) {
        try {
            $this->conn->beginTransaction();

            $queryVenta = "INSERT INTO ventas (usuario_id, metodo_pago_id, total, fecha) VALUES (?, ?, 0, NOW())";
            $stmtV = $this->conn->prepare($queryVenta);
            $stmtV->execute([$usuario_id, $metodo_pago_id]);
            $venta_id = $this->conn->lastInsertId();

            $total_venta = 0;

            foreach ($items as $item) {
                $queryProd = "SELECT precio, stock FROM productos WHERE id = ? FOR UPDATE";
                $stmtP = $this->conn->prepare($queryProd);
                $stmtP->execute([$item['id']]);
                $producto = $stmtP->fetch(PDO::FETCH_ASSOC);

                if ($producto['stock'] < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para ID: " . $item['id']);
                }

                $subtotal = $producto['precio'] * $item['cantidad'];
                $total_venta += $subtotal;

                $queryDetalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
                $this->conn->prepare($queryDetalle)->execute([$venta_id, $item['id'], $item['cantidad'], $producto['precio']]);

                $queryUpdateStock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $this->conn->prepare($queryUpdateStock)->execute([$item['cantidad'], $item['id']]);
            }

            $this->conn->prepare("UPDATE ventas SET total = ? WHERE id = ?")->execute([$total_venta, $venta_id]);

            $this->conn->commit();
            return $venta_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    // --- FUNCIÓN ÚNICA DE HISTORIAL ---
    public function listarHistorial($rol_id, $usuario_id, $inicio = null, $fin = null) {
        if ($rol_id == 2) { 
            // Empleado: Solo ve lo suyo de HOY
            $query = "SELECT v.*, u.nombre as nombre_usuario, m.nombre as metodo_pago 
                      FROM ventas v 
                      JOIN usuarios u ON v.usuario_id = u.id 
                      JOIN metodos_pago m ON v.metodo_pago_id = m.id
                      WHERE v.usuario_id = ? AND DATE(v.fecha) = CURDATE()
                      ORDER BY v.fecha DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuario_id]);
        } else {
            // Admin o Superadmin: Lógica con Filtro opcional
            $query = "SELECT v.*, u.nombre as nombre_usuario, m.nombre as metodo_pago 
                      FROM ventas v 
                      JOIN usuarios u ON v.usuario_id = u.id 
                      JOIN metodos_pago m ON v.metodo_pago_id = m.id";
            
            if ($inicio && $fin) {
                $query .= " WHERE v.fecha BETWEEN ? AND ? ORDER BY v.fecha DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$inicio . " 00:00:00", $fin . " 23:59:59"]);
            } else {
                $query .= " ORDER BY v.fecha DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentaPorId($id) {
        $sql = "SELECT v.*, u.nombre as empleado, m.nombre as metodo_pago 
                FROM ventas v 
                JOIN usuarios u ON v.usuario_id = u.id 
                JOIN metodos_pago m ON v.metodo_pago_id = m.id 
                WHERE v.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleVenta($id) {
        $sql = "SELECT dv.*, p.nombre 
                FROM detalle_ventas dv 
                JOIN productos p ON dv.producto_id = p.id 
                WHERE dv.venta_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportePorFechas($inicio, $fin) {
        $sql = "SELECT v.*, u.nombre as nombre_usuario, m.nombre as metodo_pago 
                FROM ventas v
                JOIN usuarios u ON v.usuario_id = u.id
                JOIN metodos_pago m ON v.metodo_pago_id = m.id
                WHERE v.fecha BETWEEN ? AND ? 
                ORDER BY v.fecha ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$inicio . " 00:00:00", $fin . " 23:59:59"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function anular($venta_id) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener los productos de esa venta para devolverlos al stock
            $queryDetalle = "SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?";
            $stmt = $this->conn->prepare($queryDetalle);
            $stmt->execute([$venta_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                // 2. Devolver stock al producto
                $queryStock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
                $this->conn->prepare($queryStock)->execute([$item['cantidad'], $item['producto_id']]);
            }

            // 3. Eliminar la venta (o podrías marcarla como anulada si tuvieras la columna 'estado')
            // Por ahora, para no complicar tu DB, la eliminaremos físicamente:
            $this->conn->prepare("DELETE FROM detalle_ventas WHERE venta_id = ?")->execute([$venta_id]);
            $this->conn->prepare("DELETE FROM ventas WHERE id = ?")->execute([$venta_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}