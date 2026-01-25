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
    'descripcion' => $metaEnProgreso ? $metaEnProgreso['descripcion'] : 'N/A',
    'objetivo' => $metaEnProgreso ? $metaEnProgreso['objetivo'] : 'N/A',
    'nombre' => $metaEnProgreso ? $metaEnProgreso['meta'] : 'N/A',
    'udv_otorgadas' => $metaEnProgreso ? $metaEnProgreso['udv_otorgadas'] : 0,
    'valor_udv' => $metaEnProgreso ? $metaEnProgreso['valor_udv'] : 0,
    'progreso_porcentaje' => $metaEnProgreso ? $metaEnProgreso['progress'] : 0,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EDU360 v10 | Interfaz de Evolución</title>
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

        /* Scrollbar Styling - Thinner and theme-adapted */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-blue);
            border-radius: 10px;
            box-shadow: 0 0 5px var(--primary-glow);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #00cef3;
        }
        
        /* Firefox support */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--primary-blue) var(--bg-dark);
        }
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
    </aside>

    <main class="main-chat">
        <div class="chat-history">
            <div class="message ai-message">
                <span class="status-badge">NODO VALIDADOR IA</span>
                <p>Bienvenido, Evolucionador. He analizado tu trayectoria previa. Para acuñar tus próximas **<?php echo $metaActualData['valor_udv']; ?> UDV** en el módulo de "<?php echo $metaActualData['nombre']; ?>",  necesito que demuestres dominio: 
                <br><br>
                <?php echo $metaActualData['descripcion']; ?>
                <br><br>
                <?php echo $metaActualData['objetivo']; ?>
                <br><br>
                ¿Cómo integrarías el concepto.</p>
            </div>
        </div>

        <div class="input-area">
            <div class="input-container">
                <textarea id="userInput" placeholder="Presenta tu evidencia de dominio aquí..." rows="3"></textarea>
                <button class="send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatHistory = document.querySelector('.chat-history');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');
    const udvNumber = document.querySelector('.udv-number');

    // Desactivar copiar, pegar y menú contextual en el input para asegurar 
    // que el evolucionador demuestre su conocimiento real.
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
        if (role === 'ai') {
            div.innerHTML = `<span class="status-badge">NODO VALIDADOR IA</span><p>${text}</p>`;
        } else {
            div.innerHTML = `<p>${text}</p>`;
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
                
                // Actualizar contadores
                if (data.udv_totales !== undefined) {
                    udvNumber.innerText = data.udv_totales;
                }

                // Si hubo progresión, recargar sidebar o avisar
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

    sendBtn.addEventListener('click', sendMessage);
});
</script>

</body>
</html>