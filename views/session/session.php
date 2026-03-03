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
                        <i class="fas fa-eye toggle-password" id="togglePassword" style="position: absolute; right: 15px !important; left: auto !important; top: 50% !important; transform: translateY(-50%) !important; cursor: pointer; color: #888; z-index: 10;"></i>
                    </div>

                    <div class="login-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Recordar dispositivo
                        </label>
                        <a href="javascript:void(0)" class="forgot-link" onclick="handleForgotPassword()">¿Olvidó su contraseña?</a>
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

            async function handleForgotPassword() {
                const { value: email } = await Swal.fire({
                    title: 'Recuperar Acceso',
                    text: 'Ingrese su correo electrónico vinculado',
                    input: 'email',
                    inputPlaceholder: 'correo@ejemplo.com',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar Código',
                    background: '#0a0a0a',
                    color: '#fff',
                    inputAttributes: {
                        style: 'background: rgba(255,255,255,0.05); color: white; border: 1px solid #333;'
                    }
                });

                if (!email) return;

                Swal.fire({
                    title: 'Procesando Solicitud',
                    didOpen: () => { Swal.showLoading(); }
                });

                // Paso 1: Enviar código
                try {
                    const response = await fetch('<?php echo base_url('/public/servermail.php'); ?>', {
                        method: 'POST',
                        body: new URLSearchParams({
                            'getcodemail': 1,
                            'email': email,
                            'type': 'forgot'
                        })
                    });
                    const data = await response.json();

                    if (data.status !== 1) {
                        return Swal.fire('Error', data.message, 'error');
                    }

                    // Paso 2: Verificar código
                    const { value: verificationCode } = await Swal.fire({
                        title: 'Código de Seguridad',
                        html: `
                            <p style="color: #888;">Hemos enviado un código a <b>${email}</b></p>
                            <input type="text" id="reset_code" class="swal2-input" placeholder="000000" maxlength="6" style="text-align: center; font-size: 2rem; letter-spacing: 10px; background: rgba(0,0,0,0.5); color: #00ff88; border: 1px solid #333;">
                        `,
                        confirmButtonText: 'Verificar Identidad',
                        showCancelButton: true,
                        background: '#0a0a0a',
                        color: '#fff',
                        preConfirm: () => {
                            const code = document.getElementById('reset_code').value;
                            if (code.length !== 6) Swal.showValidationMessage('Ingrese el código de 6 dígitos');
                            return code;
                        }
                    });

                    if (!verificationCode) return;

                    Swal.fire({
                        title: 'Verificando...',
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const verifyRes = await fetch('<?php echo base_url('/public/servermail.php'); ?>', {
                        method: 'POST',
                        body: new URLSearchParams({
                            'verifycode': 1,
                            'email': email,
                            'codigo': verificationCode
                        })
                    });
                    const verifyData = await verifyRes.json();

                    if (verifyData.status !== 1) {
                        return Swal.fire('Error', 'Código inválido o expirado', 'error');
                    }

                    // Paso 3: Nueva contraseña
                    const { value: passwords } = await Swal.fire({
                        title: 'Restablecer Contraseña',
                        html: `
                            <div style="position: relative; margin-bottom: 20px;">
                                <input type="password" id="new_pw" class="swal2-input" placeholder="Nueva contraseña" style="width: 80%;">
                                <i class="fas fa-eye" onclick="toggleResetPw('new_pw')" style="position: absolute; right: 15%; top: 25px; cursor: pointer; color: #888;"></i>
                            </div>
                            <div style="position: relative;">
                                <input type="password" id="new_pw_rep" class="swal2-input" placeholder="Repetir contraseña" style="width: 80%;">
                                <i class="fas fa-eye" onclick="toggleResetPw('new_pw_rep')" style="position: absolute; right: 15%; top: 25px; cursor: pointer; color: #888;"></i>
                            </div>
                        `,
                        confirmButtonText: 'Actualizar Contraseña',
                        showCancelButton: true,
                        background: '#0a0a0a',
                        color: '#fff',
                        preConfirm: () => {
                            const p1 = document.getElementById('new_pw').value;
                            const p2 = document.getElementById('new_pw_rep').value;
                            if (p1.length < 8) return Swal.showValidationMessage('Mínimo 8 caracteres');
                            if (p1 !== p2) return Swal.showValidationMessage('Las contraseñas no coinciden');
                            return p1;
                        }
                    });

                    if (!passwords) return;

                    Swal.fire({
                        title: 'Actualizando...',
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const resetRes = await fetch('<?php echo base_url('/public/servermail.php'); ?>', {
                        method: 'POST',
                        body: new URLSearchParams({
                            'resetpassword': 1,
                            'email': email,
                            'password': passwords,
                            'codigo': verificationCode
                        })
                    });
                    const resetData = await resetRes.json();

                    if (resetData.status === 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Contraseña Actualizada',
                            text: 'Ahora puede iniciar sesión con su nueva clave',
                            background: '#0a0a0a',
                            color: '#fff'
                        });
                    } else {
                        Swal.fire('Error', resetData.message, 'error');
                    }

                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'Fallo en la comunicación con el sistema', 'error');
                }
            }

            function toggleResetPw(id) {
                const input = document.getElementById(id);
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                event.target.classList.toggle('fa-eye-slash');
            }

            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
        </script>


<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>