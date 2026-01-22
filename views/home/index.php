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
            /*height: 180px;*/
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

        /* Beta Offer Section */
        .beta-offer-section {
            background: linear-gradient(135deg, rgba(0, 168, 232, 0.1) 0%, rgba(0, 80, 158, 0.2) 100%);
            border: 1px solid rgba(0, 168, 232, 0.3);
            border-radius: 30px;
            margin: 40px 0;
            padding: 40px;
            backdrop-filter: blur(15px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        .beta-offer-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 168, 232, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .beta-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            position: relative;
            z-index: 1;
        }
        .beta-content h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #fff;
        }
        .badge-beta {
            background: linear-gradient(45deg, #ffd700, #ff8c00);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            text-transform: uppercase;
            filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
        }
        .beta-content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
        }
        .beta-perks {
            display: flex;
            gap: 20px;
        }
        .beta-perks span {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .beta-perks i {
            color: #ffd700;
        }
        .beta-counter-box {
            background: rgba(0, 0, 0, 0.3);
            padding: 30px;
            border-radius: 25px;
            text-align: center;
            min-width: 250px;
            border: 1px solid rgba(0, 168, 232, 0.2);
        }
        .counter-display {
            font-size: 4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
            line-height: 1;
        }
        .counter-display .total {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.4);
        }
        .progress-container {
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin: 15px 0;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #00a8e8, #ffd700);
            box-shadow: 0 0 15px rgba(0, 168, 232, 0.5);
            transition: width 1.5s cubic-bezier(0.1, 0.7, 1.0, 0.1);
        }
        .remaining {
            font-weight: 600;
            color: #ffd700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        /* Beta Modal */
        .beta-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        .beta-modal.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            background: linear-gradient(135deg, #0a1128 0%, #001f3f 100%);
            border: 1px solid rgba(0, 168, 232, 0.3);
            border-radius: 30px;
            padding: 50px;
            max-width: 500px;
            width: 90%;
            position: relative;
            transform: scale(0.7);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }
        .beta-modal.active .modal-content {
            transform: scale(1);
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: color 0.3s;
        }
        .close-modal:hover {
            color: #fff;
        }
        .modal-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .modal-header i {
            font-size: 4rem;
            color: #ffd700;
            margin-bottom: 15px;
            animation: modalBounce 2s infinite;
        }
        @keyframes modalBounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        .modal-header h3 {
            font-size: 2rem;
            color: #fff;
        }
        .modal-body ul {
            list-style: none;
            padding: 0;
            margin: 20px 0 30px 0;
        }
        .modal-body li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }
        .modal-body li i {
            color: #00ff88;
        }
        .modal-body .btn-main {
            width: 100%;
            padding: 18px;
            font-size: 1.2rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            box-shadow: 0 10px 20px rgba(0, 168, 232, 0.3);
        }

        @media (max-width: 768px) {
            .beta-container {
                flex-direction: column;
                text-align: center;
            }
            .beta-perks {
                justify-content: center;
            }
            .beta-counter-box {
                width: 100%;
            }
        }
    </style>

    <section class="hero">
        <h1>Paradigma EDU360</h1>
        <p>Soberanía Cognitiva y Rigor Federal</p>
        <button class="btn-main" onclick="window.location.href='https://www.edu360global.org/university-institute/'">Conoce más</button>
    </section>

    <!-- Beta Offer Section -->
    <section class="beta-offer-section">
        <div class="beta-container">
            <div class="beta-content">
                <h2>Oferta Inicial: Nodos <span class="badge-beta">Beta</span></h2>
                <p>Sé uno de los primeros 100 y obtén un <strong>2% de participación</strong> en EDU360 University.</p>
                <div class="beta-perks">
                    <span><i class="fas fa-infinity"></i> Miembro Vitalicio</span>
                    <span><i class="fas fa-graduation-cap"></i> Todas las Carreras</span>
                </div>
            </div>
            <div class="beta-counter-box">
                <div class="counter-display">
                    <span id="beta-count"><?php echo $sum_tipo_nodo; ?></span><span class="total">/100</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 0%" data-target="<?php echo ($sum_tipo_nodo / 100) * 100; ?>%"></div>
                </div>
                <p class="remaining">¡Solo quedan <?php echo 100 - $sum_tipo_nodo; ?> cupos!</p>
            </div>
        </div>
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
                <img src="<?php echo img('home_cards/prog_etica.png'); ?>" alt="Ética en IA Educativa">
                <h3>Ética en IA Educativa</h3>
                <p>Humanismo en la era algorítmica.</p>
            </div>
            <div class="card">
                <img src="<?php echo img('home_cards/edu360universidad.png'); ?>" alt="Autogobierno Digital">
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
<!-- Beta Modal -->
<div id="beta-modal" class="beta-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-header">
            <i class="fas fa-rocket"></i>
            <h3>Oportunidad Única</h3>
        </div>
        <div class="modal-body">
            <p>Conviértete en un Nodo <strong class="badge-beta">Beta</strong> hoy mismo y asegura tu lugar en el futuro de la educación.</p>
            <ul>
                <li><i class="fas fa-check"></i> 2% de participación en el proyecto.</li>
                <li><i class="fas fa-check"></i> Acceso vitalicio a toda la oferta académica.</li>
                <li><i class="fas fa-check"></i> Estatus de Socio Fundador.</li>
            </ul>
            <button class="btn-main" onclick="window.location.href='<?php echo base_url('/registro'); ?>'">Adquirir Ahora</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Counter Animation
        const progressBar = document.querySelector('.progress-bar');
        if(progressBar) {
            setTimeout(() => {
                progressBar.style.width = progressBar.getAttribute('data-target');
            }, 500);
        }

        // Modal Logic
        const modal = document.getElementById('beta-modal');
        const closeBtn = document.querySelector('.close-modal');

        // Show modal after 2 seconds if not closed recently
        if(!localStorage.getItem('beta_modal_closed')) {
            setTimeout(() => {
                modal.classList.add('active');
            }, 2000);
        }

        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.classList.remove('active');
                localStorage.setItem('beta_modal_closed', 'true');
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('active');
                localStorage.setItem('beta_modal_closed', 'true');
            }
        }
    });
</script>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>