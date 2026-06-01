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
            header("Location: views/dashboard.php"); 
        } else {
            header("Location: views/login.php?error=1"); 
        }
        exit;
        break;

    case 'logout':
        session_destroy();
        header("Location: views/login.php");
        exit;
        break;

    case 'consulta_chatbot':
        require_once 'controllers/ChatbotController.php';
        $chatbot = new ChatbotController($db);
        $chatbot->responder();
        exit;
        break;

    // ==========================================================================
    // CASOS: RECUPERACIÓN DE CONTRASEÑA (CORREGIDO Y SIN DUPLICADOS)
    // ==========================================================================

    case 'solicitarRecuperacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new Usuario($db);
            $correoInput = trim($_POST['correo']);
            $user = $userModel->buscarPorCorreo($correoInput);

            if ($user) {
                // 1. Generar token único seguro y guardar en BD
                $token = bin2hex(random_bytes(32));
                $userModel->guardarTokenRecuperacion($user['id'], $token);

                // 2. Construcción del enlace seguro
                $enlace = "http://localhost/alemarket1/views/restablecer_password.php?token=" . $token;

                // ==========================================================================
                // INTEGRACIÓN DE PHPMAILER (ENVÍO REAL DE GMAIL CON SOPORTE.ALEMARKET)
                // ==========================================================================
                require_once __DIR__ . '/libs/PHPMailer/PHPMailer-master/src/Exception.php';
                require_once __DIR__ . '/libs/PHPMailer/PHPMailer-master/src/PHPMailer.php';
                require_once __DIR__ . '/libs/PHPMailer/PHPMailer-master/src/SMTP.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    // CONFIGURACIÓN DEL SERVIDOR SMTP DE GMAIL
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; 
                    $mail->SMTPAuth   = true;
                    
                    // CREDENCIALES CORPORATIVAS DE ALEMARKET
                    $mail->Username   = 'soporte.alemarket@gmail.com'; 
                    $mail->Password   = 'npvk gray ejlj mwis'; // ✅ Tu clave de la captura sin saltos
                    
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Opciones para evitar bloqueos de certificados SSL locales en XAMPP
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    // REMITENTE Y DESTINATARIO
                    $mail->setFrom('soporte.alemarket@gmail.com', 'AleMarket');
                    $mail->addAddress($correoInput, $user['nombre']);

                    // CONTENIDO ESTILIZADO DEL CORREO
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Restablecer Contraseña - AleMarket';
                    
                    $mail->Body = "
                        <div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px;'>
                            <h2 style='color: #1e3a8a; margin-bottom: 20px;'>AleMarket</h2>
                            <p>Hola <strong>{$user['nombre']}</strong>,</p>
                            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta de acceso al sistema de inventarios.</p>
                            <p>Para continuar con el proceso y asignar una nueva clave, haz clic en el siguiente botón:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$enlace}' style='background-color: #1e3a8a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Restablecer mi contraseña</a>
                            </p>
                            <p style='color: #64748b; font-size: 0.85rem; margin-top: 25px;'>Si tú no realizaste esta solicitud, puedes ignorar este correo de manera segura.</p>
                        </div>
                    ";

                    $mail->send();
                    header("Location: views/recuperar.php?status=enviado");

                } catch (Exception $e) {
                    // Redirige con estado de error de envío si algo falla en el servidor
                    header("Location: views/recuperar.php?status=error_envio");
                }
                // ==========================================================================

            } else {
                header("Location: views/recuperar.php?status=no_existe");
            }
            exit;
        }
        break;

    case 'procesarRestablecer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new Usuario($db);
            $token = $_POST['token'];
            $nueva_password = $_POST['password'];

            // Validar que el token sea real y no haya expirado
            $user = $userModel->validarToken($token);

            if ($user) {
                // Actualizar contraseña con encriptación interna del modelo
                $userModel->actualizarPassword($user['id'], $nueva_password);
                
                // Limpiar los tokens de la DB
                $userModel->limpiarToken($user['id']);

                header("Location: views/login.php?status=password_cambiada");
            } else {
                header("Location: views/restablecer_password.php?token=" . $token . "&error=token_invalido");
            }
            exit;
        }
        break;

    default:
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: views/login.php");
        } else {
            header("Location: views/dashboard.php");
        }
        break;
}