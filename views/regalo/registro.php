<?php
require_once __DIR__ . '/../../config/modulo.php';
require_once __DIR__ . '/../../views/layouts/header.php';
$_COOKIE['modulo'] = $_ENV['MODULO'] ?? ''; 
$modulo = new Modulo();

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hash = $_POST['hash'];
    $nombre_completo = $_POST['nombre_completo'];
    $estatus_soberania = $_POST['estatus_soberania'];
    
    if(!$modulo->ifUsuarioExist($email)){
        $modulo->createUser($email, $password, $hash, $nombre_completo, $estatus_soberania);
        // REDIRECCION ESPECÍFICA PARA EL FLUJO DE TARJETAS DE REGALO
        header('Location: ' . base_url('regalo/pago?code=' . urlencode($_GET['code'])));
        exit;
    }else{        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El usuario ya existe. Si ya tienes cuenta, por favor inicia sesión o contacta a soporte.',
            });
        });
        </script>";
    }
}
?>
<style>
    .error { border-color: red; }
    
    /* Pequeños ajustes visuales para la versión de regalo */
    .reg-card { border: 1px solid rgba(255, 215, 0, 0.3); box-shadow: 0 0 30px rgba(255, 215, 0, 0.1); }
    .status-badge { background: rgba(255, 215, 0, 0.2); color: #FFD700; border: 1px solid #FFD700; }
    .btn-register { background: linear-gradient(45deg, #FFD700, #FDB931); color: black; font-weight: bold; }
</style>
<main class="evolution-container">
    <div class="reg-card">
        <div class="reg-info">
            <h2>Consolida tu Identidad</h2>
            <p>Estás a un paso de reclamar tu regalo e ingresar a la Red Soberana. Registra tus datos para asegurar tu Bóveda Cognitiva.</p>
            
            <div class="hash-monitor">
                <span class="hash-label">Hash de Identidad Generado:</span>
                <div id="visual-hash">PENDING_ID_ENCRYPTION_...</div>
            </div>

            <div style="margin-top: auto; font-size: 0.7rem; color: #555;">
                <i class="fas fa-lock"></i> Protocolo de encriptación AES-256 habilitado.
            </div>
        </div>

        <div class="reg-form">
            <div class="form-header">
                <div class="status-badge">Estatus: En Consolidación</div><br>
                <span>Registro de Regalo</span>
            </div>

            <form id="formEvolucionador" method="post">
                <div class="input-box">
                    <input type="text" id="nombre_completo" name="nombre_completo" required onkeyup="updateHash()">
                    <label>Nombre Completo</label>
                </div>

                <div class="input-box">
                    <input type="email" id="email" name="email" required onkeyup="updateHash()" value="<?php echo htmlspecialchars($tarjeta['destinatario_email'] ?? ''); ?>" <?php echo !empty($tarjeta['destinatario_email']) ? 'readonly style="color:#888;"' : ''; ?>>
                    <label>Email Institucional / Soberano</label>
                </div>

                <div class="input-box">
                    <input minlength="8" type="password" id="password" name="password" required>
                    <label>Contraseña</label>
                </div>

                <div class="input-box">
                    <input minlength="8" type="password" id="password_repeat" name="password_repeat" required onblur="validatePassword()">
                    <label>Repetir Contraseña</label>
                </div>

                <div class="input-box">
                    <select name="estatus_soberania" id="estatus_soberania" style="background: transparent; color: white; border: none; border-bottom: 2px solid #333; width: 100%; padding: 10px 0; pointer-events: none;">
                        <option value="En Consolidación" style="background: #111;" selected>Estatus: En Consolidación</option>
                    </select>
                </div>

                <!-- Hidden field for hash -->
                <input type="hidden" id="hash" name="hash" value="">

                <div style="margin: 20px 0; font-size: 0.8rem; color: #888;">
                    <input type="checkbox" required> Acepto el protocolo de soberanía cognitiva y los términos de El Inquisidor.
                </div>

                <button type="submit" class="btn-register">
                    Consolidar y Reclamar Regalo
                </button>
            </form>
        </div>
    </div>
</main>

<script>
function updateHash() {
    const nombre = document.getElementById('nombre_completo').value;
    const email = document.getElementById('email').value;
    const hashDisplay = document.getElementById('visual-hash');
    const hashText = document.getElementById('hash');
    
    if(nombre || email) {
        const rawString = nombre + email + "S0V3R3IGN";
        let hash = 0;
        for (let i = 0; i < rawString.length; i++) {
            hash = ((hash << 5) - hash) + rawString.charCodeAt(i);
            hash |= 0; 
        }
        const hexHash = Math.abs(hash).toString(16).repeat(4).substring(0, 42);
        hashDisplay.innerText = "0x" + hexHash.toUpperCase();
        hashText.value = "0x" + hexHash.toUpperCase();
    } else {
        hashDisplay.innerText = "PENDING_ID_ENCRYPTION_...";
        hashText.value = "";
    }
}

// Inicializar el hash en caso de que el email ya venga pre-llenado
document.addEventListener('DOMContentLoaded', function() {
    updateHash();
});

function validatePassword() {
    const password = document.getElementById('password').value;
    const passwordRepeat = document.getElementById('password_repeat').value;
    
    if(password !== passwordRepeat) {
        document.getElementById('password_repeat').setCustomValidity('Las contraseñas no coinciden');
        document.getElementById('password_repeat').classList.add('error');
    } else {
        document.getElementById('password_repeat').setCustomValidity('');
        document.getElementById('password_repeat').classList.remove('error');
    }
}

document.getElementById('formEvolucionador').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const email = formData.get('email');

    Swal.fire({
        title: 'Consolidando Identidad',
        text: 'Enviando código de seguridad a su correo...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('<?php echo base_url('/public/servermail.php'); ?>', {
        method: 'POST',
        body: new URLSearchParams({
            'getcodemail': 1,
            'email': email,
            'type': 'registro'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 1) {
            Swal.fire({
                title: 'Protocolo de Seguridad',
                html: `
                    <p style="color: #888;">Hemos enviado un código de 6 dígitos a <b>${email}</b></p>
                    <input type="text" id="verification_code" class="swal2-input" placeholder="000000" maxlength="6" style="text-align: center; font-size: 2rem; letter-spacing: 10px; background: rgba(0,0,0,0.5); color: #00ff88; border: 1px solid #333;">
                `,
                confirmButtonText: 'Verificar e Iniciar Ascensión',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                background: '#0a0a0a',
                color: '#fff',
                preConfirm: () => {
                    const code = Swal.getPopup().querySelector('#verification_code').value;
                    if (!code || code.length !== 6) {
                        Swal.showValidationMessage('Ingrese un código válido de 6 dígitos');
                    }
                    return code;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    verifyAndSubmit(email, result.value);
                }
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Fallo en la conexión con el servidor federal', 'error');
    });
});

function verifyAndSubmit(email, code) {
    Swal.fire({
        title: 'Verificando Protocolo',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('<?php echo base_url('/public/servermail.php'); ?>', {
        method: 'POST',
        body: new URLSearchParams({
            'verifycode': 1,
            'email': email,
            'codigo': code
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 1) {
            Swal.fire({
                title: 'Identidad Consolidada',
                text: 'Protocolo completado con éxito. Redirigiendo a activación de Nodo...',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                document.getElementById('formEvolucionador').submit();
            });
        } else {
            Swal.fire('Error', 'Código de verificación incorrecto', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
