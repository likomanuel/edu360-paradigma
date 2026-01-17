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

        .input-container input {
            width: 100%;
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 20px 60px 20px 20px;
            border-radius: 30px;
            color: white;
            font-size: 1rem;
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
    </style>
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="logo"><i class="fas fa-brain"></i> EDU360 v10</div>
        <br>
        <div class="udv-counter">
            <div class="udv-label">Dominio Acuñado</div>
            <div class="udv-number">12.5</div>
            <div class="udv-label">UDV de 20.0</div>
        </div>
        
        <div class="progress-list">
            <p><small>METAS DEL DIPLOMADO</small></p>
            <div style="font-size: 0.85rem; color: #aaa;">
                <p><i class="fas fa-check-circle" style="color: var(--primary-blue);"></i> Neuroplasticidad Aplicada</p>
                <p><i class="fas fa-circle-notch"></i> Arquitectura de Sistemas</p>
                <p><i class="far fa-circle"></i> Ética Cognitiva</p>
            </div>
        </div>
    </aside>

    <main class="main-chat">
        <div class="chat-history">
            <div class="message ai-message">
                <span class="status-badge">NODO VALIDADOR IA</span>
                <p>Bienvenido, Evolucionador. He analizado tu trayectoria previa. Para acuñar tus próximas **2.5 UDV** en el módulo de "Soberanía Intelectual", necesito que demuestres dominio: 
                <br><br>
                ¿Cómo integrarías el concepto de **SRAA** en una empresa que actualmente mide el desempeño por horas-hombre? Describe el protocolo de transición.</p>
            </div>

            </div>

        <div class="input-area">
            <div class="input-container">
                <input type="text" placeholder="Presenta tu evidencia de dominio aquí...">
                <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </main>
</div>

</body>
</html>