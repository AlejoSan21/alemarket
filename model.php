<?php

require_once __DIR__ . '/config.php';

class Model {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($usuario, $password) {

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND estado = 1");
        $stmt->execute([$usuario]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

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
//eliminar producto
    public function eliminarProducto($id) {

    try {

        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id]);

    } catch (PDOException $e) {

        // error por FK (producto vendido)
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
// =====================
// HISTORIAL VENTAS
// =====================
public function historialVentas() {

    $sql = "SELECT v.*, u.nombre AS empleado
            FROM ventas v
            JOIN usuarios u ON v.usuario_id = u.id
            ORDER BY v.fecha DESC";

    return $this->pdo->query($sql)->fetchAll();
}
}

$model=new Model($pdo);