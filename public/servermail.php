<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Allow: GET, POST, OPTIONS, PUT, DELETE');
header('Content-Type: application/json');
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/modulo.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$modulo = new Modulo();
$db = $modulo->getDb();
$modulo->ensureLinksTable(); // Asegurar que la tabla LINKS exista
function sendEmail($to, $subject, $body) {
  // Configuración de PHPMailer
  $mail = new PHPMailer(true);
  $obj = array('status' => 0, 'message' => 'Error al enviar el correo');
  try {
    $mail->isSMTP();
    //$mail->Host = 'server121.web-hosting.com';
    $mail->Host = 'mail.privateemail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'contacto@edu360global.org';
    $mail->Password = $_ENV['EMAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Caracteres y codificacion
    $mail->CharSet = 'UTF-8';    

    $mail->setFrom('contacto@edu360global.org', 'edu360global.org');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    if($mail->send()){
      $obj['status'] = 1;
      $obj['message'] = 'Correo enviado';
    } else {
      $obj['message'] = $mail->ErrorInfo;
    }
    return $obj;

  } catch (Exception $e) {
    $obj['status'] = 0;
    $obj['message'] = $mail->ErrorInfo;
    return $obj;  
  }
}


// --- Capa de Compatibilidad ---
if (!function_exists('sqlconector')) {
    function sqlconector($query, $params = []) {
        global $modulo;
        return $modulo->getDb()->sqlconector($query, $params);
    }
}

if(isset($_POST['getcodemail'])){
  $codigo = rand(100000, 999999); // Generar un codigo de 6 digitos
  $email = $_POST['email'];
  $type = $_POST['type'] ?? 'login'; // 'login' or 'registro'
  $password = $_POST['password'] ?? '';
  $obj = array('status' => 0, 'message' => 'Error al enviar el correo','codigo'=>'null');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $obj['message'] = "Formato de correo electronico invalido";
  } else {
    $userExists = $modulo->ifUsuarioExist($email);
    
    $proceed = false;
    if ($type === 'registro') {
        if (!$userExists) {
            $proceed = true;
        } else {
            $obj['message'] = 'El correo ya está registrado';
        }
    } else { // login
        if ($userExists) {
            // Validar contraseña antes de enviar el código
            if ($password !== '' && $password == trim($modulo->getPassword($email))) {
                $proceed = true;
            } else {
                $obj['message'] = 'Contraseña incorrecta';
            }
        } else {
            $obj['message'] = 'El correo no está registrado';
        }
    }

    if($proceed){
      sqlconector("INSERT INTO LINKS (LINK,CORREO) VALUES('$codigo','$email')");
      sendEmail($email, "Codigo de Verificación - Edu360", "Su código de verificación es: <br><h2 style='color:#007bff; letter-spacing: 5px;'>$codigo</h2> <br>Generado por Edu360 para su seguridad no conteste este email.");
      $obj['status'] = 1;
      $obj['message'] = 'Codigo enviado';
      $obj['codigo'] = $codigo;
    }
  }
  echo json_encode($obj);
}

if(isset($_POST['verifycode'])){
    $email = $_POST['email'];
    $codigo = $_POST['codigo'];
    $obj = array('status' => 0, 'message' => 'Código inválido');

    // Usar prepared statements con sqlconector
    $stmt = sqlconector("SELECT * FROM LINKS WHERE CORREO = ? AND LINK = ? ORDER BY ID DESC LIMIT 1", [$email, $codigo]);
    $row = $stmt->fetch();

    if($row){
        // Eliminar el código después de usarlo
        sqlconector("DELETE FROM LINKS WHERE ID = ?", [$row['ID']]);
        $obj['status'] = 1;
        $obj['message'] = 'Código verificado';
    }
    echo json_encode($obj);
}

if(isset($_POST['sendmailtecno'])){
  $cliente = $_POST['cliente'];
  $asunto = $_POST['asunto'];
  $mensaje = $_POST['mensaje'];
  
  $asunto = mb_convert_encoding($asunto, 'UTF-8', 'auto');
  $mensaje = mb_convert_encoding($mensaje, 'UTF-8', 'auto');
  
  $email = "president@edu360global.org";
  
  sendEmail($email, "Asistencia Inline Profit", "Tienes una Nueva asistencia para el Cliente:<br>$cliente<br>Asunto Tratado: $asunto<br> <u><b>Problema Presentado:</b></u> $mensaje <hr>Recuerda contestar desde el correo de soporte este mensaje es solo un recordatorio.");  
} 

if(isset($_POST['contact_form'])){
  $nombre = $_POST['nombre'] ?? 'No proporcionado';
  $email_cliente = $_POST['email'] ?? 'No proporcionado';
  $asunto = $_POST['asunto'] ?? 'Sin Asunto';
  $mensaje = $_POST['mensaje'] ?? 'Sin Mensaje';
  
  $asunto_mail = "Nuevo Mensaje de Contacto: " . $asunto;
  $cuerpo_mail = "
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Email:</strong> $email_cliente</p>
    <p><strong>Asunto:</strong> $asunto</p>
    <p><strong>Mensaje:</strong><br>$mensaje</p>
    <hr>
    <p>Este mensaje fue enviado desde el formulario de contacto de EDU360.</p>
  ";
  
  // Enviar a la dirección de contacto configurada
  $destinatario = $_ENV['EMAIL_CONTACTO'];
  $resultado = sendEmail($destinatario, $asunto_mail, $cuerpo_mail);
  
  echo json_encode($resultado);
}

if(isset($_GET['status'])){
  $email = $_GET['email'];
  $message = "Prueba de Email a: " . $email;
  $result = sendEmail($email, "Prueba de Email", $message);
  echo json_encode(array('status' => $result['status'], 'message' => $result['message']));
}


