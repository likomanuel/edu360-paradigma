<?php
/**
 * Webhook de Stripe para EDU360 Paradigma
 * 
 * Este archivo maneja los eventos enviados por Stripe cuando ocurren pagos.
 * Es más confiable que procesar_pago.php porque no depende de la redirección del usuario.
 * 
 * IMPORTANTE: Configura esta URL en tu Dashboard de Stripe:
 * Developers > Webhooks > Add endpoint
 * URL: https://tudominio.com/webhook.php
 * Eventos a escuchar: checkout.session.completed
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/modulo.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Inicializar el módulo que contiene la conexión a la base de datos
$modulo = new Modulo();
$db = $modulo->getDb();

// Verificar que la tabla nodos_activos existe
$modulo->ensureNodosActivosTable();

// 1. Configuración de Stripe
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

// El "Webhook Secret" lo obtienes en: Stripe Dashboard > Developers > Webhooks
// Agrega esta variable a tu archivo .env
$endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

// 2. Obtener el evento de Stripe
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    // Verificamos que la petición venga REALMENTE de Stripe (seguridad crítica)
    $event = \Stripe\Webhook::constructEvent(
        $payload, 
        $sig_header, 
        $endpoint_secret
    );
    
    error_log("\n[WEBHOOK] Evento recibido de Stripe: " . $event->type, 3, Modulo::LOG_PATH);
    
} catch(\UnexpectedValueException $e) {
    // Payload inválido
    error_log("\n[WEBHOOK ERROR] Payload inválido: " . $e->getMessage(), 3, Modulo::LOG_PATH);
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Firma inválida (alguien intenta engañar al script)
    error_log("\n[WEBHOOK ERROR] Firma inválida - Posible intento de fraude: " . $e->getMessage(), 3, Modulo::LOG_PATH);
    http_response_code(400);
    exit();
}

// 3. Manejar diferentes tipos de eventos
switch ($event->type) {
    case 'checkout.session.completed':
        // Pago completado exitosamente
        $session = $event->data->object;
        
        $email_usuario = $session->customer_details->email ?? null;
        $session_id = $session->id;
        $monto = $session->amount_total / 100;
        $moneda = strtoupper($session->currency ?? 'USD');
        $client_reference_id = $session->client_reference_id ?? null;
        
        if ($email_usuario) {
            activarNodoSoberano($db, $email_usuario, $session_id, $monto, $moneda);
            
            if ($client_reference_id) {
                // Si viene de una tarjeta de regalo, marcarla como reclamada
                $db->sqlconector(
                    "UPDATE tarjetas_regalo SET estatus = 'Reclamada' WHERE codigo = ?",
                    [$client_reference_id]
                );
                error_log("\n[WEBHOOK GIFT] Tarjeta $client_reference_id marcada como Reclamada", 3, Modulo::LOG_PATH);
            }
        } else {
            error_log("\n[WEBHOOK ERROR] No se encontró email en la sesión: $session_id", 3, Modulo::LOG_PATH);
        }
        break;
        
    case 'charge.refunded':
        // Manejo de reembolsos (opcional, para futuro)
        $charge = $event->data->object;
        error_log("\n[WEBHOOK] Reembolso detectado - Charge ID: " . $charge->id, 3, Modulo::LOG_PATH);
        // Aquí podrías desactivar el nodo si hay un reembolso
        break;
        
    default:
        // Evento no manejado
        error_log("\n[WEBHOOK] Evento no manejado: " . $event->type, 3, Modulo::LOG_PATH);
}

// 4. Respondemos a Stripe con un 200 OK para que sepa que recibimos el mensaje
http_response_code(200);

/**
 * Función de Activación del Nodo de Soberanía
 * 
 * @param Database $db Instancia de la base de datos
 * @param string $email Email del evolucionador
 * @param string $session_id ID de sesión de Stripe
 * @param float $monto Monto del pago
 * @param string $moneda Moneda del pago
 */
function activarNodoSoberano($db, $email, $session_id, $monto, $moneda = 'USD') {
    try {
        // 1. Verificar si ya existe para evitar duplicados
        $check = $db->row_sqlconector(
            "SELECT id FROM nodos_activos WHERE stripe_session_id = ?",
            [$session_id]
        );
        
        if (!$check) {
            // 2. Buscar ID del Evolucionador
            $user = $db->row_sqlconector(
                "SELECT id_evolucionador FROM evolucionadores WHERE email_verificado = ?",
                [$email]
            );

            if ($user) {
                // 3. Registrar el nodo activo
                $tipo_nodo_defaul = "Omega";
                $sum_tipo_nodo = $db->row_sqlconector("SELECT COUNT(tipo_nodo) AS sum_tipo_nodo FROM nodos_activos WHERE tipo_nodo = 'Beta' AND estatus='Activado'")['sum_tipo_nodo'];
                if ($sum_tipo_nodo < 500) {
                    $tipo_nodo_defaul = "Beta";
                }
                $db->sqlconector(
                    "INSERT INTO nodos_activos (id_evolucionador, stripe_session_id, monto, estatus, tipo_nodo) 
                     VALUES (?, ?, ?, 'Activado', ?)",
                    [$user['id_evolucionador'], $session_id, $monto, $tipo_nodo_defaul]
                );

                // 4. Activar estatus en la tabla principal
                $db->sqlconector(
                    "UPDATE evolucionadores SET estatus_soberania = 'Activo' WHERE id_evolucionador = ?",
                    [$user['id_evolucionador']]
                );
                
                error_log(
                    "\n[WEBHOOK SUCCESS] Nodo activado - Usuario: $email | Session: $session_id | Monto: $monto $moneda",
                    3,
                    Modulo::LOG_PATH
                );
                
                // TODO: Aquí podrías enviar un email automático de bienvenida al usuario
                // enviarEmailBienvenida($email, $user['id_evolucionador']);
                
            } else {
                error_log(
                    "\n[WEBHOOK WARNING] Pago recibido pero usuario no encontrado - Email: $email | Session: $session_id",
                    3,
                    Modulo::LOG_PATH
                );
            }
        } else {
            error_log(
                "\n[WEBHOOK INFO] Pago duplicado ignorado - Session: $session_id",
                3,
                Modulo::LOG_PATH
            );
        }
        
    } catch (Exception $e) {
        error_log(
            "\n[WEBHOOK ERROR] Error al activar nodo - Email: $email | Error: " . $e->getMessage(),
            3,
            Modulo::LOG_PATH
        );
    }
}