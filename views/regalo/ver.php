<?php
// Variables pasadas desde el controlador: $tarjeta
require_once __DIR__ . '/../../views/layouts/header.php';
?>
<style>
    :root {
        --gift-gold: #FFD700;
        --gift-rose: #ff477e;
        --dark-bg: #050505;
        --card-bg: rgba(15, 15, 15, 0.85);
    }
    body {
        margin: 0;
        background-color: var(--dark-bg);
        color: white;
        background-image: 
            radial-gradient(circle at top right, rgba(255, 71, 126, 0.1), transparent 40%),
            radial-gradient(circle at bottom left, rgba(255, 215, 0, 0.1), transparent 40%);
        font-family: 'Montserrat', sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Contenedor Principal */
    .view-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        perspective: 1000px;
    }

    /* La Tarjeta de Regalo (Física) */
    .gift-card {
        width: 100%;
        max-width: 600px;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 25px;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.8),
            inset 0 0 10px rgba(255, 215, 0, 0.1),
            0 0 30px rgba(255, 215, 0, 0.2);
        padding: 40px;
        position: relative;
        overflow: hidden;
        animation: float 6s ease-in-out infinite, slideIn 1s ease-out forwards;
        transform-style: preserve-3d;
        z-index: 2;
    }

    /* Resplandor animado de la tarjeta */
    .gift-card::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: linear-gradient(
            to bottom right, 
            rgba(255,215,0,0) 0%, 
            rgba(255,215,0,0.1) 40%, 
            rgba(255,255,255,0.4) 50%, 
            rgba(255,215,0,0.1) 60%, 
            rgba(255,215,0,0) 100%
        );
        transform: rotate(45deg);
        animation: shine 4s infinite linear;
        pointer-events: none;
    }

    /* Encabezado Tarjeta */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 20px;
    }
    
    .card-logo {
        font-weight: 900;
        font-size: 1.5rem;
        color: var(--gift-gold);
        letter-spacing: 2px;
        text-shadow: 0 0 10px rgba(255,215,0,0.5);
    }
    .card-amount {
        font-size: 2.5rem;
        font-weight: bold;
        color: #fff;
    }
    .card-amount span {
        font-size: 1rem;
        color: var(--gift-gold);
    }

    /* Cuerpo de la Tarjeta */
    .card-body {
        text-align: center;
        margin-bottom: 40px;
    }
    .badge {
        display: inline-block;
        background: rgba(255, 71, 126, 0.2);
        color: var(--gift-rose);
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 71, 126, 0.4);
    }
    .welcome-msg {
        font-size: 1.2rem;
        line-height: 1.8;
        color: #ddd;
        font-style: italic;
    }
    .sender-name {
        margin-top: 15px;
        font-size: 0.9rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Pie de Tarjeta (Código) */
    .card-footer {
        background: rgba(0,0,0,0.5);
        border-radius: 15px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .card-footer span {
        font-size: 0.7rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }
    .card-number {
        font-family: 'Fira Code', monospace, sans-serif;
        font-size: 1.6rem;
        letter-spacing: 4px;
        color: var(--gift-gold);
        text-shadow: 0 0 5px rgba(255,215,0,0.3);
    }

    /* El Mensaje de Bienvenida Debajo de la Tarjeta */
    .bottom-section {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        padding: 0 20px 40px 20px;
        animation: slideUp 1s ease-out 0.5s forwards;
        opacity: 0;
    }
    
    .bottom-section h2 {
        font-size: 2rem;
        margin-bottom: 15px;
        color: #fff;
    }
    .bottom-section p {
        color: #aaa;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    /* Botón Siguiente */
    .btn-next {
        background: linear-gradient(45deg, var(--gift-rose), #ff8fa3);
        color: white;
        text-decoration: none;
        padding: 18px 50px;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s ease;
        display: inline-block;
        box-shadow: 0 10px 20px rgba(255, 71, 126, 0.3);
        border: none;
        cursor: pointer;
    }
    .btn-next:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 15px 30px rgba(255, 71, 126, 0.5);
        background: linear-gradient(45deg, #ff8fa3, var(--gift-rose));
    }
    
    .btn-disabled {
        background: #333 !important;
        color: #888 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }

    /* Animaciones */
    @keyframes float {
        0% { transform: translateY(0px) rotateX(2deg) rotateY(2deg); }
        50% { transform: translateY(-15px) rotateX(-2deg) rotateY(-2deg); }
        100% { transform: translateY(0px) rotateX(2deg) rotateY(2deg); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-50px) scale(0.9); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shine {
        0% { top: -150%; left: -150%; }
        100% { top: 150%; left: 150%; }
    }
    
    /* Efectos de Destello en la caja */
    .sparkle {
        position: absolute;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: white;
        box-shadow: 0 0 10px 2px rgba(255, 215, 0, 0.8);
        animation: twinkle 2s infinite ease-in-out;
    }
    @keyframes twinkle {
        0%, 100% { opacity: 0; transform: scale(0.5); }
        50% { opacity: 1; transform: scale(1.5); }
    }
</style>

<div class="view-container">
    
    <!-- Partículas Decorativas -->
    <div class="sparkle" style="top: 20%; left: 15%; animation-delay: 0s;"></div>
    <div class="sparkle" style="top: 10%; right: 20%; animation-delay: 0.5s;"></div>
    <div class="sparkle" style="bottom: 30%; left: 10%; animation-delay: 1s;"></div>
    <div class="sparkle" style="bottom: 15%; right: 15%; animation-delay: 1.5s;"></div>

    <div class="gift-card">
        <div class="card-header">
            <div class="card-logo">
                <i class="fas fa-crown"></i> EDU360
            </div>
            <div class="card-amount">
                <?php echo number_format($tarjeta['monto_cobrar'], 2); ?> <span>USD</span>
            </div>
        </div>

        <div class="card-body">
            <div class="badge">
                <i class="fas fa-star"></i> Acceso Vitalicio Concedido
            </div>
            <div class="welcome-msg">
                "<?php echo htmlspecialchars($tarjeta['mensaje']); ?>"
            </div>
            <div class="sender-name">
                <i class="fas fa-gift"></i> Un regalo de: <?php echo htmlspecialchars($tarjeta['sender_email']); ?>
            </div>
        </div>

        <div class="card-footer">
            <span>Código de Autenticación Soberana</span>
            <div class="card-number">
                <?php echo htmlspecialchars($tarjeta['codigo']); ?>
            </div>
        </div>
    </div>
</div>

<div class="bottom-section">
    <?php if ($tarjeta['estatus'] === 'Reclamada'): ?>
        <h2 style="color: var(--gift-rose);"><i class="fas fa-lock"></i> Tarjeta Reclamada</h2>
        <p>Esta tarjeta de regalo ya ha sido utilizada y su nodo correspondiente ha sido activado.</p>
        <a href="<?php echo base_url(); ?>" class="btn-next btn-disabled">Volver al Inicio</a>
    <?php else: ?>
        <h2>¡Felicidades, <?php echo htmlspecialchars(explode('@', $tarjeta['destinatario_email'])[0]); ?>!</h2>
        <p>Has recibido una Beca o Regalo exclusivo para integrarte al Ecosistema EDU360 Paradigma. Al presionar "Iniciar", comenzarás tu proceso de registro y se activará tu Nodo con la inversión ya cubierta.</p>
        <a href="<?php echo base_url('regalo/registro?code=' . urlencode($tarjeta['codigo'])); ?>" class="btn-next">
            INICIAR MI EVOLUCIÓN <i class="fas fa-arrow-right"></i>
        </a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
