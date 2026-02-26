<?php
use Dotenv\Dotenv;
use App\Services\StripeService;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$codigo = $_GET['code'] ?? null;
// La validación de existencia de la tarjeta ya la hace el controlador, tenemos `$tarjeta` disponible.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stripeService = new StripeService($_ENV['STRIPE_SECRET_KEY']);

        // Convertir monto a centavos para Stripe
        $monto_centavos = intval($tarjeta['monto_cobrar'] * 100);

        // Generamos la sesión enviando el 'client_reference_id' con el código de la tarjeta
        // Esto será leído por webhook.php o procesar_pago.php para marcarla como reclamada
        $checkout_session = $stripeService->createCheckoutSession([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Activación de Nodo Soberano - Regalo EDU360'],
                    'unit_amount' => $monto_centavos, 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'client_reference_id' => $codigo, // CRÍTICO: Para ligar el pago con la tarjeta de regalo
            'success_url' => base_url('procesar_pago.php') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => base_url('regalo/pago?code=' . urlencode($codigo)), 
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        exit();

    } catch (Exception $e) {
        $error_mensaje = "Error al iniciar el proceso de pago. " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Beca - EDU360</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #00a8e8;
            --cyber-green: #00ff88;
            --bg-dark: #0a0a0a;
            --gift-gold: #FFD700;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 50% 50%, rgba(255, 215, 0, 0.1), transparent 60%);
        }

        .checkout-box {
            background: rgba(20, 20, 20, 0.9);
            padding: 50px;
            border-radius: 20px;
            border: 1px solid rgba(255,215,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8), inset 0 0 20px rgba(255,215,0,0.05);
            animation: fadeIn 0.8s ease-out;
        }

        .checkout-box h1 {
            color: var(--gift-gold);
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .checkout-box p {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .price-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            text-shadow: 0 0 10px rgba(255,255,255,0.2);
        }

        .price-display span {
            font-size: 1.2rem;
            color: var(--gift-gold);
        }

        .btn-pay {
            display: block;
            width: 100%;
            padding: 18px;
            background: linear-gradient(45deg, var(--gift-gold), #ffb300);
            color: black;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 30px;
            box-shadow: 0 10px 20px rgba(255,215,0,0.2);
            text-transform: uppercase;
        }

        .btn-pay:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 215, 0, 0.4);
            background: linear-gradient(45deg, #ffb300, var(--gift-gold));
        }

        .secure-notice {
            margin-top: 20px;
            font-size: 0.8rem;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>

    <div class="checkout-box">
        <h1><i class="fas fa-gem"></i> Activación de Nodo</h1>
        <p>Tu cuenta ha sido consolidada. Para reclamar el regalo y activar funcionalmente el nodo de soberanía en la red, completa el proceso de pago a continuación.</p>
        
        <div class="price-display">
            <?php echo number_format($tarjeta['monto_cobrar'], 2); ?> <span>USD</span>
        </div>
        <p style="color: #666; font-size: 0.8rem;">(Inversión cubierta por el Emisor: <?php echo htmlspecialchars($tarjeta['sender_email']); ?>)</p>

        <?php if(isset($error_mensaje)): ?>
            <div style="color: #ff477e; margin: 20px 0; background: rgba(255,71,126,0.1); padding: 10px; border-radius: 5px;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <button type="submit" class="btn-pay">
                <i class="fab fa-stripe"></i> Pagar y Activar Nodo
            </button>
        </form>

        <div class="secure-notice">
            <i class="fas fa-lock"></i> Pagos encriptados de forma segura por Stripe
        </div>
    </div>

</body>
</html>
