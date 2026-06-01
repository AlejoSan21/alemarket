<?php
// auth/auth_superadmin.php
require_once __DIR__ . '/auth_login.php';

// Solo el Administrador (ID 3 en tu caso) puede pasar
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: dashboard.php?error=solo_admin");
    exit();
}