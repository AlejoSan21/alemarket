<?php
// auth/auth_login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no existe la sesión, lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Views/login.php");
    exit();
}