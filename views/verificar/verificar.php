<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="login-wrapper" style="min-height: 80vh; background: radial-gradient(circle at center, #0a0a0a 0%, #000 100%);">
    <div class="login-box" style="max-width: 800px; border: 1px solid rgba(0, 168, 232, 0.3); background: rgba(10, 10, 10, 0.8); backdrop-filter: blur(20px);">
        <div class="login-header">
            <i class="fas fa-shield-alt" style="font-size: 4rem; color: var(--primary-blue); filter: drop-shadow(0 0 15px var(--primary-blue));"></i>
            <h2 style="font-size: 2.5rem; margin-top: 20px; letter-spacing: 3px; font-weight: 800; text-transform: uppercase;">Portal de Verificación</h2>
            <p style="color: #888; letter-spacing: 1px;">Blockchain-Style Certificate Explorer</p>
        </div>

        <form action="<?= base_url('verificar/buscar') ?>" method="GET" class="search-container" style="margin-top: 40px;">
            <div class="input-group" style="margin-bottom: 30px;">
                <i class="fas fa-search" style="color: var(--primary-blue); left: 20px;"></i>
                <input type="text" name="q" class="login-input" placeholder="Buscar por Hash de Identidad o Email Verificado..." 
                       style="padding-left: 55px; height: 60px; font-size: 1.1rem; border-radius: 12px; background: rgba(0,0,0,0.6);" required>
            </div>
            
            <button type="submit" class="btn-login" style="border-radius: 12px; height: 60px; font-size: 1.2rem; letter-spacing: 2px;">
                EXPLORAR CERTIFICADOS
            </button>
        </form>

        <div class="verification-stats" style="margin-top: 50px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="stat-card" style="padding: 20px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                <i class="fas fa-link" style="color: var(--primary-blue); margin-bottom: 10px;"></i>
                <div style="font-size: 0.7rem; color: #666; text-transform: uppercase;">Estado de Red</div>
                <div style="font-size: 1rem; color: #0f0;">Sincronizado</div>
            </div>
            <div class="stat-card" style="padding: 20px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                <i class="fas fa-certificate" style="color: var(--primary-blue); margin-bottom: 10px;"></i>
                <div style="font-size: 0.7rem; color: #666; text-transform: uppercase;">Metodología</div>
                <div style="font-size: 1rem; color: #eee;">SRAA Verified</div>
            </div>
            <div class="stat-card" style="padding: 20px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                <i class="fas fa-microchip" style="color: var(--primary-blue); margin-bottom: 10px;"></i>
                <div style="font-size: 0.7rem; color: #666; text-transform: uppercase;">Protocolo</div>
                <div style="font-size: 1rem; color: #eee;">EDU360-v10</div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
