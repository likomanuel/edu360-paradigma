<?php
require_once __DIR__ . '/../../config/modulo.php';
require_once __DIR__ . '/../../views/layouts/header.php';
$modulo = new Modulo();

$link = "pagos";
$caption = "Activar el Nodo";
$user = $modulo->getUser($_SESSION['email']);
if($user['estatus_soberania'] == 'Activo'){
    $link = "neuroeducacion";
    $caption = "IA & Neuroeducación";
}

?>
    <style>
        :root {
            --primary-blue: #00a8e8;
            --cyber-green: #00ff88;
            --bg-dark: #0a0a0a;
            --card-bg: #161616;
            --border-color: rgba(255, 255, 255, 0.05);
            --text-muted: #888;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            padding-bottom: 50px;
        }

        /* --- NAVEGACIÓN --- */
        header {
            background: #111;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo { font-weight: 700; color: var(--primary-blue); font-size: 0.9rem; }
        .user-nav { display: flex; align-items: center; gap: 15px; font-size: 0.8rem; }
        .udv-counter { background: rgba(0, 168, 232, 0.1); color: var(--primary-blue); padding: 5px 12px; border-radius: 20px; border: 1px solid var(--primary-blue); font-weight: bold; }

        /* --- GRID PRINCIPAL --- */
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .block {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--border-color);
            transition: 0.3s;
        }
        .block:hover { border-color: rgba(0, 168, 232, 0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }

        h2 { font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 0; display: flex; align-items: center; gap: 10px; color: var(--primary-blue); }

        /* --- 1. BLOQUE PERFIL (4 columnas) --- */
        .profile-block { grid-column: span 4; text-align: center; }
        .avatar-container { width: 80px; height: 80px; background: linear-gradient(45deg, var(--primary-blue), var(--cyber-green)); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        .user-hash { font-family: 'Fira Code', monospace; font-size: 0.7rem; color: var(--cyber-green); background: #000; padding: 5px; border-radius: 4px; display: block; margin-top: 10px; }
        .profile-data { margin-top: 20px; text-align: left; font-size: 0.85rem; }
        .data-item { margin-bottom: 10px; border-bottom: 1px solid #222; padding-bottom: 5px; }
        .data-item label { color: var(--text-muted); display: block; font-size: 0.7rem; }

        /* --- 2. MIS EVOLUCIONES (8 columnas) --- */
        .evolutions-block { grid-column: span 8; }
        .evolution-item { background: #1f1f1f; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px; }
        .evolution-icon { font-size: 1.5rem; color: var(--cyber-green); }
        .evolution-info { flex: 1; }
        .evolution-info h4 { margin: 0; font-size: 0.9rem; }
        .progress-bar { height: 6px; background: #333; border-radius: 3px; margin-top: 8px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--cyber-green); box-shadow: 0 0 10px var(--cyber-green); }

        /* --- 3. MIS LOGROS (6 columnas) --- */
        .achievements-block { grid-column: span 6; }
        .achievement-tag { display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.03); padding: 10px; border-radius: 6px; margin-bottom: 10px; border-left: 3px solid var(--primary-blue); font-size: 0.85rem; }
        .achievement-tag i { color: #ffd700; }

        /* --- 4. CERTIFICADOS (6 columnas) --- */
        .certificates-block { grid-column: span 6; }
        .cert-gallery { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px; }
        .cert-thumb { position: relative; border-radius: 6px; overflow: hidden; cursor: pointer; aspect-ratio: 16/9; border: 1px solid #333; }
        .cert-thumb img { width: 100%; height: 100%; object-fit: cover; opacity: 0.6; transition: 0.3s; }
        .cert-thumb:hover img { opacity: 1; transform: scale(1.05); }
        .cert-thumb .overlay { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.8); font-size: 0.6rem; padding: 5px; text-align: center; }

        /* --- RESPONSIVE --- */
        @media (max-width: 900px) {
            .profile-block, .evolutions-block, .achievements-block, .certificates-block {
                grid-column: span 12;
            }
        }

        @media (max-width: 600px) {
            .cert-gallery { grid-template-columns: 1fr; }
            .user-nav span { display: none; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo"><i class="fas fa-microchip"></i> PARADIGMA EDU360</div>
        <div class="udv-counter"><a  style="text-decoration: none; color: white;" href="<?php echo base_url($link); ?>"><?php echo $caption; ?></a></div>
        <div class="user-nav">
            <span class="udv-counter"><?php echo $user['total_udv_acumuladas']; ?> UDV</span>
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
        </div>
    </header>

    <main class="dashboard-container">
        
        <section class="block profile-block">
            <h2><i class="fas fa-id-card"></i> Perfil</h2>
            <div class="avatar-container">
                <i class="fas fa-user-astronaut"></i>
            </div>
            <strong><?php echo $user['nombre_completo']; ?></strong>
            <span class="user-hash">ID: <?php echo $user['hash_identidad']; ?></span>
            
            <div class="profile-data">
                <div class="data-item">
                    <label>Email Verificado</label>
                    <?php echo $user['email_verificado']; ?>
                </div>
                <div class="data-item">
                    <label>Estatus de Soberanía</label>
                    <span style="color: var(--cyber-green);"><?php echo $user['estatus_soberania']; ?></span>
                </div>
                <div class="data-item">
                    <label>Fecha de Ascensión</label>
                    <?php echo $user['creado_at']; ?>
                </div>
            </div>
        </section>

        <section class="block evolutions-block">
            <h2><i class="fas fa-dna"></i> Mis Evoluciones (En curso)</h2>
            
            <div class="evolution-item">
                <div class="evolution-icon"><i class="fas fa-brain"></i></div>
                <div class="evolution-info">
                    <h4>IA Generativa y Neurociencia Aplicada</h4>
                    <div class="progress-bar"><div class="progress-fill" style="width: 75%;"></div></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">75% Completado - Próximo hito: Auditoría de Jules</small>
                </div>
            </div>

            <div class="evolution-item">
                <div class="evolution-icon"><i class="fas fa-code-branch"></i></div>
                <div class="evolution-info">
                    <h4>Arquitecturas de Nodos Federales</h4>
                    <div class="progress-bar"><div class="progress-fill" style="width: 30%;"></div></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">30% Completado - Próximo hito: Despliegue en Staging</small>
                </div>
            </div>
        </section>

        <section class="block achievements-block">
            <h2><i class="fas fa-trophy"></i> Mis Logros</h2>
            <div class="achievement-tag">
                <i class="fas fa-certificate"></i>
                <span>Fundamentos de Soberanía Cognitiva</span>
            </div>
            <div class="achievement-tag">
                <i class="fas fa-shield-alt"></i>
                <span>Protocolo de El Inquisidor Nivel 1</span>
            </div>
            <div class="achievement-tag">
                <i class="fas fa-bolt"></i>
                <span>Primer Acuñado de UDV Exitoso</span>
            </div>
        </section>

        <section class="block certificates-block">
            <h2><i class="fas fa-file-contract"></i> Galería de Certificados</h2>
            <div class="cert-gallery">
                <div class="cert-thumb">
                    <img src="https://via.placeholder.com/300x180/111/00a8e8?text=Certificado+01" alt="Certificado">
                    <div class="overlay">Soberanía Digital v1.0</div>
                </div>
                <div class="cert-thumb">
                    <img src="https://via.placeholder.com/300x180/111/00ff88?text=Certificado+02" alt="Certificado">
                    <div class="overlay">Liderazgo en Redes</div>
                </div>
                <div class="cert-thumb">
                    <img src="https://via.placeholder.com/300x180/111/666?text=Proximamente" alt="Certificado">
                    <div class="overlay">En curso...</div>
                </div>
            </div>
        </section>

    </main>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
