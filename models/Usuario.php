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

    // Registra un nuevo usuario incluyendo su CORREO ELECTRÓNICO
    public function crear($nombre, $usuario, $correo, $password, $rol_id) {
        $password_segura = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO " . $this->table_name . " (nombre, usuario, correo, password, rol_id, estado) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$nombre, $usuario, $correo, $password_segura, $rol_id]);
    }

    // Obtiene datos de un usuario específico
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza datos básicos, ROL Y CORREO ELECTRÓNICO
    public function actualizar($id, $nombre, $usuario, $correo, $rol_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = ?, usuario = ?, correo = ?, rol_id = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$nombre, $usuario, $correo, $rol_id, $id]);
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

    // Buscar un usuario por su nombre de usuario (Login)
    public function obtenerPorUsuario($usuario) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Guarda el token generado y le da 1 hora de vida
    public function guardarTokenRecuperacion($id, $token) {
        $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        $query = "UPDATE " . $this->table_name . " 
                  SET token_recuperacion = ?, token_expiracion = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$token, $expiracion, $id]);
    }

    // Verifica si un token es válido y no ha expirado
    public function validarToken($token) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE token_recuperacion = ? AND token_expiracion > NOW() 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Limpia los tokens una vez se cambie la contraseña con éxito
    public function limpiarToken($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET token_recuperacion = NULL, token_expiracion = NULL 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Sincronizado: Buscar un usuario por su correo electrónico (Estructura Posicional)
    // En models/Usuario.php

public function buscarPorCorreo($correo) {
    // 1. Limpiamos espacios en blanco al inicio y al final por seguridad
    $correoLimpio = trim($correo);
    
    // Agregamos explícitamente todos los campos que el LoginController va a usar
    $query = "SELECT id, nombre, usuario, correo, estado FROM " . $this->table_name . " WHERE correo = ? LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$correoLimpio]);
    
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return null;
}
}