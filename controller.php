<?php

require_once __DIR__ . '/model.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    // LOGIN
    case 'login':

        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        $user = $model->login($usuario, $password);

        if ($user) {

            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol_id'] = $user['rol_id'];

            header("Location: public/dashboard.php");
            exit;

        } else {

            header("Location: public/login.php?error=1");
            exit;
        }

    break;

    // LOGOUT
    case 'logout':

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }

        header("Location: public/login.php");
        exit;

    break;

    // CREAR EMPLEADO
    case 'crear_empleado':

        if ($_SESSION['rol_id'] != 1) {
            die("Acceso denegado");
        }

        $model->crearEmpleado(
            $_POST['nombre'],
            $_POST['usuario'],
            $_POST['password']
        );

        header("Location: public/usuarios.php?ok=1");
        exit;

    break;

    // AGREGAR PRODUCTO
    case 'agregar_producto':

        $model->agregarProducto(
            $_POST['codigo'],
            $_POST['nombre'],
            $_POST['precio'],
            $_POST['stock']
        );

        header("Location: public/productos.php?ok=1");
        exit;

    break;

    // EDITAR PRODUCTO
    case 'editar_producto':

        $model->actualizarProducto(
            $_POST['id'],
            $_POST['codigo'],
            $_POST['nombre'],
            $_POST['precio'],
            $_POST['stock']
        );

        header("Location: public/productos.php");
        exit;

    break;

    // ELIMINAR PRODUCTO
    case 'eliminar_producto':

    $resultado = $model->eliminarProducto($_GET['id']);

    if ($resultado === "no_se_puede") {

        header("Location: public/productos.php?error=vendido");
        exit;

    }

    header("Location: public/productos.php?ok=eliminado");
    exit;

break;

    // REGISTRAR VENTA
    case 'registrar_venta':

        $usuario_id = $_SESSION['usuario_id'];
        $metodo_pago_id = $_POST['metodo_pago'];

        $items = json_decode($_POST['items'], true);

        $model->crearVenta($usuario_id, $metodo_pago_id, $items);

        header("Location: public/ventas.php?ok=1");
        exit;

    break;

    default:
        echo "Acción no válida";
}