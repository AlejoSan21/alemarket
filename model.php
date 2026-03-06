<?php

require_once __DIR__ . '/config.php';

class Model {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================= LOGIN =================

    public function login($usuario, $password) {

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND estado = 1");
        $stmt->execute([$usuario]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // ================= EMPLEADOS =================

    public function crearEmpleado($nombre, $usuario, $password) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nombre, usuario, password, rol_id)
            VALUES (?, ?, ?, 2)
        ");

        return $stmt->execute([$nombre, $usuario, $hash]);
    }

    // ================= PRODUCTOS =================

    public function obtenerProductos() {
        return $this->pdo->query("SELECT * FROM productos")->fetchAll();
    }

    public function obtenerProducto($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function agregarProducto($codigo, $nombre, $precio, $stock) {

        if ($stock < 0) return false;

        $stmt = $this->pdo->prepare("
            INSERT INTO productos (codigo_barras,nombre,precio,stock)
            VALUES (?,?,?,?)
        ");

        return $stmt->execute([$codigo,$nombre,$precio,$stock]);
    }

    public function actualizarProducto($id,$codigo,$nombre,$precio,$stock) {

        if ($stock < 0) return false;

        $stmt = $this->pdo->prepare("
            UPDATE productos
            SET codigo_barras=?, nombre=?, precio=?, stock=?
            WHERE id=?
        ");

        return $stmt->execute([$codigo,$nombre,$precio,$stock,$id]);
    }

    public function eliminarProducto($id) {

        try {
            $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return "no_se_puede";
        }
    }

    // ================= VENTAS =================

    public function crearVenta($usuario_id, $metodo_pago_id, $items) {

        if (!$items || count($items) == 0) {
            throw new Exception("No hay productos en la venta");
        }

        $this->pdo->beginTransaction();

        try {

            $total = 0;

            foreach ($items as $item) {

                if ($item['cantidad'] <= 0) {
                    throw new Exception("Cantidad inválida");
                }

                $stmt = $this->pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
                $stmt->execute([$item['producto_id']]);
                $producto = $stmt->fetch();

                if (!$producto) {
                    throw new Exception("Producto no existe");
                }

                if ($producto['stock'] < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para: " . $producto['nombre']);
                }

                $total += $item['subtotal'];
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO ventas (usuario_id, metodo_pago_id, total)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([$usuario_id, $metodo_pago_id, $total]);

            $venta_id = $this->pdo->lastInsertId();

            foreach ($items as $item) {

                $detalle = $this->pdo->prepare(
                    "INSERT INTO detalle_venta
                    (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $detalle->execute([
                    $venta_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio'],
                    $item['subtotal']
                ]);

                $updateStock = $this->pdo->prepare(
                    "UPDATE productos SET stock = stock - ? WHERE id = ?"
                );

                $updateStock->execute([$item['cantidad'], $item['producto_id']]);
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {

            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ================= HISTORIAL =================

  public function historialVentas($usuario_id, $rol_id, $desde = null, $hasta = null) {

    $where = "";
    $params = [];

    if ($rol_id != 1) {
        $where .= " v.usuario_id = ? ";
        $params[] = $usuario_id;
    } else {
        $where .= " 1=1 ";
    }

    if ($desde && $hasta) {
        $where .= " AND DATE(v.fecha) BETWEEN ? AND ? ";
        $params[] = $desde;
        $params[] = $hasta;
    }

    $sql = "SELECT v.*, u.nombre AS empleado, m.nombre AS metodo_pago
            FROM ventas v
            JOIN usuarios u ON v.usuario_id = u.id
            JOIN metodos_pago m ON v.metodo_pago_id = m.id
            WHERE $where
            ORDER BY v.fecha DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function productoMasVendido($usuario_id, $rol_id) {

    if ($rol_id == 1) {

        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido
                FROM detalle_venta dv
                JOIN productos p ON dv.producto_id = p.id
                GROUP BY dv.producto_id
                ORDER BY total_vendido DESC
                LIMIT 1";

        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

    } else {

        $stmt = $this->pdo->prepare("
            SELECT p.nombre, SUM(dv.cantidad) as total_vendido
            FROM detalle_venta dv
            JOIN productos p ON dv.producto_id = p.id
            JOIN ventas v ON dv.venta_id = v.id
            WHERE v.usuario_id = ?
            GROUP BY dv.producto_id
            ORDER BY total_vendido DESC
            LIMIT 1
        ");

        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
public function resumenHistorial($usuario_id, $rol_id, $desde = null, $hasta = null) {

    $where = "1=1";
    $params = [];

    if ($rol_id != 1) {
        $where .= " AND usuario_id = ?";
        $params[] = $usuario_id;
    }

    if ($desde && $hasta) {
        $where .= " AND DATE(fecha) BETWEEN ? AND ?";
        $params[] = $desde;
        $params[] = $hasta;
    }

    $sql = "SELECT COUNT(*) as cantidad, SUM(total) as total
            FROM ventas
            WHERE $where";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function ventasPorMetodoPago($usuario_id, $rol_id, $desde = null, $hasta = null) {

    $where = "1=1";
    $params = [];

    if ($rol_id != 1) {
        $where .= " AND v.usuario_id = ?";
        $params[] = $usuario_id;
    }

    if ($desde && $hasta) {
        $where .= " AND DATE(v.fecha) BETWEEN ? AND ?";
        $params[] = $desde;
        $params[] = $hasta;
    }

    $sql = "SELECT mp.nombre as metodo_pago, SUM(v.total) as total
            FROM ventas v
            JOIN metodos_pago mp ON v.metodo_pago_id = mp.id
            WHERE $where
            GROUP BY v.metodo_pago_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function ventasPorDia($usuario_id, $rol_id, $desde = null, $hasta = null) {

    $where = "1=1";
    $params = [];

    if ($rol_id != 1) {
        $where .= " AND v.usuario_id = ?";
        $params[] = $usuario_id;
    }

    if ($desde && $hasta) {
        $where .= " AND DATE(v.fecha) BETWEEN ? AND ?";
        $params[] = $desde;
        $params[] = $hasta;
    }

    $sql = "SELECT DATE(v.fecha) as fecha, SUM(v.total) as total
            FROM ventas v
            WHERE $where
            GROUP BY DATE(v.fecha)
            ORDER BY DATE(v.fecha) ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function obtenerVentaPorId($id) {

        $stmt = $this->pdo->prepare("SELECT * FROM ventas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleVenta($venta_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.nombre, dv.cantidad, dv.precio_unitario
            FROM detalle_venta dv
            INNER JOIN productos p ON dv.producto_id = p.id
            WHERE dv.venta_id = ?
        ");

        $stmt->execute([$venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ================= REPORTE DEL DÍA =================

public function obtenerVentasDelDia() {

    $stmt = $this->pdo->prepare("
        SELECT v.*, u.nombre AS empleado
        FROM ventas v
        JOIN usuarios u ON v.usuario_id = u.id
        WHERE DATE(v.fecha) = CURDATE()
        ORDER BY v.fecha DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function totalVentasDelDia() {

    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as cantidad, SUM(total) as total
        FROM ventas
        WHERE DATE(fecha) = CURDATE()
    ");

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function VentasPorEmpleado () {
    $sql = "SELECT u.nombre, SUM(v.total) as total 
    FROM ventas v
    JOIN usuarios u ON v.usuario_id = u.id
    GROUP BY v.usuario_id
    ORDER BY total DESC";
    
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
public function ventasHoy() {

    $stmt = $this->pdo->query("
        SELECT COUNT(*) as total
        FROM ventas
        WHERE DATE(fecha) = CURDATE()
    ");

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function dineroHoy() {

    $stmt = $this->pdo->query("
        SELECT SUM(total) as total
        FROM ventas
        WHERE DATE(fecha) = CURDATE()
    ");

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function totalProductos() {

    $stmt = $this->pdo->query("
        SELECT COUNT(*) as total
        FROM productos
    ");

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// ================= STOCK BAJO =================

public function productosStockBajo($limite = 5) {

    $stmt = $this->pdo->prepare("
        SELECT nombre, stock
        FROM productos
        WHERE stock <= ?
        ORDER BY stock ASC
    ");

    $stmt->execute([$limite]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// ================= TOP PRODUCTOS =================

public function topProductos($limite = 5) {

    $stmt = $this->pdo->prepare("
        SELECT p.nombre, SUM(dv.cantidad) as total_vendido
        FROM detalle_venta dv
        JOIN productos p ON dv.producto_id = p.id
        GROUP BY dv.producto_id
        ORDER BY total_vendido DESC
        LIMIT ?
    ");

    $stmt->bindValue(1, $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

$model = new Model($pdo);