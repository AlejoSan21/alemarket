<?php
// models/Usuario.php

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Verifica credenciales y si el usuario está activo
    public function login($usuario, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario = ? AND estado = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Lista usuarios uniendo con la tabla roles
    public function listar() {
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.rol_id = r.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registra un nuevo usuario con contraseña encriptada
    public function crear($nombre, $usuario, $password, $rol_id) {
        $password_segura = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (nombre, usuario, password, rol_id, estado) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $usuario, $password_segura, $rol_id]);
    }

    // Obtiene datos de un usuario específico
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza datos básicos y el rol
    public function actualizar($id, $nombre, $usuario, $rol_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = ?, usuario = ?, rol_id = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $usuario, $rol_id, $id]);
    }

    // Cambia el estado (Activo/Inactivo)
    public function actualizarEstado($id, $estado) {
        $query = "UPDATE " . $this->table_name . " SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$estado, $id]);
    }
    // Eliminar usuario (Físico)
    public function eliminar($id) {
        // Primero verificamos si tiene ventas para evitar errores de integridad
        $check = $this->conn->prepare("SELECT COUNT(*) FROM ventas WHERE usuario_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return "tiene_ventas";
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Resetear contraseña (Lo que usaría el Superadmin si al empleado se le olvida)
    public function actualizarPassword($id, $nueva_password) {
        $password_segura = password_hash($nueva_password, PASSWORD_BCRYPT);
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$password_segura, $id]);
    }
}