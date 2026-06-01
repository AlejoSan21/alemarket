<?php
class Producto {
    private $conn;
    private $table_name = "productos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE estado = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- NUEVA: Buscar por código para evitar el error de duplicado ---
    public function buscarPorCodigo($codigo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE codigo_barras = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($codigo, $nombre, $precio, $stock) {
        $query = "INSERT INTO " . $this->table_name . " (codigo_barras, nombre, precio, stock, estado) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$codigo, $nombre, $precio, $stock]);
    }

    // --- MEJORADA: Reactiva y actualiza la información al mismo tiempo ---
    public function reactivar($id, $nombre, $precio, $stock) {
        $query = "UPDATE " . $this->table_name . " SET estado = 1, nombre = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $precio, $stock, $id]);
    }

    public function actualizar($id, $codigo, $nombre, $precio, $stock) {
        $query = "UPDATE " . $this->table_name . " 
                  SET codigo_barras = ?, nombre = ?, precio = ?, stock = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$codigo, $nombre, $precio, $stock, $id]);
    }

    public function eliminar($id) {
        $query = "UPDATE " . $this->table_name . " SET estado = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}