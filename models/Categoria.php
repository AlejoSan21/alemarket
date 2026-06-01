<?php
/**
 * Clase Categoria: Gestiona los datos de la tabla 'categorias'.
 */
class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear una nueva categoría
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $this->nombre);
        
        return $stmt->execute();
    }

    // Método para listar todas las categorías (útil para el inventario)
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>