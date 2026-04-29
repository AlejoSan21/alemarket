<?php
/**
 * Modelo Rol: Maneja las consultas a la tabla 'roles'.
 */
class Rol {
    private $conn;
    private $table_name = "roles";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtiene todos los roles para llenar un menú desplegable (select)
    public function listar() {
        $query = "SELECT id, nombre FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>