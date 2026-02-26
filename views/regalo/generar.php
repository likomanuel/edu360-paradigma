<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>
<style>
    :root {
        --primary-gold: #ffd700;
        --secondary-gold: #ffb300;
        --dark-bg: #0a0a0a;
        --card-bg: rgba(20, 20, 20, 0.7);
    }
    body {
        background-color: var(--dark-bg);
        color: white;
        background-image: 
            radial-gradient(circle at 15% 50%, rgba(255, 215, 0, 0.08), transparent 25%),
            radial-gradient(circle at 85% 30%, rgba(255, 179, 0, 0.08), transparent 25%);
        font-family: 'Montserrat', sans-serif;
    }
    .gift-container {
        max-width: 800px;
        margin: 60px auto;
        padding: 40px;
        background: var(--card-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255, 215, 0, 0.05);
        animation: fadeIn 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }
    .gift-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
    }
    .title-box {
        text-align: center;
        margin-bottom: 40px;
    }
    .title-box h1 {
        font-size: 2.5rem;
        background: linear-gradient(45deg, #fff, var(--primary-gold));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .title-box p {
        color: #aaa;
        font-size: 1.1rem;
    }
    .form-group {
        margin-bottom: 25px;
        position: relative;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--primary-gold);
        font-weight: 600;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }
    .form-control {
        width: 100%;
        padding: 15px;
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: white;
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary-gold);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
        background: rgba(0,0,0,0.6);
    }
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    .btn-grad {
        background-image: linear-gradient(to right, #FFD700 0%, #FDB931 51%, #FFD700 100%);
        margin: 20px 0;
        padding: 15px 45px;
        text-align: center;
        text-transform: uppercase;
        transition: 0.5s;
        background-size: 200% auto;
        color: #000;            
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
        border-radius: 50px;
        border: none;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        width: 100%;
        display: block;
        letter-spacing: 2px;
    }
    .btn-grad:hover {
        background-position: right center; /* change the direction of the change here */
        color: #000;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 5px 25px rgba(255, 215, 0, 0.6);
    }
    
    /* Result Box */
    .result-box {
        display: none;
        background: rgba(0, 255, 136, 0.05);
        border: 1px solid #00ff88;
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
        text-align: center;
        animation: slideUp 0.5s ease;
    }
    .result-box h3 {
        color: #00ff88;
        margin-bottom: 20px;
    }
    .link-container {
        display: flex;
        align-items: center;
        background: rgba(0,0,0,0.5);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
        margin-top: 15px;
    }
    .link-input {
        flex-grow: 1;
        background: transparent;
        border: none;
        color: white;
        font-size: 1rem;
        outline: none;
    }
    .btn-copy {
        background: #00ff88;
        color: black;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-copy:hover {
        background: #00cc6a;
        transform: scale(1.05);
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container">
    <div class="gift-container">
        <div class="title-box">
            <h1><i class="fas fa-gift"></i> Regalar Soberanía</h1>
            <p>Genera una tarjeta de regalo EDU360 University.</p>
        </div>

        <form id="giftForm">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Tu Correo (Sponsor)</label>
                <input type="email" name="sender_email" class="form-control" placeholder="Tu correo electrónico..." required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Correo del Destinatario</label>
                <input type="email" name="destinatario_email" class="form-control" placeholder="A quién le regalaremos el acceso..." required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-comment-dots"></i> Mensaje de Bienvenida</label>
                <textarea name="mensaje" class="form-control" placeholder="Escribe un mensaje inspirador para el Evolucionador..." required></textarea>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-dollar-sign"></i> Monto (USD)</label>
                <input type="number" name="monto_cobrar" class="form-control" placeholder="Ej. 750" value="750" required min="1" step="0.01">
            </div>
            
            <button type="submit" class="btn-grad" id="btnSubmit">Generar Tarjeta <i class="fas fa-magic"></i></button>
        </form>

        <div class="result-box" id="resultBox">
            <h3><i class="fas fa-check-circle"></i> ¡Tarjeta Generada con Éxito!</h3>
            <p>Comparte el siguiente enlace con el destinatario para que inicie su proceso de registro de inmediato.</p>
            <div class="link-container">
                <input type="text" id="giftLink" class="link-input" readonly value="">
                <button class="btn-copy" onclick="copyLink()"><i class="fas fa-copy"></i> Copiar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('giftForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const btnSubmit = document.getElementById('btnSubmit');
    const formData = new FormData(form);
    
    // UI Loading state
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    btnSubmit.disabled = true;

    fetch('<?php echo base_url("regalo/guardar"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status) {
            document.getElementById('giftForm').style.display = 'none';
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('giftLink').value = data.link;
            
            Swal.fire({
                icon: 'success',
                title: 'Creada',
                text: 'La tarjeta se generó y guardó correctamente en la bóveda.',
                background: '#111',
                color: '#fff'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                background: '#111',
                color: '#fff'
            });
            btnSubmit.innerHTML = 'Generar Tarjeta Mágica <i class="fas fa-magic"></i>';
            btnSubmit.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Problema de conexión con el servidor', 'error');
        btnSubmit.innerHTML = 'Generar Tarjeta Mágica <i class="fas fa-magic"></i>';
        btnSubmit.disabled = false;
    });
});

function copyLink() {
    const linkInput = document.getElementById('giftLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    Swal.fire({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        title: 'Enlace copiado al portapapeles',
        background: '#00ff88',
        color: '#000'
    });
}
</script>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
