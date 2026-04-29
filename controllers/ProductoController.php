<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    private $db;
    private $productoModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productoModel = new Producto($this->db);
    }

    public function listarProductos() {
        return $this->productoModel->listar();
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo_barras'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock  = $_POST['stock'] ?? 0;

            // 1. Buscamos si el código ya existe (aunque esté en estado 0)
            $existente = $this->productoModel->buscarPorCodigo($codigo);

            if ($existente) {
                if ($existente['estado'] == 0) {
                    // 2. Si existe pero estaba "borrado", lo reactivamos con los datos nuevos
                    $this->productoModel->reactivar($existente['id'], $nombre, $precio, $stock);
                    header("Location: ../views/productos.php?ok=reactivado");
                } else {
                    // 3. Si ya existe y está activo, avisamos que está duplicado
                    header("Location: ../views/productos.php?error=codigo_duplicado");
                }
            } else {
                // 4. Si es un código totalmente nuevo, lo creamos
                try {
                    $this->productoModel->crear($codigo, $nombre, $precio, $stock);
                    header("Location: ../views/productos.php?ok=creado");
                } catch (PDOException $e) {
                    header("Location: ../views/productos.php?error=sistema");
                }
            }
            exit;
        }
    }

    // Asegúrate de que estas funciones estén así dentro de tu ProductoController.php

    public function obtenerPorId($id) {
        return $this->productoModel->obtenerPorId($id);
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            // Ahora buscamos directamente codigo_barras que viene del formulario
            $codigo = $_POST['codigo_barras']; 
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $stock = $_POST['stock'];

            $res = $this->productoModel->actualizar($id, $codigo, $nombre, $precio, $stock);
            
            if ($res) {
                header("Location: ../views/productos.php?ok=actualizado");
            } else {
                header("Location: ../views/productos.php?error=actualizar");
            }
            exit;
        }
    }
    public function eliminar($id) {
        $res = $this->productoModel->eliminar($id);
        header("Location: ../views/productos.php?ok=eliminado");
        exit;
    }
}

// Enrutador
if (isset($_GET['action'])) {
    $controller = new ProductoController();
    $action = $_GET['action'];

    if ($action === 'guardar') $controller->guardar();
    if ($action === 'actualizar') $controller->actualizar();
    if ($action === 'eliminar' && isset($_GET['id'])) $controller->eliminar($_GET['id']);
}