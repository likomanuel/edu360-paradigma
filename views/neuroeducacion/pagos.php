<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
use App\Services\StripeService;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

// La lógica de creación de sesión de Stripe ahora solo ocurre si se envía el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stripeService = new StripeService($_ENV['STRIPE_SECRET_KEY']);

        $checkout_session = $stripeService->createCheckoutSession([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Acceso al Nodo Fundacional - EDU360'],
                    'unit_amount' => 2000, // $20.00
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => base_url('procesar_pago.php') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => base_url('neuroeducacion/pagos.php'), // Volver a esta misma página
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        exit();

    } catch (Exception $e) {
        $error_mensaje = "Error al iniciar el proceso de pago. Por favor, intente de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacción de Soberanía - EDU360</title>
    <link rel="icon" type="image/x-icon" href="<?php echo img('favicon/university/favicon.ico'); ?>" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script async src="https://js.stripe.com/v3/buy-button.js"></script>
    <style>
        :root {
            --primary-blue: #00a8e8;
            --cyber-green: #00ff88;
            --bg-dark: #0a0a0a;
            --card-bg: rgba(20, 20, 20, 0.95);
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.9)), 
                              url('https://images.unsplash.com/photo-1639762681485-074b7f938ba0?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
        }

        /* --- HEADER --- */
        header {
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .logo { font-weight: 700; color: var(--primary-blue); }

        /* --- CONTENEDOR DE PAGO --- */
        .payment-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1100px;
            margin: 40px auto;
            gap: 40px;
            padding: 20px;
        }

        /* Lado de Información */
        .info-panel h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .subtitle { color: var(--primary-blue); font-size: 0.9rem; font-weight: bold; margin-bottom: 30px; }
        .content p { line-height: 1.8; color: #ccc; margin-bottom: 20px; }
        
        .verify-box {
            background: rgba(0, 168, 232, 0.05);
            border-left: 3px solid var(--primary-blue);
            padding: 15px;
            margin-top: 30px;
        }
        .verify-box a { color: var(--cyber-green); text-decoration: none; font-family: 'Fira Code', monospace; font-size: 0.8rem; }

        /* Lado de Stripe */
        .checkout-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .checkout-card::before {
            content: "MODO HABILITADO";
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.6rem;
            color: #34e743ff;
            border: 1px solid #34e743ff;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .price-tag { font-size: 3rem; font-weight: 700; margin-bottom: 10px; }
        .price-tag span { font-size: 1rem; color: #888; }

        .secure-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.75rem;
            color: #666;
            margin-top: 25px;
            border-top: 1px solid #222;
            padding-top: 20px;
        }

        /* Botón de Simulación para Staging */
        .btn-simulate {
            margin-top: 15px;
            background: none;
            border: 1px dashed #444;
            color: #666;
            padding: 10px;
            cursor: pointer;
            font-size: 0.7rem;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-simulate:hover { border-color: var(--cyber-green); color: var(--cyber-green); }

        /* --- MODAL DE AYUDA FLOTANTE --- */
        .help-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .help-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            border: 1px solid var(--primary-blue);
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 168, 232, 0.3);
            animation: slideUp 0.4s ease;
        }

        .help-modal h3 {
            color: var(--primary-blue);
            margin-top: 0;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .help-modal h4 {
            color: var(--cyber-green);
            font-size: 1.1rem;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .help-modal p, .help-modal li {
            color: #ccc;
            line-height: 1.8;
            font-size: 0.9rem;
        }

        .help-modal ul {
            padding-left: 20px;
        }

        .help-modal .info-box {
            background: rgba(0, 168, 232, 0.1);
            border-left: 3px solid var(--primary-blue);
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .help-modal .warning-box {
            background: rgba(255, 193, 7, 0.1);
            border-left: 3px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .help-modal .test-card {
            background: rgba(0, 255, 136, 0.05);
            border: 1px dashed var(--cyber-green);
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-family: 'Fira Code', monospace;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: #666;
            font-size: 2rem;
            cursor: pointer;
            transition: 0.3s;
            line-height: 1;
        }

        .close-modal:hover {
            color: var(--cyber-green);
            transform: rotate(90deg);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            to { 
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 850px) {
            .payment-grid { grid-template-columns: 1fr; }
            .info-panel { text-align: center; }
            .help-modal {
                padding: 30px 20px;
                width: 95%;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo" onclick="window.location.href='<?php echo base_url(); ?>'" style="cursor: pointer;"><i class="fas fa-shield-alt"></i> PARADIGMA EDU360</div>
        <div style="font-size: 0.7rem; color: #555;">SSL ENCRYPTION ACTIVE</div>
    </header>

    <main class="payment-grid">
        
        <section class="info-panel">
            <h1>Transfiere Valor al Sistema</h1>
            <p class="subtitle">Creado por el Dr. Manuel Aguilera, PhD</p>

            <div class="content">
                <p>El <strong>Paradigma EDU360</strong> es el primer ecosistema educativo digital avanzado para el mundo hispano.</p>
                <p>No vendemos cursos. Operamos un laboratorio vivo del futuro educativo donde cada transacción activa un nodo de conocimiento soberano.</p>

                <div class="verify-box">
                    <strong>Verificación de certificaciones</strong><br>
                    <a href="<?php echo base_url('verify'); ?>" target="_blank">
                        <i class="fas fa-external-link-alt"></i> verify.edu360.global
                    </a>
                </div>
            </div>
        </section>

        <section class="checkout-card">
            <h2>Acceso al Nodo</h2>
            <div class="price-tag">20.00 <span>USD</span></div>
            <p style="font-size: 0.8rem; color: #888; margin-bottom: 30px;">
                Cuota única de activación para el Nodo Fundacional.
            </p>

            <?php if (isset($error_mensaje)): ?>
                <div style="color: #ff4444; margin-bottom: 20px; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_mensaje; ?>
                </div>
            <?php endif; ?>

            <stripe-buy-button
            buy-button-id="buy_btn_1Sq0H0E0riMQis9OIH6QxMbE" 
            publishable-key="pk_test_51Sj3ePE0riMQis9O7MtULrr8hgKTdwqiXvvXlHJdNQ3MUwmEHXLCr7FsMThMhIXdzQ9vVfE2jPU3SYA2PPxJ1sq200IXJPDzJ1" 
            success-url="<?php echo base_url('procesar_pago.php'); ?>?session_id={CHECKOUT_SESSION_ID}" >
            </stripe-buy-button>

            <form method="POST">
                <button type="submit" id="checkout-button" style="width: 100%; padding: 15px; background: var(--primary-blue); color: white; border: none; border-radius: 5px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-bottom: 20px;">
                    <i class="fab fa-stripe"></i> Pagar con Tarjeta ($20.00 USD)
                </button>
            </form>
            <div class="secure-notice">
                <i class="fas fa-lock"></i>
                <span>Pagos procesados de forma segura por Stripe. No almacenamos datos de su tarjeta.</span>
            </div>

            <button class="btn-simulate" onclick="showPaymentHelp()">
                <i class="fas fa-question-circle"></i> Ayuda con el pago
            </button>
        </section>

    </main>

    <!-- Modal de Ayuda Flotante -->
    <div class="help-overlay" id="helpOverlay" onclick="closePaymentHelp()">
        <div class="help-modal" onclick="event.stopPropagation()">
            <button class="close-modal" onclick="closePaymentHelp()">&times;</button>
            
            <h3><i class="fas fa-credit-card"></i> Guía de Pago con Stripe</h3>
            <p>Stripe es una plataforma de pagos segura utilizada por millones de empresas en todo el mundo.</p>

            <h4><i class="fas fa-shield-alt"></i> Seguridad</h4>
            <div class="info-box">
                <strong>Tus datos están protegidos:</strong>
                <ul>
                    <li>Conexión encriptada SSL/TLS</li>
                    <li>No almacenamos información de tu tarjeta</li>
                    <li>Cumplimiento PCI DSS Nivel 1</li>
                    <li>Autenticación 3D Secure cuando sea necesario</li>
                </ul>
            </div>

            <h4><i class="fas fa-list-ol"></i> Cómo Proceder</h4>
            <p><strong>Paso 1:</strong> Haz clic en el botón azul de Stripe arriba</p>
            <p><strong>Paso 2:</strong> Se abrirá una ventana segura de pago</p>
            <p><strong>Paso 3:</strong> Ingresa los datos de tu tarjeta</p>
            <p><strong>Paso 4:</strong> Confirma el pago</p>
            <p><strong>Paso 5:</strong> Serás redirigido automáticamente a tu panel de soberanía</p>

            <h4><i class="fas fa-credit-card"></i> Tarjetas de Prueba (Modo Test)</h4>
            <div class="test-card">
                <strong>Número de tarjeta:</strong> 4242 4242 4242 4242<br>
                <strong>Fecha de expiración:</strong> Cualquier fecha futura (ej: 12/34)<br>
                <strong>CVC:</strong> Cualquier 3 dígitos (ej: 123)<br>
                <strong>Código postal:</strong> Cualquier 5 dígitos (ej: 12345)
            </div>

            <div class="warning-box">
                <strong><i class="fas fa-exclamation-triangle"></i> Nota:</strong> 
                Actualmente estamos en modo de prueba. Usa las tarjetas de prueba proporcionadas arriba. 
                No se realizarán cargos reales a tu tarjeta.
            </div>

            <h4><i class="fas fa-question"></i> ¿Problemas con el pago?</h4>
            <p>Si experimentas algún problema:</p>
            <ul>
                <li>Verifica que tu conexión a internet sea estable</li>
                <li>Asegúrate de usar los datos correctos de la tarjeta</li>
                <li>Intenta con otro navegador si el problema persiste</li>
                <li>Contacta a soporte: <strong style="color: var(--cyber-green)">soporte@edu360.global</strong></li>
            </ul>
        </div>
    </div>

    <script>
        /**
         * Muestra el modal de ayuda con información sobre el pago
         */
        function showPaymentHelp() {
            const overlay = document.getElementById('helpOverlay');
            overlay.style.display = 'block';
            // Prevenir scroll del body cuando el modal está abierto
            document.body.style.overflow = 'hidden';
        }

        /**
         * Cierra el modal de ayuda
         */
        function closePaymentHelp() {
            const overlay = document.getElementById('helpOverlay');
            overlay.style.display = 'none';
            // Restaurar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePaymentHelp();
            }
        });
    </script>
</body>
</html>