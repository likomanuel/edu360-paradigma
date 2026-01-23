<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<style>
    :root {
        --primary-gold: #ffd700;
        --secondary-gold: #ff8c00;
        --dark-bg: #030a1a;
        --accent-blue: #00a8e8;
    }

    .about-hero {
        padding: 100px 20px;
        text-align: center;
        background: radial-gradient(circle at center, rgba(0, 168, 232, 0.15) 0%, transparent 70%);
        position: relative;
        overflow: hidden;
    }

    .about-hero h1 {
        font-size: 4rem;
        margin-bottom: 20px;
        background: linear-gradient(45deg, #fff, var(--primary-gold));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-transform: uppercase;
        letter-spacing: 4px;
    }

    .section-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .rector-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-radius: 40px;
        padding: 50px;
        display: flex;
        gap: 50px;
        align-items: center;
        margin-bottom: 80px;
        position: relative;
        overflow: hidden;
    }

    .rector-card::after {
        content: 'RECTORÍA';
        position: absolute;
        top: 20px;
        right: -30px;
        font-size: 8rem;
        font-weight: 900;
        color: rgba(255, 215, 0, 0.03);
        transform: rotate(-10deg);
        pointer-events: none;
    }

    .rector-image {
        flex: 1;
        max-width: 400px;
    }

    .rector-image img {
        width: 100%;
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        border: 2px solid rgba(255, 215, 0, 0.3);
    }

    .rector-info {
        flex: 2;
    }

    .rector-info h2 {
        font-size: 2.5rem;
        color: var(--primary-gold);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .linkedin-link {
        color: #0077b5;
        font-size: 1.8rem;
        transition: transform 0.3s, color 0.3s;
        display: inline-flex;
    }

    .linkedin-link:hover {
        color: #00a0dc;
        transform: translateY(-3px) scale(1.1);
    }

    .rector-tagline {
        font-size: 1.2rem;
        color: var(--accent-blue);
        margin-bottom: 25px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .rector-description {
        line-height: 1.8;
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 30px;
    }

    .metadata-box {
        background: rgba(0, 168, 232, 0.1);
        padding: 20px;
        border-radius: 15px;
        border: 1px solid rgba(0, 168, 232, 0.2);
        display: inline-block;
    }

    .metadata-item {
        margin-bottom: 10px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
    }

    .metadata-label {
        color: var(--accent-blue);
        font-weight: bold;
    }

    .grid-mision-vision {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 80px;
    }

    .mv-card {
        background: rgba(255, 255, 255, 0.05);
        padding: 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: transform 0.3s;
    }

    .mv-card:hover {
        transform: translateY(-10px);
        border-color: var(--accent-blue);
    }

    .mv-card i {
        font-size: 3rem;
        color: var(--accent-blue);
        margin-bottom: 20px;
    }

    .mv-card h3 {
        font-size: 2rem;
        margin-bottom: 20px;
        color: #fff;
    }

    .mv-card p {
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.7);
    }

    .values-section {
        background: rgba(255, 215, 0, 0.05);
        border-radius: 50px;
        padding: 60px;
        margin-bottom: 80px;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .value-item {
        text-align: center;
    }

    .value-item h4 {
        color: var(--primary-gold);
        font-size: 1.3rem;
        margin-bottom: 15px;
    }

    .identity-box {
        text-align: center;
        padding: 60px;
        background: linear-gradient(135deg, rgba(0, 168, 232, 0.1) 0%, rgba(0, 0, 0, 0.3) 100%);
        border-radius: 40px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    @media (max-width: 992px) {
        .rector-card {
            flex-direction: column;
            text-align: center;
        }
        .grid-mision-vision {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="about-hero">
    <div class="glow-sphere"></div>
    <h1>¿Quiénes Somos?</h1>
    <p>La Red de Emancipación Intelectual en la Era de la IA</p>
</div>

<div class="section-container">
    <!-- Rectoría -->
    <section class="rector-card">
        <div class="rector-image">
            <img src="<?php echo img('home_cards/rector.jpeg'); ?>" alt="Dr. Manuel José Aguilera">
        </div>
        <div class="rector-info">
            <h2>
                Dr. Manuel José Aguilera
                <a href="http://www.linkedin.com/in/dr-manuel-aguilera-3792b13a3" target="_blank" rel="noopener noreferrer" class="linkedin-link" title="LinkedIn Profile">                
                    <i class="ri-linkedin-box-fill"></i>
                </a>
            </h2>
            <div class="rector-tagline">Arquitecto de Soberanía Cognitiva y Visionario del Capital Intelectual</div>
            <p class="rector-description">
                Líder estratégico con la misión de desintermediar el conocimiento global. Como Rector Fundador de EDU360 University Institute, ha transformado la educación tradicional en un ecosistema de activos digitales inmutables. 
                <br><br>
                Autor del modelo de Rigor Federal y de los 23 reglamentos institucionales que blindan la integridad de la Red. Bajo su liderazgo, EDU360 se posiciona como la infraestructura estándar para la emancipación intelectual.
            </p>
            
            <div class="metadata-box">
                <div class="metadata-item"><span class="metadata-label">Status del Nodo:</span> Rector / Nodo Alfa Fundador</div>
                <div class="metadata-item"><span class="metadata-label">Protocolo:</span> Kérnel v10 Activo</div>
                <div class="metadata-item"><span class="metadata-label">Verificación:</span> Firma Digital Encriptada en el Ledger Federal</div>
            </div>
        </div>
    </section>

    <!-- Misión y Visión -->
    <div class="grid-mision-vision">
        <div class="mv-card">
            <i class="fas fa-bullseye"></i>
            <h3>Nuestra Misión</h3>
            <p>Empoderar al individuo mediante la Soberanía Cognitiva y el uso estratégico de la Inteligencia Artificial, transformando el aprendizaje en un activo digital inmutable. Existimos para desintermediar el conocimiento, permitiendo que cada Evolucionador sea el único dueño de su progreso.</p>
        </div>
        <div class="mv-card">
            <i class="fas fa-eye"></i>
            <h3>Nuestra Visión</h3>
            <p>Convertirnos en la infraestructura estándar de confianza intelectual en Iberoamérica para el 2030. Una red global donde los títulos burocráticos sean reemplazados por Nodos de Dominio Verificado, donde el prestigio se demuestra y se protege criptográficamente.</p>
        </div>
    </div>

    <!-- Valores -->
    <section class="values-section">
        <h2 style="text-align: center; font-size: 2.5rem; color: #fff;">Nuestros Valores</h2>
        <div class="values-grid">
            <div class="value-item">
                <h4>Soberanía Cognitiva</h4>
                <p>Derecho inalienable a ser el único dueño de su conocimiento y progreso profesional.</p>
            </div>
            <div class="value-item">
                <h4>Rigor Federal</h4>
                <p>Auditoría de IA para garantizar acreditación basada en evidencia de dominio inmutable.</p>
            </div>
            <div class="value-item">
                <h4>Transparencia de Nodo</h4>
                <p>Certificaciones rastreables y verificadas, eliminando el fraude académico.</p>
            </div>
            <div class="value-item">
                <h4>Autonomía Descentralizada</h4>
                <p>Éxito basado en capacidad demostrada, sin estructuras burocráticas.</p>
            </div>
            <div class="value-item">
                <h4>Humanismo Evolucionador</h4>
                <p>El uso de la tecnología como herramienta para expandir la conciencia, la ética y el liderazgo humano, no para reemplazarlos.</p>
            </div>            
        </div>
    </section>

    <!-- Identidad -->
    <section class="identity-box">
        <h3 style="color: var(--accent-blue); margin-bottom: 20px;">Nuestra Identidad</h3>
        <p style="font-size: 1.2rem; line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
            EDU360 University Institute no es una institución educativa convencional; es un ecosistema de autogobierno académico diseñado para liderar la era de la Inteligencia Artificial. Fundamentada en un modelo de Soberanía Digital, nuestra identidad se define por la eliminación de la burocracia institucional en favor del empoderamiento individual.
        </p>
    </section>
</div>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
