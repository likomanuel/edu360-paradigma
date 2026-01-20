<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>
    <style>
        .grid-pillars {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 168, 232, 0.2);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }
        .card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-blue);
            box-shadow: 0 10px 30px rgba(0, 168, 232, 0.3);
        }
        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: transform 0.5s ease;
        }
        .card:hover img {
            transform: scale(1.05);
        }
        .card i {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
            display: block;
        }
    </style>

    <section class="hero">
        <h1>Paradigma EDU360</h1>
        <p>Soberanía Cognitiva y Rigor Federal</p>
        <button class="btn-main" onclick="window.location.href='https://www.edu360global.org/university-institute/'">Conoce más</button>
    </section>

    <section class="pillars">
        <h2>Nuestros Pilares</h2>
        <div class="grid-pillars">
            <div class="card">
                <img src="<?php echo img('home_cards/pilar_ia.png'); ?>" alt="IA Aplicada">
                <h3>IA aplicada</h3>
                <p>Implementación de inteligencia artificial en entornos educativos modernos.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/pilar_neurociencia.png'); ?>" alt="Neurociencia">
                <h3>Neurociencia</h3>
                <p>Estudio del aprendizaje basado en el funcionamiento del cerebro.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/pilar_infraestructura.png'); ?>" alt="Infraestructura">
                <h3>Infraestructura</h3>
                <p>Soporte técnico y tecnológico para la educación digital.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/pilar_liderazgo.png'); ?>" alt="Liderazgo">
                <h3>Liderazgo</h3>
                <p>Formación de líderes para la era de la información global.</p>
            </div>
        </div>
    </section>
  
    <section class="pillars">
        <h2>Programas</h2>
        <div class="grid-pillars">
            <div class="card">
                <img src="<?php echo img('home_cards/prog_neuroeducacion.png'); ?>" alt="Neuroeducación Aplicada">
                <h3>Neuroeducación Aplicada</h3>
                <p>Implementación de inteligencia artificial en entornos educativos modernos.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/prog_sraa.png'); ?>" alt="Sistema SRAA">
                <h3>Sistema SRAA</h3>
                <p>Progresión acumulativa sin retrocesos.</p>
            </div>
            <div class="card">
                <i class="fas fa-network-wired"></i>
                <h3>Ética en IA Educativa</h3>
                <p>Humanismo en la era algorítmica.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/edu360universidad.jpeg'); ?>" alt="Autogobierno Digital">
                <h3>Autogobierno Digital</h3>
                <p>Operación con mínima intervención humana.</p>
            </div>
        </div>
    </section>

<?php
if(isset($_SESSION['staging']) && $_SESSION['staging'] == true) {
?>
<div id="staging-trigger" onclick="toggleStagingMenu()">
    <i class="fas fa-terminal"></i>
    <span>STAGING PROMPT</span>
</div>

<div id="staging-panel" class="staging-panel">
    <div class="panel-header">
        <h3><i class="fas fa-shield-alt"></i> SISTEMA EL INQUISIDOR</h3>
        <button onclick="toggleStagingMenu()">&times;</button>
    </div>

    <div class="panel-body">
        <div class="control-group">
            <label>1. Autoridad Visual</label>
            <button class="btn-tool" onclick="testAuthority()">Validar H1 "Death of Diploma"</button>
        </div>

        <div class="control-group">
            <label>2. Stress-Test: Jules (Inquisidor)</label>
            <form action="InquisidorAgente.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="audit_file" class="input-dark">
                <button type="submit" class="btn-action">Cargar para Auditoría</button>
            </form>
            <div class="status-indicator">Min. 90% Densidad Técnica</div>
        </div>

        <div class="control-group">
            <label>3. Simulación Financiera (Test Mode)</label>
            <div class="flex-row">
                <button class="btn-payment stripe" onclick="simulatePayment('Stripe')">Stripe</button>
                <button class="btn-payment paypal" onclick="simulatePayment('PayPal')">PayPal</button>
            </div>
        </div>

        <div class="control-group">
            <label>4. Visualización de Nodos</label>
            <a href="nodos_grid.php" class="btn-link">Abrir "The Grid" (100 Slots)</a>
        </div>

        <div class="control-group">
            <label>5. Log de Auditoría Técnica</label>
            <div class="log-viewer">
                <code>[System]: Esperando conexión con PDO...</code>
                <code>[Auth]: Fase de Auditoría en curso.</code>
            </div>
            <a href="staging_audit.log" target="_blank" class="btn-log">Ver log completo</a>
        </div>
    </div>
</div>
<script>
    function toggleStagingMenu() {
        document.getElementById('staging-panel').classList.toggle('active');
    }

    function testAuthority() {
        // Simulación de cambio de H1 para validar estilos de branding
        const h1 = document.querySelector('h1');
        const sub = document.querySelector('.hero p');
        if(h1) h1.innerText = "THE DEATH OF THE DIPLOMA: THE SOVEREIGN NETWORK IS LIVE.";
        if(sub) sub.innerText = "Fase de Auditoría de Nodos en curso. Acceso restringido bajo protocolo de El Inquisidor.";
        alert("Modo de Autoridad Activado para Pruebas Visuales.");
    }

    function simulatePayment(platform) {
        if(confirm("¿Simular pago exitoso vía " + platform + " (Test Mode)?")) {
            alert("Pago Procesado. Ejecutando Trigger: Creando entrada en nodos_activos...");
            // Aquí llamarías a un script PHP vía Fetch para el post-pago
        }
    }
</script>
<?php
}
?>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>