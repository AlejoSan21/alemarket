<?php
// index.php (Ubicado en la RAÍZ de ALEMARKET1)
session_start();

require_once 'config/database.php'; 
require_once 'models/Usuario.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? 'inicio';

switch ($action) {
    case 'procesar_login':
        $userModel = new Usuario($db);
        $user = $userModel->login($_POST['usuario'], $_POST['password']);

        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol_id'] = $user['rol_id'];
            header("Location: views/dashboard.php"); // <--- RUTA A LA CARPETA VIEWS
        } else {
            header("Location: views/login.php?error=1"); // <--- RUTA A LA CARPETA VIEWS
        }
        exit;
        break;

    case 'logout':
        session_destroy();
        header("Location: views/login.php");
        exit;
        break;

    default:
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: views/login.php");
        } else {
            header("Location: views/dashboard.php");
        }
        break;
}