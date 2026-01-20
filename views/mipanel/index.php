<?php
if(!isset($_SESSION['email'])){
    header("Location: " . base_url("/session") );
    exit();
}
//require_once __DIR__ . '/../../views/layouts/header.php';
$modulo = new Modulo();

use App\Controllers\NeuroEducacionController;

$neuroEducacionController = new NeuroEducacionController();

$link = "pagos";
$caption = "Activar el Nodo";
$verificado = 0;
$user = $modulo->getUser($_SESSION['email']);
if($user['estatus_soberania'] == 'Activo'){
    $link = "neuroeducacion";
    $caption = "IA & Neuroeducación";
}
if($user['verificado'] == 1){
    $verificado = 100;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="<?php echo css('style.css'); ?>" />
    <link rel="stylesheet" href="<?php echo css('sweetalert2.css'); ?>" />
    <script src="<?php echo js('jquery-3.5.1.js'); ?>"></script>
    <script src="<?php echo js('sweetalert2.js'); ?>"></script>
    </head>
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

        /* --- MODAL CSS --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #111;
            border: 1px solid var(--primary-blue);
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 0 30px rgba(0, 168, 232, 0.3);
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
            color: var(--primary-blue);
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 1px;
            border-bottom: 1px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            background: #000;
            border: 1px solid #333;
            color: white;
            padding: 10px;
            border-radius: 4px;
            font-family: inherit;
            font-size: 0.9rem;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: var(--primary-blue);
            outline: none;
            box-shadow: 0 0 10px rgba(0, 168, 232, 0.2);
        }

        .form-group input[readonly] {
            color: #666;
            border-color: #222;
        }

        .modal-footer {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            border: none;
            transition: 0.3s;
        }

        .btn-cancel { background: #222; color: white; }
        .btn-cancel:hover { background: #333; }
        .btn-proceed { background: var(--primary-blue); color: #000; }
        .btn-proceed:hover { background: #008fca; box-shadow: 0 0 20px rgba(0, 168, 232, 0.4); }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: var(--text-muted);
            cursor: pointer;
        }
    </style>
<body>

    <header>
        <div class="logo" style="text-transform: uppercase;"><i class="fas fa-microchip"></i> <?php echo $neuroEducacionController->artefactoActivo($user['id_evolucionador'])['nombre']; ?></div>
        <div class="aula-counter">
            <a  style="text-decoration: none; color: white;" href="<?php echo base_url('/'); ?>">Home</a>            
        </div>
        <div class="udv-counter">
            <a  style="text-decoration: none; color: white;" href="<?php echo base_url($link); ?>"><?php echo $caption; ?></a>            
        </div>
        <div class="aula-counter">
            <a  style="text-decoration: none; color: white;" href="<?php echo base_url('aula'); ?>">Aula Virtual</a>            
        </div>
        <div class="user-nav">
            <span class="udv-counter"><?php echo $user['total_udv_acumuladas']; ?> UDV</span>
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
        </div>
    </header>

    <main class="dashboard-container">
        
        <section class="block profile-block">
            <h2><i class="fas fa-id-card"></i> Perfil</h2>
            <div class="avatar-container" onclick="document.getElementById('file-upload').click();" style="cursor: pointer; overflow: hidden; position: relative;" title="Haz clic para cambiar foto">
                <?php if(!empty($user['foto'])): ?>
                    <img src="<?php echo base_url('public/users/'.$user['hash_identidad'].'/perfil/'.$user['foto']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user-astronaut"></i>
                <?php endif; ?>
                <div style="position: absolute; bottom: 0; background: rgba(0,0,0,0.5); width: 100%; font-size: 0.5rem; padding: 2px 0;">EDITAR</div>
            </div>
            <input type="file" id="file-upload" style="display: none;" accept="image/*" onchange="uploadFoto(this);">

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

            <?php 
            $evoluciones = $neuroEducacionController->misEvoluciones($user['id_evolucionador']);
            
            // Especial: Verificación de Identidad (se mantiene como primer hito manual por ahora)
            ?>
            <div class="evolution-item">
                <div class="evolution-icon"><i class="fas fa-user-shield"></i></div>
                <div class="evolution-info">
                    <h4>Verificación de Identidad 
                        <span style="color: var(--text-muted); font-size: 0.7rem; cursor: pointer; border-bottom: 1px solid var(--text-muted);" onclick="verificarIdentidad('<?php if($user['verificado'] == 0){ echo $user['hash_identidad']; }else{echo 'verificado';}?>')">Verificar</span></h4>
                    <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $verificado; ?>%;"></div></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;"><?php echo $verificado; ?>% Completado - Próximo hito: Auditoría de Jules</small>
                </div>
            </div>

            <?php 
            if (empty($evoluciones)): 
            ?>
            <div class="evolution-item" style="opacity: 0.5;">
                <div class="evolution-icon"><i class="fas fa-lock"></i></div>
                <div class="evolution-info">
                    <h4>Nodos de Aprendizaje</h4>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">Activa un nodo para comenzar tu evolución.</small>
                </div>
            </div>
            <?php 
            else:
                foreach($evoluciones as $evo): 
                    $isLocked = ($evo['status'] === 'Bloqueado');
                    $isCulminado = ($evo['status'] === 'Culminado');
                    $icon = $isCulminado ? 'fa-check-circle' : ($isLocked ? 'fa-lock' : 'fa-brain');
                    $color = $isCulminado ? 'var(--cyber-green)' : ($isLocked ? 'var(--text-muted)' : 'var(--primary-blue)');
            ?>
            <div class="evolution-item" style="<?php echo $isLocked ? 'opacity: 0.5;' : ''; ?>">
                <div class="evolution-icon" style="color: <?php echo $color; ?>;"><i class="fas <?php echo $icon; ?>"></i></div>
                <div class="evolution-info">
                    <h4><?php echo $evo['meta']; ?></h4>
                    <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $evo['progress']; ?>%; background: <?php echo $color; ?>; box-shadow: 0 0 10px <?php echo $color; ?>;"></div></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">
                        <?php echo round($evo['progress']); ?>% Completado - <?php echo $evo['status']; ?>
                    </small>
                </div>
            </div>
            <?php 
                endforeach; 
            endif; 
            ?>
        </section>

        <section class="block achievements-block">
            <h2><i class="fas fa-trophy"></i> Mis Logros</h2>
            <?php 
            $logros = $neuroEducacionController->logrosEvolucionador($user['id_evolucionador']);
            if (empty($logros)):
            ?>
            <div class="achievement-tag" style="border-left: 3px solid var(--text-muted);">
                <i class="fas fa-info-circle" style="color: var(--text-muted);"></i>
                <span>No hay logros registrados aún.</span>
            </div>
            <?php 
            else:
                foreach($logros as $logro): 
                    $isCulminado = ($logro['estatus'] == 'Culminado');
                    $statusLabel = $isCulminado ? 'Culminado' : 'En Proceso';
                    $borderStyle = $isCulminado ? 'border-left: 3px solid var(--cyber-green);' : 'border-left: 3px solid var(--primary-blue);';
                    $iconClass = $isCulminado ? 'fa-check-circle' : 'fa-spinner fa-spin';
                    $iconColor = $isCulminado ? 'var(--cyber-green)' : 'var(--primary-blue)';
            ?>
            <div class="achievement-tag" style="<?php echo $borderStyle; ?>">
                <i class="fas <?php echo $iconClass; ?>" style="color: <?php echo $iconColor; ?>;"></i>
                <span><?php echo $logro['nombre'] . ' - ' . $logro['nivel_trayectoria'] . ' (' . $statusLabel . ')'; ?></span>
            </div>
            <?php 
                endforeach; 
            endif; 
            ?>
        </section>

        <section class="block certificates-block">
            <h2><i class="fas fa-file-contract"></i> Galería de Certificados</h2>
            <div class="cert-gallery">
                <?php 
                $certDir = __DIR__ . "/../../public/users/" . $user['hash_identidad'] . "/certificados/";
                $hasCerts = false;
                if (is_dir($certDir)) {
                    $certs = glob($certDir . "*.png");
                    foreach ($certs as $certPath) {
                        $hasCerts = true;
                        $certFile = basename($certPath);
                        $certUrl = base_url("public/users/" . $user['hash_identidad'] . "/certificados/" . $certFile);
                        // Limpiar el nombre para el overlay (quitar 'Certificado_', extension y timestamps)
                        $displayName = str_replace(['Certificado_', '.png'], '', $certFile);
                        if (strpos($displayName, '_') !== false) {
                            $parts = explode('_', $displayName);
                            $displayName = "Módulo " . $parts[0];
                        }
                ?>
                <div class="cert-thumb" onclick="window.open('<?php echo $certUrl; ?>', '_blank')">
                    <img src="<?php echo $certUrl; ?>" alt="Certificado">
                    <div class="overlay"><?php echo $displayName; ?></div>
                </div>
                <?php 
                    }
                } 
                
                if (!$hasCerts):
                ?>
                <div class="cert-thumb" style="opacity: 0.3; cursor: default;">
                    <img src="https://via.placeholder.com/300x180/111/444?text=Sin+Certificados" alt="Proximamente">
                    <div class="overlay">En curso...</div>
                </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- Modal de Verificación -->
    <div id="verifyModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></span>
            <h3><i class="fas fa-user-shield"></i> Protocolo de Verificación</h3>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 20px;">
                Para certificar tu identidad en la red Paradigma, Jules realizará una auditoría OSINT basada en los siguientes datos:
            </p>
            
            <form id="verifyForm">
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" value="<?php echo $user['nombre_completo']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Ciudad o Región</label>
                    <input type="text" name="ciudad" placeholder="Ej: Madrid, España" required>
                </div>
                <div class="form-group">
                    <label>Gremio o Profesión</label>
                    <input type="text" name="profesion" placeholder="Ej: Ingeniero de Software" required>
                </div>
                <div class="form-group">
                    <label>Empresa o Institución</label>
                    <input type="text" name="empresa" placeholder="Ej: Google Cloud">
                </div>
                <div class="form-group">
                    <label>Usuario de Red Social (LinkedIn/Twitter)</label>
                    <input type="text" name="red_social" placeholder="Ej: @username o link al perfil">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Abortar</button>
                    <button type="submit" class="btn btn-proceed">Proceder con Auditoría</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function verificarIdentidad(status) {
        if (status === 'verificado') {
            alert('Tu identidad ya ha sido auditada y certificada.');
            return;
        }
        document.getElementById('verifyModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('verifyModal').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        let modal = document.getElementById('verifyModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    document.getElementById('verifyForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = this.querySelector('.btn-proceed');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-sync fa-spin"></i> Auditando...';
        btn.disabled = true;

        fetch('<?php echo base_url("public/procesar_verificacion.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Auditoría completada. Los resultados han sido registrados.');
                location.reload();
            } else {
                alert('Error en auditoría: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Fallo en la conexión con el nodo auditor.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    };

    function uploadFoto(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('foto', input.files[0]);

            fetch('<?php echo base_url("public/upload_foto.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al subir la foto.');
            });
        }
    }
    </script>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
