<?php
// auth/auth_empleado.php
require_once __DIR__ . '/auth_login.php';

// Verificamos que tenga un rol permitido (según tus IDs de base de datos)
if (!isset($_SESSION['rol_id']) || ($_SESSION['rol_id'] != 2 && $_SESSION['rol_id'] != 3)) {
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}