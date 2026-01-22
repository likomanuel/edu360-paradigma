<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<style>
    .tech-header {
        padding: 80px 20px;
        text-align: center;
        background: radial-gradient(circle at top, rgba(0, 168, 232, 0.2) 0%, transparent 70%);
    }

    .tech-header h1 {
        font-size: 3rem;
        color: #fff;
        margin-bottom: 20px;
    }

    .tech-flow {
        max-width: 1000px;
        margin: 40px auto;
        padding: 40px;
        position: relative;
    }

    .flow-item {
        display: flex;
        align-items: flex-start;
        gap: 30px;
        margin-bottom: 60px;
        position: relative;
    }

    .flow-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 30px;
        top: 60px;
        bottom: -40px;
        width: 2px;
        background: linear-gradient(to bottom, #ffd700, transparent);
    }

    .flow-icon {
        background: #000;
        border: 2px solid #ffd700;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #ffd700;
        flex-shrink: 0;
        z-index: 1;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
    }

    .flow-content {
        background: rgba(255, 255, 255, 0.03);
        padding: 30px;
        border-radius: 20px;
        border-left: 4px solid #ffd700;
        flex-grow: 1;
    }

    .flow-content h3 {
        color: #ffd700;
        margin-bottom: 10px;
    }

    .flow-content p {
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.6;
    }

    .regulations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .reg-box {
        background: #0a1128;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .reg-box span {
        display: block;
        font-size: 2rem;
        font-weight: 800;
        color: #00a8e8;
        margin-bottom: 5px;
    }
</style>

<div class="tech-header">
    <h1>Protocolo de Rigor Federal</h1>
    <p style="font-size: 1.2rem; color: #ffd700;">Nuestra Arquitectura de Confianza Técnica</p>
</div>

<div class="tech-flow">
    <div class="flow-item">
        <div class="flow-icon"><i class="fas fa-microchip"></i></div>
        <div class="flow-content">
            <h3>Kérnel v10</h3>
            <p>El núcleo de nuestra plataforma opera bajo el protocolo Kérnel v10, integrando auditoría de IA en tiempo real para cada interacción de aprendizaje.</p>
        </div>
    </div>

    <div class="flow-item">
        <div class="flow-icon"><i class="fas fa-file-contract"></i></div>
        <div class="flow-content">
            <h3>23 Reglamentos Institucionales</h3>
            <p>Un blindaje legal y técnico de grado federal que garantiza la integridad total de la red y la validez de cada activo intelectual emitido.</p>
        </div>
    </div>

    <div class="flow-item">
        <div class="flow-icon"><i class="fas fa-fingerprint"></i></div>
        <div class="flow-content">
            <h3>Ledger Federal</h3>
            <p>Toda acreditación se registra en nuestro Ledger Federal, un sistema de firmas digitales encriptadas que elimina cualquier posibilidad de fraude académico.</p>
        </div>
    </div>

    <div class="flow-item">
        <div class="flow-icon"><i class="fas fa-project-diagram"></i></div>
        <div class="flow-content">
            <h3>Nodos de Dominio</h3>
            <p>Reemplazamos los títulos tradicionales por Nodos de Dominio Verificado, permitiendo una monetización directa del mérito en la economía del conocimiento.</p>
        </div>
    </div>
</div>

<div class="section-container">
    <h2 style="text-align: center; margin-bottom: 30px;">Infraestructura de Grado Federal</h2>
    <div class="regulations-grid">
        <div class="reg-box">
            <span>23</span>
            Reglamentos Activos
        </div>
        <div class="reg-box">
            <span>100%</span>
            Auditoría de IA
        </div>
        <div class="reg-box">
            <span>0</span>
            Intermediarios
        </div>
        <div class="reg-box">
            <span>∞</span>
            Seguridad Criptográfica
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
