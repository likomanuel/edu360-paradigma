<?php
if(!isset($_SESSION['email'])){
    header("Location: " . base_url("/session") );
    exit();
}
$modulo = new Modulo();

use App\Controllers\NeuroEducacionController;
$neuroEducacionController = new NeuroEducacionController();

$user = $modulo->getUser($_SESSION['email']);
$evoluciones = $neuroEducacionController->misEvoluciones($user['id_evolucionador']);

// Encontrar la meta activa (la primera 'En Proceso')
$metaEnProgreso = null;
foreach ($evoluciones as $evo) {
    if ($evo['status'] === 'En Proceso') {
        $metaEnProgreso = $evo;
        break;
    }
}

// Variable para futuros usos de comunicacion
$metaActualData = [
    'id' => $metaEnProgreso ? $metaEnProgreso['id'] : 0,
    'descripcion' => $metaEnProgreso ? $metaEnProgreso['descripcion'] : 'N/A',
    'objetivo' => $metaEnProgreso ? $metaEnProgreso['objetivo'] : 'N/A',
    'nombre' => $metaEnProgreso ? $metaEnProgreso['meta'] : 'N/A',
    'udv_otorgadas' => $metaEnProgreso ? $metaEnProgreso['udv_otorgadas'] : 0,
    'valor_udv' => $metaEnProgreso ? $metaEnProgreso['valor_udv'] : 0,
    'progreso_porcentaje' => $metaEnProgreso ? $metaEnProgreso['progress'] : 0,
];

// Obtener historial de la meta actual
$historialMetaActual = [];
if ($metaActualData['id'] > 0) {
    $historialMetaActual = $neuroEducacionController->getFullHistorial($user['id_evolucionador'], $metaActualData['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EDU360 v10 | Interfaz de Evolución</title>
    <link rel="icon" type="image/x-icon" href="<?php echo img('favicon/university/favicon.ico'); ?>" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Heredamos tus estilos base y añadimos la interfaz de chat */
        :root {
            --primary-blue: #00a8e8;
            --primary-glow: rgba(0, 168, 232, 0.3);
            --bg-dark: #0a0a0a;
            --card-bg: rgba(20, 20, 20, 0.9);
            --cyber-green: #00ff88;
        }
        
        body { font-family: 'Montserrat', sans-serif; background: var(--bg-dark); color: white; margin: 0; overflow: hidden; }

        .app-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: 100vh;
        }

        /* Sidebar de Progreso */
        .sidebar {
            background: #111;
            border-right: 1px solid #222;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .udv-counter {
            background: linear-gradient(45deg, #001f2d, #004a66);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--primary-blue);
            box-shadow: 0 0 20px var(--primary-glow);
            margin-bottom: 30px;
        }

        .udv-number { font-size: 3rem; font-weight: 700; color: var(--primary-blue); }
        .udv-label { font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; }

        /* Área de Chat / Auditoría */
        .main-chat {
            overflow-y: scroll;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            position: relative;
        }

        .chat-history {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            max-width: 80%;
            padding: 20px;
            border-radius: 15px;
            line-height: 1.6;
            animation: fadeIn 0.5s ease;
        }

        .ai-message {
            align-self: flex-start;
            background: var(--card-bg);
            border-left: 4px solid var(--primary-blue);
            backdrop-filter: blur(10px);
        }

        .user-message {
            align-self: flex-end;
            background: var(--primary-blue);
            color: black;
            font-weight: 500;
        }

        /* Input de Evidencia */
        .input-area {
            padding: 30px;
            background: rgba(0,0,0,0.8);
            border-top: 1px solid #222;
        }

        .input-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .input-container textarea {
            width: 100%;
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 20px 60px 20px 20px;
            border-radius: 20px;
            color: white;
            font-size: 1rem;
            resize: vertical;
            min-height: 60px;
            max-height: 200px;
            font-family: 'Montserrat', sans-serif;
        }

        .send-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-blue);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 5px 10px;
            border-radius: 20px;
            background: #222;
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
        }

        /* Evolution Log Drawer */
        .evolution-drawer {
            position: fixed;
            right: -450px;
            top: 0;
            width: 450px;
            height: 100vh;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid #333;
            z-index: 1000;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
        }

        .evolution-drawer.active {
            right: 0;
        }

        .drawer-header {
            padding: 25px;
            border-bottom: 1px solid #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .history-item {
            margin-bottom: 25px;
            border-bottom: 1px solid #222;
            padding-bottom: 15px;
        }

        .history-meta-tag {
            font-size: 0.65rem;
            color: var(--primary-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }

        .history-text {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 5px 0;
        }

        .history-time {
            font-size: 0.7rem;
            opacity: 0.4;
        }

        /* Buttons */
        .btn-history {
            background: transparent;
            border: 1px solid #333;
            color: white;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: auto;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-history:hover {
            border-color: var(--primary-blue);
            background: var(--primary-glow);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: var(--primary-blue); border-radius: 10px; }
    </style>
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="logo"><i class="fas fa-brain"></i> EDU360 v10 <a href="<?php echo base_url('mipanel'); ?>" style="color: var(--primary-blue); font-size: 0.8rem; text-decoration: none; margin-left: 10px;"><i class="fas fa-arrow-left"></i> Volver</a></div>
        <br>
        <div class="udv-counter">
            <div class="udv-label">Dominio Acuñado</div>
            <div class="udv-number"><?php echo round($metaActualData['udv_otorgadas'], 2); ?></div>
            <div class="udv-label">UDV de <?php echo $metaActualData['valor_udv']; ?></div>
        </div>
        
        <div class="progress-list">
            <p><small>METAS DEL DIPLOMADO</small></p>
            <div style="font-size: 0.85rem; color: #aaa;">
                <?php foreach($evoluciones as $evo): 
                    $isCulminado = ($evo['status'] === 'Culminado');
                    $isLocked = ($evo['status'] === 'Bloqueado');
                    $icon = $isCulminado ? 'fa-check-circle' : ($isLocked ? 'fa-circle' : 'fa-circle-notch fa-spin');
                    $color = $isCulminado ? 'var(--cyber-green)' : ($isLocked ? '#444' : 'var(--primary-blue)');
                ?>
                    <p style="<?php echo $isLocked ? 'opacity: 0.4;' : ''; ?>">
                        <i class="fas <?php echo $icon; ?>" style="color: <?php echo $color; ?>;"></i> 
                        <?php echo $evo['meta']; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="btn-history" id="openDrawer">
            <i class="fas fa-history"></i> Bitácora de Evolución
        </button>
    </aside>

    <main class="main-chat">
        <div class="chat-history">
            <div class="message ai-message">
                <span class="status-badge">NODO VALIDADOR IA</span>
                <p>Bienvenido, Evolucionador. He analizado tu trayectoria previa. Para acuñar tus próximas **<?php echo $metaActualData['valor_udv']; ?> UDV** en el módulo de "<?php echo $metaActualData['nombre']; ?>", necesito que demuestres dominio: 
                <br><br>
                <?php echo $metaActualData['descripcion']; ?>
                <br><br>
                <?php echo $metaActualData['objetivo']; ?>
                <br><br>
                ¿Cómo integrarías estos conceptos en tu práctica educativa?</p>
            </div>

            <?php foreach($historialMetaActual as $msg): ?>
                <div class="message <?php echo ($msg['role'] === 'user' ? 'user-message' : 'ai-message'); ?>">
                    <?php if($msg['role'] === 'assistant'): ?>
                        <span class="status-badge">NODO VALIDADOR IA</span>
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($msg['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="input-area">
            <div class="input-container">
                <textarea id="userInput" placeholder="Presenta tu evidencia de dominio aquí..." rows="3"></textarea>
                <button class="send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </main>
</div>

<!-- Drawer de Historial -->
<div class="evolution-drawer" id="historyDrawer">
    <div class="drawer-header">
        <h3 style="margin:0"><i class="fas fa-stream"></i> Registro de Evolución</h3>
        <button id="closeDrawer" style="background:none; border:none; color:white; cursor:pointer; font-size:1.2rem"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-content" id="drawerContent">
        <p style="text-align:center; opacity:0.5">Cargando bitácora...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatHistory = document.querySelector('.chat-history');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');
    const udvNumber = document.querySelector('.udv-number');
    const historyDrawer = document.getElementById('historyDrawer');
    const openDrawer = document.getElementById('openDrawer');
    const closeDrawer = document.getElementById('closeDrawer');
    const drawerContent = document.getElementById('drawerContent');

    // Scroll al final del chat al cargar
    chatHistory.scrollTop = chatHistory.scrollHeight;

    // Desactivar copiar, pegar y menú contextual en el input
    ['copy', 'paste', 'cut', 'contextmenu'].forEach(event => {
        userInput.addEventListener(event, (e) => {
            e.preventDefault();
            userInput.style.borderColor = 'red';
            setTimeout(() => userInput.style.borderColor = '#333', 500);
        });
    });

    function appendMessage(role, text) {
        const div = document.createElement('div');
        div.className = `message ${role}-message`;
        if (role === 'ai' || role === 'assistant') {
            div.innerHTML = `<span class="status-badge">NODO VALIDADOR IA</span><p>${text.replace(/\n/g, '<br>')}</p>`;
        } else {
            div.innerHTML = `<p>${text.replace(/\n/g, '<br>')}</p>`;
        }
        chatHistory.appendChild(div);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    async function sendMessage() {
        const msg = userInput.value.trim();
        if (!msg) return;

        appendMessage('user', msg);
        userInput.value = '';
        userInput.disabled = true;
        sendBtn.disabled = true;

        try {
            const response = await fetch('<?php echo base_url("procesar_auditoria.php"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensaje: msg })
            });

            const data = await response.json();

            if (data.error) {
                appendMessage('ai', 'Error: ' + data.error);
            } else {
                appendMessage('ai', data.mensaje);
                
                if (data.udv_totales !== undefined) {
                    udvNumber.innerText = parseFloat(data.udv_totales).toFixed(2);
                }

                if (data.progresion) {
                    appendMessage('ai', '<i class="fas fa-medal"></i> **¡META ACUÑADA!** Has desbloqueado el siguiente nivel. Recargando tu trayectoria...');
                    setTimeout(() => location.reload(), 3000);
                }
            }
        } catch (e) {
            appendMessage('ai', 'Error de conexión con el Nodo Validador.');
        } finally {
            userInput.disabled = false;
            sendBtn.disabled = false;
            userInput.focus();
        }
    }

    async function loadFullHistory() {
        drawerContent.innerHTML = '<p style="text-align:center; opacity:0.5">Sincronizando con el archivo cerebral...</p>';
        try {
            const response = await fetch('<?php echo base_url("obtener_historial_completo.php"); ?>');
            const data = await response.json();
            
            if (data.length === 0) {
                drawerContent.innerHTML = '<p style="text-align:center; opacity:0.5; margin-top:20px">No hay registros previos en esta trayectoria.</p>';
                return;
            }

            drawerContent.innerHTML = '';
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'history-item';
                div.innerHTML = `
                    <span class="history-meta-tag">${item.meta_nombre || 'General'}</span>
                    <p class="history-text"><strong>${item.role === 'user' ? 'Tú' : 'IA'}:</strong> ${item.content}</p>
                    <span class="history-time"><i class="far fa-clock"></i> ${new Date(item.created_at).toLocaleString()}</span>
                `;
                drawerContent.appendChild(div);
            });
        } catch (e) {
            drawerContent.innerHTML = '<p style="text-align:center; color:red">Error al cargar la bitácora.</p>';
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    openDrawer.addEventListener('click', () => {
        historyDrawer.classList.add('active');
        loadFullHistory();
    });

    closeDrawer.addEventListener('click', () => {
        historyDrawer.classList.remove('active');
    });
});
</script>

</body>
</html>