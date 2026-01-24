<?php
require_once __DIR__ . '/../../config/modulo.php';
require_once __DIR__ . '/../../views/layouts/header.php';
$_COOKIE['modulo'] = $_ENV['MODULO']; // Fuerza el uso de la DB staging en esta petición
$modulo = new Modulo();

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hash = $_POST['hash'];
    $nombre_completo = $_POST['nombre_completo'];
    $estatus_soberania = $_POST['estatus_soberania'];
    
    if(!$modulo->ifUsuarioExist($email)){
        $modulo->createUser($email, $password, $hash, $nombre_completo, $estatus_soberania);
        header('Location: ' . base_url('/session'));
        exit;
    }else{        
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El usuario ya existe',
        });
        </script>";
    }
}

?>
<style>
    .error {
        border-color: red;
    }
</style>
<main class="evolution-container">
        <div class="reg-card">
            <div class="reg-info">
                <h2>Inicia tu Evolución</h2>
                <p>Al registrarte en la Red Soberana, dejas de ser un usuario para convertirte en un <strong>Evolucionador</strong>. Tu actividad generará UDV (Unidades de Valor) que se acuñarán en tu bóveda personal.</p>
                
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
                    <span>Formulario de Ascensión</span>
                </div>

                <form id="formEvolucionador" method="post">
                    <div class="input-box">
                        <input type="text" id="nombre_completo" name="nombre_completo" required onkeyup="updateHash()">
                        <label>Nombre Completo</label>
                    </div>

                    <div class="input-box">
                        <input type="email" id="email" name="email" required onkeyup="updateHash()">
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
                            <option value="Activo" style="background: #111;">Estatus: Activo</option>
                            <option value="En Consolidación" style="background: #111;" selected>Estatus: En Consolidación</option>
                        </select>
                    </div>

                    <!-- Hidden field for hash -->
                    <input type="hidden" id="hash" name="hash" value="">

                    <div style="margin: 20px 0; font-size: 0.8rem; color: #888;">
                        <input type="checkbox" required> Acepto el protocolo de soberanía cognitiva y los términos de El Inquisidor.
                    </div>

                    <button type="submit" class="btn-register">
                        Consolidar Identidad
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Función espectacular para simular la creación del HASH en tiempo real
        function updateHash() {
            const nombre = document.getElementById('nombre_completo').value;
            const email = document.getElementById('email').value;
            const hashDisplay = document.getElementById('visual-hash');
            const hashText = document.getElementById('hash');
            
            if(nombre || email) {
                // Simulación de un hash SHA-256 basado en los inputs
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

        function validatePassword() {
            const password = document.getElementById('password').value;
            const passwordRepeat = document.getElementById('password_repeat').value;
            
            if(password !== passwordRepeat) {
                document.getElementById('password_repeat').setCustomValidity('Las contraseñas no coinciden');
                document.getElementById('password_repeat').classList.add('error');
                document.getElementById('password_repeat').value = '';
            } else {
                document.getElementById('password_repeat').setCustomValidity('');
                document.getElementById('password_repeat').classList.remove('error');
            }
        }

    </script>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>