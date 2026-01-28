<?php
require_once __DIR__ . '/../../config/modulo.php';
require_once __DIR__ . '/../../views/layouts/header.php';
$_COOKIE['modulo'] = $_ENV['MODULO']; // Fuerza el uso de la DB staging en esta petición
$modulo = new Modulo();

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if($modulo->ifUsuarioExist($email) && $password == trim($modulo->getPassword($email))){
        $user = $modulo->getUser($email);
        $_SESSION['staging'] = false;
        $_SESSION['email'] = $email;
        $_SESSION['status'] = $user['estatus_soberania'];
        $_SESSION['id_evolucionador'] = $user['id_evolucionador'];
        header('Location: ' . base_url('/index'));
        exit;
    }else{        
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Credenciales incorrectas',
        });
        </script>";
    }
}
?>

    <section class="login-wrapper">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-user-astronaut"></i>
                <h2>Acceso Cognitivo</h2>
                <p>Identifíquese en el sistema federal</p>
            </div>
            
                <form id="formAcceso" method="POST">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="login-input" placeholder="Correo electrónico" required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="login-input" placeholder="Contraseña de acceso" required>
                    </div>

                    <div class="login-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Recordar dispositivo
                        </label>
                        <a href="#" class="forgot-link">¿Olvidó su contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="login-footer">
                    <p>¿Aún no eres miembro? <a href="<?php echo base_url('/session/registro'); ?>">Solicitar acceso</a></p>
                </div>
            </div>
        </section>

        <script>
            let verificationSuccessful = false;
            document.getElementById('formAcceso').addEventListener('submit', function(e) {
                if (verificationSuccessful) return; // Permitir el envío real del formulario

                e.preventDefault();
                
                const formData = new FormData(this);
                const email = formData.get('email');
                const password = formData.get('password');

                Swal.fire({
                    title: 'Validando Credenciales',
                    text: 'Iniciando protocolo de acceso...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Paso 1: Validar contraseña y solicitar código
                fetch('<?php echo base_url('/public/servermail.php'); ?>', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'getcodemail': 1,
                        'email': email,
                        'password': password,
                        'type': 'login'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 1) {
                        // Paso 2: Mostrar modal para ingresar código
                        Swal.fire({
                            title: 'Verificación requerida',
                            html: `
                                <p style="color: #888;">Se ha enviado un código de acceso a <b>${email}</b></p>
                                <input type="text" id="verification_code" class="swal2-input" placeholder="000000" maxlength="6" style="text-align: center; font-size: 2rem; letter-spacing: 10px; background: rgba(0,0,0,0.5); color: #00ff88; border: 1px solid #333;">
                            `,
                            confirmButtonText: 'Verificar Identidad',
                            showCancelButton: true,
                            background: '#0a0a0a',
                            color: '#fff',
                            preConfirm: () => {
                                const code = Swal.getPopup().querySelector('#verification_code').value;
                                if (!code || code.length !== 6) {
                                    Swal.showValidationMessage('Ingrese el código de 6 dígitos');
                                }
                                return code;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                verifyAndLogin(email, result.value);
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Fallo en la comunicación con el nodo central', 'error');
                });
            });

            function verifyAndLogin(email, code) {
                Swal.fire({
                    title: 'Finalizando Autenticación',
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
                            title: 'Protocolo Autorizado',
                            text: 'Redireccionando al sistema...',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            verificationSuccessful = true;
                            document.getElementById('formAcceso').submit();
                        });
                    } else {
                        Swal.fire('Error', 'Código de verificación incorrecto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Fallo al verificar el código', 'error');
                });
            }
        </script>


<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>