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


if(isset($_POST['getcodemail'])){
  $codigo = rand(100000, 999999); // Generar un codigo de 6 digitos
  $email = $_POST['email'];
  $obj = array('status' => 0, 'message' => 'Error al enviar el correo');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $obj['message'] = "Formato de correo electronico invalido";
  } else {
    if($modulo->ifUsuarioExist($email)){
      sqlconector("INSERT INTO LINKS (LINK,CORREO) VALUES('$codigo','$email')");
      sendEmail($email, "Codigo Inline Profit", "Copie este codigo: <br> $codigo <br>Generado por Inlineprofit para su seguridad no conteste este email.");
      $obj['status'] = 1;
      $obj['message'] = 'Codigo enviado';
    } else {
      $obj['message'] = 'El correo no esta registrado';
    }
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

