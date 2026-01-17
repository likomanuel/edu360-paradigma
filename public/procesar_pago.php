<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/modulo.php';
require_once __DIR__ . '/../src/helpers.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Inicializar el módulo que contiene la conexión a la base de datos
$modulo = new Modulo();
$db = $modulo->getDb();

// Verificar que la tabla nodos_activos existe, si no existe crearla
$modulo->ensureNodosActivosTable();

// 1. Configuración de API (Usa tu Secret Key de Stripe)
// TODO: Mover esta clave a variables de entorno (.env) por seguridad
$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

// 2. Obtener el Session ID desde la URL
$session_id = $_GET['session_id'] ?? null;

// LOG DE DEPURACIÓN TEMPORAL
error_log("\n[DEBUG_PAGO] URL Recibida: " . $_SERVER['REQUEST_URI'], 3, Modulo::LOG_PATH);
error_log("\n[DEBUG_PAGO] GET params: " . json_encode($_GET), 3, Modulo::LOG_PATH);
error_log("\n[PROCESAR_PAGO] Session ID extraído: " . ($session_id ?? 'VACÍO'), 3, Modulo::LOG_PATH);

if (!$session_id) {
    die("Acceso no autorizado: No se encontró ID de sesión.");
}

try {
    // 3. Verificar el estado real del pago en los servidores de Stripe
    $session = $stripe->checkout->sessions->retrieve($session_id);

    if ($session->payment_status === 'paid') {
        
        // El pago es REAL y EXITOSO.
        $email_usuario = $session->customer_details->email;
        $monto = $session->amount_total / 100; // Stripe maneja centavos
        $moneda = strtoupper($session->currency);

        // Log de información del pago recibido
        error_log(
            "\n[PROCESAR_PAGO] Pago recibido de Stripe - Email: $email_usuario | Session: $session_id | Monto: $monto $moneda",
            3,
            Modulo::LOG_PATH
        );

        // 4. Lógica de Base de Datos usando los métodos del proyecto
        // Primero: Verificamos si este pago ya fue procesado antes para evitar duplicados
        $check = $db->row_sqlconector(
            "SELECT id FROM nodos_activos WHERE stripe_session_id = ?", 
            [$session_id]
        );
        
        if (!$check) {
            // Buscamos al evolucionador por su email para obtener su ID
            $user = $db->row_sqlconector(
                "SELECT id_evolucionador FROM evolucionadores WHERE email_verificado = ?", 
                [$email_usuario]
            );

            if ($user) {
                // Insertamos en nodos_activos
                $db->sqlconector(
                    "INSERT INTO nodos_activos (id_evolucionador, stripe_session_id, monto, estatus) 
                     VALUES (?, ?, ?, 'Activado')",
                    [$user['id_evolucionador'], $session_id, $monto]
                );

                // IMPORTANTE: Actualizar el estatus_soberania del evolucionador a 'Activo'
                $db->sqlconector(
                    "UPDATE evolucionadores SET estatus_soberania = 'Activo' WHERE id_evolucionador = ?",
                    [$user['id_evolucionador']]
                );
                
                error_log("\nPago procesado exitosamente - Usuario: $email_usuario | Session: $session_id | Monto: $monto $moneda", 3, Modulo::LOG_PATH);
                $mensaje_exito = "Nodo de Soberanía activado correctamente para: " . htmlspecialchars($email_usuario);
            } else {
                // El pago se hizo pero el usuario no existe en la tabla evolucionadores
                error_log("\nPago recibido pero usuario no encontrado - Email: $email_usuario | Session: $session_id", 3, Modulo::LOG_PATH);
                $mensaje_exito = "Pago recibido, pero el evolucionador no está registrado. Contacte a soporte.";
            }
        } else {
            error_log("\nIntento de procesar pago duplicado - Session: $session_id", 3, Modulo::LOG_PATH);
            $mensaje_exito = "Esta transacción ya fue procesada anteriormente.";
        }

    } else {
        error_log("\nPago no completado - Session: $session_id | Estado: " . $session->payment_status, 3, Modulo::LOG_PATH);
        header("Location: /pago_fallido.php");
        exit;
    }

} catch (Exception $e) {
    // Log de error técnico
    error_log("\nError en Stripe: " . $e->getMessage() . " | Session: " . ($session_id ?? 'N/A'), 3, Modulo::LOG_PATH);
    die("Error procesando el pago. Por favor contacte a soporte técnico.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Nodo - EDU360</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #0a0a0a; color: white; font-family: 'Montserrat', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; text-align: center; }
        .success-card { background: #161616; padding: 50px; border-radius: 20px; border: 1px solid #00a8e8; box-shadow: 0 0 30px rgba(0, 168, 232, 0.2); }
        .icon { font-size: 4rem; color: #00ff88; margin-bottom: 20px; }
        .btn-dash { display: inline-block; margin-top: 30px; padding: 15px 30px; background: #00a8e8; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon">✓</div>
        <h1>CONEXIÓN ESTABLECIDA</h1>
        <p><?php echo $mensaje_exito; ?></p>
        <p style="color: #666; font-size: 0.8rem;">ID de Transacción: <?php echo htmlspecialchars($session_id); ?></p>
        <a href="<?php echo base_url('mipanel'); ?>" class="btn-dash">ACCEDER AL PANEL DE SOBERANÍA</a>
    </div>
</body>
</html>