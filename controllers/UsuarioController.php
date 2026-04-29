<?php
// controllers/UsuarioController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $db;
    private $usuarioModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }

    public function listar() {
        return $this->usuarioModel->listar();
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';
            $rol_id = $_POST['rol_id'] ?? ''; 

            // Validación: Solo letras y espacios en el nombre
            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
                header("Location: ../views/usuarios.php?error=nombre_invalido");
                exit;
            }

            // Validación: Contraseña fuerte
            if(!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)){
                header("Location: ../views/usuarios.php?error=password_debil");
                exit;
            }

            $resultado = $this->usuarioModel->crear($nombre, $usuario, $password, $rol_id);

            if ($resultado === true) {
                header("Location: ../views/usuarios.php?ok=creado");
            } else {
                header("Location: ../views/usuarios.php?error=guardar");
            }
            exit;
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $usuario = $_POST['usuario'];
            $rol_id = $_POST['rol_id'];
            $nueva_pass = $_POST['nueva_password'] ?? '';

            // 1. Actualizar datos básicos
            $res = $this->usuarioModel->actualizar($id, $nombre, $usuario, $rol_id);

            // 2. Si el Superadmin escribió algo en el campo de contraseña, la actualizamos también
            if (!empty($nueva_pass)) {
                $this->usuarioModel->actualizarPassword($id, $nueva_pass);
            }

            if ($res) {
                header("Location: ../views/usuarios.php?ok=actualizado");
            } else {
                header("Location: ../views/usuarios.php?error=actualizar");
            }
            exit;
        }
    }

    public function cambiarEstado($id, $nuevo_estado) {
        $res = $this->usuarioModel->actualizarEstado($id, $nuevo_estado);
        
        if($res) {
            header("Location: ../views/usuarios.php?ok=estado_actualizado");
        } else {
            header("Location: ../views/usuarios.php?error=estado");
        }
        exit;
    }
    public function eliminar($id) {
        $res = $this->usuarioModel->eliminar($id);
        if ($res === true) {
            header("Location: ../views/usuarios.php?ok=eliminado");
        } elseif ($res === "tiene_ventas") {
            header("Location: ../views/usuarios.php?error=usuario_con_ventas");
        } else {
            header("Location: ../views/usuarios.php?error=eliminar");
        }
        exit;
    }
    public function resetearClave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nueva_p = $_POST['nueva_password'];
            $this->usuarioModel->actualizarPassword($id, $nueva_p);
            header("Location: ../views/usuarios.php?ok=clave_cambiada");
            exit;
        }
    }
}

// ROUTING: Captura de acciones por GET
if (isset($_GET['action'])) {
    $controller = new UsuarioController();
    $id = $_GET['id'] ?? null;

    if ($_GET['action'] === 'registrar') {
        $controller->registrar();
    } elseif ($_GET['action'] === 'actualizar') {
        $controller->actualizar();
    } elseif ($_GET['action'] === 'activar' && $id) {
        $controller->cambiarEstado($id, 1);
    } elseif ($_GET['action'] === 'desactivar' && $id) {
        $controller->cambiarEstado($id, 0);
    } elseif ($_GET['action'] === 'eliminar' && $id) {
        $controller->eliminar($id);
    } elseif ($_GET['action'] === 'reset_pass') {
        $controller->resetearClave();
    }
}