<?php
require_once __DIR__ . '/../../config/modulo.php';
require_once __DIR__ . '/../../views/layouts/header.php';
$_COOKIE['modulo'] = $_ENV['MODULO']; // Fuerza el uso de la DB staging en esta petición
$modulo = new Modulo();

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if($modulo->ifStagingUsuarioExist($email) && $password == trim($modulo->getStagingPassword($email))){
        $_SESSION['staging'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['status'] = 'En Consolidacion';
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
                <h2>Acceso Staging Edu360</h2>
                <p>Modulo de Pruebas para el sistema federal</p>
            </div>
            
            <form action="#" method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input name="email" type="email" class="login-input" placeholder="Correo electrónico" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input name="password" type="password" class="login-input" placeholder="Contraseña de acceso" required>
                </div>

                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox"> Recordar dispositivo
                    </label>
                    <a href="#" class="forgot-link">¿Ayuda?</a>
                </div>

                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                <p>¿Entraste por Error aqui? <a href="<?php echo base_url('/'); ?>">Regresar</a></p>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/../../views/layouts/footer.php';
?>