<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['email'])){
    header("Location: " . base_url("/session") );
    exit();
}
require_once __DIR__ . '/../../config/modulo.php';
$modulo = new Modulo();

?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula Virtual Inmersiva - Paradigma EDU360</title>
    <link rel="icon" type="image/x-icon" href="<?php echo img('favicon/university/favicon.ico'); ?>" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-blue: #00a8e8;
            --cyber-green: #00ff88;
            --bg-dark: #050505;
            --panel-bg: #121212;
            --border-color: rgba(0, 168, 232, 0.2);
            --text-muted: #888;
            --header-height: 70px;
        }

        /* Scrollbar personalizado para el tema oscuro */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-blue); }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Evita scroll doble */
        }

        /* --- HEADER (Consistente) --- */
        header {
            height: var(--header-height);
            background: rgba(10, 10, 10, 0.9);
            padding: 0 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            box-sizing: border-box;
        }
        .logo { font-weight: 700; color: var(--primary-blue); display: flex; align-items: center; gap: 10px; }
        .classroom-status { font-family: 'Fira Code', monospace; font-size: 0.75rem; color: var(--cyber-green); border: 1px solid var(--cyber-green); padding: 4px 10px; border-radius: 4px; }

        /* --- LAYOUT PRINCIPAL DEL AULA --- */
        .classroom-layout {
            flex: 1;
            display: flex;
            height: calc(100vh - var(--header-height));
            position: relative;
        }

        /* --- BLOQUE IZQUIERDO: NAVEGADOR DE CONTENIDO --- */
        .content-sidebar {
            width: 320px;
            background: var(--panel-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #222;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--primary-blue);
        }

        .resource-list {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            flex: 1;
        }

        .resource-item {
            padding: 15px 20px;
            border-bottom: 1px solid #1a1a1a;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .resource-item:hover { background: rgba(0, 168, 232, 0.05); padding-left: 25px; }
        .resource-item.active { background: rgba(0, 168, 232, 0.1); border-right: 3px solid var(--primary-blue); }
        .resource-item i { color: var(--primary-blue); margin-top: 4px; }
        .resource-title { font-size: 0.9rem; font-weight: 500; display: block; margin-bottom: 4px; }
        .resource-meta { font-size: 0.7rem; color: var(--text-muted); font-family: 'Fira Code', monospace; }

        /* --- ÁREA DERECHA PRINCIPAL --- */
        .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #080808;
        }

        /* BLOQUE DERECHA SUPERIOR: VISOR PDF */
        .viewer-container {
            flex: 2; /* Ocupa 2/3 del espacio vertical disponible */
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .viewer-frame {
            flex: 1;
            border: 1px solid var(--border-color);
            box-shadow: inset 0 0 30px rgba(0, 168, 232, 0.1);
            background: #000;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Iframe para el PDF */
        #pdf-frame { width: 100%; height: 100%; border: none; display: none; /* Oculto al inicio */ }

        /* Estado vacío (placeholder) */
        .empty-state {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; color: var(--primary-blue); }

        /* BLOQUE DERECHA INFERIOR: NOTAS */
        .notes-section {
            flex: 1; /* Ocupa 1/3 del espacio */
            background: var(--panel-bg);
            border-top: 2px solid var(--border-color);
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-height: 250px; /* Altura mínima */
        }

        .notes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--cyber-green);
            font-family: 'Fira Code', monospace;
        }
        
        .notes-toolbar i { margin-left: 15px; cursor: pointer; color: var(--text-muted); transition: 0.3s; }
        .notes-toolbar i:hover { color: var(--primary-blue); }

        .notes-input {
            flex: 1;
            background: #0a0a0a;
            border: 1px solid #333;
            color: #ddd;
            padding: 15px;
            font-family: 'Fira Code', 'Courier New', monospace;
            resize: none; /* Evita que el usuario rompa el layout */
            font-size: 0.9rem;
            border-radius: 4px;
            outline: none;
            transition: 0.3s;
        }
        .notes-input:focus { border-color: var(--primary-blue); box-shadow: 0 0 15px rgba(0, 168, 232, 0.2); }

        /* --- RESPONSIVE --- */
        @media (max-width: 900px) {
            .classroom-layout { flex-direction: column; height: auto; overflow-y: auto; }
            .content-sidebar { width: 100%; height: 300px; border-right: none; border-bottom: 1px solid var(--border-color); }
            .viewer-container { height: 500px; flex: none; }
            .notes-section { height: 300px; flex: none; }
            body { overflow: auto; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo"><i class="fas fa-brain"></i> AULA INMERSIVA EDU360 <a href="<?php echo base_url('mipanel'); ?>" style="color: var(--primary-blue); font-size: 0.8rem; text-decoration: none; margin-left: 10px;"><i class="fas fa-arrow-left"></i> Volver</a></div>
        <div class="classroom-status">
            <i class="fas fa-wifi"></i> CONEXIÓN NEURONAL: ESTABLE
        </div>
    </header>

    <main class="classroom-layout">
        
        <aside class="content-sidebar">
            <div class="sidebar-header">
                <i class="fas fa-layer-group"></i> Módulos de Estudio
            </div>
            <ul class="resource-list">
                <li class="resource-item" onclick="loadPDF('<?php echo base_url('public/pdf/el_paradigma_edu360.pdf'); ?>', this)">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <span class="resource-title">El Paradigma de la Educación EDU360</span>
                        <span class="resource-meta">PDF | 2.4 MB | Lectura Obligatoria</span>
                    </div>
                </li>
                <li class="resource-item" onclick="loadPDF('<?php echo base_url('public/pdf/DM-01- El Manifiesto del Soberano.pdf'); ?>', this)">
                    <i class="fas fa-file-alt"></i>
                    <div>
                        <span class="resource-title">El Manifiesto del Soberano</span>
                        <span class="resource-meta">PDF | 286 KB | Técnico</span>
                    </div>
                </li>
                <li class="resource-item" onclick="loadPDF('https://unec.edu.az/application/uploads/2014/12/pdf-sample.pdf', this)">
                    <i class="fas fa-microchip"></i>
                    <div>
                        <span class="resource-title">IA y Neuroeducación: La Nueva Frontera</span>
                        <span class="resource-meta">PDF | 3.8 MB | Avanzado</span>
                    </div>
                </li>
                <li class="resource-item fake">
                    <i class="fas fa-lock"></i>
                    <div>
                        <span class="resource-title" style="color: #666;">Protocolo de El Inquisidor (Bloqueado)</span>
                        <span class="resource-meta">Requiere Nivel 2</span>
                    </div>
                </li>
            </ul>
        </aside>

        <section class="main-area">
            
            <div class="viewer-container">
                <div class="viewer-frame">
                    <div class="empty-state" id="viewer-placeholder">
                        <i class="fas fa-satellite-dish"></i>
                        <h3>Esperando Selección de Datos</h3>
                        <p>Seleccione un módulo del panel izquierdo para iniciar la transmisión.</p>
                    </div>
                    <iframe id="pdf-frame" src=""></iframe>
                </div>
            </div>

            <div class="notes-section">
                <div class="notes-header">
                    <span><i class="fas fa-terminal"></i> Bitácora de Investigación Personal</span>
                    <div class="notes-toolbar">
                        <i class="fas fa-save" id="btn-save-note" title="Guardar en Nodo Local"></i>
                        <i class="fas fa-eraser" id="btn-clear-note" title="Limpiar Bitácora"></i>
                        <i class="fas fa-share-alt" id="btn-export-note" title="Exportar a la Red"></i>
                    </div>
                </div>
                <textarea class="notes-input" id="notes-textarea" placeholder="> Ingrese sus observaciones y análisis aquí..."></textarea>
            </div>

        </section>
    </main>

    <script>
        /**
         * Función para cargar PDFs en el visor
         * @param {string} url - La URL del archivo PDF
         * @param {HTMLElement} element - El elemento de la lista que se clickeó
         */
        function loadPDF(url, element) {
            const frame = document.getElementById('pdf-frame');
            const placeholder = document.getElementById('viewer-placeholder');
            const items = document.querySelectorAll('.resource-item');

            // 1. Actualizar interfaz activa
            items.forEach(item => item.classList.remove('active'));
            if (!element.classList.contains('fake')) {
                 element.classList.add('active');

                // 2. Mostrar efecto de "Cargando" (Opcional, para más show)
                placeholder.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i><h3>Estableciendo enlace seguro...</h3>';
                placeholder.style.display = 'flex';
                frame.style.display = 'none';

                // 3. Simular pequeña demora y cargar
                setTimeout(() => {
                    frame.src = url;
                    placeholder.style.display = 'none';
                    frame.style.display = 'block';
                }, 800); // 800ms de drama tecnológico
            }
        }
        
        // Nota sobre PDFs:
        // Algunos navegadores pueden bloquear la carga de PDFs externos en iframes por políticas de seguridad (CORS/X-Frame-Options).
        // He usado PDFs de ejemplo públicos que suelen funcionar. En producción, los PDFs deben servirse desde tu mismo dominio.

        // --- LÓGICA DE LA BITÁCORA ---
        const notesTextarea = document.getElementById('notes-textarea');
        const btnSave = document.getElementById('btn-save-note');
        const btnClear = document.getElementById('btn-clear-note');
        const btnExport = document.getElementById('btn-export-note');

        // 1. Cargar nota guardada
        window.addEventListener('DOMContentLoaded', () => {
            const savedNote = localStorage.getItem('edu360_aula_notes');
            if (savedNote) {
                notesTextarea.value = savedNote;
            }
        });

        // 2. Auto-guardado
        notesTextarea.addEventListener('input', () => {
            localStorage.setItem('edu360_aula_notes', notesTextarea.value);
        });

        // 3. Botón Guardar (Manual)
        btnSave.addEventListener('click', () => {
            localStorage.setItem('edu360_aula_notes', notesTextarea.value);
            Swal.fire({
                title: '¡Guardado!',
                text: 'Tus notas se han sincronizado con el nodo local.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                background: '#121212',
                color: '#fff'
            });
        });

        // 4. Botón Limpiar
        btnClear.addEventListener('click', () => {
            Swal.fire({
                title: '¿Limpiar bitácora?',
                text: "Esta acción eliminará todas tus observaciones locales.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00a8e8',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar',
                background: '#121212',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    notesTextarea.value = '';
                    localStorage.removeItem('edu360_aula_notes');
                    Swal.fire({
                        title: 'Borrado',
                        text: 'La bitácora está limpia.',
                        icon: 'info',
                        background: '#121212',
                        color: '#fff',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // 5. Botón Exportar
        btnExport.addEventListener('click', () => {
            const content = notesTextarea.value;
            if (!content.trim()) {
                Swal.fire({
                    title: 'Bitácora Vacía',
                    text: 'No hay datos para exportar.',
                    icon: 'error',
                    background: '#121212',
                    color: '#fff'
                });
                return;
            }

            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            const date = new Date().toISOString().slice(0, 10);
            
            a.href = url;
            a.download = `bitacora_investigacion_${date}.txt`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                title: 'Exportación Exitosa',
                text: 'Se ha generado el archivo de bitácora.',
                icon: 'success',
                background: '#121212',
                color: '#fff'
            });
        });
    </script>

</body>
</html>