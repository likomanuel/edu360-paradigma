<?php
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<style>
    .contact-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        max-width: 1200px;
        margin: 80px auto;
        padding: 0 20px;
    }

    .contact-info h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        background: linear-gradient(45deg, #00a8e8, #ffd700);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .contact-item {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        align-items: center;
    }

    .contact-item i {
        font-size: 1.5rem;
        color: #ffd700;
        width: 50px;
        height: 50px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .contact-form {
        background: rgba(255, 255, 255, 0.03);
        padding: 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.8);
    }

    .form-control {
        width: 100%;
        padding: 12px 20px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #00a8e8;
        outline: none;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(45deg, #00a8e8, #00509e);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 168, 232, 0.3);
    }

    @media (max-width: 992px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 40px;
        }
    }
</style>

<div class="contact-container">
    <div class="contact-info">
        <h1>Contáctanos</h1>
        <p style="font-size: 1.2rem; margin-bottom: 40px; color: rgba(255, 255, 255, 0.7);">Estamos aquí para resolver tus dudas sobre el ecosistema EDU360 y tu camino como Evolucionador.</p>
        
        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div>
                <h4 style="color: #fff;">Email</h4>
                <p>president@edu360global.org</p>
            </div>
        </div>

        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
                <h4 style="color: #fff;">Sede Digital</h4>
                <p>Infraestructura Global Descentralizada</p>
            </div>
        </div>

        <div class="contact-item">
            <i class="fas fa-globe"></i>
            <div>
                <h4 style="color: #fff;">Redes</h4>
                <p>@edu360global</p>
            </div>
        </div>
    </div>

    <div class="contact-form">
        <form id="contactForm">
            <input type="hidden" name="contact_form" value="1">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Tu nombre..." required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
            </div>
            <div class="form-group">
                <label>Asunto</label>
                <input type="text" name="asunto" class="form-control" placeholder="Motivo de contacto" required>
            </div>
            <div class="form-group">
                <label>Mensaje</label>
                <textarea name="mensaje" class="form-control" rows="5" placeholder="Cuéntanos más..." required></textarea>
            </div>
            <button type="submit" class="btn-submit" id="submitBtn">Enviar Mensaje</button>
            <div id="responseMessage" style="margin-top: 20px; text-align: center; display: none;"></div>
        </form>
    </div>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const btn = document.getElementById('submitBtn');
        const responseMsg = document.getElementById('responseMessage');
        const formData = new FormData(form);
        
        btn.disabled = true;
        btn.innerText = 'Enviando...';
        
        fetch('public/servermail.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            responseMsg.style.display = 'block';
            if(data.status === 1) {
                responseMsg.style.color = '#00ff00';
                responseMsg.innerText = '¡Mensaje enviado con éxito!';
                form.reset();
            } else {
                responseMsg.style.color = '#ff0000';
                responseMsg.innerText = 'Error: ' + data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            responseMsg.style.display = 'block';
            responseMsg.style.color = '#ff0000';
            responseMsg.innerText = 'Hubo un error al procesar tu solicitud.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Enviar Mensaje';
            setTimeout(() => {
                responseMsg.style.display = 'none';
            }, 5000);
        });
    });
    </script>
</div>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>
