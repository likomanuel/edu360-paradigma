<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<style>
    .feature-hero {
        padding: 120px 20px;
        text-align: center;
        background: url('<?php echo img('quienes_somos_vision.png'); ?>') center/cover no-repeat;
        position: relative;
    }

    .feature-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(3, 10, 26, 0.85);
        backdrop-filter: blur(5px);
    }

    .feature-hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .feature-hero h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        color: #fff;
        text-shadow: 0 0 20px rgba(0, 168, 232, 0.5);
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 40px;
        padding: 80px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(0, 168, 232, 0.2);
        padding: 40px;
        border-radius: 30px;
        transition: all 0.4s;
        text-align: center;
    }

    .feature-card:hover {
        background: rgba(0, 168, 232, 0.05);
        border-color: #00a8e8;
        transform: scale(1.02);
    }

    .feature-card i {
        font-size: 4rem;
        color: #00a8e8;
        margin-bottom: 25px;
    }

    .feature-card h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #fff;
    }

    .feature-card p {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.7;
    }

    .cta-section {
        background: linear-gradient(90deg, #001f3f, #00a8e8);
        padding: 60px;
        border-radius: 40px;
        text-align: center;
        margin: 60px 20px;
    }

    .cta-section h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .btn-white {
        background: #fff;
        color: #001f3f;
        padding: 15px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-white:hover {
        background: #ffd700;
        transform: translateY(-5px);
    }
</style>

<div class="feature-hero">
    <div class="feature-hero-content">
        <h1>Construyendo el Futuro del Conocimiento</h1>
        <p style="font-size: 1.3rem; color: rgba(255, 255, 255, 0.8);">Más que educación, somos una infraestructura de soberanía digital centrada en el individuo.</p>
    </div>
</div>

<div class="feature-grid">
    <div class="feature-card">
        <i class="fas fa-network-wired"></i>
        <h3>Desintermediación</h3>
        <p>Eliminamos la burocracia institucional. El conocimiento fluye directamente del aprendizaje al activo digital, validado por IA sin sesgos humanos.</p>
    </div>
    <div class="feature-card">
        <i class="fas fa-cube"></i>
        <h3>Activos Inmutables</h3>
        <p>Cada hito de aprendizaje se convierte en un registro inmutable en nuestra red, garantizando que tu progreso sea verificable para siempre.</p>
    </div>
    <div class="feature-card">
        <i class="fas fa-robot"></i>
        <h3>Auditoría de IA</h3>
        <p>Utilizamos Inteligencia Artificial avanzada para supervisar y validar el dominio real de los temas, asegurando un estándar de excelencia global.</p>
    </div>
    <div class="feature-card">
        <i class="fas fa-user-shield"></i>
        <h3>Soberanía Digital</h3>
        <p>Tú eres el único dueño de tu historial académico y profesional. Acceso, control y monetización total de tu capital intelectual.</p>
    </div>
</div>

<div class="section-container">
    <div class="cta-section">
        <h2>¿Listo para ser un Evolucionador?</h2>
        <p>Únete a la red que está redefiniendo el prestigio académico en Iberoamérica.</p>
        <a href="<?php echo base_url('/registro'); ?>" class="btn-white">Comenzar Ahora</a>
    </div>
</div>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
