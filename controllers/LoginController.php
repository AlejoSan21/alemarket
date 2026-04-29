<?php
// controllers/LoginController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {
    
    public function acceder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new Usuario($db);

            $user = $userModel->login($_POST['usuario'], $_POST['password']);

            if ($user) {
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                
                // Sincronizado con tu columna de la DB: rol_id
                $_SESSION['rol_id'] = $user['rol_id']; 
                
                header("Location: ../views/dashboard.php");
                exit;
            } else {
                header("Location: ../views/login.php?error=1");
                exit;
            }
        }
    }

    public function salir() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_destroy();
        header("Location: ../views/login.php");
        exit;
    }
}

// Lógica para capturar la petición
if (isset($_GET['action'])) {
    $auth = new LoginController();
    if ($_GET['action'] === 'login') $auth->acceder();
    if ($_GET['action'] === 'logout') $auth->salir();
}