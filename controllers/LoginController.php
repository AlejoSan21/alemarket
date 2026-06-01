<?php
// controllers/LoginController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

// INCLUSIÓN MANUAL DE PHPMAILER
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                $_SESSION['rol_id'] = $user['rol_id']; 
                
                header("Location: http://localhost/alemarket1/views/dashboard.php");
                exit;
            } else {
                header("Location: http://localhost/alemarket1/views/login.php?error=1");
                exit;
            }
        }
    }

    public function salir() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_destroy();
        header("Location: http://localhost/alemarket1/views/login.php");
        exit;
    }

    public function solicitarRecuperacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new Usuario($db);

            // Limpiamos espacios en blanco del input por seguridad
            $correoInput = trim($_POST['correo']);
            $user = $userModel->buscarPorCorreo($correoInput);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $userModel->guardarTokenRecuperacion($user['id'], $token);

                // Enlace seguro apuntando correctamente a la vista usando URL absoluta
                $enlace = "http://localhost/alemarket1/views/restablecer_password.php?token=" . $token;

                $mail = new PHPMailer(true);

                try {
                    // CONFIGURACIÓN SMTP
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'tu_correo_emisor@gmail.com'; // REEMPLAZAR CON TU CORREO DE GMAIL
                    $mail->Password   = 'abcd efgh ijkl mnop';        // REEMPLAZAR CON TU CONTRASEÑA DE APLICACIÓN
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('tu_correo_emisor@gmail.com', 'AleMarket');
                    $mail->addAddress($correoInput, $user['nombre']);

                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Restablecer Contraseña - AleMarket';
                    
                    $mail->Body = "
                        <div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px;'>
                            <h2 style='color: #1e3a8a;'>AleMarket</h2>
                            <p>Hola <strong>{$user['nombre']}</strong>,</p>
                            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta de acceso al sistema.</p>
                            <p>Para continuar con el proceso, haz clic en el siguiente botón:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$enlace}' style='background-color: #1e3a8a; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Restablecer mi contraseña</a>
                            </p>
                            <p style='color: #64748b; font-size: 0.85rem;'>Si tú no realizaste esta solicitud, puedes ignorar este correo de manera segura.</p>
                        </div>
                    ";

                    $mail->send();
                    
                    // Redirección fija absoluta para éxito
                    header("Location: http://localhost/alemarket1/views/recuperar.php?status=enviado");
                    exit;

                } catch (Exception $e) {
                    // Redirección fija absoluta para error en envío SMTP
                    header("Location: http://localhost/alemarket1/views/recuperar.php?status=error_envio");
                    exit;
                }

            } else {
                // Redirección fija absoluta si el correo no existe en la BD
                header("Location: http://localhost/alemarket1/views/recuperar.php?status=no_existe");
                exit;
            }
        }
    }

    public function procesarRestablecer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new Usuario($db);

            $token = $_POST['token'];
            $nueva_password = $_POST['password'];

            $user = $userModel->validarToken($token);

            if ($user) {
                $userModel->actualizarPassword($user['id'], $nueva_password);
                $userModel->limpiarToken($user['id']);

                header("Location: http://localhost/alemarket1/views/login.php?status=password_cambiada");
                exit;
            } else {
                header("Location: http://localhost/alemarket1/views/restablecer_password.php?token=" . $token . "&error=token_invalido");
                exit;
            }
        }
    }
}

// Enrutador del MVC
if (isset($_GET['action'])) {
    $auth = new LoginController();
    if ($_GET['action'] === 'login') $auth->acceder();
    if ($_GET['action'] === 'logout') $auth->salir();
    if ($_GET['action'] === 'solicitarRecuperacion') $auth->solicitarRecuperacion();
    if ($_GET['action'] === 'procesarRestablecer') $auth->procesarRestablecer();
}